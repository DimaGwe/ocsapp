<?php
/**
 * LOCAL-ONLY preview seed, part 3. Adds one demo shop for each of the
 * 3 service-based categories in the new 9-category taxonomy that had
 * no existing demo data: Home Services, Wellness & Beauty, Events & Catering.
 * These are represented as "shops" with a single symbolic listing,
 * same data model as everything else - no real booking system yet.
 * Same tagging convention as the earlier demo seed scripts.
 *
 * Do NOT run against staging or prod without checking first.
 * Run: php database/migrations/seed_demo_service_categories.php
 * Undo: php database/migrations/cleanup_demo_service_categories.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$shops = [
    [
        'seller' => ['email' => 'demo-seller-homeservices@ocsapp.local', 'first' => 'Demo', 'last' => 'Home Services Seller'],
        'shop' => ['name' => 'Menage Eclair (Demo)', 'slug' => 'demo-menage-eclair', 'type' => 'home_services'],
        'product' => ['name' => 'Nettoyage Residentiel (Demo)', 'slug' => 'demo-nettoyage-residentiel', 'sku' => 'DEMO-SERV-001', 'price' => 89.00, 'image' => 'assets/images/products/68f75ea31cb5a_1761042083.jpg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-wellness@ocsapp.local', 'first' => 'Demo', 'last' => 'Wellness Seller'],
        'shop' => ['name' => 'Spa Zen (Demo)', 'slug' => 'demo-spa-zen', 'type' => 'wellness_beauty'],
        'product' => ['name' => 'Massage Relaxant 60 min (Demo)', 'slug' => 'demo-massage-relaxant', 'sku' => 'DEMO-WELL-001', 'price' => 95.00, 'image' => 'assets/images/products/68f9a2a535075_1761190565.jpeg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-catering@ocsapp.local', 'first' => 'Demo', 'last' => 'Catering Seller'],
        'shop' => ['name' => 'Traiteur Fiesta (Demo)', 'slug' => 'demo-traiteur-fiesta', 'type' => 'events_catering'],
        'product' => ['name' => 'Forfait Traiteur Evenementiel (Demo)', 'slug' => 'demo-forfait-traiteur', 'sku' => 'DEMO-EVENT-001', 'price' => 450.00, 'image' => 'assets/images/products/68f9a338626a9_1761190712.jpg'],
    ],
];

$db->beginTransaction();

try {
    foreach ($shops as $row) {
        $stmt = $db->prepare("
            INSERT INTO users (email, role, password, first_name, last_name, status, is_test_account, created_at, updated_at)
            VALUES (:email, 'seller', :password, :first, :last, 'active', 1, NOW(), NOW())
        ");
        $stmt->execute([
            'email' => $row['seller']['email'],
            'password' => password_hash('demo-preview-only', PASSWORD_BCRYPT),
            'first' => $row['seller']['first'],
            'last' => $row['seller']['last'],
        ]);
        $sellerId = (int) $db->lastInsertId();

        $stmt = $db->prepare("
            INSERT INTO shops (seller_id, name, slug, shop_type, description, is_active, is_approved, average_rating, reviews_count, packaging_time, created_at, updated_at)
            VALUES (:seller_id, :name, :slug, :type, :desc, 1, 1, 4.5, 8, 25, NOW(), NOW())
        ");
        $stmt->execute([
            'seller_id' => $sellerId,
            'name' => $row['shop']['name'],
            'slug' => $row['shop']['slug'],
            'type' => $row['shop']['type'],
            'desc' => 'Local demo shop used to preview the 9-category taxonomy (service vertical).',
        ]);
        $shopId = (int) $db->lastInsertId();

        $stmt = $db->prepare("
            INSERT INTO products (brand_id, name, slug, sku, short_description, base_price, unit, status, product_type, seller_id, show_on_home, stock_quantity, created_at, updated_at)
            VALUES (NULL, :name, :slug, :sku, :short_desc, :price, 'piece', 'active', 'seller', :seller_id, 0, 50, NOW(), NOW())
        ");
        $stmt->execute([
            'name' => $row['product']['name'],
            'slug' => $row['product']['slug'],
            'sku' => $row['product']['sku'],
            'short_desc' => 'Demo listing for a service-based shop category preview.',
            'price' => $row['product']['price'],
            'seller_id' => $sellerId,
        ]);
        $productId = (int) $db->lastInsertId();

        $db->prepare("
            INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order, created_at)
            VALUES (:product_id, :path, :alt, 1, 0, NOW())
        ")->execute([
            'product_id' => $productId,
            'path' => $row['product']['image'],
            'alt' => $row['product']['name'],
        ]);

        $db->prepare("
            INSERT INTO shop_inventory (shop_id, product_id, price, stock_quantity, status, created_at, updated_at)
            VALUES (:shop_id, :product_id, :price, 40, 'active', NOW(), NOW())
        ")->execute([
            'shop_id' => $shopId,
            'product_id' => $productId,
            'price' => $row['product']['price'],
        ]);

        echo "Seeded shop '{$row['shop']['name']}' (id {$shopId}, type {$row['shop']['type']}) with listing '{$row['product']['name']}' (id {$productId})\n";
    }

    $db->commit();
    echo "Done.\n";
} catch (\Throwable $e) {
    $db->rollBack();
    echo "FAILED, rolled back: " . $e->getMessage() . "\n";
    exit(1);
}
