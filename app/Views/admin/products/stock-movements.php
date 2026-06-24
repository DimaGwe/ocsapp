<?php
/**
 * Stock Movements Page
 * Shows stock movement history for a product
 * File: app/Views/admin/products/stock-movements.php
 */

$pageTitle = 'Stock Movements - ' . htmlspecialchars($product['name']);
$currentPage = 'stock';

// Get translations
$lang = $_SESSION['lang'] ?? 'en';
$t = [];
if ($lang === 'fr') {
    $t = [
        'title' => 'Mouvements de stock',
        'back' => 'Retour au stock',
        'product_details' => 'Détails du produit',
        'sku' => 'SKU',
        'price' => 'Prix',
        'current_stock' => 'Stock actuel',
        'movements' => 'Mouvements',
        'date' => 'Date',
        'type' => 'Type',
        'shop' => 'Boutique',
        'quantity' => 'Quantité',
        'reason' => 'Raison',
        'user' => 'Utilisateur',
        'no_movements' => 'Aucun mouvement trouvé',
        'no_movements_desc' => 'Aucun mouvement de stock enregistré pour ce produit',
        // Movement types
        'allocation' => 'Allocation',
        'restock' => 'Réapprovisionnement',
        'adjustment' => 'Ajustement',
        'sale' => 'Vente',
        'return' => 'Retour',
        'damage' => 'Dommage',
        'admin_restock' => 'Réappro Admin',
    ];
} else {
    $t = [
        'title' => 'Stock Movements',
        'back' => 'Back to Stock',
        'product_details' => 'Product Details',
        'sku' => 'SKU',
        'price' => 'Price',
        'current_stock' => 'Current Stock',
        'movements' => 'Movements',
        'date' => 'Date',
        'type' => 'Type',
        'shop' => 'Shop',
        'quantity' => 'Quantity',
        'reason' => 'Reason',
        'user' => 'User',
        'no_movements' => 'No movements found',
        'no_movements_desc' => 'No stock movements recorded for this product',
        // Movement types
        'allocation' => 'Allocation',
        'restock' => 'Restock',
        'adjustment' => 'Adjustment',
        'sale' => 'Sale',
        'return' => 'Return',
        'damage' => 'Damage',
        'admin_restock' => 'Admin Restock',
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

    .movements-section {
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

    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 16px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 32px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -32px;
        top: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: white;
        z-index: 1;
    }

    .marker-allocation { background: #4299e1; }
    .marker-restock { background: #48bb78; }
    .marker-adjustment { background: #ed8936; }
    .marker-sale { background: #9f7aea; }
    .marker-return { background: #38b2ac; }
    .marker-damage { background: #f56565; }
    .marker-admin_restock { background: #667eea; }

    .movement-card {
        background: #f7fafc;
        border-radius: 8px;
        padding: 16px;
        border-left: 4px solid #e2e8f0;
    }

    .movement-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 12px;
    }

    .movement-type {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .type-allocation { background: #bee3f8; color: #2c5282; }
    .type-restock { background: #c6f6d5; color: #22543d; }
    .type-adjustment { background: #feebc8; color: #7c2d12; }
    .type-sale { background: #e9d8fd; color: #44337a; }
    .type-return { background: #b2f5ea; color: #234e52; }
    .type-damage { background: #fed7d7; color: #742a2a; }
    .type-admin_restock { background: #c3dafe; color: #2d3748; }

    .movement-time {
        font-size: 12px;
        color: #718096;
    }

    .movement-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .detail-label {
        font-size: 11px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
    }

    .quantity-positive {
        color: #22543d;
    }

    .quantity-negative {
        color: #742a2a;
    }

    .movement-reason {
        font-size: 13px;
        color: #4a5568;
        padding: 8px 12px;
        background: white;
        border-radius: 6px;
        margin-top: 8px;
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

        .movement-header {
            flex-direction: column;
            gap: 8px;
        }

        .movement-details {
            grid-template-columns: 1fr;
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
                    <span class="meta-label"><?= $t['current_stock'] ?></span>
                    <span class="meta-value"><?= number_format($product['total_stock'] ?? $product['stock_quantity'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Movements Section -->
<div class="movements-section">
    <h3 class="section-title">
        <i class="fas fa-history"></i> <?= $t['movements'] ?>
        <span style="color: #718096; font-weight: 400; font-size: 14px;">
            (<?= count($movements) ?> <?= count($movements) === 1 ? 'record' : 'records' ?>)
        </span>
    </h3>

    <?php if (!empty($movements)): ?>
        <div class="timeline">
            <?php foreach ($movements as $movement): ?>
                <?php
                $movementType = $movement['type'] ?? 'adjustment';
                $movementTypeLabel = $t[$movementType] ?? ucfirst($movementType);
                $isPositive = in_array($movementType, ['allocation', 'restock', 'return', 'admin_restock']);
                ?>
                <div class="timeline-item">
                    <div class="timeline-marker marker-<?= $movementType ?>">
                        <?php if ($movementType === 'allocation'): ?>
                            <i class="fas fa-share"></i>
                        <?php elseif ($movementType === 'restock' || $movementType === 'admin_restock'): ?>
                            <i class="fas fa-plus"></i>
                        <?php elseif ($movementType === 'adjustment'): ?>
                            <i class="fas fa-sync"></i>
                        <?php elseif ($movementType === 'sale'): ?>
                            <i class="fas fa-shopping-cart"></i>
                        <?php elseif ($movementType === 'return'): ?>
                            <i class="fas fa-undo"></i>
                        <?php elseif ($movementType === 'damage'): ?>
                            <i class="fas fa-exclamation-triangle"></i>
                        <?php else: ?>
                            <i class="fas fa-box"></i>
                        <?php endif; ?>
                    </div>

                    <div class="movement-card">
                        <div class="movement-header">
                            <span class="movement-type type-<?= $movementType ?>">
                                <?= $movementTypeLabel ?>
                            </span>
                            <span class="movement-time">
                                <i class="fas fa-clock"></i>
                                <?= date('M d, Y - h:i A', strtotime($movement['created_at'])) ?>
                            </span>
                        </div>

                        <div class="movement-details">
                            <?php if (!empty($movement['shop_name'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label"><?= $t['shop'] ?></span>
                                    <span class="detail-value"><?= htmlspecialchars($movement['shop_name']) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="detail-item">
                                <span class="detail-label"><?= $t['quantity'] ?></span>
                                <span class="detail-value <?= $isPositive ? 'quantity-positive' : 'quantity-negative' ?>">
                                    <?= $isPositive ? '+' : '-' ?><?= number_format($movement['quantity']) ?>
                                </span>
                            </div>

                            <?php if (!empty($movement['first_name'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label"><?= $t['user'] ?></span>
                                    <span class="detail-value">
                                        <?= htmlspecialchars($movement['first_name'] . ' ' . $movement['last_name']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($movement['reason'])): ?>
                            <div class="movement-reason">
                                <i class="fas fa-info-circle"></i>
                                <?= htmlspecialchars($movement['reason']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-history"></i>
            </div>
            <h4 class="empty-title"><?= $t['no_movements'] ?></h4>
            <p class="empty-desc"><?= $t['no_movements_desc'] ?></p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
