<?php

namespace App\Helpers;

/**
 * StoreCreditHelper - Buyer Store Credit (Returns & Refund Policy Track A,
 * Sec. A6). "If you choose OCSAPP store credit instead of a cash refund, we
 * add a 10% bonus value to the credit... always optional; cash refund
 * remains your right under Quebec consumer law and is never withheld in
 * favor of credit."
 *
 * Every balance change writes an audit row to store_credit_transactions -
 * same pattern already used for business net-30 credit (distribution_credit_
 * events, Sec 5) - the running balance on users.store_credit_balance is a
 * denormalized cache for fast reads, never the source of truth on its own.
 */
class StoreCreditHelper
{
    /** Sec. A6: 10% bonus when a buyer chooses store credit over cash refund. */
    const REFUND_BONUS_RATE = 0.10;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    public static function getBalance(int $userId): float
    {
        $stmt = self::db()->prepare("SELECT store_credit_balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Founding Buyer Program referral credit (Buyer ToS Sec. 12.1): "$5
     * credit toward a future Delivery Fee" for both the referrer and the
     * referred buyer, unlimited. Reuses this same balance/ledger rather than
     * a second wallet - a $5 credit spends exactly like store credit already
     * does at checkout.
     */
    public static function addReferralCredit(int $userId, float $amount, string $note): array
    {
        if ($amount <= 0) {
            return ['success' => false, 'new_balance' => null, 'error' => 'Referral credit amount must be greater than zero.'];
        }
        return self::adjustBalance($userId, $amount, 'referral_bonus', $note, null, null);
    }

    /**
     * Issues store credit for a claim resolved in the buyer's favour, at
     * the claimed value plus the 10% bonus (Sec. A6). This is the store-
     * credit alternative to ReturnsDispatchHelper::refundBuyer() - the two
     * are mutually exclusive per claim, chosen by the buyer's
     * order_claims.preferred_refund_method at filing time, never both.
     *
     * @return array{success: bool, credited_amount: ?float, bonus_amount: ?float, new_balance: ?float, error: ?string}
     */
    public static function addClaimRefundCredit(int $userId, float $claimedValue, int $claimId): array
    {
        if ($claimedValue <= 0) {
            return ['success' => false, 'credited_amount' => null, 'bonus_amount' => null, 'new_balance' => null, 'error' => 'Claimed value must be greater than zero.'];
        }

        $bonusAmount = round($claimedValue * self::REFUND_BONUS_RATE, 2);
        $totalCredit = round($claimedValue + $bonusAmount, 2);

        $result = self::adjustBalance(
            $userId,
            $totalCredit,
            'claim_refund_bonus',
            "Store credit for claim #{$claimId}: \${$claimedValue} + 10% bonus (\${$bonusAmount})",
            $claimId,
            null
        );

        if (!$result['success']) {
            return ['success' => false, 'credited_amount' => null, 'bonus_amount' => null, 'new_balance' => null, 'error' => $result['error']];
        }

        return [
            'success' => true,
            'credited_amount' => $totalCredit,
            'bonus_amount' => $bonusAmount,
            'new_balance' => $result['new_balance'],
            'error' => null,
        ];
    }

    /**
     * Applies up to $requestedAmount of the buyer's available store credit
     * toward an order at checkout, capped at whatever balance actually
     * exists - never goes negative. Returns the amount actually applied,
     * which may be less than requested.
     *
     * @return array{success: bool, applied_amount: float, new_balance: ?float, error: ?string}
     */
    public static function applyToCheckout(int $userId, float $requestedAmount, int $orderId): array
    {
        if ($requestedAmount <= 0) {
            return ['success' => true, 'applied_amount' => 0.00, 'new_balance' => self::getBalance($userId), 'error' => null];
        }

        $available = self::getBalance($userId);
        $applyAmount = round(min($requestedAmount, $available), 2);

        if ($applyAmount <= 0) {
            return ['success' => true, 'applied_amount' => 0.00, 'new_balance' => $available, 'error' => null];
        }

        $result = self::adjustBalance(
            $userId,
            -$applyAmount,
            'checkout_applied',
            "Applied at checkout to order #{$orderId}",
            null,
            $orderId
        );

        if (!$result['success']) {
            return ['success' => false, 'applied_amount' => 0.00, 'new_balance' => $available, 'error' => $result['error']];
        }

        return ['success' => true, 'applied_amount' => $applyAmount, 'new_balance' => $result['new_balance'], 'error' => null];
    }

    /**
     * Shared balance-adjustment primitive - locks the user row, applies the
     * delta, writes the audit row, all in one transaction so concurrent
     * checkout + claim-refund requests can never race the balance.
     *
     * @return array{success: bool, new_balance: ?float, error: ?string}
     */
    private static function adjustBalance(int $userId, float $delta, string $type, string $notes, ?int $claimId, ?int $orderId): array
    {
        $db = self::db();
        $startedTransaction = false;

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $stmt = $db->prepare("SELECT store_credit_balance FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $currentBalance = $stmt->fetchColumn();

            if ($currentBalance === false) {
                if ($startedTransaction) $db->rollBack();
                return ['success' => false, 'new_balance' => null, 'error' => 'User not found.'];
            }

            $newBalance = round((float)$currentBalance + $delta, 2);
            if ($newBalance < 0) {
                if ($startedTransaction) $db->rollBack();
                return ['success' => false, 'new_balance' => null, 'error' => 'Insufficient store credit balance.'];
            }

            $db->prepare("UPDATE users SET store_credit_balance = ? WHERE id = ?")->execute([$newBalance, $userId]);

            $db->prepare("
                INSERT INTO store_credit_transactions
                (user_id, amount, type, reference_claim_id, reference_order_id, balance_after, notes, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ")->execute([$userId, $delta, $type, $claimId, $orderId, $newBalance, $notes]);

            if ($startedTransaction) $db->commit();

            return ['success' => true, 'new_balance' => $newBalance, 'error' => null];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('StoreCreditHelper::adjustBalance error: ' . $e->getMessage());
            return ['success' => false, 'new_balance' => null, 'error' => $e->getMessage()];
        }
    }
}
