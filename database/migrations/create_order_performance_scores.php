<?php
/**
 * Migration: Create order_performance_scores table
 * Stores per-order performance scores for suppliers, drivers, and businesses.
 * Scores are calculated when a distribution request reaches delivered/completed.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$check = $db->query("SHOW TABLES LIKE 'order_performance_scores'")->fetchColumn();
if ($check) {
    echo "Table order_performance_scores already exists — skipped.\n";
    exit(0);
}

$db->exec("
    CREATE TABLE order_performance_scores (
        id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        distribution_request_id INT            NOT NULL,
        po_id                   BIGINT UNSIGNED NULL                  COMMENT 'Set for supplier scores',
        entity_type             ENUM('supplier','driver','business')  NOT NULL,
        entity_id               BIGINT UNSIGNED NOT NULL,

        -- Component scores (0-100 each)
        total_score             TINYINT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'Weighted composite 0-100',
        response_score          TINYINT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'Speed of first action (0-40)',
        timeliness_score        TINYINT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'On-time delivery/preparation (0-40)',
        completion_score        TINYINT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'Outcome quality (0-20)',

        details                 JSON NULL                             COMMENT 'Raw data used to compute score',
        calculated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        INDEX idx_dr   (distribution_request_id),
        INDEX idx_entity (entity_type, entity_id),
        INDEX idx_po   (po_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

echo "Migration complete: order_performance_scores table created.\n";
