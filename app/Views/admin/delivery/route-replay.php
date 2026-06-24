<?php
/**
 * OCS Admin Route Replay
 * Watch a driver's full GPS path for any past date with animated playback.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$pageTitle   = $currentLang === 'fr' ? 'Relecture de parcours' : 'Route Replay';
$currentPage = 'route-replay';
$gmapsKey    = $gmapsKey ?? '';

$t = [
    'en' => [
        'title'         => 'Route Replay',
        'subtitle'      => "Select a driver and date to watch their delivery path",
        'lbl_driver'    => 'Driver',
        'lbl_date'      => 'Date',
        'btn_load'      => 'Load Replay',
        'stat_driver'   => 'Driver',
        'stat_date'     => 'Date',
        'stat_pings'    => 'GPS Pings',
        'stat_duration' => 'Duration',
        'stat_distance' => 'Distance',
        'btn_play'      => 'Play',
        'btn_pause'     => 'Pause',
        'lbl_speed'     => 'Speed',
        'lbl_time'      => 'Time',
        'lbl_speed2'    => 'Speed',
        'lbl_heading'   => 'Heading',
        'lbl_accuracy'  => 'Accuracy',
        'avail_dates'   => 'Available Dates',
        'empty_msg'     => 'Select a driver and date, then click <strong>Load Replay</strong>',
        'loading'       => 'Loading GPS data...',
        'js_no_driver'  => 'Please select a driver.',
        'js_fail_load'  => 'Failed to load replay data.',
        'js_no_data'    => 'No GPS data found for this driver on ',
        'js_min'        => 'min',
        'js_pings'      => 'pings',
    ],
    'fr' => [
        'title'         => 'Relecture de parcours',
        'subtitle'      => 'Sélectionnez un livreur et une date pour visualiser son parcours de livraison',
        'lbl_driver'    => 'Livreur',
        'lbl_date'      => 'Date',
        'btn_load'      => 'Charger la relecture',
        'stat_driver'   => 'Livreur',
        'stat_date'     => 'Date',
        'stat_pings'    => 'Points GPS',
        'stat_duration' => 'Durée',
        'stat_distance' => 'Distance',
        'btn_play'      => 'Lire',
        'btn_pause'     => 'Pause',
        'lbl_speed'     => 'Vitesse',
        'lbl_speed2'    => 'Vitesse',
        'lbl_heading'   => 'Direction',
        'lbl_accuracy'  => 'Précision',
        'avail_dates'   => 'Dates disponibles',
        'empty_msg'     => 'Sélectionnez un livreur et une date, puis cliquez sur <strong>Charger la relecture</strong>',
        'loading'       => 'Chargement des données GPS...',
        'js_no_driver'  => 'Veuillez sélectionner un livreur.',
        'js_fail_load'  => 'Échec du chargement des données de relecture.',
        'js_no_data'    => 'Aucune donnée GPS trouvée pour ce livreur le ',
        'js_min'        => 'min',
        'js_pings'      => 'pings',
        'lbl_time'      => 'Heure',
    ],
];
$t = $t[$currentLang] ?? $t['en'];

ob_start();
?>
<style>
/* ── Layout ───────────────────────────────────────────────── */
.replay-wrap {
    display: flex;
    gap: 0;
    height: calc(100vh - 64px);
    margin: 0 -32px -32px -32px;
    overflow: hidden;
}

