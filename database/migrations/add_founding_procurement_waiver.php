<?php
/**
 * Migration: Founding Business Procurement Fee waiver (Business Account
 * Agreement Sec 7.9) - the 1% Procurement Fee is waived on a Founding
 * Business's first $10,000 of Approvisionnement volume.
 *
 * Each request records how much of its items total was covered by the waiver
 * (founding_waived_volume) and the fee that was not charged
 * (procurement_fee_waived). The allowance left is 10,000 minus the sum of
 * founding_waived_volume over the business's non-cancelled requests
 * (FoundingBusinessHelper::procurementWaiverRemaining()).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function fpwColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    $columns = [
        'founding_waived_volume' => "ADD COLUMN founding_waived_volume DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER service_fee",
        'procurement_fee_waived' => "ADD COLUMN procurement_fee_waived DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER founding_waived_volume",
    ];
    foreach ($columns as $name => $ddl) {
        if (fpwColumnExists($db, 'distribution_requests', $name)) {
            echo "distribution_requests.{$name} already exists, skipping.\n";
            continue;
        }
        $db->exec("ALTER TABLE distribution_requests {$ddl}");
        echo "Added distribution_requests.{$name}.\n";
    }

    echo "Done.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
