<?php

namespace App\Helpers;

/**
 * FoundingBuyerHelper - Founding Buyer Program, First-Order Free Delivery
 * (Buyer Terms of Service Sec. 12.1/12.2): "the first two hundred (200)
 * buyer accounts to place a qualifying delivery Order," waiving that
 * Order's base Delivery Fee only - Oversize/Additional-Stop/Long-Distance
 * surcharges still apply (Sec 12.3).
 *
 * Two steps (Dima 2026-09-28: founding status is earned by PAYING, not by
 * placing an order):
 *  1. Checkout - isEligibleAtCheckout() decides the fee waiver. The fee shown
 *     at checkout must already be final, so the waiver is locked in on the
 *     order row (orders.founding_buyer_delivery_waived), but no slot is taken.
 *  2. Payment confirmed - grantForPaidOrders() claims the slot for the buyer
 *     of a paid order that carries the waiver: founding number, badge,
 *     Founders' Wall, confirmation email. Called from every buyer-order
 *     payment path (PaymentController::completePayment for card/PayPal/
 *     store credit, AdminOrdersController::markAsPaid for Interac).
 *
 * Unpaid orders never consume a slot, so an abandoned or unpaid order costs
 * the program nothing. Near the cap, a few waived orders can be pending while
 * only one slot is left: the first to pay gets the slot, the others keep the
 * waived fee they were shown but get no status.
 *
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
     * Call once per checkout (not once per shop-order), inside the checkout
     * transaction, before creating the order row(s): "first delivery Order"
     * means the whole checkout, even though it's stored as one order row per
     * shop. Locks the user row, so two checkouts by the same account run one
     * after the other and can't both get the waiver.
     *
     * Eligible = not already a Founding Buyer, a slot is still open, no paid
     * order yet, and no other unpaid order already carrying the waiver (one
     * free delivery per account, even with several unpaid orders).
     */
    public static function isEligibleAtCheckout(int $userId): bool
    {
        $db = self::db();
        try {
            $userStmt = $db->prepare("SELECT founding_buyer FROM users WHERE id = ? FOR UPDATE");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
            if (!$user || (int)$user['founding_buyer'] === 1) {
                return false;
            }

            if (self::remainingSlots() <= 0) {
                return false;
            }

            $orderStmt = $db->prepare("
                SELECT COUNT(*) FROM orders
                WHERE user_id = ?
                  AND (payment_status = 'paid'
                       OR (founding_buyer_delivery_waived > 0 AND status NOT IN ('cancelled', 'refunded')))
            ");
            $orderStmt->execute([$userId]);
            return (int)$orderStmt->fetchColumn() === 0;
        } catch (\Exception $e) {
            error_log('FoundingBuyerHelper::isEligibleAtCheckout error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Payment confirmed for these orders: grant Founding Buyer status to the
     * buyer of any paid order carrying the waiver, if a slot is still open.
     * Idempotent (already-granted accounts are skipped), so it is safe on the
     * webhook + redirect double-fire. Runs its own transaction, so call it
     * after the payment transaction has committed. Never throws.
     */
    public static function grantForPaidOrders(array $orderIds): void
    {
        $orderIds = array_values(array_filter(array_map('intval', $orderIds)));
        if (empty($orderIds)) {
            return;
        }

        $db = self::db();
        try {
            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
            $stmt = $db->prepare("
                SELECT DISTINCT user_id FROM orders
                WHERE id IN ($placeholders) AND payment_status = 'paid' AND founding_buyer_delivery_waived > 0
            ");
            $stmt->execute($orderIds);
            $userIds = array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
        } catch (\Exception $e) {
            error_log('FoundingBuyerHelper::grantForPaidOrders lookup error: ' . $e->getMessage());
            return;
        }

        foreach ($userIds as $userId) {
            $number = self::claimSlot($userId);
            if ($number !== null) {
                logger("Founding Buyer #{$number} granted to user #{$userId} on payment", 'info');
                FoundersWallHelper::onFoundingGrantedForUser($userId, 'buyer', $number);
            }
        }
    }

    /** Takes the next slot for this account. Null when already granted, full, or on error. */
    private static function claimSlot(int $userId): ?int
    {
        $db = self::db();
        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $userStmt = $db->prepare("SELECT founding_buyer FROM users WHERE id = ? FOR UPDATE");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_buyer_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || (int)$user['founding_buyer'] === 1
                || !$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return null;
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_buyer_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE users SET founding_buyer = 1, founding_buyer_number = ?, founding_buyer_granted_at = NOW()
                WHERE id = ?
            ")->execute([$slotNumber, $userId]);

            if ($startedTransaction) $db->commit();

            return $slotNumber;
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingBuyerHelper::claimSlot error: ' . $e->getMessage());
            return null;
        }
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_buyer_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }
}
