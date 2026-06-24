<?php $currentPage = 'earnings'; include __DIR__ . '/layout-header.php'; ?>

<style>
/* Period Filter Tabs */
.period-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.period-tab {
    padding: 10px 24px;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    font-weight: 500;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s;
}

.period-tab:hover {
    border-color: #3b82f6;
    color: #3b82f6;
}

.period-tab.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* Summary Cards */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.summary-card.green {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #86efac;
}

.summary-card.orange {
    background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    border-color: #fdba74;
}

.summary-card.blue {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #93c5fd;
}

.summary-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    font-weight: 500;
}

.summary-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 16px;
}

.summary-value.green {
    color: #00b207;
}

.summary-value.orange {
    color: #ea580c;
}

.summary-value.blue {
    color: #3b82f6;
}

.summary-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 16px;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.summary-stat {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #4b5563;
}

.summary-stat strong {
    color: #1f2937;
}

/* Earnings Table */
.earnings-table-container {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.earnings-table-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.earnings-table-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}

.earnings-table {
    width: 100%;
    border-collapse: collapse;
}

.earnings-table thead {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

.earnings-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.earnings-table th:last-child {
    text-align: center;
}

.earnings-table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #374151;
}

.earnings-table tbody tr:hover {
    background: #f9fafb;
}

.earnings-table tbody tr:last-child td {
    border-bottom: none;
}

/* Type Badge */
.type-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.type-badge.b2c {
    background: #dbeafe;
    color: #1e40af;
}

.type-badge.b2b {
    background: #f3e8ff;
    color: #6b21a8;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.status-badge.paid {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.cancelled {
    background: #fee2e2;
    color: #991b1b;
}

/* Money Values */
.money {
    font-weight: 600;
    color: #00b207;
}

