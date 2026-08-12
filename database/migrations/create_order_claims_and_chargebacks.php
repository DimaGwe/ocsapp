<?php
/**
 * Migration: order_claims + chargebacks (Ecosystem Backend Requirements
 * Sec. 4.1/4.2/4.3)
 *
 * Fully greenfield - confirmed by full-repo grep before building: no
 * returns/claims/chargeback table exists anywhere, only a static legal
 * policy page (legal_content page_type='refund'). No buyer-facing "request
 * a return" flow, no admin review queue.
 *
 * "Automated fault determination" is built honestly: there is no image-
 * comparison/AI capability in this codebase (or anywhere reasonable to add
 * one this round), so a rules engine that silently and autonomously decides
 * who owes money would be fabricating a capability that doesn't exist. What
 * IS genuinely automatable: return-window/claim-window enforcement (Track A
 * 14-day, Track B 48hr), and buyer-change-of-mind classification (that's a
 * claim TYPE, not an inference). Everything else - transit-caused vs
 * vendor-caused - is computed as a SUGGESTED classification from structured
 * signals (pickup/delivery evidence presence) and requires an admin to
 * confirm before any Dynamic Chargeback executes. Same graceful-degradation-
 * to-human-review pattern already used this session for NEQ verification
 * and the credit bureau gate.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $stmt = $db->query("SHOW TABLES LIKE 'order_claims'");
    if ($stmt->fetch()) {
        echo "order_claims already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE order_claims (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                track ENUM('A','B') NOT NULL COMMENT 'A=B2C Marche order, B=B2B distribution request',
                order_id BIGINT UNSIGNED NULL,
                distribution_request_id INT NULL,
                filed_by_user_id BIGINT UNSIGNED NOT NULL,
                claim_type ENUM('damaged','missing_item','wrong_item','not_as_described','buyer_change_of_mind','other') NOT NULL,
                description TEXT NULL,
                evidence_photos JSON NULL,
                claimed_value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                claim_deadline_at DATETIME NOT NULL COMMENT 'delivered_at + 14 days (A) or delivery scan + 48hrs (B), computed at filing time',
                fault_determination ENUM('pending','vendor_caused','transit_caused','buyer_caused') NOT NULL DEFAULT 'pending',
                fault_signals JSON NULL COMMENT 'structured signals behind the suggested classification, for admin review + audit',
                fault_determined_at DATETIME NULL,
                fault_determined_by BIGINT UNSIGNED NULL COMMENT 'NULL = still just a system suggestion, not yet admin-confirmed',
                status ENUM('submitted','window_expired','under_review','approved','denied','resolved') NOT NULL DEFAULT 'submitted',
                resolution ENUM('chargeback','absorbed','returnless_refund','denied') NULL,
                admin_notes TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_order (order_id),
                INDEX idx_distribution_request (distribution_request_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "order_claims table created.\n";
    }

    $stmt = $db->query("SHOW TABLES LIKE 'chargebacks'");
    if ($stmt->fetch()) {
        echo "chargebacks already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE chargebacks (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_claim_id BIGINT UNSIGNED NOT NULL,
                party_type ENUM('seller','supplier') NOT NULL,
                party_id BIGINT UNSIGNED NOT NULL COMMENT 'shops.id for seller, suppliers.id for supplier',
                claimed_value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                reverse_logistics_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                total_deduction DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                status ENUM('pending_deduction','deducted','disputed','reversed') NOT NULL DEFAULT 'pending_deduction',
                dispute_window_ends_at DATETIME NOT NULL,
                counter_evidence TEXT NULL,
                counter_evidence_submitted_at DATETIME NULL,
                resolved_at DATETIME NULL,
                resolved_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_order_claim (order_claim_id),
                INDEX idx_party (party_type, party_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "chargebacks table created.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
