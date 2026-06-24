<?php
/**
 * Visitor Analytics Dashboard
 * File: app/Views/admin/visitor-analytics/index.php
 */

$pageTitle = 'Visitor Analytics';
$currentPage = 'visitor-analytics';

// Helper function to safely format duration
function formatDuration($seconds) {
    if (!$seconds || $seconds <= 0) return '0:00';
    $seconds = (float)$seconds;
    $minutes = (int)floor((float)$seconds / 60.0);
    $secs = (int)fmod((float)$seconds, 60.0);
    return sprintf('%d:%02d', $minutes, $secs);
}

// Helper function to safely calculate percentage
function safePercent($value, $total, $decimals = 0) {
    if (!$total || $total == 0) return 0;
    return round(((float)$value / (float)$total) * 100, $decimals);
}

// Helper function to safely divide
function safeDivide($numerator, $denominator, $decimals = 1) {
    if (!$denominator || $denominator == 0) return 0;
    return round((float)$numerator / (float)$denominator, $decimals);
}

// Start output buffering
ob_start();
?>

<style>
/* Analytics Dashboard Styles */
.analytics-container {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
}

.page-subtitle {
    color: #6b7280;
    margin-top: 5px;
    font-size: 14px;
}

.period-selector {
    display: flex;
    gap: 10px;
    background: white;
    padding: 5px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.period-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
    color: #4a5568;
    text-decoration: none;
}

.period-btn.active {
    background: #00b207;
    color: white;
}

.period-btn:hover:not(.active) {
    background: #f3f4f6;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.icon-visitors { background: #e0e7ff; }
.icon-pageviews { background: #dbeafe; }
.icon-session { background: #fef3c7; }
.icon-conversion { background: #dcfce7; }

.stat-content h3 {
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 10px;
    line-height: 1;
}

.stat-change {
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #1a202c;
    margin: 40px 0 20px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
}

/* Charts */
.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 1024px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

.chart-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.chart-header {
    margin-bottom: 20px;
}

.chart-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1a202c;
}

.chart-container {
    position: relative;
    height: 300px;
}

/* Tables */
.table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 30px;
}

.table-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
}

.table-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1a202c;
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #f9fafb;
}

th {
    padding: 12px 25px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
}

td {
    padding: 16px 25px;
    border-top: 1px solid #e5e7eb;
    font-size: 14px;
}

