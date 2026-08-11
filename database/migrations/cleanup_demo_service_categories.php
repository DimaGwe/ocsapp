<?php
/**
 * Removes everything inserted by seed_demo_service_categories.php.
 * Run: php database/migrations/cleanup_demo_service_categories.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$db->beginTransaction();
try {
    $emails = ['demo-seller-homeservices@ocsapp.local', 'demo-seller-wellness@ocsapp.local', 'demo-seller-catering@ocsapp.local'];
    $slugs = ['demo-menage-eclair', 'demo-spa-zen', 'demo-traiteur-fiesta'];

    $emailIn = implode(',', array_fill(0, count($emails), '?'));
    $slugIn = implode(',', array_fill(0, count($slugs), '?'));

    $sellerIds = $db->prepare("SELECT id FROM users WHERE email IN ($emailIn)");
    $sellerIds->execute($emails);
    $sellerIds = $sellerIds->fetchAll(\PDO::FETCH_COLUMN);

    $shopIds = $db->prepare("SELECT id FROM shops WHERE slug IN ($slugIn)");
    $shopIds->execute($slugs);
    $shopIds = $shopIds->fetchAll(\PDO::FETCH_COLUMN);

    $productIds = $db->query("SELECT id FROM products WHERE slug LIKE 'demo-nettoyage-residentiel%' OR slug LIKE 'demo-massage-relaxant%' OR slug LIKE 'demo-forfait-traiteur%'")->fetchAll(\PDO::FETCH_COLUMN);

    if ($shopIds) {
        $in = implode(',', array_fill(0, count($shopIds), '?'));
        $db->prepare("DELETE FROM shop_inventory WHERE shop_id IN ($in)")->execute($shopIds);
    }
    if ($productIds) {
        $in = implode(',', array_fill(0, count($productIds), '?'));
        $db->prepare("DELETE FROM product_images WHERE product_id IN ($in)")->execute($productIds);
        $db->prepare("DELETE FROM products WHERE id IN ($in)")->execute($productIds);
    }
    if ($shopIds) {
        $in = implode(',', array_fill(0, count($shopIds), '?'));
        $db->prepare("DELETE FROM shops WHERE id IN ($in)")->execute($shopIds);
    }
    if ($sellerIds) {
        $in = implode(',', array_fill(0, count($sellerIds), '?'));
        $db->prepare("DELETE FROM users WHERE id IN ($in) AND is_test_account = 1")->execute($sellerIds);
    }

    $db->commit();
    echo "Cleaned up: " . count($shopIds) . " shops, " . count($productIds) . " products, " . count($sellerIds) . " demo users.\n";
} catch (\Throwable $e) {
    $db->rollBack();
    echo "FAILED, rolled back: " . $e->getMessage() . "\n";
    exit(1);
}
