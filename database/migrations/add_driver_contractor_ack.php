<?php
/**
 * Migration: Add independent-contractor acknowledgment column to driver_applications
 * - contractor_status_acknowledged: applicant confirmed they understand they are
 *   engaged as an independent contractor, not an employee
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
    echo "Adding contractor-status acknowledgment column to driver_applications...\n";

    addColIfMissing($db, 'driver_applications', 'contractor_status_acknowledged',
        "TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=applicant acknowledged independent contractor status at signup' AFTER previous_experience");

    echo "\nMigration complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
