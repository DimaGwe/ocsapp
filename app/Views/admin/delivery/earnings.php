<?php
/**
 * OCS Admin Delivery Earnings
 * File: app/Views/admin/delivery/earnings.php
 */

$pageTitle = 'Delivery Earnings';
$currentPage = 'driver-earnings';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'delivery_earnings' => 'Delivery Earnings',
        'manage_payouts' => 'Manage driver payouts and earnings',
        'back' => 'Back',
        'total_gross' => 'Total Gross',
        'platform_commission' => 'Platform Commission',
        'net_to_drivers' => 'Net to Drivers',
        'pending_payment' => 'Pending Payment',
        'all' => 'All',
        'pending' => 'Pending',
        'paid' => 'Paid',
        'mark_paid' => 'Mark Selected as Paid',
        'order' => 'Order',
        'driver' => 'Driver',
        'base_fee' => 'Service Fee (5%)',
        'distance' => 'Delivery Fee',
        'handling' => 'Handling Fee',
        'total' => 'Total',
        'commission' => 'Commission (20%)',
        'net' => 'Net',
        'status' => 'Status',
        'date' => 'Date',
        'no_earnings' => 'No earnings records found',
        'select_earnings' => 'Please select earnings to mark as paid',
        'confirm_mark_paid' => 'Mark as paid?',
        'marked_success' => 'Marked as paid successfully!'
    ],
    'fr' => [
        'delivery_earnings' => 'Gains des Livraisons',
        'manage_payouts' => 'Gérer les paiements et gains des livreurs',
        'back' => 'Retour',
        'total_gross' => 'Total Brut',
        'platform_commission' => 'Commission Plateforme',
        'net_to_drivers' => 'Net aux Livreurs',
        'pending_payment' => 'Paiement en Attente',
        'all' => 'Tout',
        'pending' => 'En Attente',
        'paid' => 'Payé',
        'mark_paid' => 'Marquer Payé',
        'order' => 'Commande',
        'driver' => 'Livreur',
        'base_fee' => 'Frais de Service (5%)',
        'distance' => 'Frais de Livraison',
        'handling' => 'Frais de Manutention',
        'total' => 'Total',
        'commission' => 'Commission',
        'net' => 'Net',
        'status' => 'Statut',
        'date' => 'Date',
        'no_earnings' => 'Aucun enregistrement de gains trouvé',
        'select_earnings' => 'Veuillez sélectionner les gains à marquer comme payés',
        'confirm_mark_paid' => 'Marquer comme payé?',
        'marked_success' => 'Marqué comme payé avec succès!'
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
}

.page-subtitle {
    color: var(--gray-600);
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all var(--transition-base);
    cursor: pointer;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-300);
}

.btn-success {
    background: #22c55e;
    color: white;
}

.btn-success:hover {
    background: #16a34a;
}

/* Summary Cards */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.summary-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
}

.summary-card.pending {
    border-left: 4px solid #f97316;
}

.summary-label {
    font-size: 14px;
    color: var(--gray-500);
    margin-bottom: 8px;
}

.summary-value {
    font-size: 28px;
    font-weight: 700;
}

