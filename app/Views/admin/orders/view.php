<?php
/**
 * Admin Order Detail View
 * Allows admin to view and manage individual orders
 * File: app/Views/admin/orders/view.php
 */

$pageTitle = 'Order #' . ($order['order_number'] ?? 'N/A');
$currentPage = 'orders';

// Get translations
$lang = $_SESSION['lang'] ?? 'en';
$t = [];
if ($lang === 'fr') {
    $t = [
        'order_details' => 'Détails de la commande',
        'back_to_orders' => 'Retour aux commandes',
        'order_info' => 'Informations sur la commande',
        'customer_info' => 'Informations client',
        'delivery_info' => 'Informations de livraison',
        'order_items' => 'Articles commandés',
        'order_timeline' => 'Chronologie de la commande',
        'actions' => 'Actions',
        'update_status' => 'Mettre à jour le statut',
        'add_note' => 'Ajouter une note',
        'send_email' => 'Envoyer un e-mail',
        'print_invoice' => 'Imprimer la facture',
        'cancel_order' => 'Annuler la commande',
        'status' => 'Statut',
        'shop' => 'Boutique',
        'order_date' => 'Date de commande',
        'subtotal' => 'Sous-total',
        'tax' => 'Taxe',
        'shipping' => 'Livraison',
        'total' => 'Total',
        'name' => 'Nom',
        'email' => 'E-mail',
        'phone' => 'Téléphone',
        'address' => 'Adresse',
        'delivery_method' => 'Méthode de livraison',
        'tracking' => 'Suivi',
        'product' => 'Produit',
        'price' => 'Prix',
        'quantity' => 'Quantité',
        'no_items' => 'Aucun article trouvé',
        'status_updated' => 'Statut mis à jour avec succès',
        'note_added' => 'Note ajoutée avec succès',
        'select_status' => 'Sélectionner un statut',
        'notes' => 'Notes',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'close' => 'Fermer',
    ];
} else {
    $t = [
        'order_details' => 'Order Details',
        'back_to_orders' => 'Back to Orders',
        'order_info' => 'Order Information',
        'customer_info' => 'Customer Information',
        'delivery_info' => 'Delivery Information',
        'order_items' => 'Order Items',
        'order_timeline' => 'Order Timeline',
        'actions' => 'Actions',
        'update_status' => 'Update Status',
        'add_note' => 'Add Note',
        'send_email' => 'Send Email',
        'print_invoice' => 'Print Invoice',
        'cancel_order' => 'Cancel Order',
        'status' => 'Status',
        'shop' => 'Shop',
        'order_date' => 'Order Date',
        'subtotal' => 'Subtotal',
        'tax' => 'Tax',
        'shipping' => 'Shipping',
        'total' => 'Total',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'address' => 'Address',
        'delivery_method' => 'Delivery Method',
        'tracking' => 'Tracking',
        'product' => 'Product',
        'price' => 'Price',
        'quantity' => 'Quantity',
        'no_items' => 'No items found',
        'status_updated' => 'Status updated successfully',
        'note_added' => 'Note added successfully',
        'select_status' => 'Select Status',
        'notes' => 'Notes',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'close' => 'Close',
    ];
}

ob_start();
?>

