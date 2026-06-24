/**
 * Driver GPS Tracker
 * Automatically broadcasts driver location while on an active delivery.
 * Uses navigator.geolocation.watchPosition for efficient updates.
 */
const DriverGPSTracker = (function() {
    let watchId = null;
    let activeDeliveryId = null;
    let lastSentAt = 0;
    let statusEl = null;
    const SEND_INTERVAL_MS = 15000; // Send every 15 seconds
    const MIN_ACCURACY_M = 500;     // Ignore readings with >500m accuracy

    function start(deliveryId) {
        if (watchId !== null) stop();
        if (!navigator.geolocation) {
            console.warn('Geolocation not supported');
            updateStatusUI('GPS not supported', 'error');
            return;
        }

        activeDeliveryId = deliveryId;

        watchId = navigator.geolocation.watchPosition(
            onPosition,
            onError,
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 5000
            }
        );

        updateStatusUI('GPS Active', 'active');
        console.log('[GPS] Tracking started for delivery #' + deliveryId);
    }

    function stop() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        activeDeliveryId = null;
        updateStatusUI('GPS Off', 'off');
        console.log('[GPS] Tracking stopped');
    }

    function onPosition(position) {
        const now = Date.now();

        // Throttle: don't send more than once per interval
        if (now - lastSentAt < SEND_INTERVAL_MS) return;

        // Skip inaccurate readings
        if (position.coords.accuracy > MIN_ACCURACY_M) {
            console.log('[GPS] Skipping inaccurate reading: ' + position.coords.accuracy + 'm');
            return;
        }

        lastSentAt = now;

        const payload = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy,
            heading: position.coords.heading,
            speed: position.coords.speed ? (position.coords.speed * 3.6) : null, // m/s to km/h
            delivery_id: activeDeliveryId
        };

        fetch('/api/delivery/location', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateStatusUI('GPS Active — ' + new Date().toLocaleTimeString(), 'active');
            }
        })
        .catch(err => {
            console.error('[GPS] Send error:', err);
            updateStatusUI('GPS Error', 'error');
        });
    }

    function onError(error) {
        console.warn('[GPS] Error:', error.message);
        switch (error.code) {
            case error.PERMISSION_DENIED:
                updateStatusUI('GPS Permission Denied', 'error');
                stop();
                break;
            case error.POSITION_UNAVAILABLE:
                updateStatusUI('GPS Unavailable', 'error');
                break;
            case error.TIMEOUT:
                updateStatusUI('GPS Timeout — Retrying', 'warning');
                break;
        }
    }

    function updateStatusUI(text, state) {
        if (!statusEl) {
            statusEl = document.getElementById('gps-status');
        }
        if (!statusEl) return;

        statusEl.textContent = text;
        statusEl.className = 'gps-status gps-' + state;
    }

    function isActive() {
        return watchId !== null;
    }

    return { start, stop, isActive };
})();
