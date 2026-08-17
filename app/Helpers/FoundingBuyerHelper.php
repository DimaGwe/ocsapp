<?php

namespace App\Helpers;

/**
 * FoundingBuyerHelper - Founding Buyer Program, First-Order Free Delivery
 * (Buyer Terms of Service Sec. 12.1/12.2): "the first two hundred (200)
 * buyer accounts to place a qualifying delivery Order," waiving that
 * Order's base Delivery Fee only - Oversize/Additional-Stop/Long-Distance
 * surcharges still apply (Sec 12.3).
 *
 * Eligibility is locked in at order-creation time, not payment time - the
 * fee shown to the buyer at checkout must already be final, and the ToS
 * frames this as a property of the Order itself ("your first delivery Order
 * carries no Delivery Fee"), not something contingent on payment succeeding.
 * The 200-slot counter is a single-row mutex (founding_buyer_program),
 * locked via SELECT...FOR UPDATE so two buyers racing for slot #200 can't
 * both win it - same pattern as every other shared-balance helper in this
 * codebase (StoreCreditHelper, BusinessCreditNoteHelper).
 */
class FoundingBuyerHelper
{
    const TOTAL_SLOTS = 200;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Call once per checkout (not once per shop-order) before creating the
     * order row(s) for a multi-shop cart - "first delivery Order" means the
     * whole checkout, even though it's stored internally as one order row
     * per shop.
     *
     * @return array{eligible: bool, founding_buyer_number: ?int}
     */
    public static function claimSlotIfEligible(int $userId): array
    {
        $db = self::db();

        $userStmt = $db->prepare("SELECT founding_buyer, founding_buyer_number FROM users WHERE id = ? FOR UPDATE");
        // Already-granted accounts (e.g. a second checkout session in the
        // same request lifecycle) just re-report their existing slot -
        // never claims a second slot for the same account.
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
        if ($user && (int)$user['founding_buyer'] === 1) {
            return ['eligible' => true, 'founding_buyer_number' => (int)$user['founding_buyer_number']];
        }

        // "First delivery Order" - this account must have zero prior orders
        // of any kind. A prior cancelled/abandoned order still counts (the
        // slot, like the fee itself, is locked in at order-creation, not
        // payment - consistent with how this account's very first order
        // already works elsewhere in this checkout flow).
        $countStmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $priorOrders = (int)$countStmt->fetchColumn();
        if ($priorOrders > 0) {
            return ['eligible' => false, 'founding_buyer_number' => null];
        }

        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_buyer_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return ['eligible' => false, 'founding_buyer_number' => null];
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_buyer_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE users SET founding_buyer = 1, founding_buyer_number = ?, founding_buyer_granted_at = NOW()
                WHERE id = ?
            ")->execute([$slotNumber, $userId]);

            if ($startedTransaction) $db->commit();

            return ['eligible' => true, 'founding_buyer_number' => $slotNumber];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingBuyerHelper::claimSlotIfEligible error: ' . $e->getMessage());
            return ['eligible' => false, 'founding_buyer_number' => null];
        }
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_buyer_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }
}
