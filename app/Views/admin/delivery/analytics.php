<?php
/**
 * OCS Admin Delivery Analytics
 * File: app/Views/admin/delivery/analytics.php
 */

$pageTitle = 'Delivery Analytics';
$currentPage = 'delivery';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'delivery_analytics' => 'Delivery Analytics',
        'performance_metrics' => 'Performance metrics and insights',
        'back' => 'Back',
        'week' => 'Week',
        'month' => 'Month',
        'all_time' => 'All Time',
        'total_deliveries' => 'Total Deliveries',
        'completed' => 'Completed',
        'success_rate' => 'success rate',
        'avg_time' => 'Avg. Time',
        'minutes' => 'minutes',
        'avg_rating' => 'Avg. Rating',
        'out_of' => 'out of',
        'delivery_trends' => 'Delivery Trends',
        'completion_rate' => 'Completion Rate',
        'top_drivers' => 'Top Performing Drivers',
        'rank' => 'Rank',
        'driver' => 'Driver',
        'deliveries' => 'Deliveries',
        'rating' => 'Rating',
        'earnings' => 'Earnings',
        'no_data' => 'No driver data available',
        'failed_cancelled' => 'Failed/Cancelled'
    ],
    'fr' => [
        'delivery_analytics' => 'Analytique des Livraisons',
        'performance_metrics' => 'Métriques de performance et analyses',
        'back' => 'Retour',
        'week' => 'Semaine',
        'month' => 'Mois',
        'all_time' => 'Tout le Temps',
        'total_deliveries' => 'Total des Livraisons',
        'completed' => 'Terminées',
        'success_rate' => 'taux de réussite',
        'avg_time' => 'Temps Moyen',
        'minutes' => 'minutes',
        'avg_rating' => 'Note Moyenne',
        'out_of' => 'sur',
        'delivery_trends' => 'Tendances de Livraison',
        'completion_rate' => 'Taux de Complétion',
        'top_drivers' => 'Meilleurs Livreurs',
        'rank' => 'Rang',
        'driver' => 'Livreur',
        'deliveries' => 'Livraisons',
        'rating' => 'Note',
        'earnings' => 'Gains',
        'no_data' => 'Aucune donnée disponible',
        'failed_cancelled' => 'Échoués/Annulés'
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

.header-actions {
    display: flex;
    align-items: center;
    gap: 16px;
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

/* Period Filter */
.period-filter {
    display: flex;
    gap: 8px;
}

.period-btn {
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all var(--transition-base);
    background: white;
    color: var(--gray-700);
    border: 1px solid var(--border);
}

.period-btn:hover {
    background: var(--gray-100);
}

.period-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* Metrics Grid */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.metric-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 16px;
}

.metric-icon.total { background: #dbeafe; color: #3b82f6; }
.metric-icon.completed { background: #dcfce7; color: #22c55e; }
.metric-icon.time { background: #f3e8ff; color: #a855f7; }
.metric-icon.rating { background: #fef3c7; color: #eab308; }

.metric-label {
    font-size: 14px;
    color: var(--gray-500);
    margin-bottom: 4px;
}

.metric-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
}

.metric-value.completed { color: #22c55e; }
.metric-value.time { color: #a855f7; }
.metric-value.rating { color: #eab308; }

.metric-sub {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
}

/* Charts */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.chart-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
}

.chart-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
}

.chart-container {
    height: 250px;
}

/* Leaderboard Table */
.leaderboard-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.leaderboard-header {
    padding: 24px;
    border-bottom: 1px solid var(--border);
}

.leaderboard-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
}

.leaderboard-table {
    width: 100%;
    border-collapse: collapse;
}

.leaderboard-table thead {
    background: var(--gray-50);
}

.leaderboard-table th {
    padding: 12px 24px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--gray-500);
}

.leaderboard-table td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
}

.leaderboard-table tbody tr:hover {
    background: var(--gray-50);
}

.rank-badge {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rank-badge i {
    font-size: 18px;
}

.rank-badge.gold i { color: #eab308; }
.rank-badge.silver i { color: #9ca3af; }
.rank-badge.bronze i { color: #ea580c; }

.driver-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.driver-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.driver-name {
    font-weight: 600;
    color: var(--dark);
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 4px;
}

.rating-stars i {
    color: #eab308;
}

.earnings-value {
    font-weight: 700;
    color: var(--primary);
}

.empty-row td {
    text-align: center;
    color: var(--gray-500);
    padding: 48px 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }

    .period-filter {
        flex-wrap: wrap;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
}

/* Missing utility classes */
.text-primary   { color: var(--primary); }
.mr-2           { margin-right: 8px; }
.text-center    { text-align: center; }
.text-right     { text-align: right; }
.text-gray-400  { color: var(--gray-400); }
</style>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-chart-line text-primary mr-2"></i>
            <?= $t['delivery_analytics'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['performance_metrics'] ?></p>
    </div>
    <div class="header-actions">
        <div class="period-filter">
            <a href="<?= url('/admin/delivery/analytics?period=week') ?>"
               class="period-btn <?= ($period ?? 'week') === 'week' ? 'active' : '' ?>">
                <?= $t['week'] ?>
            </a>
            <a href="<?= url('/admin/delivery/analytics?period=month') ?>"
               class="period-btn <?= ($period ?? '') === 'month' ? 'active' : '' ?>">
                <?= $t['month'] ?>
            </a>
            <a href="<?= url('/admin/delivery/analytics?period=all') ?>"
               class="period-btn <?= ($period ?? '') === 'all' ? 'active' : '' ?>">
                <?= $t['all_time'] ?>
            </a>
        </div>
        <a href="<?= url('/admin/delivery') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left mr-2"></i> <?= $t['back'] ?>
        </a>
    </div>
</div>

<!-- Metrics Grid -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon total">
            <i class="fa-solid fa-box"></i>
        </div>
        <p class="metric-label"><?= $t['total_deliveries'] ?></p>
        <p class="metric-value"><?= number_format($metrics['total'] ?? 0) ?></p>
    </div>

    <div class="metric-card">
        <div class="metric-icon completed">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <p class="metric-label"><?= $t['completed'] ?></p>
        <p class="metric-value completed"><?= number_format($metrics['completed'] ?? 0) ?></p>
        <p class="metric-sub">
            <?= ($metrics['total'] ?? 0) > 0 ? number_format(($metrics['completed'] / $metrics['total']) * 100, 1) : 0 ?>% <?= $t['success_rate'] ?>
        </p>
    </div>

    <div class="metric-card">
        <div class="metric-icon time">
            <i class="fa-solid fa-clock"></i>
        </div>
        <p class="metric-label"><?= $t['avg_time'] ?></p>
        <p class="metric-value time"><?= number_format($metrics['avg_time'] ?? 0) ?></p>
        <p class="metric-sub"><?= $t['minutes'] ?></p>
    </div>

    <div class="metric-card">
        <div class="metric-icon rating">
            <i class="fa-solid fa-star"></i>
        </div>
        <p class="metric-label"><?= $t['avg_rating'] ?></p>
        <p class="metric-value rating"><?= number_format($metrics['avg_rating'] ?? 0, 1) ?></p>
        <p class="metric-sub"><?= $t['out_of'] ?> 5.0</p>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h2 class="chart-title"><?= $t['delivery_trends'] ?></h2>
        <div class="chart-container">
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h2 class="chart-title"><?= $t['completion_rate'] ?></h2>
        <div class="chart-container">
            <canvas id="completionChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Drivers Leaderboard -->
<div class="leaderboard-card">
    <div class="leaderboard-header">
        <h2 class="leaderboard-title"><?= $t['top_drivers'] ?></h2>
    </div>
    <table class="leaderboard-table">
        <thead>
            <tr>
                <th><?= $t['rank'] ?></th>
                <th><?= $t['driver'] ?></th>
                <th class="text-center"><?= $t['deliveries'] ?></th>
                <th class="text-center"><?= $t['rating'] ?></th>
                <th class="text-right"><?= $t['earnings'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($topDrivers)): ?>
                <?php foreach ($topDrivers as $index => $driver): ?>
                <tr>
                    <td>
                        <?php
                        $rankClasses = ['gold', 'silver', 'bronze'];
                        $rankIcons = ['fa-crown', 'fa-medal', 'fa-medal'];
                        $rankClass = $rankClasses[$index] ?? '';
                        $rankIcon = $rankIcons[$index] ?? 'fa-user';
                        ?>
                        <div class="rank-badge <?= $rankClass ?>">
                            <i class="fas <?= $rankIcon ?>"></i>
                            <span>#<?= $index + 1 ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="driver-info">
                            <div class="driver-avatar">
                                <?= strtoupper(substr($driver['first_name'] ?? '', 0, 1) . substr($driver['last_name'] ?? '', 0, 1)) ?>
                            </div>
                            <span class="driver-name">
                                <?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?>
                            </span>
                        </div>
                    </td>
                    <td class="text-center">
                        <strong><?= number_format($driver['delivery_count'] ?? 0) ?></strong>
                    </td>
                    <td class="text-center">
                        <?php if (!empty($driver['avg_rating'])): ?>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <span><?= number_format($driver['avg_rating'], 1) ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-gray-400">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <span class="earnings-value"><?= currency($driver['total_earnings'] ?? 0) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="empty-row">
                    <td colspan="5"><?= $t['no_data'] ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Prepare trends data
const trendsData = <?= json_encode($trends ?? []) ?>;
const dates = trendsData.map(item => new Date(item.date).toLocaleDateString('<?= $currentLang === 'fr' ? 'fr-CA' : 'en-US' ?>', {month: 'short', day: 'numeric'}));
const totals = trendsData.map(item => parseInt(item.total));
const completed = trendsData.map(item => parseInt(item.completed));

// Delivery Trends Chart
new Chart(document.getElementById('trendsChart'), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: '<?= $t['total_deliveries'] ?>',
            data: totals,
            borderColor: 'rgb(0, 178, 7)',
            backgroundColor: 'rgba(0, 178, 7, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: '<?= $t['completed'] ?>',
            data: completed,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Completion Rate Chart
const metrics = <?= json_encode($metrics ?? ['total' => 0, 'completed' => 0]) ?>;
const total = parseInt(metrics.total || 0);
const completedCount = parseInt(metrics.completed || 0);
const failed = total - completedCount;

new Chart(document.getElementById('completionChart'), {
    type: 'doughnut',
    data: {
        labels: ['<?= $t['completed'] ?>', '<?= $t['failed_cancelled'] ?>'],
        datasets: [{
            data: [completedCount, failed],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
