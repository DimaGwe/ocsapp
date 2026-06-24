<?php $currentPage = 'available'; include __DIR__ . '/layout-header.php'; ?>

<style>
    .available-orders-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-header h1 {
        font-size: 28px;
        color: #1f2937;
        margin: 0;
    }

    .delivery-count {
        background: #3b82f6;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-left: 10px;
    }

    .refresh-btn {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
    }

    .refresh-btn:hover {
        background: #2563eb;
    }

    .refresh-btn svg {
        width: 16px;
        height: 16px;
    }

    .deliveries-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .delivery-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .delivery-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f3f4f6;
    }

    .order-number {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }

    .delivery-type-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-b2c {
        background: #00b207;
        color: white;
    }

    .badge-b2b {
        background: #3b82f6;
        color: white;
    }

    .delivery-fee {
        font-size: 18px;
        font-weight: 700;
        color: #00b207;
    }

    .location-section {
        margin-bottom: 16px;
    }

    .location-item {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }

    .location-icon {
        flex-shrink: 0;
        margin-top: 2px;
    }

    .location-icon svg {
        width: 20px;
        height: 20px;
    }

    .location-details {
        flex: 1;
    }

    .location-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #6b7280;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .location-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .location-address {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.4;
    }

    .delivery-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #6b7280;
    }

    .meta-item svg {
        width: 16px;
        height: 16px;
        color: #9ca3af;
    }

    .customer-phone {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .customer-phone:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .customer-phone svg {
        width: 16px;
        height: 16px;
    }

    .order-notes {
        background: #fef3c7;
        border-left: 3px solid #f59e0b;
        padding: 10px 12px;
        margin-bottom: 16px;
        border-radius: 4px;
    }

    .order-notes-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #92400e;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .order-notes-text {
        font-size: 13px;
        color: #78350f;
        line-height: 1.5;
    }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .btn {
        flex: 1;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        text-align: center;
    }

    .btn-accept {
        background: #00b207;
        color: white;
    }

    .btn-accept:hover {
        background: #009206;
    }

    .btn-reject {
        background: white;
        color: #ef4444;
        border: 2px solid #ef4444;
    }

    .btn-reject:hover {
        background: #fef2f2;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-state h2 {
        font-size: 20px;
        color: #1f2937;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 15px;
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .available-orders-container {
            padding: 15px;
        }

        .page-header h1 {
            font-size: 24px;
        }

        .deliveries-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .delivery-card {
            padding: 16px;
        }

        .card-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }

    /* Loading state */
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<div class="available-orders-container">
    <div class="page-header">
        <div>
            <h1>
                <?php echo htmlspecialchars($pageTitle); ?>
                <?php if (!empty($orders)): ?>
                    <span class="delivery-count"><?php echo count($orders); ?></span>
                <?php endif; ?>
            </h1>
        </div>
        <button class="refresh-btn" onclick="refreshPage()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <?php echo $fr ? 'Actualiser' : 'Refresh'; ?>
        </button>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h2><?php echo $fr ? 'Aucune livraison disponible' : 'No Deliveries Available'; ?></h2>
            <p><?php echo $fr ? 'Aucune livraison assignée pour le moment. Restez en ligne pour recevoir de nouvelles assignations !' : 'No deliveries assigned right now. Stay online to receive new assignments!'; ?></p>
        </div>
    <?php else: ?>
        <div class="deliveries-grid">
            <?php foreach ($orders as $order): ?>
                <div class="delivery-card">
                    <div class="card-header">
                        <div>
                            <div class="order-number">#<?php echo htmlspecialchars($order['order_number']); ?></div>
                            <span class="delivery-type-badge <?php echo ($order['delivery_type'] === 'distribution') ? 'badge-b2b' : 'badge-b2c'; ?>">
                                <?php echo ($order['delivery_type'] === 'distribution') ? 'B2B' : 'B2C'; ?>
                            </span>
                        </div>
                        <div class="delivery-fee">
                            $<?php echo number_format($order['delivery_fee'], 2); ?>
                        </div>
                    </div>

                    <div class="location-section">
                        <!-- Pickup Location -->
                        <div class="location-item">
                            <div class="location-icon">
                                <svg fill="currentColor" viewBox="0 0 24 24" style="color: #3b82f6;">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                            <div class="location-details">
                                <div class="location-label"><?php echo $fr ? 'Ramassage' : 'Pickup'; ?></div>
                                <div class="location-name"><?php echo htmlspecialchars($order['shop_name']); ?></div>
                                <div class="location-address"><?php echo htmlspecialchars($order['shop_address']); ?></div>
                            </div>
                        </div>

                        <!-- Dropoff Location -->
                        <div class="location-item">
                            <div class="location-icon">
                                <svg fill="currentColor" viewBox="0 0 24 24" style="color: #00b207;">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                </svg>
                            </div>
                            <div class="location-details">
                                <div class="location-label"><?php echo $fr ? 'Livraison' : 'Dropoff'; ?></div>
                                <div class="location-name">
                                    <?php echo htmlspecialchars($order['customer_first_name'] . ' ' . $order['customer_last_name']); ?>
                                </div>
                                <div class="location-address"><?php echo htmlspecialchars($order['delivery_address']); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="delivery-meta">
                        <?php if (!empty($order['distance_km'])): ?>
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                <?php echo number_format($order['distance_km'], 1); ?> km
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($order['estimated_time'])): ?>
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                ~<?php echo htmlspecialchars($order['estimated_time']); ?> min
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($order['customer_phone'])): ?>
                        <a href="tel:<?php echo htmlspecialchars($order['customer_phone']); ?>" class="customer-phone">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <?php echo htmlspecialchars($order['customer_phone']); ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($order['notes'])): ?>
                        <div class="order-notes">
                            <div class="order-notes-label"><?php echo $fr ? 'Notes de livraison' : 'Delivery Notes'; ?></div>
                            <div class="order-notes-text"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="card-actions">
                        <button class="btn btn-accept" onclick="acceptDelivery(<?php echo $order['id']; ?>)">
                            <?php echo $fr ? 'Accepter la livraison' : 'Accept Delivery'; ?>
                        </button>
                        <button class="btn btn-reject" onclick="rejectDelivery(<?php echo $order['id']; ?>)">
                            <?php echo $fr ? 'Refuser' : 'Reject'; ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const _L = {
        lang: <?php echo json_encode($currentLang); ?>,
        confirmAccept:    <?php echo $fr ? "'Accepter cette livraison ?'" : "'Accept this delivery assignment?'"; ?>,
        accepting:        <?php echo $fr ? "'Acceptation...'" : "'Accepting...'"; ?>,
        acceptSuccess:    <?php echo $fr ? "'Livraison acceptée avec succès !'" : "'Delivery accepted successfully!'"; ?>,
        acceptFail:       <?php echo $fr ? "'Échec de l\\'acceptation de la livraison'" : "'Failed to accept delivery'"; ?>,
        acceptLabel:      <?php echo $fr ? "'Accepter la livraison'" : "'Accept Delivery'"; ?>,
        rejectPrompt:     <?php echo $fr ? "'Veuillez indiquer la raison du refus :'" : "'Please provide a reason for rejecting this delivery:'"; ?>,
        rejecting:        <?php echo $fr ? "'Refus...'" : "'Rejecting...'"; ?>,
        rejectSuccess:    <?php echo $fr ? "'Livraison refusée.'" : "'Delivery rejected.'"; ?>,
        rejectFail:       <?php echo $fr ? "'Échec du refus de la livraison'" : "'Failed to reject delivery'"; ?>,
        rejectLabel:      <?php echo $fr ? "'Refuser'" : "'Reject'"; ?>,
        networkError:     <?php echo $fr ? "'Erreur réseau. Veuillez réessayer.'" : "'Network error. Please try again.'"; ?>,
        errorPrefix:      <?php echo $fr ? "'Erreur : '" : "'Error: '"; ?>
    };

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function refreshPage() {
        window.location.reload();
    }

    async function acceptDelivery(deliveryId) {
        if (!confirm(_L.confirmAccept)) return;

        const button = event.target;
        button.disabled = true;
        button.textContent = _L.accepting;

        try {
            const response = await fetch('/delivery/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: new URLSearchParams({
                    delivery_id: deliveryId,
                    csrf_token: getCsrfToken()
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(_L.acceptSuccess);
                window.location.reload();
            } else {
                alert(_L.errorPrefix + (data.message || _L.acceptFail));
                button.disabled = false;
                button.textContent = _L.acceptLabel;
            }
        } catch (error) {
            console.error('Error:', error);
            alert(_L.networkError);
            button.disabled = false;
            button.textContent = _L.acceptLabel;
        }
    }

    async function rejectDelivery(deliveryId) {
        const reason = prompt(_L.rejectPrompt);
        if (!reason || reason.trim() === '') return;

        const button = event.target;
        button.disabled = true;
        button.textContent = _L.rejecting;

        try {
            const response = await fetch('/delivery/reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: new URLSearchParams({
                    delivery_id: deliveryId,
                    reason: reason.trim(),
                    csrf_token: getCsrfToken()
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(_L.rejectSuccess);
                window.location.reload();
            } else {
                alert(_L.errorPrefix + (data.message || _L.rejectFail));
                button.disabled = false;
                button.textContent = _L.rejectLabel;
            }
        } catch (error) {
            console.error('Error:', error);
            alert(_L.networkError);
            button.disabled = false;
            button.textContent = _L.rejectLabel;
        }
    }
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
