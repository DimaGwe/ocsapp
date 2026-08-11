<?php
/**
 * Removes everything inserted by seed_demo_marketplace_content.php.
 * Safe to run even if the seed script was only partially applied.
 * Run: php database/migrations/cleanup_demo_marketplace_content.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$db->beginTransaction();
try {
    $sellerIds = $db->query("SELECT id FROM users WHERE email LIKE 'demo-seller-%@ocsapp.local'")->fetchAll(\PDO::FETCH_COLUMN);
    $buyerIds = $db->query("SELECT id FROM users WHERE email = 'demo-buyer@ocsapp.local'")->fetchAll(\PDO::FETCH_COLUMN);
    $shopIds = $db->query("SELECT id FROM shops WHERE slug LIKE 'demo-%'")->fetchAll(\PDO::FETCH_COLUMN);
    $productIds = $db->query("SELECT id FROM products WHERE slug LIKE 'demo-%'")->fetchAll(\PDO::FETCH_COLUMN);

    if ($shopIds) {
        $in = implode(',', array_fill(0, count($shopIds), '?'));
        $db->prepare("DELETE FROM orders WHERE shop_id IN ($in)")->execute($shopIds);
        $db->prepare("DELETE FROM shop_inventory WHERE shop_id IN ($in)")->execute($shopIds);
    }
    if ($productIds) {
        $in = implode(',', array_fill(0, count($productIds), '?'));
        $db->prepare("DELETE FROM order_items WHERE product_id IN ($in)")->execute($productIds);
        $db->prepare("DELETE FROM product_images WHERE product_id IN ($in)")->execute($productIds);
        $db->prepare("DELETE FROM product_categories WHERE product_id IN ($in)")->execute($productIds);
    }
    if ($shopIds) {
        $in = implode(',', array_fill(0, count($shopIds), '?'));
        $db->prepare("DELETE FROM shops WHERE id IN ($in)")->execute($shopIds);
    }
    if ($productIds) {
        $in = implode(',', array_fill(0, count($productIds), '?'));
        $db->prepare("DELETE FROM products WHERE id IN ($in)")->execute($productIds);
    }

    $allUserIds = array_merge($sellerIds, $buyerIds);
    if ($allUserIds) {
        $in = implode(',', array_fill(0, count($allUserIds), '?'));
        $db->prepare("DELETE FROM users WHERE id IN ($in) AND is_test_account = 1")->execute($allUserIds);
    }

    $db->commit();
    echo "Cleaned up: " . count($shopIds) . " shops, " . count($productIds) . " products, " . count($allUserIds) . " demo users.\n";
} catch (\Throwable $e) {
    $db->rollBack();
    echo "FAILED, rolled back: " . $e->getMessage() . "\n";
    exit(1);
}
