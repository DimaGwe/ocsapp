<?php $currentPage = 'history'; include __DIR__ . '/layout-header.php'; ?>

<style>
    .history-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .history-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 30px;
    }

    .history-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .count-badge {
        background: #3b82f6;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .table-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .history-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .history-table th {
        padding: 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .history-table td {
        padding: 16px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
    }

    .history-table tbody tr {
        background: white;
        transition: background 0.2s;
    }

    .history-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .history-table tbody tr:hover {
        background: #f0f9ff;
    }

    .order-link {
        color: #3b82f6;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }

    .order-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-b2c {
        background: #dcfce7;
        color: #166534;
    }

    .type-b2b {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-delivered {
        background: #dcfce7;
        color: #166534;
    }

    .status-assigned {
        background: #fef3c7;
        color: #92400e;
    }

    .status-accepted {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-picked_up {
        background: #fed7aa;
        color: #9a3412;
    }

    .status-on_the_way {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-failed {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-cancelled {
        background: #f3f4f6;
        color: #6b7280;
    }

    .route-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 13px;
    }

    .route-from {
        color: #6b7280;
        font-weight: 500;
    }

    .route-arrow {
        color: #9ca3af;
        margin: 0 4px;
    }

    .route-to {
        color: #374151;
    }

    .fee-amount {
        font-weight: 600;
        color: #00b207;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 600;
        color: #9ca3af;
        margin: 0;
    }

    .pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 30px;
        padding: 0 10px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 14px;
    }

    .pagination-controls {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .page-btn {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    .page-btn:hover:not(.disabled) {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .page-btn.active {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .page-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .history-container { padding: 15px; }
        .history-header h1 { font-size: 22px; }
        .hide-mobile { display: none; }
        .history-table { min-width: 500px; }
        .pagination { justify-content: center; }
        .pagination-info { width: 100%; text-align: center; }
        .history-table th, .history-table td { padding: 12px 10px; }
    }

    @media (max-width: 480px) {
        .history-container { padding: 12px; }
        .history-header h1 { font-size: 18px; }
        .history-table { min-width: 360px; }
        .history-table th, .history-table td { padding: 10px 8px; font-size: 13px; }
        .status-badge { padding: 4px 8px; font-size: 11px; }
        .fee-amount { font-size: 13px; }
        .page-btn { padding: 6px 10px; font-size: 13px; }
        .count-badge { font-size: 12px; padding: 3px 8px; }
    }
</style>

<div class="history-container">
    <div class="history-header">
        <h1><?php echo $fr ? 'Historique de livraisons' : 'Delivery History'; ?></h1>
        <?php if ($total > 0): ?>
            <span class="count-badge"><?php echo number_format($total); ?></span>
        <?php endif; ?>
    </div>

    <?php if (empty($deliveries)): ?>
        <div class="table-wrapper">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3><?php echo $fr ? 'Aucun historique de livraison pour le moment.' : 'No delivery history yet.'; ?></h3>
            </div>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <div class="table-scroll">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th><?php echo $fr ? 'Commande n°' : 'Order #'; ?></th>
                            <th class="hide-mobile"><?php echo $fr ? 'Type' : 'Type'; ?></th>
                            <th><?php echo $fr ? 'Client' : 'Customer'; ?></th>
                            <th class="hide-mobile"><?php echo $fr ? 'Trajet' : 'Route'; ?></th>
                            <th><?php echo $fr ? 'Statut' : 'Status'; ?></th>
                            <th><?php echo $fr ? 'Frais' : 'Fee'; ?></th>
                            <th><?php echo $fr ? 'Date' : 'Date'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deliveries as $delivery): ?>
                            <tr>
                                <td>
                                    <a href="/delivery/details?id=<?php echo htmlspecialchars($delivery['id']); ?>" class="order-link">
                                        #<?php echo htmlspecialchars($delivery['order_number']); ?>
                                    </a>
                                </td>
                                <td class="hide-mobile">
                                    <?php
                                    $type = strtoupper($delivery['delivery_type'] ?? 'B2C');
                                    $typeClass = $type === 'B2B' ? 'type-b2b' : 'type-b2c';
                                    ?>
                                    <span class="type-badge <?php echo $typeClass; ?>"><?php echo $type; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $customerName = trim(($delivery['customer_first_name'] ?? '') . ' ' . ($delivery['customer_last_name'] ?? ''));
                                    echo htmlspecialchars($customerName ?: 'N/A');
                                    ?>
                                </td>
                                <td class="hide-mobile">
                                    <div class="route-info">
                                        <div class="route-from">
                                            <?php echo htmlspecialchars($delivery['shop_name'] ?? 'Shop'); ?>
                                        </div>
                                        <div class="route-to">
                                            <span class="route-arrow">→</span>
                                            <?php
                                            $address = $delivery['delivery_address'] ?? 'N/A';
                                            // Truncate long addresses
                                            if (strlen($address) > 50) {
                                                $address = substr($address, 0, 47) . '...';
                                            }
                                            echo htmlspecialchars($address);
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $status = $delivery['status'] ?? 'assigned';
                                    $statusClass = 'status-' . str_replace(' ', '_', strtolower($status));
                                    $statusLabelsEn = [
                                        'assigned'   => 'Assigned',
                                        'accepted'   => 'Accepted',
                                        'picked_up'  => 'Picked Up',
                                        'on_the_way' => 'On the Way',
                                        'delivered'  => 'Delivered',
                                        'failed'     => 'Failed',
                                        'cancelled'  => 'Cancelled',
                                    ];
                                    $statusLabelsFr = [
                                        'assigned'   => 'Assignée',
                                        'accepted'   => 'Acceptée',
                                        'picked_up'  => 'Ramassée',
                                        'on_the_way' => 'En route',
                                        'delivered'  => 'Livrée',
                                        'failed'     => 'Échouée',
                                        'cancelled'  => 'Annulée',
                                    ];
                                    $statusLabel = $fr
                                        ? ($statusLabelsFr[$status] ?? ucfirst(str_replace('_', ' ', $status)))
                                        : ($statusLabelsEn[$status] ?? ucfirst(str_replace('_', ' ', $status)));
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($statusLabel); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fee-amount">
                                        $<?php echo number_format($delivery['delivery_fee'] ?? 0, 2); ?> CAD
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $date = $delivery['delivered_at'] ?? $delivery['accepted_at'] ?? $delivery['assigned_at'] ?? $delivery['created_at'];
                                    if ($date) {
                                        $ts = strtotime($date);
                                        if ($fr) {
                                            $frMonths = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                                            echo (int)date('j', $ts) . ' ' . $frMonths[(int)date('n', $ts)] . ' ' . date('Y', $ts);
                                        } else {
                                            echo date('M j, Y', $ts);
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <div class="pagination-info">
                    <?php echo $fr ? 'Page ' . $page . ' de ' . $totalPages : 'Page ' . $page . ' of ' . $totalPages; ?>
                </div>
                <div class="pagination-controls">
                    <?php if ($page > 1): ?>
                        <a href="/delivery/history?page=<?php echo $page - 1; ?>" class="page-btn"><?php echo $fr ? 'Précédent' : 'Previous'; ?></a>
                    <?php else: ?>
                        <span class="page-btn disabled"><?php echo $fr ? 'Précédent' : 'Previous'; ?></span>
                    <?php endif; ?>

                    <?php
                    // Show page numbers
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    if ($startPage > 1): ?>
                        <a href="/delivery/history?page=1" class="page-btn">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="page-btn disabled">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="page-btn active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="/delivery/history?page=<?php echo $i; ?>" class="page-btn"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): ?>
                        <?php if ($endPage < $totalPages - 1): ?>
                            <span class="page-btn disabled">...</span>
                        <?php endif; ?>
                        <a href="/delivery/history?page=<?php echo $totalPages; ?>" class="page-btn"><?php echo $totalPages; ?></a>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="/delivery/history?page=<?php echo $page + 1; ?>" class="page-btn"><?php echo $fr ? 'Suivant' : 'Next'; ?></a>
                    <?php else: ?>
                        <span class="page-btn disabled"><?php echo $fr ? 'Suivant' : 'Next'; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/layout-footer.php'; ?>
