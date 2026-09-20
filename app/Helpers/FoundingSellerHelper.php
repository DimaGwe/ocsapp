<?php

namespace App\Helpers;

/**
 * FoundingSellerHelper - Founding Seller Partner Program (Seller Agreement Sec
 * 3.2/6.1-6.2/Schedule A): a 20-shop cohort locked to Experience-tier commission
 * (12% delivery / 6% pickup, no monthly fee) for 12 months from approval, plus
 * the first 5 completed delivery orders commission-free.
 *
 * Same single-row-mutex pattern as FoundingBuyerHelper - a global, race-safe
 * counter (founding_seller_program) decides eligibility at the moment a shop is
 * approved (AdminShopController::approve(), the confirmed "Date d'effet" per
 * Seller Agreement Sec 3.1), locked via SELECT...FOR UPDATE so two shops
 * approved in quick succession can't both claim slot #20.
 */
class FoundingSellerHelper
{
    const TOTAL_SLOTS = 20;
    const LOCK_MONTHS = 12;
    const FREE_DELIVERIES = 5;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Call once, at shop-approval time. Idempotent - a shop already granted
     * founding status just re-reports its existing slot.
     *
     * @return array{eligible: bool, founding_partner_number: ?int}
     */
    public static function claimSlotIfEligible(int $shopId): array
    {
        $db = self::db();

        $shopStmt = $db->prepare("SELECT founding_partner, founding_partner_number FROM shops WHERE id = ? FOR UPDATE");
        $shopStmt->execute([$shopId]);
        $shop = $shopStmt->fetch(\PDO::FETCH_ASSOC);
        if ($shop && (int)$shop['founding_partner'] === 1) {
            return ['eligible' => true, 'founding_partner_number' => (int)$shop['founding_partner_number']];
        }

        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_seller_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return ['eligible' => false, 'founding_partner_number' => null];
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_seller_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE shops SET
                    founding_partner = 1, founding_partner_number = ?, founding_partner_granted_at = NOW(),
                    founding_partner_expires_at = DATE_ADD(NOW(), INTERVAL ? MONTH),
                    founding_free_deliveries_remaining = ?,
                    subscription_package = 'Experience', commission_rate = 12.00, pickup_commission_rate = 6.00
                WHERE id = ?
            ")->execute([$slotNumber, self::LOCK_MONTHS, self::FREE_DELIVERIES, $shopId]);

            if ($startedTransaction) $db->commit();

            return ['eligible' => true, 'founding_partner_number' => $slotNumber];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingSellerHelper::claimSlotIfEligible error: ' . $e->getMessage());
            return ['eligible' => false, 'founding_partner_number' => null];
        }
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_seller_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }
}
