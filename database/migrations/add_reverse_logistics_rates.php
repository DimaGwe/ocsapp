<?php
/**
 * Migration: reverse-logistics rates on delivery_zones (Ecosystem Backend
 * Requirements Sec. 4.4 "published reverse-logistics rate, Schedule C.1a")
 *
 * The requirements doc references "Schedule C.1a" but that literal schedule
 * doesn't exist anywhere in the codebase or docs - the actual numbers live
 * in OCSAPP_Ecosystem_Pricing_Strategy.pdf Section 8.4a (found via research
 * before building this), zone-calibrated same as every other surcharge this
 * session: B2C (Marche) reverse pickup $9.99/$10.99/$11.99, B2B
 * (Fournisseur/Distribution) $24.00/$27.00/$30.00, West Island/Laval/
 * Montreal Core.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function rlColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    foreach (['reverse_logistics_rate_b2c', 'reverse_logistics_rate_b2b'] as $col) {
        if (!rlColumnExists($db, 'delivery_zones', $col)) {
            $db->exec("ALTER TABLE delivery_zones ADD COLUMN {$col} DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER oversize_increment_rate");
            echo "delivery_zones.{$col} added.\n";
        } else {
            echo "delivery_zones.{$col} already exists. Skipping.\n";
        }
    }

    $b2cRates = ['WI' => 9.99, 'LAV' => 10.99, 'MTL' => 11.99];
    foreach ($b2cRates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET reverse_logistics_rate_b2c = ? WHERE code = ? AND reverse_logistics_rate_b2c = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].reverse_logistics_rate_b2c -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }
    $b2bRates = ['WI' => 24.00, 'LAV' => 27.00, 'MTL' => 30.00];
    foreach ($b2bRates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET reverse_logistics_rate_b2b = ? WHERE code = ? AND reverse_logistics_rate_b2b = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].reverse_logistics_rate_b2b -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
