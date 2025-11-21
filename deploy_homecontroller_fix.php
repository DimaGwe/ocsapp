<?php
/**
 * Deployment Script: Fix Best Sellers Filter
 * This script removes the seller_id = 1 filter from HomeController
 *
 * INSTRUCTIONS:
 * 1. Upload this file to /var/www/ocsapp.ca/ on the server
 * 2. Run: php /var/www/ocsapp.ca/deploy_homecontroller_fix.php
 * 3. Delete this file after successful deployment
 */

echo "=================================================================\n";
echo "HomeController Fix - Remove seller_id Filter\n";
echo "=================================================================\n\n";

$targetFile = '/var/www/ocsapp.ca/app/Controllers/HomeController.php';

// Check if file exists
if (!file_exists($targetFile)) {
    die("❌ ERROR: HomeController.php not found at {$targetFile}\n");
}

echo "✓ Found HomeController.php\n";

// Read current content
$content = file_get_contents($targetFile);

if ($content === false) {
    die("❌ ERROR: Could not read HomeController.php\n");
}

echo "✓ Read file content (" . strlen($content) . " bytes)\n";

// Create backup
$backupFile = $targetFile . '.backup_' . date('YmdHis');
if (!file_put_contents($backupFile, $content)) {
    die("❌ ERROR: Could not create backup\n");
}

echo "✓ Backup created: {$backupFile}\n";

// Fix #1: Remove seller_id filter from Best Sellers query
$oldPattern1 = "            // Products manually selected by admin via \"Show on Home\" checkbox
            // Filtered to show only OCS Store (Shop ID 1) AND admin-created products (seller_id = 1)
            \$stmt = \$db->query(\"
                SELECT p.*,
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.show_on_home = 1
                  AND p.status = 'active'
                  AND si.status = 'active'
                  AND p.seller_id = 1
                ORDER BY p.sort_order DESC, p.created_at DESC
                LIMIT 24
            \");";

$newPattern1 = "            // Products manually selected by admin via \"Show on Home\" checkbox
            // Filtered to show only OCS Store (Shop ID 1) - ALL products regardless of seller
            \$stmt = \$db->query(\"
                SELECT p.*,
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.show_on_home = 1
                  AND p.status = 'active'
                  AND si.status = 'active'
                ORDER BY p.sort_order DESC, p.created_at DESC
                LIMIT 24
            \");";

// Fix #2: Remove seller_id filter from Most Selling Products query
$oldPattern2 = "            // Get products with highest sales volume from last 30 days
            // Filtered to show only OCS Store (Shop ID 1) AND admin-created products (seller_id = 1)
            \$stmt = \$db->query(\"
                SELECT p.*,
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug,
                       c.name as category_name,
                       c.slug as category_slug,
                       COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                    AND o.status IN ('delivered', 'confirmed', 'preparing', 'ready')
                    AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                WHERE p.status = 'active'
                  AND si.status = 'active'
                  AND p.seller_id = 1
                GROUP BY p.id
                HAVING total_sold > 0
                ORDER BY total_sold DESC, p.created_at DESC
                LIMIT 24
            \");";

$newPattern2 = "            // Get products with highest sales volume from last 30 days
            // Filtered to show only OCS Store (Shop ID 1) - ALL products regardless of seller
            \$stmt = \$db->query(\"
                SELECT p.*,
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug,
                       c.name as category_name,
                       c.slug as category_slug,
                       COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                    AND o.status IN ('delivered', 'confirmed', 'preparing', 'ready')
                    AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                WHERE p.status = 'active'
                  AND si.status = 'active'
                GROUP BY p.id
                HAVING total_sold > 0
                ORDER BY total_sold DESC, p.created_at DESC
                LIMIT 24
            \");";

// Apply fixes
$fixed = 0;

if (strpos($content, $oldPattern1) !== false) {
    $content = str_replace($oldPattern1, $newPattern1, $content);
    echo "✓ Fix #1 applied: Removed seller_id filter from Best Sellers query\n";
    $fixed++;
} else {
    echo "⚠ Fix #1 already applied or pattern not found\n";
}

if (strpos($content, $oldPattern2) !== false) {
    $content = str_replace($oldPattern2, $newPattern2, $content);
    echo "✓ Fix #2 applied: Removed seller_id filter from Most Selling Products query\n";
    $fixed++;
} else {
    echo "⚠ Fix #2 already applied or pattern not found\n";
}

if ($fixed === 0) {
    echo "\n❌ No changes needed. File may already be updated.\n";
    exit(0);
}

// Write updated content
if (!file_put_contents($targetFile, $content)) {
    die("\n❌ ERROR: Could not write updated file\n");
}

echo "✓ HomeController.php updated successfully!\n";
echo "\n=================================================================\n";
echo "DEPLOYMENT COMPLETE\n";
echo "=================================================================\n";
echo "Changes applied: {$fixed}\n";
echo "Backup location: {$backupFile}\n";
echo "\nResult:\n";
echo "- ALL products in OCS Store (shop_id=1) marked 'show on home' will now display\n";
echo "- No longer filtered by seller_id = 1\n";
echo "\nTest the homepage to verify all 7+ products appear in Best Sellers section.\n";
echo "=================================================================\n";
