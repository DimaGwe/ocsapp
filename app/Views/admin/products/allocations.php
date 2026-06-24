<?php
/**
 * Product Allocations Page
 * Shows which shops have this product allocated
 * File: app/Views/admin/products/allocations.php
 */

$pageTitle = 'Product Allocations - ' . htmlspecialchars($product['name']);
$currentPage = 'stock';

// Get translations
$lang = $_SESSION['lang'] ?? 'en';
$t = [];
if ($lang === 'fr') {
    $t = [
        'title' => 'Allocations de produits',
        'back' => 'Retour au stock',
        'product_details' => 'Détails du produit',
        'sku' => 'SKU',
        'price' => 'Prix',
        'total_allocated' => 'Total alloué',
        'allocations' => 'Allocations',
        'shop' => 'Boutique',
        'seller' => 'Vendeur',
        'allocated' => 'Alloué',
        'in_stock' => 'En stock',
        'status' => 'Statut',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'no_allocations' => 'Aucune allocation trouvée',
        'no_allocations_desc' => 'Ce produit n\'est pas encore alloué à une boutique',
    ];
} else {
    $t = [
        'title' => 'Product Allocations',
        'back' => 'Back to Stock',
        'product_details' => 'Product Details',
        'sku' => 'SKU',
        'price' => 'Price',
        'total_allocated' => 'Total Allocated',
        'allocations' => 'Allocations',
        'shop' => 'Shop',
        'seller' => 'Seller',
        'allocated' => 'Allocated',
        'in_stock' => 'In Stock',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'no_allocations' => 'No allocations found',
        'no_allocations_desc' => 'This product has not been allocated to any shops yet',
    ];
}

ob_start();
?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 2px solid #e2e8f0;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        color: #4a5568;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .back-btn:hover {
        background: #edf2f7;
        border-color: #cbd5e0;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 32px;
    }

    .product-info {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 24px;
        align-items: start;
    }

    .product-image {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }

    .product-details h2 {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 12px;
    }

    .product-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 16px;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .meta-label {
        font-size: 12px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .meta-value {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
    }

    .allocations-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }

    .allocations-table {
        width: 100%;
        border-collapse: collapse;
    }

    .allocations-table th {
        background: #f7fafc;
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .allocations-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
        color: #4a5568;
    }

    .allocations-table tr:hover {
        background: #f7fafc;
    }

    .shop-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .shop-name {
        font-weight: 600;
        color: #2d3748;
    }

    .shop-id {
        font-size: 12px;
        color: #718096;
    }

    .seller-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .seller-name {
        font-weight: 500;
        color: #2d3748;
    }

    .seller-email {
        font-size: 12px;
        color: #718096;
    }

    .quantity-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: #edf2f7;
        border-radius: 6px;
        font-weight: 600;
    }

    .quantity-allocated {
        color: #2b6cb0;
    }

    .quantity-stock {
        color: #2f855a;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: #c6f6d5;
        color: #22543d;
    }

    .status-inactive {
        background: #fed7d7;
        color: #742a2a;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
    }

    .empty-icon {
        font-size: 48px;
        color: #cbd5e0;
        margin-bottom: 16px;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
    }

    .empty-desc {
        font-size: 14px;
        color: #718096;
    }

    @media (max-width: 768px) {
        .product-info {
            grid-template-columns: 1fr;
        }

        .product-meta {
            grid-template-columns: 1fr;
        }

        .allocations-table {
            font-size: 12px;
        }

        .allocations-table th,
        .allocations-table td {
            padding: 8px;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title"><?= $t['title'] ?></h1>
    <a href="<?= url('admin/products/stock') ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <?= $t['back'] ?>
    </a>
</div>

<!-- Product Card -->
<div class="product-card">
    <div class="product-info">
        <img
            src="<?= !empty($product['primary_image']) ? url($product['primary_image']) : asset('images/placeholder.svg') ?>"
            alt="<?= htmlspecialchars($product['name']) ?>"
            class="product-image"
        >
        <div class="product-details">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label"><?= $t['sku'] ?></span>
                    <span class="meta-value"><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><?= $t['price'] ?></span>
                    <span class="meta-value">$<?= number_format($product['base_price'], 2) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><?= $t['total_allocated'] ?></span>
                    <span class="meta-value"><?= number_format($product['allocated_stock'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Allocations Section -->
<div class="allocations-section">
    <h3 class="section-title">
        <i class="fas fa-store"></i> <?= $t['allocations'] ?>
        <span style="color: #718096; font-weight: 400; font-size: 14px;">
            (<?= count($allocations) ?> <?= count($allocations) === 1 ? 'shop' : 'shops' ?>)
        </span>
    </h3>

    <?php if (!empty($allocations)): ?>
        <div style="overflow-x: auto;">
            <table class="allocations-table">
                <thead>
                    <tr>
                        <th><?= $t['shop'] ?></th>
                        <th><?= $t['seller'] ?></th>
                        <th><?= $t['allocated'] ?></th>
                        <th><?= $t['in_stock'] ?></th>
                        <th><?= $t['status'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $allocation): ?>
                        <tr>
                            <td>
                                <div class="shop-info">
                                    <span class="shop-name">
                                        <?= htmlspecialchars($allocation['shop_name']) ?>
                                    </span>
                                    <span class="shop-id">Shop ID: <?= $allocation['shop_id'] ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="seller-info">
                                    <?php if ($allocation['seller_first_name']): ?>
                                        <span class="seller-name">
                                            <?= htmlspecialchars($allocation['seller_first_name'] . ' ' . $allocation['seller_last_name']) ?>
                                        </span>
                                        <span class="seller-email"><?= htmlspecialchars($allocation['seller_email']) ?></span>
                                    <?php else: ?>
                                        <span class="seller-name">OCS Store</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="quantity-badge quantity-allocated">
                                    <i class="fas fa-boxes"></i>
                                    <?= number_format($allocation['allocated_quantity']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="quantity-badge quantity-stock">
                                    <i class="fas fa-warehouse"></i>
                                    <?= number_format($allocation['stock_quantity']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $allocation['shop_is_active'] ? 'active' : 'inactive' ?>">
                                    <?= $allocation['shop_is_active'] ? $t['active'] : $t['inactive'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <h4 class="empty-title"><?= $t['no_allocations'] ?></h4>
            <p class="empty-desc"><?= $t['no_allocations_desc'] ?></p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