<style>
    .order-detail-page {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 2px solid #e2e8f0;
    }

    .header-left {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #4a5568;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #00b207;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background: #00b207;
        color: white;
    }

    .btn-primary:hover {
        background: #009606;
    }

    .btn-secondary {
        background: #f7fafc;
        color: #4a5568;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #edf2f7;
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    /* Main Grid */
    .order-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Info Rows */
    .info-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid #f7fafc;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 13px;
        font-weight: 600;
        color: #718096;
    }

    .info-value {
        font-size: 14px;
        color: #2d3748;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-confirmed {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-processing {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-ready {
        background: #d1fae5;
        color: #065f46;
    }

    .status-out_for_delivery {
        background: #fce7f3;
        color: #831843;
    }

    .status-delivered {
        background: #dcfce7;
        color: #166534;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-refunded {
        background: #f3f4f6;
        color: #4b5563;
    }

    /* Payment Status Badges */
    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
    }
    .payment-paid { background: #d1fae5; color: #065f46; }
    .payment-pending { background: #fef3c7; color: #92400e; }
    .payment-failed { background: #fee2e2; color: #991b1b; }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    .btn-warning:hover {
        background: #d97706;
    }

    /* Order Items Table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }

    .items-table thead {
        background: #f7fafc;
    }

    .items-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .items-table td {
        padding: 16px;
        border-top: 1px solid #e2e8f0;
        font-size: 14px;
        color: #4a5568;
    }

    .product-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }

    .product-name {
        font-weight: 600;
        color: #2d3748;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 24px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -29px;
        top: 24px;
        width: 2px;
        height: calc(100% - 12px);
        background: #e2e8f0;
    }

    .timeline-dot {
        position: absolute;
        left: -36px;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        border: 3px solid #00b207;
    }

    .timeline-content {
        background: #f7fafc;
        padding: 12px 16px;
        border-radius: 8px;
    }

    .timeline-status {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .timeline-date {
        font-size: 12px;
        color: #718096;
    }

    .timeline-notes {
        font-size: 13px;
        color: #4a5568;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
    }

    /* Modals */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 32px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        font-size: 20px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 20px;
    }

    .modal-body {
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
    }

    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #00b207;
        box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    /* Empty State */
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
    }

    /* Totals */
    .order-totals {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 2px solid #e2e8f0;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }

    .total-row.grand-total {
        font-size: 18px;
        font-weight: 700;
        color: #1a202c;
        padding-top: 12px;
        border-top: 2px solid #e2e8f0;
        margin-top: 8px;
    }

    @media (max-width: 1024px) {
        .order-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="header-left">
        <a href="<?= url('admin/orders') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <?= $t['back_to_orders'] ?>
        </a>
        <h1 class="page-title"><?= $t['order_details'] ?>: #<?= htmlspecialchars($order['order_number']) ?></h1>
    </div>
    <div class="header-actions">
        <?php if (($order['payment_status'] ?? '') === 'pending' && ($order['payment_method'] ?? '') === 'transfer'): ?>
            <button onclick="markAsPaid(<?= $order['id'] ?>)" class="btn btn-warning" id="markPaidBtn">
                <i class="fas fa-check-double"></i>
                Mark as Paid
            </button>
        <?php endif; ?>
        <button onclick="printInvoice()" class="btn btn-secondary">
            <i class="fas fa-print"></i>
            <?= $t['print_invoice'] ?>
        </button>
        <button onclick="openStatusModal()" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            <?= $t['update_status'] ?>
        </button>
    </div>
</div>

<div class="order-detail-page">
    <!-- Main Grid -->
    <div class="order-grid">
        <!-- Left Column -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Order Info Card -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    <?= $t['order_info'] ?>
                </h2>

                <div class="info-row">
                    <div class="info-label"><?= $t['status'] ?></div>
                    <div class="info-value">
                        <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                            <i class="fas fa-circle" style="font-size: 8px;"></i>
                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                        </span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Payment</div>
                    <div class="info-value">
                        <?php
                        $pm = $order['payment_method'] ?? 'N/A';
                        $pmLabels = ['card' => 'Credit/Debit Card', 'paypal' => 'PayPal', 'transfer' => 'Interac e-Transfer', 'cash' => 'Cash', 'stripe' => 'Stripe'];
                        echo htmlspecialchars($pmLabels[$pm] ?? ucfirst($pm));
                        ?>
                        &nbsp;
                        <?php
                        $ps = $order['payment_status'] ?? 'pending';
                        $psClass = match($ps) { 'paid' => 'payment-paid', 'failed' => 'payment-failed', default => 'payment-pending' };
                        ?>
                        <span class="payment-badge <?= $psClass ?>">
                            <i class="fas fa-<?= $ps === 'paid' ? 'check-circle' : ($ps === 'failed' ? 'times-circle' : 'clock') ?>" style="font-size: 10px;"></i>
                            <?= ucfirst($ps) ?>
                        </span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label"><?= $t['shop'] ?></div>
                    <div class="info-value">
                        <?= htmlspecialchars($order['shop_name'] ?? 'OCS Store') ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label"><?= $t['order_date'] ?></div>
                    <div class="info-value">
                        <?= date('F d, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label"><?= $t['delivery_method'] ?></div>
                    <div class="info-value">
                        <?= htmlspecialchars($order['delivery_method'] ?? 'Standard Shipping') ?>
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-shopping-bag"></i>
                    <?= $t['order_items'] ?>
                </h2>

                <?php if (!empty($items)): ?>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th><?= $t['product'] ?></th>
                                <th><?= $t['price'] ?></th>
                                <th><?= $t['quantity'] ?></th>
                                <th><?= $t['total'] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-cell">
                                            <img
                                                src="<?= !empty($item['product_image']) ? url($item['product_image']) : asset('images/placeholder.svg') ?>"
                                                alt="<?= htmlspecialchars($item['product_name'] ?? 'Product') ?>"
                                                class="product-image"
                                            >
                                            <span class="product-name">
                                                <?= htmlspecialchars($item['product_name'] ?? 'Unknown Product') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?= currency($item['price']) ?></td>
                                    <td><?= (int)$item['quantity'] ?></td>
                                    <td><strong><?= currency($item['price'] * $item['quantity']) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="order-totals">
                        <div class="total-row">
                            <span><?= $t['subtotal'] ?></span>
                            <span><?= currency($order['subtotal'] ?? 0) ?></span>
                        </div>
                        <?php if (($order['tax'] ?? 0) > 0): ?>
                            <div class="total-row">
                                <span><?= $t['tax'] ?></span>
                                <span><?= currency($order['tax']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (($order['shipping_cost'] ?? 0) > 0): ?>
                            <div class="total-row">
                                <span><?= $t['shipping'] ?></span>
                                <span><?= currency($order['shipping_cost']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="total-row grand-total">
                            <span><?= $t['total'] ?></span>
                            <span><?= currency($order['total']) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="empty-title"><?= $t['no_items'] ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Customer Info Card -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-user"></i>
                    <?= $t['customer_info'] ?>
                </h2>

                <div class="info-row">
                    <div class="info-label"><?= $t['name'] ?></div>
                    <div class="info-value">
                        <?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label"><?= $t['email'] ?></div>
                    <div class="info-value">
                        <a href="mailto:<?= htmlspecialchars($order['email'] ?? '') ?>" style="color: #00b207;">
                            <?= htmlspecialchars($order['email'] ?? 'N/A') ?>
                        </a>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label"><?= $t['phone'] ?></div>
                    <div class="info-value">
                        <a href="tel:<?= htmlspecialchars($order['phone'] ?? '') ?>" style="color: #00b207;">
                            <?= htmlspecialchars($order['phone'] ?? 'N/A') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delivery Info Card -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-truck"></i>
                    <?= $t['delivery_info'] ?>
                </h2>

                <div class="info-row">
                    <div class="info-label"><?= $t['address'] ?></div>
                    <div class="info-value">
                        <?= htmlspecialchars($order['shipping_address'] ?? 'N/A') ?><br>
                        <?= htmlspecialchars(($order['shipping_city'] ?? '') . ', ' . ($order['shipping_province'] ?? '')) ?><br>
                        <?= htmlspecialchars($order['shipping_postal_code'] ?? '') ?>
                    </div>
                </div>

                <?php if (!empty($order['delivery_assigned']) && !empty($order['estimated_ready_time'])): ?>
                    <div class="info-row">
                        <div class="info-label">Estimated Ready</div>
                        <div class="info-value">
                            <strong style="color: #00b207;">
                                <?= date('g:i A', strtotime($order['estimated_ready_time'])) ?>
                            </strong>
                            <br>
                            <small style="color: #718096;">
                                <?= date('M d, Y', strtotime($order['estimated_ready_time'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order['tracking_number'])): ?>
                    <div class="info-row">
                        <div class="info-label"><?= $t['tracking'] ?></div>
                        <div class="info-value">
                            <strong><?= htmlspecialchars($order['tracking_number']) ?></strong>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Message Driver Card (only when out for delivery) -->
            <?php if (($order['status'] ?? '') === 'out_for_delivery' && !empty($order['driver_id'])): ?>
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-comment-dots" style="color:#6d28d9;"></i>
                    Message Driver
                </h2>
                <p style="font-size:13px;color:#6b7280;margin:0 0 12px;">
                    Send a real-time message to the driver currently handling this order.
                    It will appear as an in-app alert on their phone.
                </p>
                <button onclick="openNotifyDriverModal()" class="btn btn-primary" style="background:#6d28d9;border-color:#6d28d9;width:100%;">
                    <i class="fas fa-paper-plane"></i> Send Message to Driver
                </button>
            </div>
            <?php endif; ?>

            <!-- Order Timeline Card -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-history"></i>
                    <?= $t['order_timeline'] ?>
                </h2>

                <div class="timeline">
                    <?php if (!empty($history)): ?>
                        <?php foreach ($history as $event): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-status">
                                        <?= ucfirst(str_replace('_', ' ', $event['status'] ?? 'unknown')) ?>
                                    </div>
                                    <div class="timeline-date">
                                        <?= date('M d, Y \a\t g:i A', strtotime($event['created_at'])) ?>
                                    </div>
                                    <?php if (!empty($event['notes'])): ?>
                                        <div class="timeline-notes">
                                            <?= htmlspecialchars($event['notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-status">Order Created</div>
                                <div class="timeline-date">
                                    <?= date('M d, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <?= $t['update_status'] ?>
        </div>
        <form id="statusForm">
            <?= csrfField() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label"><?= $t['status'] ?></label>
                    <select name="status" class="form-select" required>
                        <option value=""><?= $t['select_status'] ?></option>
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready for Pickup/Delivery</option>
                        <option value="out_for_delivery" <?= $order['status'] === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $t['notes'] ?> (Optional)</label>
                    <textarea name="notes" class="form-textarea" placeholder="Add any notes about this status change..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeStatusModal()" class="btn btn-secondary">
                    <?= $t['cancel'] ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?= $t['save'] ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Get CSRF token - try multiple methods
function getCsrfToken() {
    // Try meta tag first
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag && metaTag.content) {
        return metaTag.content;
    }

    // Try hidden input in any form
    const hiddenInput = document.querySelector('input[name="_csrf_token"]');
    if (hiddenInput && hiddenInput.value) {
        return hiddenInput.value;
    }

    // Log warning if not found
    console.warn('CSRF token not found');
    return '';
}

function getCsrfName() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag?.dataset?.name || '_csrf_token';
}

// Open status modal
function openStatusModal() {
    document.getElementById('statusModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Close status modal
function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusModal();
    }
});

// Close modal on overlay click
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});

// Handle status form submission
document.getElementById('statusForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const csrfToken = getCsrfToken();
    const csrfName = getCsrfName();

    console.log('Submitting status update...', {
        orderId: <?= $order['id'] ?>,
        csrfName: csrfName,
        hasToken: !!csrfToken
    });

    const formData = new FormData(this);
    formData.append('order_id', <?= $order['id'] ?>);
    formData.append(csrfName, csrfToken);

    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

    try {
        const response = await fetch('<?= url('admin/orders/update-status') ?>', {
            method: 'POST',
            body: formData
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned non-JSON response');
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            alert('<?= $t['status_updated'] ?>');
            location.reload();
        } else {
            alert(data.message || data.error || 'Failed to update status');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the status: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Mark order as paid (Interac e-Transfer)
async function markAsPaid(orderId) {
    if (!confirm('Confirm that Interac e-Transfer payment has been received for this order?')) {
        return;
    }

    const btn = document.getElementById('markPaidBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const csrfToken = getCsrfToken();
    const csrfName = getCsrfName();

    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append(csrfName, csrfToken);

    try {
        const response = await fetch('<?= url('admin/orders/mark-paid') ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message || 'Order marked as paid successfully!');
            location.reload();
        } else {
            alert(data.error || 'Failed to mark as paid.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// Print invoice
function printInvoice() {
    window.print();
}

// ── Notify Driver ─────────────────────────────────────────────────────────────
function openNotifyDriverModal() {
    document.getElementById('notifyDriverModal').style.display = 'flex';
    document.getElementById('notifyMessage').focus();
}
function closeNotifyDriverModal() {
    document.getElementById('notifyDriverModal').style.display = 'none';
    document.getElementById('notifyMessage').value = '';
    document.getElementById('notifyType').value = 'info';
    document.getElementById('notifyStatus').textContent = '';
}
document.getElementById('notifyDriverForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn    = this.querySelector('button[type=submit]');
    const status = document.getElementById('notifyStatus');
    const msg    = document.getElementById('notifyMessage').value.trim();
    const type   = document.getElementById('notifyType').value;
    if (!msg) return;

    btn.disabled = true;
    btn.textContent = 'Sending…';
    status.textContent = '';

    try {
        const res = await fetch('/admin/orders/<?= (int)($order['id'] ?? 0) ?>/notify-driver', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': getCsrfToken() },
            body: JSON.stringify({ message: msg, type }),
        });
        const data = await res.json();
        if (data.success) {
            status.style.color = '#16a34a';
            status.textContent = '✓ Message sent to driver';
            setTimeout(closeNotifyDriverModal, 1500);
        } else {
            throw new Error(data.error || 'Failed');
        }
    } catch (err) {
        status.style.color = '#dc2626';
        status.textContent = '✗ ' + err.message;
    } finally {
        btn.disabled = false;
        btn.textContent = 'Send';
    }
});
</script>

<!-- Notify Driver Modal -->
<div id="notifyDriverModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width:460px;">
        <div class="modal-header" style="background:#6d28d9;color:white;">
            <i class="fas fa-comment-dots"></i> Message Driver
        </div>
        <form id="notifyDriverForm">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Message</label>
                    <textarea id="notifyMessage" class="form-textarea" rows="4"
                        placeholder="e.g. Item #3 is missing — skip it and proceed to delivery."
                        required style="resize:vertical;"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Urgency</label>
                    <select id="notifyType" class="form-select">
                        <option value="info">ℹ️ Info — shown as banner</option>
                        <option value="warning">⚠️ Warning — shown as banner</option>
                        <option value="urgent">🚨 Urgent — full-screen alert, must acknowledge</option>
                    </select>
                </div>
                <div id="notifyStatus" style="margin-top:10px;font-size:13px;font-weight:600;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeNotifyDriverModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background:#6d28d9;border-color:#6d28d9;">Send</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
