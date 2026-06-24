<?php
/**
 * Migration: Extend delivery_assignments to support B2B distribution deliveries
 * Adds distribution_request_id, delivery_type, and makes order_id/shop_id nullable
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function fkExists(PDO $pdo, string $table, string $fkName): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");
    $stmt->execute([$table, $fkName]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    $pdo = Database::getConnection();
    echo "Extending delivery_assignments for unified B2B/B2C delivery management...\n";

    // 1. Add delivery_type column
    if (!columnExists($pdo, 'delivery_assignments', 'delivery_type')) {
        $pdo->exec("ALTER TABLE delivery_assignments ADD COLUMN delivery_type ENUM('order','distribution') NOT NULL DEFAULT 'order'");
        echo "- Added delivery_type column\n";
    } else {
        echo "- delivery_type already exists\n";
    }

    // 2. Add distribution_request_id column
    if (!columnExists($pdo, 'delivery_assignments', 'distribution_request_id')) {
        $pdo->exec("ALTER TABLE delivery_assignments ADD COLUMN distribution_request_id BIGINT NULL DEFAULT NULL");
        echo "- Added distribution_request_id column\n";
    } else {
        echo "- distribution_request_id already exists\n";
    }

    // 3. Add pickup_address column (for B2B where pickup is from supplier, not a shop)
    if (!columnExists($pdo, 'delivery_assignments', 'pickup_address')) {
        $pdo->exec("ALTER TABLE delivery_assignments ADD COLUMN pickup_address TEXT NULL DEFAULT NULL");
        echo "- Added pickup_address column\n";
    } else {
        echo "- pickup_address already exists\n";
    }

    // 4. Make order_id nullable — drop FK first, modify, re-add
    if (fkExists($pdo, 'delivery_assignments', 'fk_delivery_assignments_order')) {
        $pdo->exec("ALTER TABLE delivery_assignments DROP FOREIGN KEY fk_delivery_assignments_order");
        echo "- Dropped FK fk_delivery_assignments_order\n";
    }
    $pdo->exec("ALTER TABLE delivery_assignments MODIFY COLUMN order_id BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Order being delivered (NULL for distribution)'");
    echo "- Made order_id nullable\n";
    // Re-add FK (nullable columns are fine with FKs — NULL values skip the constraint)
    if (!fkExists($pdo, 'delivery_assignments', 'fk_delivery_assignments_order')) {
        $pdo->exec("ALTER TABLE delivery_assignments ADD CONSTRAINT fk_delivery_assignments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE");
        echo "- Re-added FK fk_delivery_assignments_order\n";
    }

    // 5. Make shop_id nullable — drop FK first, modify, re-add
    if (fkExists($pdo, 'delivery_assignments', 'fk_delivery_assignments_shop')) {
        $pdo->exec("ALTER TABLE delivery_assignments DROP FOREIGN KEY fk_delivery_assignments_shop");
        echo "- Dropped FK fk_delivery_assignments_shop\n";
    }
    $pdo->exec("ALTER TABLE delivery_assignments MODIFY COLUMN shop_id BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Shop fulfilling the order (NULL for distribution)'");
    echo "- Made shop_id nullable\n";
    if (!fkExists($pdo, 'delivery_assignments', 'fk_delivery_assignments_shop')) {
        $pdo->exec("ALTER TABLE delivery_assignments ADD CONSTRAINT fk_delivery_assignments_shop FOREIGN KEY (shop_id) REFERENCES shops(id)");
        echo "- Re-added FK fk_delivery_assignments_shop\n";
    }

    // 6. Add index on distribution_request_id
    $stmt = $pdo->query("SHOW INDEX FROM delivery_assignments WHERE Column_name = 'distribution_request_id'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE delivery_assignments ADD INDEX idx_distribution_request_id (distribution_request_id)");
        echo "- Added index on distribution_request_id\n";
    } else {
        echo "- Index on distribution_request_id already exists\n";
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
