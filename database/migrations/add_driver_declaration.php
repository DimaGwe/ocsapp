<?php
/**
 * Migration: Add self-declaration columns to driver_applications
 * - criminal_record: whether the applicant declared a criminal conviction
 * - criminal_record_details: optional explanation
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = \Database::getConnection();

function addColIfMissing(\PDO $db, string $table, string $column, string $definition): void {
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
    echo "Adding declaration columns to driver_applications...\n";

    addColIfMissing($db, 'driver_applications', 'criminal_record',
        "TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=no conviction declared, 1=conviction declared' AFTER previous_experience");

    addColIfMissing($db, 'driver_applications', 'criminal_record_details',
        "TEXT NULL AFTER criminal_record");

    echo "\nMigration complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
