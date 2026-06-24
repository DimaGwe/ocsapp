<?php
/**
 * OCS Admin Live Delivery Map — Google Maps
 * File: app/Views/admin/delivery/live-map.php
 */

$pageTitle   = 'Live Delivery Map';
$currentPage = 'live-map';
$currentLang = $_SESSION['language'] ?? 'fr';
$gmapsKey    = $gmapsKey ?? '';

$t = [
    'en' => [
        'title'             => 'Live Driver Map',
        'subtitle'          => 'Real-time GPS tracking — updates every 10s',
        'label_online'      => 'Online',
        'label_available'   => 'Available',
        'label_on_delivery' => 'On Delivery',
        'label_po_pickup'   => 'PO Pickup',
        'label_updated'     => 'Updated',
        'legend_available'  => 'Available',
        'legend_delivery'   => 'On Delivery',
        'legend_pickup'     => 'PO Pickup',
        'legend_path'       => 'Path taken',
        'legend_planned'    => 'Planned route',
        'sidebar_title'     => 'Active Drivers',
        'sidebar_hint'      => 'Click a driver to centre map',
        'search_placeholder'=> 'Search driver...',
        'no_drivers'        => 'No active drivers online',
        'no_gps_title'      => 'Drivers without GPS',
        'no_gps_msg'        => "driver(s) are online but haven't sent a GPS location yet. They won't appear on the map.",
        'gps_live'          => 'Live',
        'gps_ago'           => 'ago',
        'no_gps'            => 'No GPS',
        'on_delivery'       => 'delivery',
        'on_deliveries'     => 'deliveries',
        'status_available'  => 'Available',
        'status_busy'       => 'Busy',
        'status_pickup'     => 'Pickup',
        'status_po_pickup'  => 'PO Pickup',
        'loading'           => 'Loading drivers...',
    ],
    'fr' => [
        'title'             => 'Carte en Direct',
        'subtitle'          => 'Suivi GPS en temps réel — mise à jour toutes les 10s',
        'label_online'      => 'En ligne',
        'label_available'   => 'Disponibles',
        'label_on_delivery' => 'En livraison',
        'label_po_pickup'   => 'Collecte BC',
        'label_updated'     => 'Mis à jour',
        'legend_available'  => 'Disponible',
        'legend_delivery'   => 'En livraison',
        'legend_pickup'     => 'Collecte BC',
        'legend_path'       => 'Trajet effectué',
        'legend_planned'    => 'Itinéraire prévu',
        'sidebar_title'     => 'Livreurs actifs',
        'sidebar_hint'      => 'Cliquer pour centrer la carte',
        'search_placeholder'=> 'Rechercher un livreur...',
        'no_drivers'        => 'Aucun livreur actif en ligne',
        'no_gps_title'      => 'Livreurs sans GPS',
        'no_gps_msg'        => "livreur(s) sont en ligne mais n'ont pas encore envoyé de position GPS. Ils n'apparaîtront pas sur la carte.",
        'gps_live'          => 'En direct',
        'gps_ago'           => 'il y a',
        'no_gps'            => 'Pas de GPS',
        'on_delivery'       => 'livraison',
        'on_deliveries'     => 'livraisons',
        'status_available'  => 'Disponible',
        'status_busy'       => 'Occupé',
        'status_pickup'     => 'Collecte',
        'status_po_pickup'  => 'Collecte BC',
        'loading'           => 'Chargement...',
    ],
];
$tr = $t[$currentLang] ?? $t['en'];

ob_start();
?>

<style>
.map-page-container {
    position: relative;
    height: calc(100vh - 64px - 64px);
    min-height: 600px;
    background: var(--gray-100);
    margin: 0 -32px -32px -32px;
}

/* Ensure admin topbar/sidebar stay above map */
.topbar  { z-index: 1000 !important; }
.sidebar { z-index: 999  !important; }

/* Stats Header */
.map-stats-header {
    position: absolute;
    top: 0; left: 0; right: 0;
    background: white;
    border-bottom: 2px solid var(--border);
    padding: 14px 24px;
    z-index: 400;
    box-shadow: var(--shadow-sm);
}
.stats-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.stats-header-title h1 { font-size: 18px; font-weight: 700; color: var(--dark); margin: 0; font-family:'Poppins',sans-serif; }
.stats-header-title p  { font-size: 12px; color: var(--gray-500); margin: 2px 0 0; }

