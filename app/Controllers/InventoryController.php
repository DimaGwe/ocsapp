<?php

namespace App\Controllers;

/**
 * InventoryController
 * Seller inventory management — add/edit/delete products in their shop_inventory.
 */
class InventoryController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
        if (!isLoggedIn() || !hasRole('seller')) {
            setFlash('error', lang_pick('Un compte vendeur est requis.', 'Seller account required.'));
            redirect(url('login'));
            exit;
        }
    }

    private function getSellerShop(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM shops WHERE seller_id = ? AND is_active = 1 LIMIT 1"
        );
        $stmt->execute([userId()]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /** Photos a seller may attach to one of their own products. */
    private const MAX_PRODUCT_PHOTOS = 6;

    /** $_FILES['images'] (multiple input) as a list of single-file arrays, empty slots skipped. */
    private function uploadedImages(): array
    {
        $f = $_FILES['images'] ?? null;
        if (!$f || !is_array($f['name'] ?? null)) {
            return [];
        }
        $out = [];
        foreach ($f['name'] as $i => $name) {
            if (($f['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = ['name' => $name, 'type' => $f['type'][$i] ?? '', 'tmp_name' => $f['tmp_name'][$i] ?? '',
                      'error' => $f['error'][$i], 'size' => $f['size'][$i] ?? 0];
        }
        return $out;
    }

    /**
     * Upload photos for a product (ImageUploadHelper: extension + finfo MIME + real image checks,
     * 5 MB max) into public/uploads/products and attach them in product_images. The first photo of a
     * product with no main photo becomes the main one. Returns [added count, bilingual error lines].
     */
    private function saveProductImages(int $productId, array $files, string $altText): array
    {
        if (!$files) {
            return [0, []];
        }
        $stmt = $this->db->prepare("SELECT COUNT(*), COALESCE(MAX(is_primary), 0), COALESCE(MAX(sort_order), 0) FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        [$count, $hasPrimary, $sort] = array_map('intval', $stmt->fetch(\PDO::FETCH_NUM));

        $uploader = new \App\Helpers\ImageUploadHelper('uploads/products');
        $insert = $this->db->prepare("
            INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $added = 0;
        $errors = [];
        foreach ($files as $file) {
            if ($count + $added >= self::MAX_PRODUCT_PHOTOS) {
                $errors[] = lang_pick('Maximum de ' . self::MAX_PRODUCT_PHOTOS . ' photos par produit.', 'Maximum ' . self::MAX_PRODUCT_PHOTOS . ' photos per product.');
                break;
            }
            $result = $uploader->upload($file);
            if (empty($result['success'])) {
                $errors[] = basename((string) $file['name']) . ' : ' . lang_pick('fichier refusé (JPG, PNG, WebP ou GIF, 5 Mo max).', 'file rejected (JPG, PNG, WebP or GIF, 5 MB max).');
                continue;
            }
            $primary = (!$hasPrimary && $added === 0) ? 1 : 0;
            $insert->execute([$productId, $result['path'], mb_substr($altText, 0, 255), $primary, ++$sort]);
            $added++;
        }
        return [$added, $errors];
    }

    /** Product id of this inventory item if the product is the seller's own (not a catalogue product), else 0. */
    private function ownedProductId(int $inventoryId, int $shopId): int
    {
        $stmt = $this->db->prepare("
            SELECT p.id FROM products p
            JOIN shop_inventory si ON si.product_id = p.id
            WHERE si.id = ? AND si.shop_id = ? AND p.product_type = 'seller' AND p.seller_id = ?
        ");
        $stmt->execute([$inventoryId, $shopId, userId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * GET /seller/inventory — list inventory items for this seller's shop
     */
    public function index(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            setFlash('info', lang_pick('Veuillez d\'abord configurer votre commerce.', 'Please set up your shop first.'));
            redirect(url('seller/shop/create'));
            return;
        }

        $stmt = $this->db->prepare("
            SELECT si.*, p.name AS product_name, p.sku,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image_path
            FROM shop_inventory si
            JOIN products p ON si.product_id = p.id
            WHERE si.shop_id = ?
            ORDER BY p.name ASC
        ");
        $stmt->execute([$shop['id']]);
        $inventory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('seller/inventory/index', ['shop' => $shop, 'inventory' => $inventory]);
    }

    /**
     * GET /seller/inventory/add — form to add an existing product to inventory
     */
    public function add(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        // Products not already in this shop's inventory
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.sku
            FROM products p
            WHERE p.id NOT IN (SELECT product_id FROM shop_inventory WHERE shop_id = ?)
            AND p.status = 'active'
            ORDER BY p.name ASC
        ");
        $stmt->execute([$shop['id']]);
        $availableProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('seller/inventory/add', ['shop' => $shop, 'availableProducts' => $availableProducts]);
    }

    /**
     * POST /seller/inventory/store — add product to shop_inventory
     */
    public function store(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', lang_pick('Jeton de sécurité invalide. Rechargez la page.', 'Invalid security token.'));
            redirect(url('seller/inventory/add'));
            return;
        }

        $productId = intval(post('product_id', 0));
        $price     = floatval(post('price', 0));
        $stock     = intval(post('stock_quantity', 0));

        if ($productId <= 0 || $price <= 0) {
            setFlash('error', lang_pick('Le produit et le prix sont requis.', 'Product and price are required.'));
            redirect(url('seller/inventory/add'));
            return;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO shop_inventory (shop_id, product_id, price, stock_quantity, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
                ON DUPLICATE KEY UPDATE price = VALUES(price), stock_quantity = VALUES(stock_quantity), updated_at = NOW()
            ");
            $stmt->execute([$shop['id'], $productId, $price, $stock]);
            setFlash('success', lang_pick('Produit ajouté à l\'inventaire.', 'Product added to inventory.'));
        } catch (\PDOException $e) {
            logger("InventoryController::store() failed: " . $e->getMessage(), 'error');
            setFlash('error', lang_pick('Le produit n\'a pas pu être ajouté. Veuillez réessayer.', 'Could not add product. Please try again.'));
        }

        redirect(url('seller/inventory'));
    }

    /**
     * GET /seller/inventory/edit?id=X — form to edit an inventory item
     */
    public function edit(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/dashboard'));
            return;
        }

        $inventoryId = intval($_GET['id'] ?? 0);
        $stmt = $this->db->prepare("
            SELECT si.*, p.name AS product_name, p.sku, p.weight AS product_weight,
                   p.product_type, p.seller_id AS product_seller_id, p.age_restricted
            FROM shop_inventory si
            JOIN products p ON si.product_id = p.id
            WHERE si.id = ? AND si.shop_id = ?
        ");
        $stmt->execute([$inventoryId, $shop['id']]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item) {
            setFlash('error', lang_pick('Article d\'inventaire introuvable.', 'Inventory item not found.'));
            redirect(url('seller/inventory'));
            return;
        }

        $images = [];
        $canEditPhotos = ($item['product_type'] ?? '') === 'seller' && (int) ($item['product_seller_id'] ?? 0) === (int) userId();
        if ($canEditPhotos) {
            $stmt = $this->db->prepare("SELECT id, image_path, is_primary FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order, id");
            $stmt->execute([$item['product_id']]);
            $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        view('seller/inventory/edit', ['shop' => $shop, 'item' => $item, 'images' => $images,
                                       'canEditPhotos' => $canEditPhotos, 'maxPhotos' => self::MAX_PRODUCT_PHOTOS]);
    }

    /**
     * POST /seller/inventory/update — update price/stock
     */
    public function update(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/dashboard'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', lang_pick('Jeton de sécurité invalide. Rechargez la page.', 'Invalid security token.'));
            redirect(url('seller/inventory'));
            return;
        }

        $inventoryId = intval(post('inventory_id', 0));
        $price       = floatval(post('price', 0));
        $stock       = intval(post('stock_quantity', 0));
        $status      = in_array(post('status', 'active'), ['active', 'inactive']) ? post('status', 'active') : 'active';

        try {
            $stmt = $this->db->prepare("
                UPDATE shop_inventory
                SET price = ?, stock_quantity = ?, status = ?, updated_at = NOW()
                WHERE id = ? AND shop_id = ?
            ");
            $stmt->execute([$price, $stock, $status, $inventoryId, $shop['id']]);

            // Weight backfill: sellers can set weight on their own products from the edit form
            if (post('weight') !== null && post('weight') !== '') {
                $weight = floatval(post('weight'));
                if ($weight <= 0) {
                    setFlash('error', lang_pick('Le poids du produit (kg) doit être supérieur à zéro.', 'Product weight (kg) must be greater than zero.'));
                    redirect(url('seller/inventory'));
                    return;
                }
                $stmt = $this->db->prepare("
                    UPDATE products p
                    JOIN shop_inventory si ON si.product_id = p.id
                    SET p.weight = ?, p.updated_at = NOW()
                    WHERE si.id = ? AND si.shop_id = ?
                      AND p.product_type = 'seller' AND p.seller_id = ?
                ");
                $stmt->execute([$weight, $inventoryId, $shop['id'], userId()]);

                if ($weight > 25) {
                    \App\Helpers\NotificationHelper::add(
                        'product_weight_review',
                        'Product weight needs review',
                        "Seller inventory item #{$inventoryId} was updated to {$weight} kg per unit - please verify.",
                        ['data' => ['inventory_id' => $inventoryId, 'weight' => $weight]]
                    );
                }
            }

            // Age restriction (18+): sellers set it on their own products only, like weight
            if (post('weight') !== null && post('weight') !== '') {
                $this->db->prepare("
                    UPDATE products p
                    JOIN shop_inventory si ON si.product_id = p.id
                    SET p.age_restricted = ?, p.updated_at = NOW()
                    WHERE si.id = ? AND si.shop_id = ?
                      AND p.product_type = 'seller' AND p.seller_id = ?
                ")->execute([post('age_restricted', '') === '1' ? 1 : 0, $inventoryId, $shop['id'], userId()]);
            }

            setFlash('success', lang_pick('Inventaire mis à jour.', 'Inventory updated.'));

            // Photos: only on the seller's own products (never on catalogue products)
            $ownedId = $this->ownedProductId($inventoryId, (int) $shop['id']);
            if ($ownedId) {
                $remove = array_filter(array_map('intval', (array) ($_POST['remove_images'] ?? [])));
                if ($remove) {
                    $in = implode(',', array_fill(0, count($remove), '?'));
                    $stmt = $this->db->prepare("SELECT id, image_path FROM product_images WHERE product_id = ? AND id IN ($in)");
                    $stmt->execute(array_merge([$ownedId], $remove));
                    $uploader = new \App\Helpers\ImageUploadHelper('uploads/products');
                    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $img) {
                        // Only files this feature stores; shared demo assets are left alone
                        if (str_starts_with((string) $img['image_path'], 'uploads/products/')) {
                            $uploader->delete($img['image_path']);
                        }
                        $this->db->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?")->execute([$img['id'], $ownedId]);
                    }
                }
                $primary = intval(post('primary_image', 0));
                if ($primary && !in_array($primary, $remove, true)) {
                    $this->db->prepare("UPDATE product_images SET is_primary = (id = ?) WHERE product_id = ?
                                        AND EXISTS (SELECT 1 FROM (SELECT id FROM product_images WHERE id = ? AND product_id = ?) x)")
                             ->execute([$primary, $ownedId, $primary, $ownedId]);
                }
                [, $imgErrors] = $this->saveProductImages($ownedId, $this->uploadedImages(), html_entity_decode((string) ($this->db->query("SELECT name FROM products WHERE id = " . (int) $ownedId)->fetchColumn() ?: ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                // Always keep exactly one main photo when the product has photos
                $this->db->prepare("
                    UPDATE product_images SET is_primary = 1
                    WHERE product_id = ? AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM product_images WHERE product_id = ? AND is_primary = 1) x)
                    ORDER BY sort_order, id LIMIT 1
                ")->execute([$ownedId, $ownedId]);
                if ($imgErrors) {
                    setFlash('info', implode(' ', $imgErrors));
                }
                redirect(url('seller/inventory/edit?id=' . $inventoryId));
                return;
            }
        } catch (\PDOException $e) {
            logger("InventoryController::update() failed: " . $e->getMessage(), 'error');
            setFlash('error', lang_pick('La mise à jour a échoué. Veuillez réessayer.', 'Update failed. Please try again.'));
        }

        redirect(url('seller/inventory'));
    }

    /**
     * POST /seller/inventory/delete — remove item from inventory
     */
    public function delete(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/dashboard'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', lang_pick('Jeton de sécurité invalide. Rechargez la page.', 'Invalid security token.'));
            redirect(url('seller/inventory'));
            return;
        }

        $inventoryId = intval(post('inventory_id', 0));

        try {
            $stmt = $this->db->prepare(
                "DELETE FROM shop_inventory WHERE id = ? AND shop_id = ?"
            );
            $stmt->execute([$inventoryId, $shop['id']]);
            setFlash('success', lang_pick('Article retiré de l\'inventaire.', 'Item removed from inventory.'));
        } catch (\PDOException $e) {
            logger("InventoryController::delete() failed: " . $e->getMessage(), 'error');
            setFlash('error', lang_pick('L\'article n\'a pas pu être retiré.', 'Could not remove item.'));
        }

        redirect(url('seller/inventory'));
    }

    /**
     * GET /seller/inventory/create-product — form to create a brand new product
     */
    public function createProduct(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        // Fetch categories for the form
        $stmt = $this->db->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('seller/inventory/create-product', ['shop' => $shop, 'categories' => $categories]);
    }

    /**
     * POST /seller/inventory/store-product — create new product and add to inventory
     */
    public function storeProduct(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', lang_pick('Jeton de sécurité invalide. Rechargez la page.', 'Invalid security token.'));
            redirect(url('seller/inventory/create-product'));
            return;
        }

        $name        = sanitize(post('name', ''));
        $description = sanitize(post('description', ''));
        $categoryId  = intval(post('category_id', 0));
        $price       = floatval(post('price', 0));
        $stock       = intval(post('stock_quantity', 0));
        $sku         = sanitize(post('sku', ''));
        $weight      = floatval(post('weight', 0));
        $ageRestricted = post('age_restricted', '') === '1' ? 1 : 0; // 18+: hidden and blocked for Home Profile members

        if (empty($name) || $price <= 0 || $categoryId <= 0) {
            setFlash('error', lang_pick('Le nom du produit, la catégorie et le prix sont requis.', 'Product name, category, and price are required.'));
            redirect(url('seller/inventory/create-product'));
            return;
        }

        if ($weight <= 0) {
            setFlash('error', lang_pick('Le poids du produit (kg) est requis et doit être supérieur à zéro.', 'Product weight (kg) is required and must be greater than zero.'));
            redirect(url('seller/inventory/create-product'));
            return;
        }

        try {
            $this->db->beginTransaction();

            // Generate a unique slug from the product name
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)) . '-' . time();

            // Create product — categories live in product_categories join table
            $stmt = $this->db->prepare("
                INSERT INTO products (name, slug, description, sku, weight, base_price, product_type, seller_id, status, age_restricted, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'seller', ?, 'active', ?, NOW(), NOW())
            ");
            $stmt->execute([$name, $slug, $description, $sku ?: null, $weight, $price, userId(), $ageRestricted]);
            $productId = $this->db->lastInsertId();

            // Implausible-weight flag: notify admin for manual review, never block the seller
            if ($weight > 25) {
                \App\Helpers\NotificationHelper::add(
                    'product_weight_review',
                    'Product weight needs review',
                    "Seller product \"{$name}\" (ID {$productId}) was listed at {$weight} kg per unit - please verify.",
                    ['data' => ['product_id' => $productId, 'weight' => $weight]]
                );
            }

            // Link category via join table
            if ($categoryId > 0) {
                $this->db->prepare("
                    INSERT INTO product_categories (product_id, category_id, is_primary, created_at)
                    VALUES (?, ?, 1, NOW())
                ")->execute([$productId, $categoryId]);
            }

            // Add to this shop's inventory
            $stmt2 = $this->db->prepare("
                INSERT INTO shop_inventory (shop_id, product_id, price, stock_quantity, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
            ");
            $stmt2->execute([$shop['id'], $productId, $price, $stock]);

            $this->db->commit();
            setFlash('success', lang_pick('Produit créé et ajouté à votre inventaire.', 'Product created and added to your inventory.'));

            // Photos after the commit: a rejected file never loses the product itself
            [, $imgErrors] = $this->saveProductImages((int) $productId, $this->uploadedImages(), html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($imgErrors) {
                setFlash('info', implode(' ', $imgErrors));
            }
        } catch (\PDOException $e) {
            $this->db->rollBack();
            logger("InventoryController::storeProduct() failed: " . $e->getMessage(), 'error');
            setFlash('error', lang_pick('Le produit n\'a pas pu être créé. Veuillez réessayer.', 'Could not create product. Please try again.'));
        }

        redirect(url('seller/inventory'));
    }
}