.money.negative {
    color: #dc2626;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-state svg {
    width: 80px;
    height: 80px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state h3 {
    font-size: 18px;
    margin-bottom: 8px;
    color: #374151;
}

.empty-state p {
    font-size: 14px;
    color: #9ca3af;
}

/* Responsive */
@media (max-width: 768px) {
    .summary-grid { grid-template-columns: 1fr; gap: 15px; }
    .summary-value { font-size: 28px; }
    .period-tabs { justify-content: center; }
    .period-tab { flex: 1; text-align: center; min-width: 100px; }
    .earnings-table-container { overflow-x: auto; }
    .earnings-table { min-width: 600px; }
    .hide-mobile { display: none !important; }
    .earnings-table th, .earnings-table td { padding: 12px 8px; font-size: 13px; }
}

@media (max-width: 480px) {
    .summary-grid { gap: 12px; }
    .summary-card { padding: 16px; }
    .summary-value { font-size: 24px; margin-bottom: 10px; }
    .summary-label { font-size: 13px; }
    .period-tabs { gap: 6px; }
    .period-tab { padding: 8px 12px; font-size: 13px; min-width: 80px; }
    .earnings-table { min-width: 380px; }
    .earnings-table th, .earnings-table td { padding: 8px 6px; font-size: 12px; }
    .earnings-table-header { padding: 14px 16px; }
    .earnings-table-header h2 { font-size: 15px; }
    .type-badge { padding: 3px 6px; font-size: 10px; }
    .status-badge { padding: 4px 8px; font-size: 11px; }
    .money { font-size: 13px; }
}
</style>

<div class="earnings-page">
    <!-- Period Filter Tabs -->
    <div class="period-tabs">
        <a href="/delivery/earnings?period=week" class="period-tab <?php echo $period === 'week' ? 'active' : ''; ?>">
            <?php echo $fr ? 'Cette semaine' : 'This Week'; ?>
        </a>
        <a href="/delivery/earnings?period=month" class="period-tab <?php echo $period === 'month' ? 'active' : ''; ?>">
            <?php echo $fr ? 'Ce mois-ci' : 'This Month'; ?>
        </a>
        <a href="/delivery/earnings?period=all" class="period-tab <?php echo $period === 'all' ? 'active' : ''; ?>">
            <?php echo $fr ? 'Tout le temps' : 'All Time'; ?>
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <!-- Net Earnings Card -->
        <div class="summary-card green">
            <div class="summary-label"><?php echo $fr ? 'Revenus nets' : 'Net Earnings'; ?></div>
            <div class="summary-value green">
                $<?php echo number_format($summary['net_earned'], 2); ?> <span style="font-size: 16px; color: #6b7280;">CAD</span>
            </div>
            <div class="summary-stats">
                <div class="summary-stat">
                    <span><?php echo $fr ? 'Total des livraisons :' : 'Total Deliveries:'; ?></span>
                    <strong><?php echo number_format($summary['total_deliveries']); ?></strong>
                </div>
                <div class="summary-stat">
                    <span><?php echo $fr ? 'Total des pourboires :' : 'Total Tips:'; ?></span>
                    <strong>$<?php echo number_format($summary['total_tips'], 2); ?> CAD</strong>
                </div>
            </div>
        </div>

        <!-- Pending Payout Card -->
        <div class="summary-card orange">
            <div class="summary-label"><?php echo $fr ? 'Paiement en attente' : 'Pending Payout'; ?></div>
            <div class="summary-value orange">
                $<?php echo number_format($summary['pending_amount'], 2); ?> <span style="font-size: 16px; color: #6b7280;">CAD</span>
            </div>
            <div class="summary-stats">
                <div class="summary-stat">
                    <span><?php echo $fr ? 'Commission déduite :' : 'Commission Deducted:'; ?></span>
                    <strong>$<?php echo number_format($summary['total_commission'], 2); ?> CAD</strong>
                </div>
            </div>
        </div>

        <!-- Paid Out Card -->
        <div class="summary-card blue">
            <div class="summary-label"><?php echo $fr ? 'Versé' : 'Paid Out'; ?></div>
            <div class="summary-value blue">
                $<?php echo number_format($summary['paid_amount'], 2); ?> <span style="font-size: 16px; color: #6b7280;">CAD</span>
            </div>
            <div class="summary-stats">
                <div class="summary-stat">
                    <span><?php echo $fr ? 'Total gagné :' : 'Total Earned:'; ?></span>
                    <strong>$<?php echo number_format($summary['total_earned'], 2); ?> CAD</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Running Balance -->
    <div style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="font-size: 14px; font-weight: 600; color: #92400e;"><?php echo $fr ? 'Solde courant (Total gagné - Total versé)' : 'Running Balance (Total Earned - Total Paid)'; ?></div>
            <div style="font-size: 28px; font-weight: 700; color: #d97706; margin-top: 4px;">
                $<?= number_format(($summary['net_earned'] ?? 0) - ($summary['paid_amount'] ?? 0), 2) ?> <span style="font-size: 14px; color: #92400e;"><?php echo $fr ? 'CAD en attente' : 'CAD pending'; ?></span>
            </div>
        </div>
        <div style="font-size: 13px; color: #92400e;">
            <?php echo $fr ? 'Les paiements sont traités chaque semaine.<br>Contactez le support pour toute question.' : 'Payouts are processed weekly.<br>Contact support for questions.'; ?>
        </div>
    </div>

    <!-- Earnings Breakdown Table -->
    <div class="earnings-table-container">
        <div class="earnings-table-header">
            <h2><?php echo $fr ? 'Détail des revenus' : 'Earnings Breakdown'; ?></h2>
        </div>

        <?php if (empty($earnings)): ?>
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3><?php echo $fr ? 'Aucun revenu pour le moment' : 'No earnings yet'; ?></h3>
                <p><?php echo $fr ? 'Complétez des livraisons pour commencer à gagner !' : 'Complete deliveries to start earning!'; ?></p>
            </div>
        <?php else: ?>
            <table class="earnings-table">
                <thead>
                    <tr>
                        <th><?php echo $fr ? 'Date' : 'Date'; ?></th>
                        <th><?php echo $fr ? 'Commande n°' : 'Order #'; ?></th>
                        <th><?php echo $fr ? 'Type' : 'Type'; ?></th>
                        <th><?php echo $fr ? 'Frais de base' : 'Base Fee'; ?></th>
                        <th><?php echo $fr ? 'Distance' : 'Distance'; ?></th>
                        <th class="hide-mobile"><?php echo $fr ? 'Pourboires' : 'Tips'; ?></th>
                        <th class="hide-mobile"><?php echo $fr ? 'Commission' : 'Commission'; ?></th>
                        <th><?php echo $fr ? 'Net' : 'Net'; ?></th>
                        <th><?php echo $fr ? 'Statut' : 'Status'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($earnings as $earning): ?>
                        <tr>
                            <td>
                                <?php
                                    $dateTs = strtotime($earning['delivered_at'] ?? $earning['created_at']);
                                    if ($fr) {
                                        $frMonthsEarn = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                                        echo (int)date('j', $dateTs) . ' ' . $frMonthsEarn[(int)date('n', $dateTs)] . ' ' . date('Y', $dateTs);
                                    } else {
                                        echo date('M j, Y', $dateTs);
                                    }
                                ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($earning['order_number']); ?></strong>
                            </td>
                            <td>
                                <?php
                                    $type = strtolower($earning['delivery_type'] ?? 'b2c');
                                    $typeClass = $type === 'b2b' ? 'b2b' : 'b2c';
                                    $typeLabel = strtoupper($type);
                                ?>
                                <span class="type-badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span>
                            </td>
                            <td>
                                <span class="money">$<?php echo number_format($earning['base_fee'], 2); ?></span>
                            </td>
                            <td>
                                <span class="money">$<?php echo number_format($earning['distance_fee'], 2); ?></span>
                            </td>
                            <td class="hide-mobile">
                                <span class="money">$<?php echo number_format($earning['tip'], 2); ?></span>
                            </td>
                            <td class="hide-mobile">
                                <span class="money negative">-$<?php echo number_format($earning['platform_commission'], 2); ?></span>
                            </td>
                            <td>
                                <strong class="money">$<?php echo number_format($earning['net_earning'], 2); ?></strong>
                            </td>
                            <td style="text-align: center;">
                                <?php
                                    $status = strtolower($earning['payment_status']);
                                    $statusClass = $status;
                                    $statusLabelsEarnFr = ['paid'=>'Payé','pending'=>'En attente','cancelled'=>'Annulé'];
                                    $statusLabel = $fr ? ($statusLabelsEarnFr[$status] ?? ucfirst($status)) : ucfirst($status);
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/layout-footer.php'; ?>
