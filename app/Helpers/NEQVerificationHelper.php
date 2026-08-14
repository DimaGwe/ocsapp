<?php

namespace App\Helpers;

/**
 * NEQVerificationHelper — Sec. 6, "Option 2": a live Registraire des
 * entreprises du Québec (REQ) lookup as a second verification layer beyond
 * format validation ("Option 1", already live). Per Jack's direction
 * (2026-08-14): the lookup runs automatically, but the result is never
 * auto-accepted or auto-rejected — it's routed to an Administrator for a
 * "double tap" confirmation (two distinct steps) before it's treated as
 * final.
 *
 * No live Registraire API integration exists yet — same situation as
 * CreditHelper's Equifax/D&B gate, and deliberately handled the same way:
 * this is real automatic-trigger + admin-review plumbing, not a fake API
 * call. Once real Registraire API access exists, only runLookup() below
 * needs a real HTTP call added.
 */
class NEQVerificationHelper
{
    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    public static function isREQConfigured(): bool
    {
        return (bool)setting('req_quebec_api_key', '');
    }

    /**
     * Automatically triggered right after a business account's NEQ passes
     * format validation (Option 1) at registration. Creates the verification
     * record admin will review before final approval.
     */
    public static function runLookup(int $businessProfileId, string $neqNumber): void
    {
        $db = self::db();

        if (self::isREQConfigured()) {
            // Live Registraire des entreprises du Québec API call would go here
            // once Jack has real credentials/API docs - deliberately not stubbed
            // with fake match/mismatch logic, same reasoning as
            // CreditHelper::runCreditCheck(): a fake "always match" would be
            // worse than an honest manual-review fallback.
            logger("NEQVerificationHelper: REQ credentials configured but no live integration exists yet — business_profile_id={$businessProfileId} falling back to manual review.", 'info');
        }

        $stmt = $db->prepare("
            INSERT INTO business_neq_verifications
            (business_profile_id, neq_number, lookup_status, looked_up_at, created_at, updated_at)
            VALUES (?, ?, 'api_not_configured', NOW(), NOW(), NOW())
        ");
        $stmt->execute([$businessProfileId, $neqNumber]);

        try {
            require_once __DIR__ . '/NotificationHelper.php';
            NotificationHelper::add(
                'business',
                '🔍 NEQ Verification Needed',
                "A new business application's NEQ ({$neqNumber}) needs manual review — no live Registraire lookup is configured yet.",
                ['link' => '/admin/business-accounts', 'icon' => 'search', 'priority' => 'normal']
            );
        } catch (\Throwable $e) { /* non-blocking */ }
    }

    /**
     * Latest verification record for a business, if any.
     */
    public static function getLatest(int $businessProfileId): ?array
    {
        $stmt = self::db()->prepare("
            SELECT * FROM business_neq_verifications
            WHERE business_profile_id = ?
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$businessProfileId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Step 1 of the double-tap confirmation: admin acknowledges they've
     * reviewed the lookup result (or the absence of one).
     */
    public static function confirmStep1(int $verificationId, int $adminId): void
    {
        self::db()->prepare("
            UPDATE business_neq_verifications
            SET admin_step1_confirmed_at = NOW(), admin_step1_confirmed_by = ?, updated_at = NOW()
            WHERE id = ?
        ")->execute([$adminId, $verificationId]);
    }

    /**
     * Step 2: a second, distinct confirmation, only permitted once step 1
     * has already happened. This is what "double tap" means here — not two
     * clicks on the same button, but two separate confirmation actions.
     */
    public static function confirmStep2(int $verificationId, int $adminId, string $notes = ''): bool
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT admin_step1_confirmed_at FROM business_neq_verifications WHERE id = ?");
        $stmt->execute([$verificationId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row || empty($row['admin_step1_confirmed_at'])) {
            return false; // step 1 must happen first
        }

        $db->prepare("
            UPDATE business_neq_verifications
            SET admin_step2_confirmed_at = NOW(), admin_step2_confirmed_by = ?,
                final_status = 'confirmed', admin_notes = ?, updated_at = NOW()
            WHERE id = ?
        ")->execute([$adminId, $notes ?: null, $verificationId]);

        return true;
    }

    public static function reject(int $verificationId, int $adminId, string $notes = ''): void
    {
        self::db()->prepare("
            UPDATE business_neq_verifications
            SET final_status = 'rejected', admin_notes = ?, updated_at = NOW()
            WHERE id = ?
        ")->execute([$notes ?: null, $verificationId]);
    }
}
