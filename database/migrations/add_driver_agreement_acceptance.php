<?php
/**
 * Migration: record acceptance of the Driver Independent Contractor Service
 * Agreement on the driver application (Agreement Sec 18 "Acceptance").
 *
 * Until now the application only captured the background-check and
 * contractor-status acknowledgments; the agreement itself was never shown
 * or accepted. Same three fields business_profiles and suppliers already
 * carry for their agreements.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function daColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    $columns = [
        'agreement_accepted_at' => "ADD COLUMN agreement_accepted_at DATETIME NULL AFTER contractor_status_acknowledged",
        'agreement_ip'          => "ADD COLUMN agreement_ip VARCHAR(45) NULL AFTER agreement_accepted_at",
        'agreement_version'     => "ADD COLUMN agreement_version INT UNSIGNED NULL AFTER agreement_ip",
    ];
    foreach ($columns as $name => $ddl) {
        if (daColumnExists($db, 'driver_applications', $name)) {
            echo "driver_applications.{$name} already exists, skipping.\n";
            continue;
        }
        $db->exec("ALTER TABLE driver_applications {$ddl}");
        echo "Added driver_applications.{$name}.\n";
    }

    echo "Done.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