tbody tr:hover {
    background: #f9fafb;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.badge-desktop { background: #dbeafe; color: #1e40af; }
.badge-mobile { background: #dcfce7; color: #166534; }
.badge-tablet { background: #fef3c7; color: #854d0e; }

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    font-size: 14px;
}

.btn-secondary {
    background: #e5e7eb;
    color: #4a5568;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state-icon {
    font-size: 48px;
    margin-bottom: 10px;
}
</style>

<div class="analytics-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>📊 Visitor Analytics</h1>
            <p class="page-subtitle">
                <?= date('F j, Y', strtotime($startDate)) ?> - <?= date('F j, Y', strtotime($endDate)) ?>
            </p>
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <div class="period-selector">
                <a href="?period=7" class="period-btn <?= $period == 7 ? 'active' : '' ?>">7 Days</a>
                <a href="?period=30" class="period-btn <?= $period == 30 ? 'active' : '' ?>">30 Days</a>
                <a href="?period=90" class="period-btn <?= $period == 90 ? 'active' : '' ?>">90 Days</a>
                <a href="?period=365" class="period-btn <?= $period == 365 ? 'active' : '' ?>">1 Year</a>
            </div>
            <a href="<?= url('admin/dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Visitor Metrics Section -->
    <h2 class="section-title">👥 Visitor Overview</h2>
    
    <div class="stats-grid">
        <!-- Total Visitors -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-content">
                    <h3>Total Visitors</h3>
                    <div class="stat-value"><?= number_format($visitors['period_visitors'] ?? 0) ?></div>
                    <div class="stat-change">
                        <?= number_format($visitors['new_visitors'] ?? 0) ?> new, 
                        <?= number_format($visitors['returning_visitors'] ?? 0) ?> returning
                    </div>
                </div>
                <div class="stat-icon icon-visitors">👥</div>
            </div>
        </div>

        <!-- Page Views -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-content">
                    <h3>Page Views</h3>
                    <div class="stat-value"><?= number_format($visitors['period_pageviews'] ?? 0) ?></div>
                    <div class="stat-change">
                        <?= safeDivide($visitors['period_pageviews'] ?? 0, $visitors['period_visitors'] ?? 0, 1) ?> pages/visitor
                    </div>
                </div>
                <div class="stat-icon icon-pageviews">📄</div>
            </div>
        </div>

        <!-- Avg Session Duration -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-content">
                    <h3>Avg Session Duration</h3>
                    <div class="stat-value">
                        <?= formatDuration($sessions['avg_duration'] ?? 0) ?>
                    </div>
                    <div class="stat-change">
                        <?= round((float)($sessions['avg_pages_per_session'] ?? 0), 1) ?> pages/session
                    </div>
                </div>
                <div class="stat-icon icon-session">⏱️</div>
            </div>
        </div>

        <!-- New vs Returning -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-content">
                    <h3>New Visitor Rate</h3>
                    <div class="stat-value">
                        <?php 
                        $totalVisitors = ($visitors['new_visitors'] ?? 0) + ($visitors['returning_visitors'] ?? 0);
                        echo safePercent($visitors['new_visitors'] ?? 0, $totalVisitors, 0);
                        ?>%
                    </div>
                    <div class="stat-change">
                        <?= number_format($visitors['new_visitors'] ?? 0) ?> of <?= number_format($totalVisitors) ?> visitors
                    </div>
                </div>
                <div class="stat-icon icon-conversion">🆕</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <!-- Visitor Trend -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>📈 Visitor Trend</h3>
            </div>
            <div class="chart-container">
                <canvas id="visitorTrendChart"></canvas>
            </div>
        </div>

        <!-- Device Breakdown -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>📱 Device Distribution</h3>
            </div>
            <div class="chart-container">
                <canvas id="deviceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Pages -->
    <div class="table-card">
        <div class="table-header">
            <h3>🔥 Most Visited Pages (Top 20)</h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Total Views</th>
                        <th>Unique Visitors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topPages)): ?>
                        <?php foreach ($topPages as $page): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($page['page_title'] ?? 'Unknown') ?></strong><br>
                                    <small style="color: #9ca3af;"><?= htmlspecialchars($page['page_url']) ?></small>
                                </td>
                                <td><?= number_format($page['views']) ?></td>
                                <td><?= number_format($page['unique_visitors']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📊</div>
                                    <h3>No Data Yet</h3>
                                    <p>Page view data will appear here once visitors browse your site</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Traffic Sources & Browser Stats -->
    <div class="charts-grid">
        <!-- Traffic Sources -->
        <div class="chart-card">
            <div class="chart-header">
                <h3>🌐 Traffic Sources</h3>
            </div>
            <div class="chart-container">
                <canvas id="trafficSourcesChart"></canvas>
            </div>
        </div>

        <!-- Browser Stats -->
        <div class="table-card" style="margin: 0;">
            <div class="table-header">
                <h3>🌐 Top Browsers</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Browser</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($browsers)): ?>
                            <?php foreach ($browsers as $browser): ?>
                                <tr>
                                    <td><?= htmlspecialchars($browser['browser']) ?></td>
                                    <td><?= number_format($browser['count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">
                                    <div style="text-align: center; padding: 20px; color: #9ca3af;">
                                        No browser data available
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Operating Systems -->
    <?php if (!empty($operatingSystems)): ?>
    <div class="table-card">
        <div class="table-header">
            <h3>💻 Operating Systems</h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Operating System</th>
                        <th>Visits</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalOSVisits = array_sum(array_column($operatingSystems, 'count'));
                    foreach ($operatingSystems as $os): 
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($os['operating_system']) ?></td>
                            <td><?= number_format($os['count']) ?></td>
                            <td><?= safePercent($os['count'], $totalOSVisits, 1) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Geographic Distribution -->
    <?php if (!empty($geoData)): ?>
    <div class="table-card">
        <div class="table-header">
            <h3>🌍 Geographic Distribution</h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Visitors</th>
                        <th>Page Views</th>
                        <th>Pages/Visitor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($geoData as $geo): ?>
                        <tr>
                            <td><?= htmlspecialchars($geo['location']) ?></td>
                            <td><?= number_format($geo['visitors']) ?></td>
                            <td><?= number_format($geo['pageviews']) ?></td>
                            <td><?= safeDivide($geo['pageviews'], $geo['visitors'], 1) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cities -->
    <?php if (!empty($cityData)): ?>
    <div class="table-card">
        <div class="table-header">
            <h3>🏙️ Top Cities</h3>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Visitors</th>
                        <th>Page Views</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cityData as $city): ?>
                        <tr>
                            <td><?= htmlspecialchars($city['location']) ?></td>
                            <td><?= number_format($city['visitors']) ?></td>
                            <td><?= number_format($city['pageviews']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hourly Traffic Pattern -->
    <?php if (!empty($hourlyTraffic)): ?>
    <div class="chart-card" style="margin-bottom: 30px;">
        <div class="chart-header">
            <h3>⏰ Hourly Traffic Pattern</h3>
        </div>
        <div class="chart-container">
            <canvas id="hourlyTrafficChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart.js defaults
Chart.defaults.font.family = 'Inter, sans-serif';

// Visitor Trend Chart
<?php if (!empty($visitorTrend)): ?>
const visitorTrendCtx = document.getElementById('visitorTrendChart').getContext('2d');
const visitorTrendData = <?= json_encode(array_values(array_map(function($item) {
    return [
        'date' => date('M j', strtotime($item['date'])),
        'visitors' => round((float)$item['visitors']),
        'pageviews' => round((float)$item['pageviews']),
        'new_visitors' => round((float)$item['new_visitors'])
    ];
}, $visitorTrend))) ?>;

new Chart(visitorTrendCtx, {
    type: 'line',
    data: {
        labels: visitorTrendData.map(item => item.date),
        datasets: [
            {
                label: 'Total Visitors',
                data: visitorTrendData.map(item => item.visitors),
                borderColor: '#00b207',
                backgroundColor: 'rgba(0, 178, 7, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6
            },
            {
                label: 'New Visitors',
                data: visitorTrendData.map(item => item.new_visitors),
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>

// Device Chart
<?php if (!empty($devices)): ?>
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceData = <?= json_encode(array_values(array_map(function($item) {
    return [
        'device' => ucfirst($item['device_type']),
        'count' => round((float)$item['count'])
    ];
}, $devices))) ?>;

new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: deviceData.map(item => item.device),
        datasets: [{
            data: deviceData.map(item => item.count),
            backgroundColor: ['#00b207', '#2196F3', '#FF9800', '#9C27B0']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15
                }
            }
        }
    }
});
<?php endif; ?>

// Traffic Sources Chart
<?php if (!empty($referrers)): ?>
const trafficCtx = document.getElementById('trafficSourcesChart').getContext('2d');
const trafficData = <?= json_encode(array_values(array_map(function($item) {
    return [
        'source' => $item['source'],
        'visits' => round((float)$item['visits'])
    ];
}, $referrers))) ?>;

new Chart(trafficCtx, {
    type: 'bar',
    data: {
        labels: trafficData.map(item => item.source),
        datasets: [{
            label: 'Visits',
            data: trafficData.map(item => item.visits),
            backgroundColor: '#00b207'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>

// Hourly Traffic Chart
<?php if (!empty($hourlyTraffic)): ?>
const hourlyCtx = document.getElementById('hourlyTrafficChart').getContext('2d');
const hourlyData = <?= json_encode(array_values(array_map(function($item) {
    return [
        'hour' => sprintf('%02d:00', $item['hour']),
        'visits' => round((float)$item['visits'])
    ];
}, $hourlyTraffic))) ?>;

new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: hourlyData.map(item => item.hour),
        datasets: [{
            label: 'Visits',
            data: hourlyData.map(item => item.visits),
            backgroundColor: '#00b207'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>