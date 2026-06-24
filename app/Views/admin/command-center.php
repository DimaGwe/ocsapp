<?php
/**
 * OCS Command Center — Founder Dashboard
 */

$orderStatusColors = [
    'pending'    => ['bg' => 'rgba(255,107,0,0.15)',  'border' => '#ff6b00', 'text' => '#ff6b00'],
    'processing' => ['bg' => 'rgba(0,212,255,0.12)',  'border' => '#00d4ff', 'text' => '#00d4ff'],
    'shipped'    => ['bg' => 'rgba(0,100,255,0.15)',  'border' => '#0064ff', 'text' => '#6eb4ff'],
    'delivered'  => ['bg' => 'rgba(0,210,110,0.12)',  'border' => '#00d26e', 'text' => '#00d26e'],
    'cancelled'  => ['bg' => 'rgba(255,34,68,0.12)',  'border' => '#ff2244', 'text' => '#ff2244'],
    'refunded'   => ['bg' => 'rgba(150,50,255,0.12)', 'border' => '#9632ff', 'text' => '#9632ff'],
];

$flagColors = [
    'danger'  => ['border' => '#ff2244', 'glow' => 'rgba(255,34,68,0.25)',   'dot' => '#ff2244', 'badge' => 'rgba(255,34,68,0.15)'],
    'warning' => ['border' => '#ff6b00', 'glow' => 'rgba(255,107,0,0.2)',    'dot' => '#ff6b00', 'badge' => 'rgba(255,107,0,0.15)'],
    'info'    => ['border' => '#00d4ff', 'glow' => 'rgba(0,212,255,0.15)',   'dot' => '#00d4ff', 'badge' => 'rgba(0,212,255,0.1)'],
];

$priorityColors = [
    'high'   => ['color' => '#ff2244', 'bg' => 'rgba(255,34,68,0.12)',   'label' => 'HIGH'],
    'medium' => ['color' => '#ff6b00', 'bg' => 'rgba(255,107,0,0.12)',   'label' => 'MED'],
    'low'    => ['color' => '#00d26e', 'bg' => 'rgba(0,210,110,0.1)',    'label' => 'LOW'],
];
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Share+Tech+Mono&display=swap');

/* ── Full-screen override: hide sidebar + topbar ── */
#sidebar,
.sidebar-overlay,
.topbar { display: none !important; }

.main-content {
    margin-left: 0 !important;
    background: #070d1a !important;
    padding: 20px 24px !important;
}

body { background: #070d1a !important; }

.cc-wrap *,
.cc-wrap {
    box-sizing: border-box;
}

/* ── Root Layout ── */
.cc-wrap {
    display: flex;
    flex-direction: column;
    gap: 18px;
    padding: 6px 0 24px;
    color: #c8d8e8;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ── Header Bar ── */
.cc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    background: rgba(0,20,45,0.9);
    border: 1px solid rgba(0,212,255,0.25);
    border-radius: 12px;
    backdrop-filter: blur(8px);
    box-shadow: 0 0 30px rgba(0,212,255,0.08), inset 0 1px 0 rgba(0,212,255,0.1);
}

.cc-title {
    display: flex;
    align-items: center;
    gap: 14px;
}

.cc-logo-ring {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid #00d4ff;
    box-shadow: 0 0 12px rgba(0,212,255,0.5), inset 0 0 8px rgba(0,212,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    animation: ring-pulse 3s ease-in-out infinite;
}

.cc-logo-ring::before {
    content: '';
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0,212,255,0.4) 0%, rgba(0,30,60,0.9) 70%);
    box-shadow: 0 0 8px rgba(0,212,255,0.6);
}

@keyframes ring-pulse {
    0%, 100% { box-shadow: 0 0 12px rgba(0,212,255,0.5), inset 0 0 8px rgba(0,212,255,0.15); }
    50%       { box-shadow: 0 0 22px rgba(0,212,255,0.8), inset 0 0 12px rgba(0,212,255,0.3); }
}

