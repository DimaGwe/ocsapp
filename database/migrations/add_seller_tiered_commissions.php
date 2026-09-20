<?php
/**
 * Migration: Seller tiered commissions (delivery/pickup split)
 *
 * shops previously had one flat commission_rate column - SellerPayoutHelper's own
 * comment explicitly deferred a real Essential/Experience/Prestige split as a
 * separate future build. This mirrors suppliers' existing subscription_package +
 * commission_rate pattern, split into delivery/pickup rates to match Seller
 * Central's published pricing and Sec A's new self-pickup checkout.
 *
 * Backfill safety: confirmed via prod query before writing this migration - every
 * existing shop row (13 demo/seed shops + the deactivated old OCS Store, id 1) sits
 * at the untouched column default commission_rate=10.00. No real seller has ever
 * had a deliberately-set rate, so backfilling everyone to Essential (15%/8%) is
 * safe - nothing live actually changes in practice.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function stcColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!stcColumnExists($db, 'shops', 'subscription_package')) {
        $db->exec("ALTER TABLE shops ADD COLUMN subscription_package ENUM('Essential','Experience','Prestige','Enterprise') NOT NULL DEFAULT 'Essential'");
        echo "shops.subscription_package added.\n";
    } else {
        echo "shops.subscription_package already exists. Skipping.\n";
    }

    if (!stcColumnExists($db, 'shops', 'pickup_commission_rate')) {
        $db->exec("ALTER TABLE shops ADD COLUMN pickup_commission_rate DECIMAL(5,2) NOT NULL DEFAULT 8.00");
        echo "shops.pickup_commission_rate added.\n";
    } else {
        echo "shops.pickup_commission_rate already exists. Skipping.\n";
    }

    // Backfill every shop still sitting at the untouched column default (10.00) to the
    // real Essential-tier delivery rate (15.00) + explicit package label, so the tiered
    // system starts from a coherent state instead of a legacy placeholder number.
    $backfillCount = $db->exec("
        UPDATE shops
        SET commission_rate = 15.00, pickup_commission_rate = 8.00, subscription_package = 'Essential'
        WHERE commission_rate = 10.00 AND subscription_package = 'Essential'
    ");
    echo "Backfilled {$backfillCount} shop row(s) to Essential tier (15%/8%).\n";

    // Column default now matches the real Essential-tier rate, so a brand-new shop
    // created without an explicit commission_rate lands on a real tier number, not
    // the old arbitrary 10.00 placeholder.
    $db->exec("ALTER TABLE shops ALTER COLUMN commission_rate SET DEFAULT 15.00");
    echo "shops.commission_rate default changed to 15.00.\n";

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
