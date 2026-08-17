<?php

namespace App\Helpers;

/**
 * BusinessCreditNoteHelper - Track B Credit Note resolution option (Returns
 * Policy Sec. B5): "applied to your account balance for use against future
 * orders, at +5% bonus value if selected instead of a cash refund (lower
 * than the Track A consumer incentive, reflecting typical B2B order volume
 * and payment terms)."
 *
 * Same balance-locked-ledger shape as StoreCreditHelper (Sec A6), scoped to
 * business_profiles instead of users, at B5's 5% rate instead of A6's 10%.
 */
class BusinessCreditNoteHelper
{
    /** Sec. B5: 5% bonus when a business chooses credit note over cash. */
    const REFUND_BONUS_RATE = 0.05;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    public static function getBalance(int $businessProfileId): float
    {
        $stmt = self::db()->prepare("SELECT credit_note_balance FROM business_profiles WHERE id = ?");
        $stmt->execute([$businessProfileId]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Issues a credit note for a Track B claim resolved in the business's
     * favour, at the claimed value plus the 5% bonus (Sec. B5).
     *
     * @return array{success: bool, credited_amount: ?float, bonus_amount: ?float, new_balance: ?float, error: ?string}
     */
    public static function addClaimRefundCredit(int $businessProfileId, float $claimedValue, int $claimId): array
    {
        if ($claimedValue <= 0) {
            return ['success' => false, 'credited_amount' => null, 'bonus_amount' => null, 'new_balance' => null, 'error' => 'Claimed value must be greater than zero.'];
        }

        $bonusAmount = round($claimedValue * self::REFUND_BONUS_RATE, 2);
        $totalCredit = round($claimedValue + $bonusAmount, 2);

        $result = self::adjustBalance(
            $businessProfileId,
            $totalCredit,
            'claim_refund_bonus',
            "Credit note for claim #{$claimId}: \${$claimedValue} + 5% bonus (\${$bonusAmount})",
            $claimId
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
     * Shared balance-adjustment primitive - locks the business_profiles row,
     * applies the delta, writes the audit row, all in one transaction.
     *
     * @return array{success: bool, new_balance: ?float, error: ?string}
     */
    private static function adjustBalance(int $businessProfileId, float $delta, string $type, string $notes, ?int $claimId): array
    {
        $db = self::db();
        $startedTransaction = false;

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $stmt = $db->prepare("SELECT credit_note_balance FROM business_profiles WHERE id = ? FOR UPDATE");
            $stmt->execute([$businessProfileId]);
            $currentBalance = $stmt->fetchColumn();

            if ($currentBalance === false) {
                if ($startedTransaction) $db->rollBack();
                return ['success' => false, 'new_balance' => null, 'error' => 'Business account not found.'];
            }

            $newBalance = round((float)$currentBalance + $delta, 2);
            if ($newBalance < 0) {
                if ($startedTransaction) $db->rollBack();
                return ['success' => false, 'new_balance' => null, 'error' => 'Insufficient credit note balance.'];
            }

            $db->prepare("UPDATE business_profiles SET credit_note_balance = ? WHERE id = ?")->execute([$newBalance, $businessProfileId]);

            $db->prepare("
                INSERT INTO business_credit_note_transactions
                (business_profile_id, amount, type, reference_claim_id, balance_after, notes, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ")->execute([$businessProfileId, $delta, $type, $claimId, $newBalance, $notes]);

            if ($startedTransaction) $db->commit();

            return ['success' => true, 'new_balance' => $newBalance, 'error' => null];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('BusinessCreditNoteHelper::adjustBalance error: ' . $e->getMessage());
            return ['success' => false, 'new_balance' => null, 'error' => $e->getMessage()];
        }
    }
}