.summary-value.gross { color: var(--dark); }
.summary-value.commission { color: #a855f7; }
.summary-value.net { color: var(--primary); }
.summary-value.pending { color: #f97316; }

/* Filters & Actions */
.filter-bar {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 20px 24px;
    margin-bottom: 24px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.status-filters {
    display: flex;
    gap: 8px;
}

.filter-btn {
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all var(--transition-base);
    background: var(--gray-200);
    color: var(--gray-700);
}

.filter-btn:hover {
    background: var(--gray-300);
}

.filter-btn.all.active {
    background: var(--primary);
    color: white;
}

.filter-btn.pending.active {
    background: #f97316;
    color: white;
}

.filter-btn.paid.active {
    background: #22c55e;
    color: white;
}

/* Earnings Table */
.earnings-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.earnings-table {
    width: 100%;
    border-collapse: collapse;
}

.earnings-table thead {
    background: var(--gray-50);
}

.earnings-table th {
    padding: 12px 20px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-500);
}

.earnings-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
}

.earnings-table tbody tr:hover {
    background: var(--gray-50);
}

.order-number {
    font-weight: 600;
    color: var(--dark);
}

.driver-name {
    font-weight: 600;
    color: var(--dark);
}

.amount {
    font-weight: 600;
}

.amount.commission {
    color: #ef4444;
}

.amount.net {
    color: var(--primary);
    font-weight: 700;
}

.status-badge {
    display: inline-flex;
    padding: 6px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.paid {
    background: #dcfce7;
    color: #166534;
}

.status-badge.pending {
    background: #ffedd5;
    color: #9a3412;
}

.date-text {
    font-size: 14px;
    color: var(--gray-600);
}

.empty-state {
    text-align: center;
    padding: 48px 24px;
    color: var(--gray-500);
}

/* Custom Checkbox */
.custom-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid var(--gray-300);
    cursor: pointer;
}

.custom-checkbox:checked {
    background: var(--primary);
    border-color: var(--primary);
}

/* Extra Filters */
.extra-filters {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.extra-filters input,
.extra-filters select {
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
}

.btn-export {
    background: #6366f1;
    color: white;
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-export:hover {
    background: #4f46e5;
}

/* Payment Modal */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-overlay.active {
    display: flex;
}

.modal-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    width: 100%;
    max-width: 480px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-card h3 {
    margin: 0 0 20px;
    font-size: 20px;
    font-family: 'Poppins', sans-serif;
}

.modal-card .form-group {
    margin-bottom: 16px;
}

.modal-card label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 6px;
    color: var(--gray-700);
}

.modal-card input,
.modal-card textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
}

.modal-card textarea {
    resize: vertical;
    height: 80px;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 20px;
}

/* Table scroll wrapper */
.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Responsive */
@media (max-width: 900px) {
    /* Hide less critical columns to reduce table width */
    .col-handling,
    .col-tip,
    .col-commission,
    .col-invoice {
        display: none;
    }
}

@media (max-width: 768px) {
    /* Page header stacks */
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .page-title {
        font-size: 22px;
    }

    /* Summary cards: 2 columns on tablet */
    .summary-grid {
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 20px;
    }

    .summary-value {
        font-size: 22px;
    }

    /* Filter bar */
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 16px;
    }

    .status-filters {
        justify-content: center;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 14px;
        font-size: 13px;
    }

    /* Action buttons full width */
    .filter-bar > div[style] {
        flex-direction: column;
    }

    .btn,
    .btn-export {
        width: 100%;
        justify-content: center;
    }

    /* Extra filters stack */
    .extra-filters {
        flex-direction: column;
        align-items: stretch;
    }

    .extra-filters input,
    .extra-filters select {
        width: 100%;
    }

    /* Table */
    .earnings-table th,
    .earnings-table td {
        padding: 10px 10px;
        font-size: 13px;
    }

    /* Modal */
    .modal-card {
        max-width: calc(100% - 32px);
        margin: 16px;
        padding: 22px;
    }

    .modal-actions {
        flex-direction: column;
    }

    .modal-actions .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    /* Summary cards: single column on phone */
    .summary-grid {
        grid-template-columns: 1fr;
    }

    /* Hide driver column too — show only essentials */
    .col-base,
    .col-distance {
        display: none;
    }

    .earnings-table th,
    .earnings-table td {
        padding: 8px 8px;
        font-size: 12px;
    }

    .page-title {
        font-size: 20px;
    }
}
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-dollar-sign text-primary mr-2"></i>
            <?= $t['delivery_earnings'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['manage_payouts'] ?></p>
    </div>
    <a href="<?= url('/admin/delivery') ?>" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left mr-2"></i> <?= $t['back'] ?>
    </a>
</div>

<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card">
        <p class="summary-label"><?= $t['total_gross'] ?></p>
        <p class="summary-value gross"><?= currency($summary['total_gross'] ?? 0) ?></p>
    </div>

    <div class="summary-card">
        <p class="summary-label"><?= $t['platform_commission'] ?></p>
        <p class="summary-value commission"><?= currency($summary['total_commission'] ?? 0) ?></p>
    </div>

    <div class="summary-card">
        <p class="summary-label"><?= $t['net_to_drivers'] ?></p>
        <p class="summary-value net"><?= currency($summary['total_net'] ?? 0) ?></p>
    </div>

    <div class="summary-card pending">
        <p class="summary-label"><?= $t['pending_payment'] ?></p>
        <p class="summary-value pending"><?= currency($summary['pending_amount'] ?? 0) ?></p>
    </div>
</div>

<!-- Filters & Actions -->
<div class="filter-bar">
    <div class="status-filters">
        <a href="<?= url('/admin/delivery/earnings?status=all') ?>"
           class="filter-btn all <?= ($selectedStatus ?? 'pending') === 'all' ? 'active' : '' ?>">
            <?= $t['all'] ?>
        </a>
        <a href="<?= url('/admin/delivery/earnings?status=pending') ?>"
           class="filter-btn pending <?= ($selectedStatus ?? 'pending') === 'pending' ? 'active' : '' ?>">
            <?= $t['pending'] ?>
        </a>
        <a href="<?= url('/admin/delivery/earnings?status=paid') ?>"
           class="filter-btn paid <?= ($selectedStatus ?? '') === 'paid' ? 'active' : '' ?>">
            <?= $t['paid'] ?>
        </a>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button onclick="showPaymentModal()" class="btn btn-success">
            <i class="fa-solid fa-check-circle mr-2"></i> <?= $t['mark_paid'] ?>
        </button>
        <button onclick="exportCSV()" class="btn-export">
            <i class="fa-solid fa-download"></i> Export CSV
        </button>
    </div>
</div>

<!-- Extra Filters -->
<div class="filter-bar" style="margin-top:-16px;">
    <div class="extra-filters">
        <label style="font-weight:600;font-size:14px;color:var(--gray-600);">Filters:</label>
        <input type="date" id="dateFrom" placeholder="From" value="<?= htmlspecialchars(get('date_from', '')) ?>">
        <input type="date" id="dateTo" placeholder="To" value="<?= htmlspecialchars(get('date_to', '')) ?>">
        <select id="driverFilter" onchange="applyFilters()">
            <option value="">All Drivers</option>
            <?php
            $driverList = [];
            if (!empty($earnings)) {
                foreach ($earnings as $e) {
                    $did = $e['driver_id'] ?? '';
                    $dname = trim(($e['driver_first_name'] ?? '') . ' ' . ($e['driver_last_name'] ?? ''));
                    if ($did && !isset($driverList[$did])) {
                        $driverList[$did] = $dname;
                    }
                }
            }
            foreach ($driverList as $did => $dname): ?>
                <option value="<?= $did ?>"><?= htmlspecialchars($dname) ?></option>
            <?php endforeach; ?>
        </select>
        <button onclick="applyFilters()" class="btn btn-secondary" style="padding:8px 16px;">Apply</button>
    </div>
</div>

<!-- Earnings Table -->
<div class="earnings-card">
    <div class="table-container">
        <table class="earnings-table">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="selectAll" class="custom-checkbox" onclick="toggleSelectAll(this)">
                    </th>
                    <th><?= $t['order'] ?></th>
                    <th><?= $t['driver'] ?></th>
                    <th class="text-right col-base"><?= $t['base_fee'] ?></th>
                    <th class="text-right col-distance"><?= $t['distance'] ?></th>
                    <th class="text-right col-handling"><?= $t['handling'] ?? 'Handling Fee' ?></th>
                    <th class="text-right col-tip">Tip</th>
                    <th class="text-right"><?= $t['total'] ?></th>
                    <th class="text-right col-commission"><?= $t['commission'] ?></th>
                    <th class="text-right"><?= $t['net'] ?></th>
                    <th class="text-center"><?= $t['status'] ?></th>
                    <th class="text-center"><?= $t['date'] ?></th>
                    <th class="text-center col-invoice">Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($earnings)): ?>
                    <?php foreach ($earnings as $earning): ?>
                    <tr>
                        <td>
                            <?php if (($earning['payment_status'] ?? '') === 'pending'): ?>
                            <input type="checkbox" class="earning-checkbox custom-checkbox" value="<?= $earning['id'] ?>">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($earning['order_number'])): ?>
                                <span class="order-number">#<?= htmlspecialchars($earning['order_number']) ?></span>
                            <?php else: ?>
                                <span class="order-number" style="color:#6b7280;font-size:12px;">Distribution</span><br>
                                <span style="font-size:11px;color:#9ca3af;"><?= htmlspecialchars($earning['notes'] ?? '') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="driver-name">
                                <?= htmlspecialchars(($earning['driver_first_name'] ?? '') . ' ' . ($earning['driver_last_name'] ?? '')) ?>
                            </span>
                        </td>
                        <td class="text-right col-base">
                            <span class="amount"><?= currency($earning['base_fee'] ?? 0) ?></span>
                        </td>
                        <td class="text-right col-distance">
                            <span class="amount"><?= currency($earning['distance_fee'] ?? 0) ?></span>
                        </td>
                        <td class="text-right col-handling">
                            <span class="amount"><?= currency($earning['bonus'] ?? 0) ?></span>
                        </td>
                        <td class="text-right col-tip">
                            <span class="amount"><?= currency($earning['tip'] ?? 0) ?></span>
                        </td>
                        <td class="text-right">
                            <span class="amount"><?= currency($earning['total_earning'] ?? 0) ?></span>
                        </td>
                        <td class="text-right col-commission">
                            <span class="amount commission">-<?= currency($earning['platform_commission'] ?? 0) ?></span>
                        </td>
                        <td class="text-right">
                            <span class="amount net"><?= currency($earning['net_earning'] ?? 0) ?></span>
                        </td>
                        <td class="text-center">
                            <?php if (($earning['payment_status'] ?? '') === 'paid'): ?>
                            <span class="status-badge paid"><?= $t['paid'] ?></span>
                            <?php else: ?>
                            <span class="status-badge pending"><?= $t['pending'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="date-text"><?= date('M d, Y', strtotime($earning['created_at'] ?? 'now')) ?></span>
                        </td>
                        <td class="text-center col-invoice">
                            <?php if (!empty($earning['dist_invoice_number'])): ?>
                                <a href="<?= url('distribution/documents/invoice?id=' . $earning['distribution_request_id']) ?>"
                                   target="_blank"
                                   style="font-size:12px;color:#4f46e5;text-decoration:none;white-space:nowrap;"
                                   title="View distribution invoice">
                                    <i class="fas fa-file-invoice" style="margin-right:4px;"></i><?= htmlspecialchars($earning['dist_invoice_number']) ?>
                                </a>
                            <?php elseif (!empty($earning['order_number'])): ?>
                                <a href="<?= url('admin/orders/view?id=' . $earning['order_id']) ?>"
                                   target="_blank"
                                   style="font-size:12px;color:#4f46e5;text-decoration:none;white-space:nowrap;"
                                   title="View order">
                                    <i class="fas fa-receipt" style="margin-right:4px;"></i>Order #<?= htmlspecialchars($earning['order_number']) ?>
                                </a>
                            <?php else: ?>
                                <span style="color:#d1d5db;font-size:12px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="empty-state">
                            <i class="fa-solid fa-receipt" style="font-size: 48px; color: var(--gray-300); margin-bottom: 16px; display: block;"></i>
                            <?= $t['no_earnings'] ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Reference Modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-card">
        <h3>Mark Earnings as Paid</h3>
        <p style="color:var(--gray-600);margin-bottom:20px;">
            <span id="modalCount">0</span> earnings selected
        </p>
        <div class="form-group">
            <label>Payment Reference (e.g., bank transfer ID)</label>
            <input type="text" id="paymentReference" placeholder="e.g., TRF-20260211-001">
        </div>
        <div class="form-group">
            <label>Notes (optional)</label>
            <textarea id="paymentNotes" placeholder="Any additional notes..."></textarea>
        </div>
        <div class="modal-actions">
            <button onclick="closePaymentModal()" class="btn btn-secondary">Cancel</button>
            <button onclick="confirmMarkPaid()" class="btn btn-success">Confirm Payment</button>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.earning-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function showPaymentModal() {
    const selected = document.querySelectorAll('.earning-checkbox:checked');
    if (selected.length === 0) {
        alert('<?= $t['select_earnings'] ?>');
        return;
    }
    document.getElementById('modalCount').textContent = selected.length;
    document.getElementById('paymentModal').classList.add('active');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.remove('active');
}

async function confirmMarkPaid() {
    const selected = Array.from(document.querySelectorAll('.earning-checkbox:checked')).map(cb => cb.value);
    const reference = document.getElementById('paymentReference').value.trim();
    const notes = document.getElementById('paymentNotes').value.trim();

    try {
        const response = await fetch('<?= url('/admin/delivery/mark-paid') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                earning_ids: selected,
                payment_reference: reference,
                payment_notes: notes
            })
        });

        const data = await response.json();

        if (data.success) {
            closePaymentModal();
            alert('<?= $t['marked_success'] ?>');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    }
}

function exportCSV() {
    const selected = Array.from(document.querySelectorAll('.earning-checkbox:checked')).map(cb => cb.value);
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= url('/admin/delivery/export-earnings') ?>';

    // Add CSRF token
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>';
    csrf.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    form.appendChild(csrf);

    if (selected.length > 0) {
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'earning_ids[]';
            input.value = id;
            form.appendChild(input);
        });
    } else {
        // Export all with current filters
        const status = '<?= htmlspecialchars($selectedStatus ?? '') ?>';
        if (status && status !== 'all') {
            const s = document.createElement('input');
            s.type = 'hidden'; s.name = 'status'; s.value = status;
            form.appendChild(s);
        }
    }

    document.body.appendChild(form);
    form.submit();
    form.remove();
}

function applyFilters() {
    const status = '<?= htmlspecialchars($selectedStatus ?? 'pending') ?>';
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const driver = document.getElementById('driverFilter').value;
    let url = '<?= url('/admin/delivery/earnings') ?>?status=' + status;
    if (dateFrom) url += '&date_from=' + dateFrom;
    if (dateTo) url += '&date_to=' + dateTo;
    if (driver) url += '&driver_id=' + driver;
    window.location.href = url;
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
