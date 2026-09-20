<?php

namespace App\Helpers;

/**
 * FoundingDriverHelper - Founding Driver Partner Program (Driver Agreement
 * Sec 6.2/8.13/Schedule D, draft): a 50-driver cohort earning a permanent
 * "Founding Driver" badge. The milestone bonus ($100/20 deliveries), referral
 * bonus ($50), and Gold-tier dispatch priority described in the source PDF
 * are deliberately deferred - no crediting/tier mechanism exists yet in this
 * codebase - so this only tracks cohort membership. No expiry: unlike the
 * Seller/Supplier rate locks, there is nothing here that reverts over time.
 *
 * Same single-row-mutex pattern as FoundingSellerHelper - a global, race-safe
 * counter (founding_driver_program) decides eligibility at the moment a
 * driver application is approved (AdminDeliveryController::approveApplicationPipeline()),
 * locked via SELECT...FOR UPDATE so two approvals in quick succession can't
 * both claim slot #50.
 */
class FoundingDriverHelper
{
    const TOTAL_SLOTS = 50;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Call once, at driver-approval time. Idempotent - a driver already
     * granted founding status just re-reports its existing slot.
     *
     * @return array{eligible: bool, founding_driver_number: ?int}
     */
    public static function claimSlotIfEligible(int $userId): array
    {
        $db = self::db();

        $userStmt = $db->prepare("SELECT founding_driver, founding_driver_number FROM users WHERE id = ? FOR UPDATE");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
        if ($user && (int)$user['founding_driver'] === 1) {
            return ['eligible' => true, 'founding_driver_number' => (int)$user['founding_driver_number']];
        }

        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_driver_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return ['eligible' => false, 'founding_driver_number' => null];
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_driver_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE users SET
                    founding_driver = 1, founding_driver_number = ?, founding_driver_granted_at = NOW()
                WHERE id = ?
            ")->execute([$slotNumber, $userId]);

            if ($startedTransaction) $db->commit();

            return ['eligible' => true, 'founding_driver_number' => $slotNumber];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingDriverHelper::claimSlotIfEligible error: ' . $e->getMessage());
            return ['eligible' => false, 'founding_driver_number' => null];
        }
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_driver_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }
}
