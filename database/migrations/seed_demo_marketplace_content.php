<?php
/**
 * LOCAL-ONLY preview seed. Populates one demo shop + product per homepage
 * section (Most Selling, Best Sellers, Top Shops, and the 4 Virtual Mall
 * types) so Dima can see what the homepage looks like with real content,
 * instead of the empty-marketplace state.
 *
 * Every row is tagged so it's trivial to find/remove later:
 *   - users.is_test_account = 1
 *   - emails/slugs/skus all prefixed "demo-"
 *
 * Do NOT run this against staging or prod - it's for local visual preview only.
 * Run: php database/migrations/seed_demo_marketplace_content.php
 * Undo: php database/migrations/cleanup_demo_marketplace_content.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$shops = [
    [
        'seller' => ['email' => 'demo-seller-grocery@ocsapp.local', 'first' => 'Demo', 'last' => 'Grocery Seller'],
        'shop' => ['name' => 'Marche Vert (Demo)', 'slug' => 'demo-marche-vert', 'type' => 'grocery_store'],
        'product' => [
            'name' => 'Organic Avocados (Demo)', 'slug' => 'demo-organic-avocados',
            'sku' => 'DEMO-GRO-001', 'price' => 6.99, 'image' => 'assets/images/products/68f9a2a535075_1761190565.jpeg',
            'category_slug' => 'fresh-vegetables',
        ],
        'show_on_home' => true,  // -> Best Sellers section
    ],
    [
        'seller' => ['email' => 'demo-seller-foodcourt@ocsapp.local', 'first' => 'Demo', 'last' => 'Food Court Seller'],
        'shop' => ['name' => 'Bistro Local (Demo)', 'slug' => 'demo-bistro-local', 'type' => 'food_court'],
        'product' => [
            'name' => 'Poutine Classique (Demo)', 'slug' => 'demo-poutine-classique',
            'sku' => 'DEMO-FOOD-001', 'price' => 12.50, 'image' => 'assets/images/products/68f9a2dc97f41_1761190620.jpg',
            'category_slug' => 'pantry-staples',
        ],
        'give_sale' => true,  // -> Most Selling section (needs a real order)
    ],
    [
        'seller' => ['email' => 'demo-seller-store@ocsapp.local', 'first' => 'Demo', 'last' => 'Store Seller'],
        'shop' => ['name' => 'Boutique Mode (Demo)', 'slug' => 'demo-boutique-mode', 'type' => 'store'],
        'product' => [
            'name' => 'Sac Fourre-Tout Eco (Demo)', 'slug' => 'demo-tote-bag-eco',
            'sku' => 'DEMO-STORE-001', 'price' => 18.00, 'image' => 'assets/images/products/68f9a338626a9_1761190712.jpg',
            'category_slug' => null,
        ],
    ],
    [
        'seller' => ['email' => 'demo-seller-products@ocsapp.local', 'first' => 'Demo', 'last' => 'Products Seller'],
        'shop' => ['name' => 'TechZone (Demo)', 'slug' => 'demo-techzone', 'type' => 'products'],
        'product' => [
            'name' => 'Ecouteurs Sans Fil (Demo)', 'slug' => 'demo-wireless-earbuds',
            'sku' => 'DEMO-PROD-001', 'price' => 34.99, 'image' => 'assets/images/products/68f75c9663ec8_1761041558.jpg',
            'category_slug' => null,
        ],
    ],
];

$db->beginTransaction();

try {
    $buyerId = null;

    foreach ($shops as $row) {
        // 1. Seller user
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

        // 2. Shop
        $stmt = $db->prepare("
            INSERT INTO shops (seller_id, name, slug, shop_type, description, is_active, is_approved, average_rating, reviews_count, packaging_time, created_at, updated_at)
            VALUES (:seller_id, :name, :slug, :type, :desc, 1, 1, 4.5, 12, 25, NOW(), NOW())
        ");
        $stmt->execute([
            'seller_id' => $sellerId,
            'name' => $row['shop']['name'],
            'slug' => $row['shop']['slug'],
            'type' => $row['shop']['type'],
            'desc' => 'Local demo shop used to preview the homepage with real content.',
        ]);
        $shopId = (int) $db->lastInsertId();

        // 3. Product
        $stmt = $db->prepare("
            INSERT INTO products (brand_id, name, slug, sku, short_description, base_price, unit, status, product_type, seller_id, show_on_home, stock_quantity, created_at, updated_at)
            VALUES (NULL, :name, :slug, :sku, :short_desc, :price, 'piece', 'active', 'seller', :seller_id, :show_on_home, 50, NOW(), NOW())
        ");
        $stmt->execute([
            'name' => $row['product']['name'],
            'slug' => $row['product']['slug'],
            'sku' => $row['product']['sku'],
            'short_desc' => 'Demo product for homepage preview purposes only.',
            'price' => $row['product']['price'],
            'seller_id' => $sellerId,
            'show_on_home' => !empty($row['show_on_home']) ? 1 : 0,
        ]);
        $productId = (int) $db->lastInsertId();

        // 4. Product image
        $stmt = $db->prepare("
            INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order, created_at)
            VALUES (:product_id, :path, :alt, 1, 0, NOW())
        ");
        $stmt->execute([
            'product_id' => $productId,
            'path' => $row['product']['image'],
            'alt' => $row['product']['name'],
        ]);

        // 5. Category link (optional)
        if (!empty($row['product']['category_slug'])) {
            $cat = $db->prepare("SELECT id FROM categories WHERE slug = ? LIMIT 1");
            $cat->execute([$row['product']['category_slug']]);
            $categoryId = $cat->fetchColumn();
            if ($categoryId) {
                $db->prepare("
                    INSERT INTO product_categories (product_id, category_id, is_primary, created_at)
                    VALUES (?, ?, 1, NOW())
                ")->execute([$productId, $categoryId]);
            }
        }

        // 6. Shop inventory (what makes the product show up in shop/mall listings)
        $stmt = $db->prepare("
            INSERT INTO shop_inventory (shop_id, product_id, price, stock_quantity, status, created_at, updated_at)
            VALUES (:shop_id, :product_id, :price, 40, 'active', NOW(), NOW())
        ");
        $stmt->execute([
            'shop_id' => $shopId,
            'product_id' => $productId,
            'price' => $row['product']['price'],
        ]);
        $shopInventoryId = (int) $db->lastInsertId();

        // 7. A real delivered order, so this product shows up in "Most Selling"
        if (!empty($row['give_sale'])) {
            if ($buyerId === null) {
                $buyer = $db->query("SELECT id FROM users WHERE role='buyer' ORDER BY id LIMIT 1")->fetchColumn();
                if (!$buyer) {
                    $db->prepare("
                        INSERT INTO users (email, role, password, first_name, last_name, status, is_test_account, created_at, updated_at)
                        VALUES ('demo-buyer@ocsapp.local', 'buyer', :password, 'Demo', 'Buyer', 'active', 1, NOW(), NOW())
                    ")->execute(['password' => password_hash('demo-preview-only', PASSWORD_BCRYPT)]);
                    $buyer = (int) $db->lastInsertId();
                }
                $buyerId = $buyer;
            }

            $orderNumber = 'DEMO-' . date('Ymd') . '-' . rand(1000, 9999);
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, shop_id, order_number, status, subtotal, tax, delivery_fee, total, payment_method, payment_status, created_at, updated_at)
                VALUES (:user_id, :shop_id, :order_number, 'delivered', :subtotal, 0, 0, :total, 'card', 'paid', NOW(), NOW())
            ");
            $stmt->execute([
                'user_id' => $buyerId,
                'shop_id' => $shopId,
                'order_number' => $orderNumber,
                'subtotal' => $row['product']['price'],
                'total' => $row['product']['price'],
            ]);
            $orderId = (int) $db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, product_id, shop_inventory_id, product_name, sku, quantity, price, subtotal, created_at)
                VALUES (:order_id, :product_id, :shop_inventory_id, :name, :sku, 3, :price, :subtotal, NOW())
            ");
            $stmt->execute([
                'order_id' => $orderId,
                'product_id' => $productId,
                'shop_inventory_id' => $shopInventoryId,
                'name' => $row['product']['name'],
                'sku' => $row['product']['sku'],
                'price' => $row['product']['price'],
                'subtotal' => $row['product']['price'] * 3,
            ]);
        }

        echo "Seeded shop '{$row['shop']['name']}' (id {$shopId}) with product '{$row['product']['name']}' (id {$productId})\n";
    }

    $db->commit();
    echo "Done. Reload the homepage to see it.\n";
} catch (\Throwable $e) {
    $db->rollBack();
    echo "FAILED, rolled back: " . $e->getMessage() . "\n";
    exit(1);
}
