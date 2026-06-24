<?php
/**
 * Migration: Supplier Switch Columns for B2B Flow
 *
 * Adds to distribution_requests:
 * - supplier_switch_pending  — flag that a price change awaits business confirmation
 * - supplier_switch_deadline — when business must respond (30min express / 12h scheduled)
 * - supplier_switch_old_amount — original total before supplier switch
 * - supplier_switch_new_amount — new total with backup supplier pricing
 * - supplier_switch_notes    — human-readable description of what changed
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

function colExists2(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    $cols = [
        'supplier_switch_pending'  => "TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Awaiting business confirmation for supplier switch'",
        'supplier_switch_deadline' => "DATETIME NULL COMMENT '30min express / 12h scheduled — when business must respond'",
        'supplier_switch_old_amount' => "DECIMAL(10,2) NULL COMMENT 'Total before supplier switch'",
        'supplier_switch_new_amount' => "DECIMAL(10,2) NULL COMMENT 'Total with backup supplier pricing'",
        'supplier_switch_notes'    => "TEXT NULL COMMENT 'Description of what changed (e.g. supplier name, price delta)'",
    ];

    foreach ($cols as $col => $definition) {
        if (!colExists2($db, 'distribution_requests', $col)) {
            $db->exec("ALTER TABLE distribution_requests ADD COLUMN {$col} {$definition}");
            echo "✓ distribution_requests.{$col} added\n";
        } else {
            echo "– distribution_requests.{$col} already exists, skipping\n";
        }
    }

    echo "\nMigration complete.\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
