<?php

namespace App\Helpers;

/**
 * FoundingBusinessHelper - Founding Business Partner Program (Business
 * Account Agreement Sec 4.3/7.9/8.12, draft): a 5-account cohort locked to
 * Debutant-tier Distribution (5% commission, $0/mo fee waived) for 6 months
 * from approval. The Approvisionnement "first $10,000 volume fee-free"
 * waiver is deliberately deferred - its fee-calc call site hasn't been
 * located yet - so this only covers the Distribution side. "Dedicated
 * account manager" is a manual ops assignment, not gated here.
 *
 * business_profiles has no commission_rate column of its own (rate is
 * normally only reached via distribution_plan_id -> distribution_plans),
 * so the locked rate lives in founding_commission_rate_override and must be
 * read preferentially wherever a shipment's commission is calculated - see
 * applyLazyExpiryIfNeeded().
 *
 * Same single-row-mutex pattern as FoundingSupplierHelper - a global,
 * race-safe counter (founding_business_program) decides eligibility at the
 * moment a business account is approved (AdminBusinessController::approve()),
 * locked via SELECT...FOR UPDATE.
 */
class FoundingBusinessHelper
{
    const TOTAL_SLOTS = 5;
    const LOCK_MONTHS = 6;
    const LOCKED_RATE = 5.00;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Call once, at business-approval time. Idempotent - a business already
     * granted founding status just re-reports its existing slot.
     *
     * @return array{eligible: bool, founding_partner_number: ?int}
     */
    public static function claimSlotIfEligible(int $businessId): array
    {
        $db = self::db();

        $bizStmt = $db->prepare("SELECT founding_partner, founding_partner_number FROM business_profiles WHERE id = ? FOR UPDATE");
        $bizStmt->execute([$businessId]);
        $biz = $bizStmt->fetch(\PDO::FETCH_ASSOC);
        if ($biz && (int)$biz['founding_partner'] === 1) {
            return ['eligible' => true, 'founding_partner_number' => (int)$biz['founding_partner_number']];
        }

        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_business_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return ['eligible' => false, 'founding_partner_number' => null];
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_business_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE business_profiles SET
                    founding_partner = 1, founding_partner_number = ?, founding_partner_granted_at = NOW(),
                    founding_partner_expires_at = DATE_ADD(NOW(), INTERVAL ? MONTH),
                    founding_commission_rate_override = ?
                WHERE id = ?
            ")->execute([$slotNumber, self::LOCK_MONTHS, self::LOCKED_RATE, $businessId]);

            if ($startedTransaction) $db->commit();

            return ['eligible' => true, 'founding_partner_number' => $slotNumber];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingBusinessHelper::claimSlotIfEligible error: ' . $e->getMessage());
            return ['eligible' => false, 'founding_partner_number' => null];
        }
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_business_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }

    /**
     * Lazy expiry (same pragmatic choice as FoundingSupplierHelper's - no
     * cron infrastructure exists in this codebase): call before any code
     * reads a business's commission rate or monthly fee for a real
     * charge/invoice calculation. A Founding business whose 6-month lock has
     * passed drops the override, falling back to its plan's standard rate
     * and billing as a side effect of that read. No-op if the business
     * isn't Founding, or is Founding but still within the window.
     *
     * Wired into AdminShipmentController's two real commission-calculation
     * call sites and scheduled_tasks/bill_distribution_plans.php's charge.
     */
    public static function applyLazyExpiryIfNeeded(int $businessId): void
    {
        $db = self::db();

        $stmt = $db->prepare("SELECT founding_partner, founding_partner_expires_at FROM business_profiles WHERE id = ? LIMIT 1");
        $stmt->execute([$businessId]);
        $biz = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$biz || (int)$biz['founding_partner'] !== 1) return;
        if (!$biz['founding_partner_expires_at']) return;
        if (strtotime($biz['founding_partner_expires_at']) >= time()) return;

        $db->prepare("
            UPDATE business_profiles SET founding_commission_rate_override = NULL
            WHERE id = ?
        ")->execute([$businessId]);
    }

    /**
     * Whether a business's Founding rate lock is currently active (granted,
     * not yet expired). Callers should still call applyLazyExpiryIfNeeded()
     * first so a stale-but-unexpired-in-DB row doesn't get treated as active.
     */
    public static function isActive(array $business): bool
    {
        return (int)($business['founding_partner'] ?? 0) === 1
            && $business['founding_commission_rate_override'] !== null;
    }
}
