<?php
/**
 * LOCAL-ONLY preview seed, part 2. Adds one demo shop per NEW shop_type
 * (bakery, butcher, pharmacy, bookstore, hardware, boutique, electronics)
 * so the expanded "Shop by Type" tiles / /shops tabs show real counts.
 * Same tagging convention as seed_demo_marketplace_content.php.
 *
 * Do NOT run against staging or prod without checking first - this
 * assumes the shops.shop_type ENUM has already been widened locally.
 * Run: php database/migrations/seed_demo_extra_shop_types.php
 * Undo: php database/migrations/cleanup_demo_extra_shop_types.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$shops = [
    [
        'seller' => ['email' => 'demo-seller-bakery@ocsapp.local', 'first' => 'Demo', 'last' => 'Bakery Seller'],
        'shop' => ['name' => 'Boulangerie du Coin (Demo)', 'slug' => 'demo-boulangerie-du-coin', 'type' => 'bakery'],
        'product' => ['name' => 'Croissants au Beurre (Demo)', 'slug' => 'demo-croissants-beurre', 'sku' => 'DEMO-BAKE-001', 'price' => 4.50, 'image' => 'assets/images/products/68f9a2dc97f41_1761190620.jpg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-butcher@ocsapp.local', 'first' => 'Demo', 'last' => 'Butcher Seller'],
        'shop' => ['name' => 'Boucherie Artisanale (Demo)', 'slug' => 'demo-boucherie-artisanale', 'type' => 'butcher'],
        'product' => ['name' => 'Cotes de Boeuf (Demo)', 'slug' => 'demo-cotes-de-boeuf', 'sku' => 'DEMO-BUTCH-001', 'price' => 22.00, 'image' => 'assets/images/products/68f9a338626a9_1761190712.jpg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-pharmacy@ocsapp.local', 'first' => 'Demo', 'last' => 'Pharmacy Seller'],
        'shop' => ['name' => 'Pharmacie Sante Plus (Demo)', 'slug' => 'demo-pharmacie-sante-plus', 'type' => 'pharmacy'],
        'product' => ['name' => 'Vitamines Quotidiennes (Demo)', 'slug' => 'demo-vitamines-quotidiennes', 'sku' => 'DEMO-PHARM-001', 'price' => 15.99, 'image' => 'assets/images/products/68f75ea31cb5a_1761042083.jpg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-bookstore@ocsapp.local', 'first' => 'Demo', 'last' => 'Bookstore Seller'],
        'shop' => ['name' => 'Librairie des Reves (Demo)', 'slug' => 'demo-librairie-des-reves', 'type' => 'bookstore'],
        'product' => ['name' => 'Roman Best-Seller (Demo)', 'slug' => 'demo-roman-best-seller', 'sku' => 'DEMO-BOOK-001', 'price' => 19.99, 'image' => 'assets/images/products/68f9a2a535075_1761190565.jpeg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-hardware@ocsapp.local', 'first' => 'Demo', 'last' => 'Hardware Seller'],
        'shop' => ['name' => 'Quincaillerie Locale (Demo)', 'slug' => 'demo-quincaillerie-locale', 'type' => 'hardware'],
        'product' => ['name' => 'Ensemble de Tournevis (Demo)', 'slug' => 'demo-ensemble-tournevis', 'sku' => 'DEMO-HARD-001', 'price' => 29.99, 'image' => 'assets/images/products/68f75c9663ec8_1761041558.jpg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-boutique@ocsapp.local', 'first' => 'Demo', 'last' => 'Boutique Seller'],
        'shop' => ['name' => 'Boutique Chic (Demo)', 'slug' => 'demo-boutique-chic', 'type' => 'boutique'],
        'product' => ['name' => 'Robe d\'ete (Demo)', 'slug' => 'demo-robe-ete', 'sku' => 'DEMO-BOUT-001', 'price' => 45.00, 'image' => 'assets/images/products/68f9a2dc97f41_1761190620.jpg'],
    ],
    [
        'seller' => ['email' => 'demo-seller-electronics@ocsapp.local', 'first' => 'Demo', 'last' => 'Electronics Seller'],
        'shop' => ['name' => 'Electro Plus (Demo)', 'slug' => 'demo-electro-plus', 'type' => 'electronics'],
        'product' => ['name' => 'Chargeur Rapide USB-C (Demo)', 'slug' => 'demo-chargeur-usb-c', 'sku' => 'DEMO-ELEC-001', 'price' => 24.99, 'image' => 'assets/images/products/68f9a338626a9_1761190712.jpg'],
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
            'desc' => 'Local demo shop used to preview the expanded shop-type taxonomy.',
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
            'short_desc' => 'Demo product for shop-type taxonomy preview purposes only.',
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

        echo "Seeded shop '{$row['shop']['name']}' (id {$shopId}, type {$row['shop']['type']}) with product '{$row['product']['name']}' (id {$productId})\n";
    }

    $db->commit();
    echo "Done.\n";
} catch (\Throwable $e) {
    $db->rollBack();
    echo "FAILED, rolled back: " . $e->getMessage() . "\n";
    exit(1);
}