.map-stats-grid { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.map-stat-item  { display: flex; align-items: center; gap: 8px; }
.map-stat-icon  { width: 30px; height: 30px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 13px; }
.map-stat-icon.online    { background: #dbeafe; color: #1d4ed8; }
.map-stat-icon.available { background: #dcfce7; color: #166534; }
.map-stat-icon.busy      { background: #ffedd5; color: #9a3412; }
.map-stat-icon.pickup    { background: #f5f3ff; color: #6d28d9; }
.map-stat-icon.refresh   { background: var(--gray-100); color: var(--gray-600); }
.map-stat-label { font-size: 10px; color: var(--gray-500); text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }
.map-stat-value { font-size: 17px; font-weight: 700; color: var(--dark); font-family:'Poppins',sans-serif; }
.map-stat-value.online    { color: #1d4ed8; }
.map-stat-value.available { color: #166534; }
.map-stat-value.busy      { color: #9a3412; }
.map-stat-value.pickup    { color: #6d28d9; }
.refresh-time { font-size: 12px; color: var(--gray-600); }

.map-legend { display: flex; align-items: center; gap: 16px; font-size: 12px; color: var(--gray-600); }
.legend-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 1px rgba(0,0,0,.2); margin-right: 4px; vertical-align: middle; }

/* Map */
.map-container-wrapper { position: absolute; top: 80px; left: 0; right: 0; bottom: 0; }
#liveMap { width: 100%; height: 100%; }

/* Sidebar */
.map-sidebar {
    position: absolute;
    top: 16px; left: 16px; bottom: 16px;
    width: 300px;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    z-index: 500;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform .3s ease;
}
.map-sidebar.collapsed { transform: translateX(-316px); }
.sidebar-toggle-btn {
    position: absolute; right: -38px; top: 16px;
    width: 34px; height: 34px;
    background: white; border: none;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    box-shadow: 2px 2px 8px rgba(0,0,0,.1);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--gray-600); transition: all .2s;
}
.sidebar-toggle-btn:hover { background: var(--primary); color: white; }
.sidebar-header { padding: 16px; border-bottom: 1px solid var(--border); background: var(--gray-50); }
.sidebar-header h2 { font-size: 14px; font-weight: 700; color: var(--dark); margin: 0 0 2px; font-family:'Poppins',sans-serif; }
.sidebar-header p  { font-size: 11px; color: var(--gray-500); margin: 0; }
.sidebar-search { padding: 10px 16px; border-bottom: 1px solid var(--border); }
.sidebar-search input { width: 100%; padding: 6px 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 13px; outline: none; box-sizing: border-box; }
.sidebar-search input:focus { border-color: var(--primary); }
.sidebar-content { flex: 1; overflow-y: auto; padding: 12px; }
.sidebar-content::-webkit-scrollbar { width: 5px; }
.sidebar-content::-webkit-scrollbar-track { background: var(--gray-100); }
.sidebar-content::-webkit-scrollbar-thumb { background-color: var(--gray-400); border-radius: 3px; }

/* Driver list */
.driver-list-item { background: white; border: 2px solid var(--border); border-radius: var(--radius-md); padding: 10px 12px; margin-bottom: 10px; cursor: pointer; transition: all .2s; }
.driver-list-item:hover        { border-color: var(--primary); box-shadow: var(--shadow-sm); }
.driver-list-item.active       { border-color: var(--primary); background: rgba(0,178,7,.04); }
.driver-list-item.pickup-active{ border-color: #7c3aed; background: #faf5ff; }
.driver-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.driver-name   { font-size: 13px; font-weight: 600; color: var(--dark); font-family:'Poppins',sans-serif; }
.driver-status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: var(--radius-full); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; }
.driver-status-badge.available { background: #dcfce7; color: #166534; }
.driver-status-badge.busy      { background: #ffedd5; color: #9a3412; }
.driver-status-badge.pickup    { background: #f5f3ff; color: #6d28d9; }
.driver-status-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; animation: blink 2s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }
.driver-meta { display: flex; align-items: center; gap: 10px; font-size: 11px; color: var(--gray-600); flex-wrap: wrap; }
.driver-tag { display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; }
.driver-tag.delivery { background: #e0f2fe; color: #0369a1; }
.driver-tag.pickup   { background: #f5f3ff; color: #6d28d9; }
.driver-tag.gps-ok   { background: #dcfce7; color: #166534; }
.driver-tag.gps-old  { background: #fef3c7; color: #92400e; }
.sidebar-empty, .sidebar-loading { text-align: center; padding: 40px 20px; color: var(--gray-500); }
.sidebar-empty i, .sidebar-loading i { font-size: 36px; color: var(--gray-300); margin-bottom: 12px; display: block; }
@keyframes spin { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }
.sidebar-loading i { color: var(--primary); animation: spin 1s linear infinite; }

/* Google Maps info window reset */
.gm-style .driver-popup { min-width: 220px; font-family:'Poppins',sans-serif; font-size: 13px; }
.gm-style .popup-header { background: var(--gray-50); padding: 10px 14px; border-bottom: 1px solid var(--border); }
.gm-style .popup-header h3 { font-size: 14px; font-weight: 700; color: var(--dark); margin: 0 0 4px; }
.gm-style .popup-body  { padding: 10px 14px; }
.gm-style .popup-info-row { display: flex; align-items: flex-start; gap: 7px; margin-bottom: 7px; font-size: 12px; color: var(--gray-700); }
.gm-style .popup-po-banner { background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 6px; padding: 7px 10px; margin-top: 8px; font-size: 11px; }
.gm-style .popup-po-banner strong { display: block; color: #6d28d9; font-size: 12px; margin-bottom: 2px; }

/* No GPS note */
.no-gps-note { position: absolute; bottom: 16px; right: 16px; z-index: 500; background: white; border-radius: 8px; padding: 10px 14px; box-shadow: var(--shadow-md); font-size: 12px; color: var(--gray-600); max-width: 240px; }
.no-gps-note strong { display: block; color: var(--dark); margin-bottom: 4px; font-size: 13px; }

/* Responsive */
@media (max-width: 768px) {
    .map-page-container { height: calc(100vh - 64px); margin: -20px; }
    .map-stats-header   { padding: 10px 14px; }
    .stats-header-content { flex-direction: column; align-items: flex-start; }
    .map-stats-grid     { width: 100%; justify-content: space-between; gap: 8px; }
    .map-container-wrapper { top: 130px; }
    .map-sidebar { position: fixed; top: auto; bottom: 0; left: 0; right: 0; width: 100%; height: 40vh; max-height: 380px; border-radius: var(--radius-lg) var(--radius-lg) 0 0; }
    .map-sidebar.collapsed { transform: translateY(calc(100% - 48px)); }
    .sidebar-toggle-btn { top: -38px; right: 14px; left: auto; border-radius: var(--radius-md) var(--radius-md) 0 0; }
    .map-legend { display: none; }
}
</style>

<div class="map-page-container">
    <!-- Stats Header -->
    <div class="map-stats-header">
        <div class="stats-header-content">
            <div class="stats-header-title">
                <h1><i class="fa-solid fa-map-location-dot" style="color:var(--primary);"></i> <?= $tr['title'] ?></h1>
                <p><?= $tr['subtitle'] ?></p>
            </div>
            <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                <div class="map-stats-grid">
                    <div class="map-stat-item">
                        <div class="map-stat-icon online"><i class="fa-solid fa-users"></i></div>
                        <div><div class="map-stat-label"><?= $tr['label_online'] ?></div><div class="map-stat-value online" id="totalOnline">0</div></div>
                    </div>
                    <div class="map-stat-item">
                        <div class="map-stat-icon available"><i class="fa-solid fa-circle-check"></i></div>
                        <div><div class="map-stat-label"><?= $tr['label_available'] ?></div><div class="map-stat-value available" id="availableCount">0</div></div>
                    </div>
                    <div class="map-stat-item">
                        <div class="map-stat-icon busy"><i class="fa-solid fa-truck"></i></div>
                        <div><div class="map-stat-label"><?= $tr['label_on_delivery'] ?></div><div class="map-stat-value busy" id="busyCount">0</div></div>
                    </div>
                    <div class="map-stat-item">
                        <div class="map-stat-icon pickup"><i class="fa-solid fa-box-open"></i></div>
                        <div><div class="map-stat-label"><?= $tr['label_po_pickup'] ?></div><div class="map-stat-value pickup" id="pickupCount">0</div></div>
                    </div>
                    <div class="map-stat-item">
                        <div class="map-stat-icon refresh"><i class="fa-solid fa-clock"></i></div>
                        <div><div class="map-stat-label"><?= $tr['label_updated'] ?></div><div class="refresh-time" id="lastRefresh">--:--</div></div>
                    </div>
                </div>
                <div class="map-legend">
                    <span><span class="legend-dot" style="background:#22c55e;"></span> <?= $tr['legend_available'] ?></span>
                    <span><span class="legend-dot" style="background:#f97316;"></span> <?= $tr['legend_delivery'] ?></span>
                    <span><span class="legend-dot" style="background:#7c3aed;"></span> <?= $tr['legend_pickup'] ?></span>
                    <span style="border-left:1px solid var(--border);margin-left:4px;padding-left:12px;">
                        <span style="display:inline-block;width:20px;height:3px;background:#3b82f6;vertical-align:middle;margin-right:4px;border-radius:2px;"></span><?= $tr['legend_path'] ?>
                    </span>
                    <span>
                        <span style="display:inline-block;width:20px;height:3px;background:#22c55e;vertical-align:middle;margin-right:4px;border-radius:2px;border-top:3px dashed #22c55e;"></span><?= $tr['legend_planned'] ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="map-sidebar" id="mapSidebar">
        <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-chevron-left" id="sidebarToggleIcon"></i>
        </button>
        <div class="sidebar-header">
            <h2><?= $tr['sidebar_title'] ?></h2>
            <p><?= $tr['sidebar_hint'] ?></p>
        </div>
        <div class="sidebar-search">
            <input type="text" id="driverSearch" placeholder="<?= htmlspecialchars($tr['search_placeholder']) ?>" oninput="filterDrivers(this.value)">
        </div>
        <div class="sidebar-content" id="driversList">
            <div class="sidebar-loading"><i class="fa-solid fa-spinner"></i><p><?= $tr['loading'] ?></p></div>
        </div>
    </div>

    <!-- Map -->
    <div class="map-container-wrapper">
        <div id="liveMap"></div>
    </div>

    <!-- No GPS note -->
    <div class="no-gps-note" id="noGpsNote" style="display:none;">
        <strong><i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;"></i> <?= $tr['no_gps_title'] ?></strong>
        <span id="noGpsCount">0</span> <?= $tr['no_gps_msg'] ?>
    </div>
</div>

<!-- Google Maps JS API (includes geometry library for polyline decoding) -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($gmapsKey) ?>&libraries=geometry&callback=initMap" async defer></script>

<script>
const MAP_CENTER     = { lat: 45.5017, lng: -73.5673 }; // Montreal
const MAP_ZOOM       = 11;
const REFRESH_MS     = 10000;
const API_ENDPOINT   = '<?= url('api/admin/delivery/active-drivers') ?>';
const ROUTE_ENDPOINT = '<?= url('admin/api/delivery/driver-route') ?>';
const TRACK_BASE     = '<?= url('track') ?>';
const PO_VIEW_BASE   = '<?= url('admin/purchase-orders/view') ?>';

const TXT = {
    no_drivers   : <?= json_encode($tr['no_drivers']) ?>,
    gps_live     : <?= json_encode($tr['gps_live']) ?>,
    gps_ago      : <?= json_encode($tr['gps_ago']) ?>,
    no_gps       : <?= json_encode($tr['no_gps']) ?>,
    on_delivery  : <?= json_encode($tr['on_delivery']) ?>,
    on_deliveries: <?= json_encode($tr['on_deliveries']) ?>,
    available    : <?= json_encode($tr['status_available']) ?>,
    busy         : <?= json_encode($tr['status_busy']) ?>,
    pickup       : <?= json_encode($tr['status_pickup']) ?>,
    po_pickup    : <?= json_encode($tr['status_po_pickup']) ?>,
    loading      : <?= json_encode($tr['loading']) ?>,
};

let map, infoWindow, markers = {}, allDrivers = [], selectedDriverId = null, refreshTimer;
let shouldFitBounds = false;

// Route overlays
let routeLayers = { breadcrumb: null, planned: null, merchant: null, customer: null, supplier: null, depot: null };

function clearRouteLayers() {
    Object.values(routeLayers).forEach(l => { if (l) l.setMap(null); });
    routeLayers = { breadcrumb: null, planned: null, merchant: null, customer: null, supplier: null, depot: null };
}

// ── Init ──────────────────────────────────────────────────────────────────────
function initMap() {
    map = new google.maps.Map(document.getElementById('liveMap'), {
        center: MAP_CENTER,
        zoom: MAP_ZOOM,
        mapTypeControl: true,
        mapTypeControlOptions: { position: google.maps.ControlPosition.TOP_RIGHT },
        streetViewControl: false,
        fullscreenControl: true,
        fullscreenControlOptions: { position: google.maps.ControlPosition.RIGHT_BOTTOM },
        zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_CENTER },
    });
    infoWindow = new google.maps.InfoWindow({ maxWidth: 300 });

    fetchDrivers();
    refreshTimer = setInterval(fetchDrivers, REFRESH_MS);
}

// ── Fetch ─────────────────────────────────────────────────────────────────────
async function fetchDrivers() {
    try {
        const res  = await fetch(API_ENDPOINT);
        const data = await res.json();
        if (Array.isArray(data.drivers)) {
            allDrivers = data.drivers;
            render(allDrivers);
            document.getElementById('lastRefresh').textContent = new Date().toLocaleTimeString();
        }
    } catch(e) { console.error('Fetch error:', e); }
}

// ── Render ────────────────────────────────────────────────────────────────────
function render(drivers) {
    updateMap(drivers);
    updateSidebar(drivers);
    updateStats(drivers);
    if (selectedDriverId) { clearRouteLayers(); fetchAndDrawRoute(selectedDriverId); }

    const noGps = drivers.filter(d => !d.lat || !d.lng);
    const noteEl = document.getElementById('noGpsNote');
    noteEl.style.display = noGps.length > 0 ? 'block' : 'none';
    if (noGps.length) document.getElementById('noGpsCount').textContent = noGps.length;
}

// ── Map markers ───────────────────────────────────────────────────────────────
function markerColor(d) {
    if (d.active_po_id)           return '#7c3aed';
    if (d.active_deliveries > 0)  return '#f97316';
    return '#22c55e';
}

function driverIcon(color) {
    return {
        path: google.maps.SymbolPath.CIRCLE,
        fillColor: color,
        fillOpacity: 0.92,
        strokeColor: 'white',
        strokeWeight: 2.5,
        scale: 10,
    };
}

function updateMap(drivers) {
    const current = new Set();

    drivers.forEach(driver => {
        if (!driver.lat || !driver.lng) return;
        const id    = `driver-${driver.driver_id}`;
        const color = markerColor(driver);
        const pos   = { lat: parseFloat(driver.lat), lng: parseFloat(driver.lng) };
        current.add(id);

        if (markers[id]) {
            markers[id].setPosition(pos);
            markers[id].setIcon(driverIcon(color));
        } else {
            const m = new google.maps.Marker({
                position: pos,
                map: map,
                icon: driverIcon(color),
                title: driver.name,
                optimized: false,
            });
            m.addListener('click', () => {
                selectedDriverId = driver.driver_id;
                shouldFitBounds  = true;
                updateSelection();
                infoWindow.setContent(buildPopup(driver));
                infoWindow.open(map, m);
                clearRouteLayers();
                fetchAndDrawRoute(driver.driver_id);
            });
            markers[id] = m;
        }

        // Update popup content if info window is open on this marker
        if (selectedDriverId === driver.driver_id && infoWindow.getMap()) {
            infoWindow.setContent(buildPopup(driver));
        }
    });

    // Remove stale markers
    Object.keys(markers).forEach(id => {
        if (!current.has(id)) { markers[id].setMap(null); delete markers[id]; }
    });
}

// ── Info Window Popup ─────────────────────────────────────────────────────────
function buildPopup(d) {
    const statusLabel = d.active_po_id
        ? `<span class="driver-status-badge pickup"><span class="driver-status-dot"></span>${TXT.po_pickup}</span>`
        : d.active_deliveries > 0
            ? `<span class="driver-status-badge busy"><span class="driver-status-dot"></span>${TXT.busy}</span>`
            : `<span class="driver-status-badge available"><span class="driver-status-dot"></span>${TXT.available}</span>`;

    let html = `<div class="driver-popup">
        <div class="popup-header"><h3>${esc(d.name)}</h3>${statusLabel}</div>
        <div class="popup-body">
            <div class="popup-info-row"><i class="fa-solid fa-phone" style="width:14px;color:#9ca3af;font-size:11px;margin-top:1px;"></i><span>${esc(d.phone || 'N/A')}</span></div>
            ${d.zone ? `<div class="popup-info-row"><i class="fa-solid fa-map-pin" style="width:14px;color:#9ca3af;font-size:11px;margin-top:1px;"></i><span>${esc(d.zone)}</span></div>` : ''}`;

    if (d.active_deliveries > 0 && d.active_tracking_code) {
        html += `<div class="popup-info-row"><i class="fa-solid fa-box" style="width:14px;color:#9ca3af;font-size:11px;margin-top:1px;"></i>
            <span>${d.active_deliveries} ${d.active_deliveries > 1 ? TXT.on_deliveries : TXT.on_delivery}</span></div>
            <a href="${TRACK_BASE}?code=${esc(d.active_tracking_code)}" target="_blank"
               style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:var(--primary);color:white;text-decoration:none;border-radius:4px;font-size:11px;font-weight:600;margin-top:8px;">
               <i class="fa-solid fa-location-arrow"></i> Track Order</a>`;
    }

    if (d.active_po_id) {
        const accepted = d.active_po_acceptance === 'accepted';
        const poStatusLabel = accepted
            ? `<span style="background:#dbeafe;color:#1e40af;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;">🚛 En Route</span>`
            : `<span style="background:#f5f3ff;color:#6d28d9;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:700;">🔔 Driver Notified</span>`;
        html += `<div class="popup-po-banner">
            <strong><i class="fa-solid fa-box-open"></i> Supplier Pickup ${poStatusLabel}</strong>
            ${esc(d.active_po_supplier || '—')} — PO #${esc(d.active_po_number || '—')}<br>
            <a href="${PO_VIEW_BASE}?id=${d.active_po_id}" target="_blank"
               style="color:#6d28d9;font-weight:600;font-size:11px;text-decoration:none;">
               <i class="fa-solid fa-arrow-right"></i> View PO</a>
        </div>`;
    }

    if (d.last_update) {
        html += `<div class="popup-info-row" style="margin-top:8px;padding-top:8px;border-top:1px solid #e5e7eb;font-size:11px;color:#9ca3af;">
            <i class="fa-solid fa-clock" style="width:14px;font-size:11px;margin-top:1px;"></i>
            <span>GPS: ${timeAgo(d.last_update)}</span></div>`;
    }

    html += `</div></div>`;
    return html;
}

// ── Sidebar ───────────────────────────────────────────────────────────────────
function updateSidebar(drivers) {
    const q = (document.getElementById('driverSearch')?.value || '').toLowerCase();
    renderDriverList(q ? drivers.filter(d => d.name.toLowerCase().includes(q)) : drivers);
}

function renderDriverList(drivers) {
    const el = document.getElementById('driversList');
    if (!drivers.length) {
        el.innerHTML = `<div class="sidebar-empty"><i class="fa-solid fa-truck-slash"></i><p>${TXT.no_drivers}</p></div>`;
        return;
    }
    el.innerHTML = drivers.map(d => {
        const hasGps   = d.lat && d.lng;
        const isActive = selectedDriverId === d.driver_id;
        const hasPo    = !!d.active_po_id;

        let gpsTag = '';
        if (d.last_update) {
            const diffMin = (Date.now() - new Date(d.last_update).getTime()) / 60000;
            gpsTag = diffMin < 3
                ? `<span class="driver-tag gps-ok"><i class="fa-solid fa-satellite-dish"></i> ${TXT.gps_live}</span>`
                : `<span class="driver-tag gps-old"><i class="fa-solid fa-satellite-dish"></i> ${Math.round(diffMin)}m ${TXT.gps_ago}</span>`;
        } else {
            gpsTag = `<span class="driver-tag gps-old">${TXT.no_gps}</span>`;
        }

        const taskTag = hasPo
            ? `<span class="driver-tag pickup"><i class="fa-solid fa-box-open"></i> ${TXT.po_pickup}${d.active_po_acceptance === 'accepted' ? ' · En Route' : ' · Notified'}</span>`
            : d.active_deliveries > 0
                ? `<span class="driver-tag delivery"><i class="fa-solid fa-truck"></i> ${d.active_deliveries} ${d.active_deliveries > 1 ? TXT.on_deliveries : TXT.on_delivery}</span>`
                : '';

        const badge = hasPo
            ? `<span class="driver-status-badge pickup"><span class="driver-status-dot"></span>${TXT.pickup}</span>`
            : d.active_deliveries > 0
                ? `<span class="driver-status-badge busy"><span class="driver-status-dot"></span>${TXT.busy}</span>`
                : `<span class="driver-status-badge available"><span class="driver-status-dot"></span>${TXT.available}</span>`;

        const cls       = isActive ? 'active' : hasPo ? 'pickup-active' : '';
        const clickArgs = hasGps ? `${d.driver_id},${d.lat},${d.lng}` : `${d.driver_id},null,null`;

        return `<div class="driver-list-item ${cls}" onclick="centerOnDriver(${clickArgs})" data-name="${esc(d.name.toLowerCase())}">
            <div class="driver-header"><span class="driver-name">${esc(d.name)}</span>${badge}</div>
            <div class="driver-meta">
                ${taskTag}${gpsTag}
                ${d.zone ? `<span style="color:var(--gray-500);"><i class="fa-solid fa-map-pin" style="font-size:10px;"></i> ${esc(d.zone)}</span>` : ''}
            </div>
        </div>`;
    }).join('');
}

function filterDrivers(q) {
    const lq = q.toLowerCase();
    renderDriverList(lq ? allDrivers.filter(d => d.name.toLowerCase().includes(lq)) : allDrivers);
}

// ── Stats ─────────────────────────────────────────────────────────────────────
function updateStats(drivers) {
    document.getElementById('totalOnline').textContent    = drivers.length;
    document.getElementById('availableCount').textContent = drivers.filter(d => !d.active_po_id && d.active_deliveries === 0).length;
    document.getElementById('busyCount').textContent      = drivers.filter(d => !d.active_po_id && d.active_deliveries > 0).length;
    document.getElementById('pickupCount').textContent    = drivers.filter(d => !!d.active_po_id).length;
}

// ── Center on driver (sidebar click) ─────────────────────────────────────────
function centerOnDriver(driverId, lat, lng) {
    selectedDriverId = driverId;
    shouldFitBounds  = true;
    updateSelection();

    if (lat && lng) {
        map.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
        map.setZoom(15);
        const m = markers[`driver-${driverId}`];
        if (m) {
            const driver = allDrivers.find(d => d.driver_id === driverId);
            if (driver) {
                infoWindow.setContent(buildPopup(driver));
                infoWindow.open(map, m);
            }
        }
    }

    clearRouteLayers();
    fetchAndDrawRoute(driverId);
}

// ── Route drawing ─────────────────────────────────────────────────────────────
async function fetchAndDrawRoute(driverId) {
    try {
        const res  = await fetch(`${ROUTE_ENDPOINT}?driver_id=${driverId}`);
        const data = await res.json();
        if (data.error) return;

        const bounds = new google.maps.LatLngBounds();
        let hasBounds = false;

        // 1. Breadcrumb trail (blue polyline — actual path taken)
        if (data.breadcrumbs && data.breadcrumbs.length > 1) {
            const path = data.breadcrumbs.map(b => ({ lat: b.lat, lng: b.lng }));
            routeLayers.breadcrumb = new google.maps.Polyline({
                path,
                strokeColor:   '#3b82f6',
                strokeWeight:  4,
                strokeOpacity: 0.8,
                map,
            });
            path.forEach(p => { bounds.extend(p); hasBounds = true; });
        }

        // 2. Planned route (green dashed — Google Directions encoded polyline)
        if (data.google_polyline) {
            const decodedPath = google.maps.geometry.encoding.decodePath(data.google_polyline);
            routeLayers.planned = new google.maps.Polyline({
                path:          decodedPath,
                strokeColor:   '#22c55e',
                strokeWeight:  4,
                strokeOpacity: 0.85,
                icons: [{
                    icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, scale: 3 },
                    offset: '0',
                    repeat: '16px',
                }],
                map,
            });
            decodedPath.forEach(p => { bounds.extend(p); hasBounds = true; });
        }

        // 3. Merchant marker (orange)
        if (data.merchant) {
            routeLayers.merchant = new google.maps.Marker({
                position: { lat: data.merchant.lat, lng: data.merchant.lng },
                map,
                title: data.merchant.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#f97316', fillOpacity: 1,
                    strokeColor: 'white', strokeWeight: 2, scale: 13,
                },
                label: { text: 'M', color: 'white', fontSize: '11px', fontWeight: 'bold' },
            });
            bounds.extend({ lat: data.merchant.lat, lng: data.merchant.lng });
            hasBounds = true;
        }

        // 4. Customer marker (red)
        if (data.customer) {
            routeLayers.customer = new google.maps.Marker({
                position: { lat: data.customer.lat, lng: data.customer.lng },
                map,
                title: data.customer.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#ef4444', fillOpacity: 1,
                    strokeColor: 'white', strokeWeight: 2, scale: 13,
                },
                label: { text: 'C', color: 'white', fontSize: '11px', fontWeight: 'bold' },
            });
            bounds.extend({ lat: data.customer.lat, lng: data.customer.lng });
            hasBounds = true;
        }

        // 5. Supplier marker (purple) — where driver collected goods
        if (data.supplier) {
            routeLayers.supplier = new google.maps.Marker({
                position: { lat: data.supplier.lat, lng: data.supplier.lng },
                map,
                title: data.supplier.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#7c3aed', fillOpacity: 1,
                    strokeColor: 'white', strokeWeight: 2, scale: 13,
                },
                label: { text: 'S', color: 'white', fontSize: '11px', fontWeight: 'bold' },
            });
            bounds.extend({ lat: data.supplier.lat, lng: data.supplier.lng });
            hasBounds = true;
        }

        // 6. Depot marker (dark blue) — drop-off destination after pickup
        if (data.depot) {
            routeLayers.depot = new google.maps.Marker({
                position: { lat: data.depot.lat, lng: data.depot.lng },
                map,
                title: data.depot.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#1e40af', fillOpacity: 1,
                    strokeColor: 'white', strokeWeight: 2, scale: 13,
                },
                label: { text: 'D', color: 'white', fontSize: '11px', fontWeight: 'bold' },
            });
            bounds.extend({ lat: data.depot.lat, lng: data.depot.lng });
            hasBounds = true;
        }

        // Fit bounds only on explicit driver click
        if (shouldFitBounds && hasBounds) {
            map.fitBounds(bounds, { top: 80, right: 40, bottom: 40, left: 320 });
            shouldFitBounds = false;
        }

    } catch(e) { console.error('Route fetch failed:', e); }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function updateSelection() {
    document.querySelectorAll('.driver-list-item').forEach(el => el.classList.remove('active'));
    const el = document.querySelector(`.driver-list-item[onclick*="centerOnDriver(${selectedDriverId},"]`);
    if (el) el.classList.add('active');
}

function toggleSidebar() {
    const sb = document.getElementById('mapSidebar');
    const ic = document.getElementById('sidebarToggleIcon');
    sb.classList.toggle('collapsed');
    ic.className = sb.classList.contains('collapsed') ? 'fa-solid fa-chevron-right' : 'fa-solid fa-chevron-left';
}

function timeAgo(dateStr) {
    if (!dateStr) return '—';
    const s = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (s < 60)   return s + 's ago';
    if (s < 3600) return Math.floor(s / 60) + 'm ago';
    return Math.floor(s / 3600) + 'h ago';
}

function esc(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
}

window.addEventListener('beforeunload', () => clearInterval(refreshTimer));
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
