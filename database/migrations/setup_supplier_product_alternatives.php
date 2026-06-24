<?php
/**
 * Migration: Create supplier_product_alternatives table
 * Stores priority-ordered backup supplier products for the B2B distribution escalation flow.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS supplier_product_alternatives (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            supplier_product_id             BIGINT UNSIGNED NOT NULL COMMENT 'The primary supplier product',
            alternative_supplier_product_id BIGINT UNSIGNED NOT NULL COMMENT 'The backup supplier product',
            priority        TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = first fallback, 2 = second, etc.',
            notes           VARCHAR(255) NULL COMMENT 'Admin notes about this alternative',
            created_by      BIGINT UNSIGNED NULL,
            created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            UNIQUE KEY uq_product_alternative (supplier_product_id, alternative_supplier_product_id),
            INDEX idx_primary (supplier_product_id),
            INDEX idx_alternative (alternative_supplier_product_id),

            CONSTRAINT fk_spa_primary
                FOREIGN KEY (supplier_product_id)
                REFERENCES supplier_products(id) ON DELETE CASCADE,

            CONSTRAINT fk_spa_alternative
                FOREIGN KEY (alternative_supplier_product_id)
                REFERENCES supplier_products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✓ Table supplier_product_alternatives created\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
