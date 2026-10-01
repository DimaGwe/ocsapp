<?php

namespace App\Helpers;

/**
 * FoundingBonusHelper - ledger for Founding program bonuses (founding_bonuses).
 *
 * Driver Agreement Sec 8.13 and Supplier Account Agreement Sec 7.4 promise a
 * one-time milestone bonus and a referral bonus. A bonus is awarded by
 * inserting its ledger row; the UNIQUE key (program, bonus_type,
 * beneficiary_id, related_id) makes that insert the single point that decides
 * "already paid or not", so calling award() again for the same bonus is a no-op.
 */
class FoundingBonusHelper
{
    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * @return int|null new ledger id, or null if this bonus was already awarded
     */
    public static function award(string $program, string $type, int $beneficiaryId, int $relatedId, float $amount, string $notes): ?int
    {
        $stmt = self::db()->prepare("
            INSERT IGNORE INTO founding_bonuses (program, bonus_type, beneficiary_id, related_id, amount, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$program, $type, $beneficiaryId, $relatedId, $amount, $notes]);
        return $stmt->rowCount() === 1 ? (int)self::db()->lastInsertId() : null;
    }

    public static function has(string $program, string $type, int $beneficiaryId, int $relatedId = 0): bool
    {
        $stmt = self::db()->prepare("SELECT 1 FROM founding_bonuses WHERE program = ? AND bonus_type = ? AND beneficiary_id = ? AND related_id = ? LIMIT 1");
        $stmt->execute([$program, $type, $beneficiaryId, $relatedId]);
        return (bool)$stmt->fetchColumn();
    }

    /** Bonuses of one beneficiary, newest first. */
    public static function forBeneficiary(string $program, int $beneficiaryId): array
    {
        $stmt = self::db()->prepare("SELECT * FROM founding_bonuses WHERE program = ? AND beneficiary_id = ? ORDER BY id DESC");
        $stmt->execute([$program, $beneficiaryId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