.cc-title-text {
    font-family: 'Orbitron', monospace;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 3px;
    color: #00d4ff;
    text-shadow: 0 0 12px rgba(0,212,255,0.6);
    text-transform: uppercase;
}

.cc-subtitle {
    font-size: 11px;
    letter-spacing: 2px;
    color: rgba(0,212,255,0.5);
    text-transform: uppercase;
    margin-top: 2px;
}

.cc-header-right {
    display: flex;
    align-items: center;
    gap: 24px;
}

.cc-clock {
    text-align: right;
}

.cc-time {
    font-family: 'Share Tech Mono', 'Courier New', monospace;
    font-size: 22px;
    color: #00d4ff;
    text-shadow: 0 0 10px rgba(0,212,255,0.5);
    letter-spacing: 2px;
    line-height: 1;
}

.cc-date {
    font-size: 11px;
    color: rgba(200,216,232,0.5);
    letter-spacing: 1px;
    text-align: right;
    margin-top: 3px;
    text-transform: uppercase;
}

.cc-status-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 20px;
    background: rgba(0,210,110,0.1);
    border: 1px solid rgba(0,210,110,0.3);
    font-size: 12px;
    font-weight: 600;
    color: #00d26e;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.cc-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00d26e;
    box-shadow: 0 0 6px #00d26e;
    animation: dot-blink 2s ease-in-out infinite;
}

@keyframes dot-blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
}

/* ── Stat Cards Row ── */
.cc-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
}

.cc-stat {
    background: rgba(0,20,45,0.85);
    border: 1px solid rgba(0,212,255,0.15);
    border-radius: 12px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.cc-stat:hover {
    border-color: rgba(0,212,255,0.4);
    box-shadow: 0 0 20px rgba(0,212,255,0.1);
}

.cc-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent, #00d4ff), transparent);
    opacity: 0.6;
}

.cc-stat-label {
    font-size: 10px;
    letter-spacing: 2px;
    color: rgba(200,216,232,0.45);
    text-transform: uppercase;
    margin-bottom: 10px;
    font-weight: 600;
}

.cc-stat-value {
    font-family: 'Orbitron', monospace;
    font-size: 28px;
    font-weight: 700;
    color: var(--accent, #00d4ff);
    text-shadow: 0 0 12px var(--accent, rgba(0,212,255,0.5));
    line-height: 1;
}

.cc-stat-sub {
    font-size: 11px;
    color: rgba(200,216,232,0.35);
    margin-top: 6px;
}

/* ── Main Grid ── */
.cc-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 14px;
}

.cc-grid-bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

/* ── Panel ── */
.cc-panel {
    background: rgba(0,20,45,0.85);
    border: 1px solid rgba(0,212,255,0.15);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.cc-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid rgba(0,212,255,0.1);
    background: rgba(0,212,255,0.04);
}

.cc-panel-title {
    font-family: 'Orbitron', monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 2.5px;
    color: rgba(0,212,255,0.8);
    text-transform: uppercase;
}

.cc-panel-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 10px;
    background: rgba(0,212,255,0.12);
    color: #00d4ff;
    border: 1px solid rgba(0,212,255,0.25);
    font-family: 'Share Tech Mono', monospace;
}

.cc-panel-body {
    padding: 16px 18px;
    flex: 1;
    overflow-y: auto;
    max-height: 320px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cc-panel-body::-webkit-scrollbar { width: 4px; }
.cc-panel-body::-webkit-scrollbar-track { background: transparent; }
.cc-panel-body::-webkit-scrollbar-thumb { background: rgba(0,212,255,0.2); border-radius: 2px; }

/* ── Backlog Items ── */
.cc-backlog-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 12px 14px;
    border-radius: 8px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    transition: background 0.2s;
}

.cc-backlog-item:hover {
    background: rgba(0,212,255,0.05);
}

.cc-priority-badge {
    font-family: 'Share Tech Mono', monospace;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 7px;
    border-radius: 4px;
    letter-spacing: 1px;
    flex-shrink: 0;
    margin-top: 1px;
}

