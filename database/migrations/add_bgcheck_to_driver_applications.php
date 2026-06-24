<?php
/**
 * Migration: Add background check columns to driver_applications
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = \Database::getConnection();

// Helper: add column only if it doesn't already exist
function addColumnIfMissing(\PDO $db, string $table, string $column, string $definition): void {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    if ((int)$stmt->fetchColumn() === 0) {
        $db->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        echo "  + {$column}\n";
    } else {
        echo "  = {$column} (already exists)\n";
    }
}

try {
    echo "Adding background check columns to driver_applications...\n";

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_status',
        "ENUM('not_requested','requested','uploaded','verified','flagged','waived') NOT NULL DEFAULT 'not_requested' AFTER pipeline_stage");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_token',
        "VARCHAR(64) NULL AFTER bgcheck_status");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_token_expires_at',
        "TIMESTAMP NULL AFTER bgcheck_token");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_doc_type',
        "VARCHAR(80) NULL AFTER bgcheck_token_expires_at");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_doc_date',
        "DATE NULL AFTER bgcheck_doc_type");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_file_path',
        "VARCHAR(500) NULL AFTER bgcheck_doc_date");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_uploaded_at',
        "TIMESTAMP NULL AFTER bgcheck_file_path");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_requested_at',
        "TIMESTAMP NULL AFTER bgcheck_uploaded_at");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_verified_at',
        "TIMESTAMP NULL AFTER bgcheck_requested_at");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_verified_by',
        "INT NULL AFTER bgcheck_verified_at");

    addColumnIfMissing($db, 'driver_applications', 'bgcheck_notes',
        "TEXT NULL AFTER bgcheck_verified_by");

    // Index on token
    try {
        $db->exec("ALTER TABLE driver_applications ADD INDEX idx_bgcheck_token (bgcheck_token)");
        echo "  + index on bgcheck_token\n";
    } catch (\Exception $e) {
        echo "  = token index (already exists)\n";
    }

    // Create upload directory on server
    $uploadDir = __DIR__ . '/../../storage/uploads/bgchecks';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0750, true);
        file_put_contents($uploadDir . '/.htaccess', "Deny from all\n");
        echo "  + created storage/uploads/bgchecks/\n";
    } else {
        echo "  = upload directory already exists\n";
    }

    echo "\nMigration complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