/* ── Sidebar ──────────────────────────────────────────────── */
.replay-sidebar {
    width: 300px;
    min-width: 300px;
    background: #fff;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 10;
}
.replay-sidebar-header {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}
.replay-sidebar-header h2 {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px;
}
.replay-sidebar-header p {
    font-size: 12px;
    color: #6b7280;
    margin: 0;
}
.replay-controls {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.replay-controls label {
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    display: block;
    margin-bottom: 4px;
}
.replay-controls select,
.replay-controls input[type=date] {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    color: #111827;
    background: #fff;
    box-sizing: border-box;
}
.replay-controls select:focus,
.replay-controls input[type=date]:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.btn-load {
    width: 100%;
    padding: 9px;
    background: #3b82f6;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s;
}
.btn-load:hover { background: #2563eb; }
.btn-load:disabled { background: #93c5fd; cursor: not-allowed; }

/* ── Stats panel ──────────────────────────────────────────── */
.replay-stats {
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    display: none;
}
.replay-stats.visible { display: block; }
.stat-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}
.stat-row span:last-child {
    font-weight: 600;
    color: #111827;
}

/* ── Playback controls ────────────────────────────────────── */
.playback-panel {
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    display: none;
}
.playback-panel.visible { display: block; }
.playback-btns {
    display: flex;
    gap: 6px;
    margin-bottom: 10px;
    align-items: center;
}
.btn-play, .btn-pause, .btn-restart {
    padding: 7px 14px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s;
}
.btn-play    { background: #10b981; color: #fff; }
.btn-play:hover { background: #059669; }
.btn-pause   { background: #f59e0b; color: #fff; display: none; }
.btn-pause:hover { background: #d97706; }
.btn-restart { background: #6b7280; color: #fff; }
.btn-restart:hover { background: #4b5563; }
.speed-label { font-size: 12px; color: #6b7280; margin-left: auto; }
.speed-select {
    padding: 5px 8px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    background: #fff;
}

/* ── Scrubber ─────────────────────────────────────────────── */
.scrubber-wrap { margin-top: 6px; }
.scrubber-wrap input[type=range] {
    width: 100%;
    accent-color: #3b82f6;
}
.scrubber-time {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #9ca3af;
    margin-top: 2px;
}

/* ── Current point info ───────────────────────────────────── */
.point-info {
    padding: 12px 16px;
    display: none;
    flex-direction: column;
    gap: 6px;
}
.point-info.visible { display: flex; }
.point-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 500;
}
.point-badge.moving  { background: #d1fae5; color: #065f46; }
.point-badge.stopped { background: #fee2e2; color: #991b1b; }
.point-detail {
    font-size: 12px;
    color: #6b7280;
    display: flex;
    justify-content: space-between;
}
.point-detail strong { color: #111827; }

/* ── Date history list ───────────────────────────────────────*/
.date-history {
    flex: 1;
    overflow-y: auto;
    padding: 12px 16px;
}
.date-history h4 {
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin: 0 0 8px;
}
.date-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    color: #374151;
    transition: background .1s;
}
.date-item:hover { background: #f3f4f6; }
.date-item.active { background: #eff6ff; color: #1d4ed8; font-weight: 600; }
.date-item .ping-count {
    font-size: 11px;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 2px 7px;
    border-radius: 10px;
}
.date-item.active .ping-count { background: #dbeafe; color: #3b82f6; }

/* ── Map ──────────────────────────────────────────────────── */
#replayMap {
    flex: 1;
    height: 100%;
}

/* ── Empty state ──────────────────────────────────────────── */
.map-empty {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f9fafb;
    flex-direction: column;
    gap: 12px;
    color: #9ca3af;
}
.map-empty i { font-size: 48px; color: #d1d5db; }
.map-empty p { font-size: 14px; }

/* ── Loading overlay ──────────────────────────────────────── */
#loadingOverlay {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,.75);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999;
    font-size: 14px;
    color: #374151;
    gap: 10px;
}
#loadingOverlay.visible { display: flex; }
.spinner {
    width: 20px; height: 20px;
    border: 2px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin .6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="replay-wrap">

    <!-- ── Sidebar ── -->
    <div class="replay-sidebar">
        <div class="replay-sidebar-header">
            <h2><i class="fas fa-route"></i> <?= $t['title'] ?></h2>
            <p><?= $t['subtitle'] ?></p>
        </div>

        <div class="replay-controls">
            <div>
                <label><?= $t['lbl_driver'] ?></label>
                <select id="driverSelect">
                    <option value="">— <?= $t['lbl_driver'] ?> —</option>
                    <?php foreach ($drivers as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><?= $t['lbl_date'] ?></label>
                <input type="date" id="dateInput" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <button class="btn-load" id="loadBtn" onclick="loadReplay()">
                <i class="fas fa-play"></i> <?= $t['btn_load'] ?>
            </button>
        </div>

        <!-- Stats -->
        <div class="replay-stats" id="replayStats">
            <div class="stat-row"><span><?= $t['stat_driver'] ?></span><span id="statDriver">—</span></div>
            <div class="stat-row"><span><?= $t['stat_date'] ?></span><span id="statDate">—</span></div>
            <div class="stat-row"><span><?= $t['stat_pings'] ?></span><span id="statPings">—</span></div>
            <div class="stat-row"><span><?= $t['stat_duration'] ?></span><span id="statDuration">—</span></div>
            <div class="stat-row"><span><?= $t['stat_distance'] ?></span><span id="statDistance">—</span></div>
        </div>

        <!-- Playback -->
        <div class="playback-panel" id="playbackPanel">
            <div class="playback-btns">
                <button class="btn-play"    id="btnPlay"    onclick="playReplay()"><i class="fas fa-play"></i> <?= $t['btn_play'] ?></button>
                <button class="btn-pause"   id="btnPause"   onclick="pauseReplay()"><i class="fas fa-pause"></i> <?= $t['btn_pause'] ?></button>
                <button class="btn-restart" id="btnRestart" onclick="restartReplay()"><i class="fas fa-redo"></i></button>
                <span class="speed-label"><?= $t['lbl_speed'] ?></span>
                <select class="speed-select" id="speedSelect">
                    <option value="1">1×</option>
                    <option value="2">2×</option>
                    <option value="5" selected>5×</option>
                    <option value="10">10×</option>
                    <option value="30">30×</option>
                </select>
            </div>
            <div class="scrubber-wrap">
                <input type="range" id="scrubber" min="0" value="0" step="1" oninput="scrubTo(this.value)">
                <div class="scrubber-time">
                    <span id="scrubStart">—</span>
                    <span id="scrubCurrent">—</span>
                    <span id="scrubEnd">—</span>
                </div>
            </div>
        </div>

        <!-- Current point -->
        <div class="point-info" id="pointInfo">
            <div class="stat-row">
                <span><?= $t['lbl_time'] ?></span>
                <strong id="ptTime">—</strong>
            </div>
            <div class="stat-row">
                <span><?= $t['lbl_speed2'] ?></span>
                <strong id="ptSpeed">—</strong>
            </div>
            <div class="stat-row">
                <span><?= $t['lbl_heading'] ?></span>
                <strong id="ptHeading">—</strong>
            </div>
            <div class="stat-row">
                <span><?= $t['lbl_accuracy'] ?></span>
                <strong id="ptAccuracy">—</strong>
            </div>
        </div>

        <!-- Date history -->
        <div class="date-history" id="dateHistory" style="display:none;">
            <h4><?= $t['avail_dates'] ?></h4>
            <div id="dateList"></div>
        </div>
    </div>

    <!-- ── Map area ── -->
    <div style="flex:1; position:relative; overflow:hidden;">
        <div id="loadingOverlay"><div class="spinner"></div> <?= $t['loading'] ?></div>
        <div id="mapEmptyState" class="map-empty" style="height:100%; display:flex;">
            <i class="fas fa-map-marked-alt"></i>
            <p><?= $t['empty_msg'] ?></p>
        </div>
        <div id="replayMap" style="width:100%; height:100%; display:none;"></div>
    </div>
</div>

<script>
const GMAPS_KEY = <?= json_encode($gmapsKey) ?>;
const _RT = {
    noDriver: <?= json_encode($t['js_no_driver']) ?>,
    failLoad: <?= json_encode($t['js_fail_load']) ?>,
    noData:   <?= json_encode($t['js_no_data']) ?>,
    min:      <?= json_encode($t['js_min']) ?>,
    pings:    <?= json_encode($t['js_pings']) ?>,
    locale:   <?= json_encode($currentLang === 'fr' ? 'fr-CA' : 'en-CA') ?>,
};
let map, driverMarker, pathPolyline, travelledPoly;
let replayPoints = [];
let replayIndex  = 0;
let replayTimer  = null;
let isPlaying    = false;

// ── Load Google Maps API ────────────────────────────────────
(function() {
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=' + GMAPS_KEY + '&callback=initMap';
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();

function initMap() {
    map = new google.maps.Map(document.getElementById('replayMap'), {
        center: { lat: 45.5, lng: -73.6 },
        zoom: 12,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_CENTER },
    });
}

// ── Load replay data ────────────────────────────────────────
async function loadReplay(dateOverride) {
    const driverId = document.getElementById('driverSelect').value;
    const date     = dateOverride || document.getElementById('dateInput').value;

    if (!driverId) { alert(_RT.noDriver); return; }

    document.getElementById('loadingOverlay').classList.add('visible');
    document.getElementById('loadBtn').disabled = true;
    pauseReplay();

    try {
        const res  = await fetch(`/api/delivery/replay-data?driver_id=${driverId}&date=${date}`);
        const data = await res.json();

        if (data.error) { alert(data.error); return; }

        replayPoints = data.points;
        replayIndex  = 0;

        renderDateHistory(data.dates, date);

        if (replayPoints.length === 0) {
            showEmpty(_RT.noData + formatDate(date));
            document.getElementById('replayStats').classList.remove('visible');
            document.getElementById('playbackPanel').classList.remove('visible');
            document.getElementById('pointInfo').classList.remove('visible');
            return;
        }

        showMap();
        buildPath();
        updateStats(data);
        setupScrubber();
        showPointInfo(0);

    } catch(e) {
        alert(_RT.failLoad);
        console.error(e);
    } finally {
        document.getElementById('loadingOverlay').classList.remove('visible');
        document.getElementById('loadBtn').disabled = false;
    }
}

function showMap() {
    document.getElementById('mapEmptyState').style.display = 'none';
    document.getElementById('replayMap').style.display = 'block';
}

function showEmpty(msg) {
    const el = document.getElementById('mapEmptyState');
    el.innerHTML = `<i class="fas fa-map-marked-alt"></i><p>${msg}</p>`;
    el.style.display = 'flex';
    document.getElementById('replayMap').style.display = 'none';
}

// ── Build full path on map ──────────────────────────────────
function buildPath() {
    if (pathPolyline) pathPolyline.setMap(null);
    if (travelledPoly) travelledPoly.setMap(null);
    if (driverMarker) driverMarker.setMap(null);

    const allLatLng = replayPoints.map(p => ({ lat: parseFloat(p.lat ?? p.latitude), lng: parseFloat(p.lng ?? p.longitude) }));

    // Full ghost path (light grey dashed)
    pathPolyline = new google.maps.Polyline({
        path: allLatLng,
        strokeColor: '#d1d5db',
        strokeWeight: 3,
        strokeOpacity: 1,
        map,
    });

    // Travelled path (blue, grows during playback)
    travelledPoly = new google.maps.Polyline({
        path: [],
        strokeColor: '#3b82f6',
        strokeWeight: 4,
        strokeOpacity: 0.9,
        map,
    });

    // Start marker (green pin)
    new google.maps.Marker({
        position: allLatLng[0],
        map,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: '#10b981',
            fillOpacity: 1,
            strokeColor: '#fff',
            strokeWeight: 2,
        },
        title: 'Start: ' + replayPoints[0].ts,
        zIndex: 5,
    });

    // End marker (red pin)
    new google.maps.Marker({
        position: allLatLng[allLatLng.length - 1],
        map,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 8,
            fillColor: '#ef4444',
            fillOpacity: 1,
            strokeColor: '#fff',
            strokeWeight: 2,
        },
        title: 'End: ' + replayPoints[replayPoints.length - 1].ts,
        zIndex: 5,
    });

    // Driver moving marker
    driverMarker = new google.maps.Marker({
        position: allLatLng[0],
        map,
        icon: {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
            scale: 5,
            fillColor: '#1d4ed8',
            fillOpacity: 1,
            strokeColor: '#fff',
            strokeWeight: 1.5,
            rotation: parseFloat(replayPoints[0].heading) || 0,
        },
        zIndex: 10,
    });

    // Fit bounds
    const bounds = new google.maps.LatLngBounds();
    allLatLng.forEach(p => bounds.extend(p));
    map.fitBounds(bounds, { top: 40, right: 40, bottom: 40, left: 40 });
}

// ── Stats ───────────────────────────────────────────────────
function updateStats(data) {
    document.getElementById('replayStats').classList.add('visible');
    document.getElementById('playbackPanel').classList.add('visible');
    document.getElementById('pointInfo').classList.add('visible');

    document.getElementById('statDriver').textContent   = data.driver;
    document.getElementById('statDate').textContent     = formatDate(data.date);
    document.getElementById('statPings').textContent    = data.total;

    // Duration
    const first = new Date(replayPoints[0].ts);
    const last  = new Date(replayPoints[replayPoints.length - 1].ts);
    const mins  = Math.round((last - first) / 60000);
    document.getElementById('statDuration').textContent = mins + ' ' + _RT.min;

    // Distance (Haversine sum)
    let dist = 0;
    for (let i = 1; i < replayPoints.length; i++) {
        dist += haversine(
            parseFloat(replayPoints[i-1].latitude), parseFloat(replayPoints[i-1].longitude),
            parseFloat(replayPoints[i].latitude),   parseFloat(replayPoints[i].longitude)
        );
    }
    document.getElementById('statDistance').textContent = dist.toFixed(1) + ' km';
}

// ── Scrubber ────────────────────────────────────────────────
function setupScrubber() {
    const s = document.getElementById('scrubber');
    s.max   = replayPoints.length - 1;
    s.value = 0;
    document.getElementById('scrubStart').textContent   = formatTime(replayPoints[0].ts);
    document.getElementById('scrubEnd').textContent     = formatTime(replayPoints[replayPoints.length - 1].ts);
    document.getElementById('scrubCurrent').textContent = formatTime(replayPoints[0].ts);
}

function scrubTo(idx) {
    pauseReplay();
    replayIndex = parseInt(idx);
    moveMarkerTo(replayIndex);
    showPointInfo(replayIndex);
}

// ── Playback ────────────────────────────────────────────────
function playReplay() {
    if (replayPoints.length === 0) return;
    if (replayIndex >= replayPoints.length - 1) replayIndex = 0;
    isPlaying = true;
    document.getElementById('btnPlay').style.display  = 'none';
    document.getElementById('btnPause').style.display = 'inline-block';
    stepReplay();
}

function pauseReplay() {
    isPlaying = false;
    clearTimeout(replayTimer);
    document.getElementById('btnPlay').style.display  = 'inline-block';
    document.getElementById('btnPause').style.display = 'none';
}

function restartReplay() {
    pauseReplay();
    replayIndex = 0;
    if (travelledPoly) travelledPoly.setPath([]);
    if (replayPoints.length > 0) {
        moveMarkerTo(0);
        showPointInfo(0);
        document.getElementById('scrubber').value = 0;
    }
}

function stepReplay() {
    if (!isPlaying) return;
    if (replayIndex >= replayPoints.length - 1) {
        pauseReplay();
        return;
    }

    replayIndex++;
    moveMarkerTo(replayIndex);
    showPointInfo(replayIndex);
    document.getElementById('scrubber').value = replayIndex;

    // Delay based on actual time diff × speed multiplier
    const speed   = parseFloat(document.getElementById('speedSelect').value);
    let   delay   = 1000 / speed; // default 1s interval compressed by speed

    if (replayIndex > 0) {
        const prev = new Date(replayPoints[replayIndex - 1].ts);
        const curr = new Date(replayPoints[replayIndex].ts);
        const realGap = curr - prev; // ms between real pings
        delay = Math.max(50, Math.min(realGap / speed, 2000));
    }

    replayTimer = setTimeout(stepReplay, delay);
}

function moveMarkerTo(idx) {
    const p   = replayPoints[idx];
    const pos = { lat: parseFloat(p.latitude), lng: parseFloat(p.longitude) };

    driverMarker.setPosition(pos);
    if (p.heading !== null && p.heading !== undefined) {
        const icon = driverMarker.getIcon();
        icon.rotation = parseFloat(p.heading);
        driverMarker.setIcon(icon);
    }

    // Grow the travelled polyline
    const path = travelledPoly.getPath();
    // Rebuild from 0..idx for scrubber seek
    const newPath = replayPoints.slice(0, idx + 1).map(pt => ({
        lat: parseFloat(pt.latitude), lng: parseFloat(pt.longitude)
    }));
    travelledPoly.setPath(newPath);

    // Pan gently if marker near edge
    const bounds = map.getBounds();
    if (bounds && !bounds.contains(pos)) {
        map.panTo(pos);
    }

    document.getElementById('scrubCurrent').textContent = formatTime(p.ts);
}

function showPointInfo(idx) {
    const p = replayPoints[idx];
    const speed = p.speed !== null ? (parseFloat(p.speed) * 3.6).toFixed(1) + ' km/h' : '—';
    const heading = p.heading !== null ? headingToDirection(parseInt(p.heading)) + ' (' + p.heading + '°)' : '—';
    const accuracy = p.accuracy !== null ? parseFloat(p.accuracy).toFixed(0) + ' m' : '—';

    document.getElementById('ptTime').textContent     = formatTime(p.ts);
    document.getElementById('ptSpeed').textContent    = speed;
    document.getElementById('ptHeading').textContent  = heading;
    document.getElementById('ptAccuracy').textContent = accuracy;
}

// ── Date history sidebar ────────────────────────────────────
function renderDateHistory(dates, activeDate) {
    const list = document.getElementById('dateList');
    const wrap = document.getElementById('dateHistory');
    if (!dates || dates.length === 0) { wrap.style.display = 'none'; return; }

    wrap.style.display = 'block';
    list.innerHTML = dates.map(d => `
        <div class="date-item ${d.date === activeDate ? 'active' : ''}"
             onclick="quickLoad('${d.date}')">
            <span>${formatDate(d.date)}</span>
            <span class="ping-count">${d.pings} ${_RT.pings}</span>
        </div>
    `).join('');
}

function quickLoad(date) {
    document.getElementById('dateInput').value = date;
    loadReplay(date);
}

// ── Helpers ─────────────────────────────────────────────────
function formatDate(d) {
    const dt = new Date(d + 'T00:00:00');
    return dt.toLocaleDateString(_RT.locale, { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
}

function formatTime(ts) {
    return new Date(ts).toLocaleTimeString(_RT.locale, { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function headingToDirection(deg) {
    const dirs = ['N','NE','E','SE','S','SW','W','NW'];
    return dirs[Math.round(deg / 45) % 8];
}

function haversine(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) ** 2 +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