.cc-backlog-text { flex: 1; min-width: 0; }

.cc-backlog-title {
    font-size: 13px;
    font-weight: 600;
    color: #e2eaf4;
    line-height: 1.3;
    margin-bottom: 3px;
}

.cc-backlog-detail {
    font-size: 11px;
    color: rgba(200,216,232,0.4);
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cc-empty {
    text-align: center;
    padding: 30px 20px;
    color: rgba(200,216,232,0.25);
    font-size: 13px;
    letter-spacing: 1px;
}

/* ── Journal ── */
.cc-journal-date {
    font-family: 'Orbitron', monospace;
    font-size: 11px;
    color: rgba(0,212,255,0.6);
    letter-spacing: 2px;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.cc-journal-body {
    font-size: 12px;
    line-height: 1.7;
    color: rgba(200,216,232,0.65);
    white-space: pre-wrap;
    word-break: break-word;
}

/* ── System Flags ── */
.cc-flag {
    padding: 12px 14px;
    border-radius: 8px;
    border-left: 3px solid;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    position: relative;
}

.cc-flag-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 4px;
    animation: dot-blink 2s ease-in-out infinite;
}

.cc-flag-content { flex: 1; min-width: 0; }

.cc-flag-title {
    font-size: 13px;
    font-weight: 700;
    color: #e2eaf4;
    margin-bottom: 3px;
}

.cc-flag-detail {
    font-size: 11px;
    color: rgba(200,216,232,0.45);
    line-height: 1.4;
}

.cc-flag-action {
    font-size: 10px;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    align-self: center;
    white-space: nowrap;
    flex-shrink: 0;
    cursor: default;
}

/* ── Orders Table ── */
.cc-order-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.cc-order-row:last-child { border-bottom: none; }

.cc-order-num {
    font-family: 'Share Tech Mono', monospace;
    font-size: 11px;
    color: rgba(0,212,255,0.7);
    flex-shrink: 0;
    width: 80px;
}

.cc-order-customer {
    flex: 1;
    font-size: 12px;
    color: rgba(200,216,232,0.7);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cc-order-status {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 1px solid;
    flex-shrink: 0;
}

.cc-order-amount {
    font-family: 'Share Tech Mono', monospace;
    font-size: 12px;
    color: #00d26e;
    flex-shrink: 0;
    width: 70px;
    text-align: right;
}

/* ── Scanline effect overlay ── */
.cc-scanlines {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    pointer-events: none;
    z-index: 9999;
    background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 2px,
        rgba(0,0,0,0.03) 2px,
        rgba(0,0,0,0.03) 4px
    );
    opacity: 0.4;
}

/* ── Corner decorations ── */
.cc-corner {
    position: absolute;
    width: 12px;
    height: 12px;
    border-color: rgba(0,212,255,0.4);
    border-style: solid;
}
.cc-corner-tl { top: 0; left: 0;  border-width: 2px 0 0 2px; }
.cc-corner-tr { top: 0; right: 0; border-width: 2px 2px 0 0; }
.cc-corner-bl { bottom: 0; left: 0;  border-width: 0 0 2px 2px; }
.cc-corner-br { bottom: 0; right: 0; border-width: 0 2px 2px 0; }

@media (max-width: 1100px) {
    .cc-stats { grid-template-columns: repeat(3, 1fr); }
    .cc-grid, .cc-grid-bottom { grid-template-columns: 1fr; }
}
</style>

<div class="cc-scanlines"></div>

<div class="cc-wrap">

    <!-- ── HEADER ── -->
    <div class="cc-header">
        <div class="cc-title">
            <div class="cc-logo-ring"></div>
            <div>
                <div class="cc-title-text">OCSAPP Command Center</div>
                <div class="cc-subtitle">Founder Operations Dashboard</div>
            </div>
        </div>
        <div class="cc-header-right">
            <div class="cc-status-pill">
                <div class="cc-status-dot"></div>
                System Online
            </div>
            <div class="cc-clock">
                <div class="cc-time" id="cc-clock">--:--:--</div>
                <div class="cc-date" id="cc-date"></div>
            </div>
        </div>
    </div>

    <!-- ── STATS ROW ── -->
    <div class="cc-stats">
        <div class="cc-stat" style="--accent: #00d4ff;">
            <div class="cc-stat-label">Active Users</div>
            <div class="cc-stat-value"><?= number_format($stats['total_users']) ?></div>
            <div class="cc-stat-sub">registered &amp; active</div>
        </div>
        <div class="cc-stat" style="--accent: #6eb4ff;">
            <div class="cc-stat-label">Orders Today</div>
            <div class="cc-stat-value"><?= number_format($stats['orders_today']) ?></div>
            <div class="cc-stat-sub"><?= date('M j, Y') ?></div>
        </div>
        <div class="cc-stat" style="--accent: #00d26e;">
            <div class="cc-stat-label">Revenue — <?= date('M Y') ?></div>
            <div class="cc-stat-value">$<?= number_format($stats['revenue_month'], 0) ?></div>
            <div class="cc-stat-sub">excl. cancelled &amp; refunded</div>
        </div>
        <div class="cc-stat" style="--accent: <?= $stats['pending_orders'] > 0 ? '#ff6b00' : '#00d26e' ?>;">
            <div class="cc-stat-label">Pending Orders</div>
            <div class="cc-stat-value"><?= number_format($stats['pending_orders']) ?></div>
            <div class="cc-stat-sub">pending + processing</div>
        </div>
        <div class="cc-stat" style="--accent: #9b72ff;">
            <div class="cc-stat-label">Active Sellers</div>
            <div class="cc-stat-value"><?= number_format($stats['active_sellers']) ?></div>
            <div class="cc-stat-sub">verified &amp; approved</div>
        </div>
    </div>

    <!-- ── MAIN GRID: Backlog + Journal ── -->
    <div class="cc-grid">

        <!-- BACKLOG -->
        <div class="cc-panel" style="position:relative;">
            <div class="cc-corner cc-corner-tl"></div>
            <div class="cc-corner cc-corner-tr"></div>
            <div class="cc-corner cc-corner-bl"></div>
            <div class="cc-corner cc-corner-br"></div>
            <div class="cc-panel-head">
                <div class="cc-panel-title">⚡ Ops Backlog</div>
                <div class="cc-panel-badge"><?= count($backlog) ?> OPEN</div>
            </div>
            <div class="cc-panel-body">
                <?php if (empty($backlog)): ?>
                    <div class="cc-empty">— No open items —</div>
                <?php else: ?>
                    <?php foreach ($backlog as $item): ?>
                        <?php $pc = $priorityColors[$item['priority']]; ?>
                        <div class="cc-backlog-item">
                            <div class="cc-priority-badge" style="background:<?= $pc['bg'] ?>; color:<?= $pc['color'] ?>; border:1px solid <?= $pc['color'] ?>33;">
                                <?= $pc['label'] ?>
                            </div>
                            <div class="cc-backlog-text">
                                <div class="cc-backlog-title"><?= htmlspecialchars($item['title']) ?></div>
                                <?php if ($item['detail']): ?>
                                    <div class="cc-backlog-detail" title="<?= htmlspecialchars($item['detail']) ?>">
                                        <?= htmlspecialchars($item['detail']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- LATEST JOURNAL -->
        <div class="cc-panel">
            <div class="cc-panel-head">
                <div class="cc-panel-title">📋 Latest Log</div>
                <?php if ($journalEntry['date']): ?>
                    <div class="cc-panel-badge"><?= htmlspecialchars($journalEntry['date']) ?></div>
                <?php endif; ?>
            </div>
            <div class="cc-panel-body">
                <?php if ($journalEntry['body']): ?>
                    <?php if ($journalEntry['date']): ?>
                        <div class="cc-journal-date">// <?= htmlspecialchars($journalEntry['date']) ?></div>
                    <?php endif; ?>
                    <div class="cc-journal-body"><?= htmlspecialchars($journalEntry['body']) ?></div>
                <?php else: ?>
                    <div class="cc-empty">— No journal entry yet this month —</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ── BOTTOM GRID: System Flags + Recent Orders ── -->
    <div class="cc-grid-bottom">

        <!-- SYSTEM FLAGS -->
        <div class="cc-panel">
            <div class="cc-panel-head">
                <div class="cc-panel-title">🛡 System Flags</div>
                <div class="cc-panel-badge" style="<?= count($systemFlags) > 0 ? 'background:rgba(255,34,68,0.15); color:#ff2244; border-color:rgba(255,34,68,0.3);' : '' ?>">
                    <?= count($systemFlags) ?> FLAG<?= count($systemFlags) !== 1 ? 'S' : '' ?>
                </div>
            </div>
            <div class="cc-panel-body">
                <?php if (empty($systemFlags)): ?>
                    <div class="cc-empty">— All systems nominal —</div>
                <?php else: ?>
                    <?php foreach ($systemFlags as $flag): ?>
                        <?php $fc = $flagColors[$flag['level']] ?? $flagColors['info']; ?>
                        <div class="cc-flag" style="border-left-color:<?= $fc['border'] ?>; background:<?= $fc['glow'] ?>;">
                            <div class="cc-flag-dot" style="background:<?= $fc['dot'] ?>; box-shadow:0 0 6px <?= $fc['dot'] ?>;"></div>
                            <div class="cc-flag-content">
                                <div class="cc-flag-title"><?= htmlspecialchars($flag['title']) ?></div>
                                <div class="cc-flag-detail"><?= htmlspecialchars($flag['detail']) ?></div>
                            </div>
                            <?php if (!empty($flag['action'])): ?>
                                <?php if (str_starts_with($flag['action'], '/')): ?>
                                    <a href="<?= htmlspecialchars($flag['action']) ?>" class="cc-flag-action"
                                       style="background:<?= $fc['badge'] ?>; color:<?= $fc['border'] ?>; border:1px solid <?= $fc['border'] ?>44;">
                                        View →
                                    </a>
                                <?php else: ?>
                                    <span class="cc-flag-action"
                                          style="background:<?= $fc['badge'] ?>; color:<?= $fc['border'] ?>; border:1px solid <?= $fc['border'] ?>44;">
                                        <?= htmlspecialchars($flag['action']) ?>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- RECENT ORDERS -->
        <div class="cc-panel">
            <div class="cc-panel-head">
                <div class="cc-panel-title">📦 Recent Orders</div>
                <a href="/admin/orders" style="font-size:10px; color:rgba(0,212,255,0.5); letter-spacing:1px; text-decoration:none; text-transform:uppercase;">
                    View All →
                </a>
            </div>
            <div class="cc-panel-body">
                <?php if (empty($recentOrders)): ?>
                    <div class="cc-empty">— No orders yet —</div>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <?php
                            $sc = $orderStatusColors[$order['status']] ?? $orderStatusColors['pending'];
                            $customer = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''));
                            if (!$customer) $customer = 'Guest';
                        ?>
                        <div class="cc-order-row">
                            <div class="cc-order-num">#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></div>
                            <div class="cc-order-customer"><?= htmlspecialchars($customer) ?></div>
                            <div class="cc-order-status" style="background:<?= $sc['bg'] ?>; color:<?= $sc['text'] ?>; border-color:<?= $sc['border'] ?>44;">
                                <?= htmlspecialchars($order['status']) ?>
                            </div>
                            <div class="cc-order-amount">$<?= number_format((float)$order['total_amount'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div><!-- .cc-wrap -->

<script>
(function () {
    const days  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    function tick() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        document.getElementById('cc-clock').textContent = h + ':' + m + ':' + s;
        document.getElementById('cc-date').textContent =
            days[now.getDay()].toUpperCase() + ' · ' +
            months[now.getMonth()].toUpperCase() + ' ' + now.getDate() + ', ' + now.getFullYear();
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
