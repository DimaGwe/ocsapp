      </div>
    </main>
  </div>
  <script>
  // ---- Business Notification Bell ----
  var bizNotifPanelOpen = false;

  function toggleBizNotifPanel() {
    bizNotifPanelOpen = !bizNotifPanelOpen;
    var panel = document.getElementById('bizNotifPanel');
    if (bizNotifPanelOpen) {
      panel.classList.add('open');
      fetchBizNotifications();
    } else {
      panel.classList.remove('open');
    }
  }

  document.addEventListener('click', function(e) {
    if (!e.target.closest('#bizNotifWrapper')) {
      var panel = document.getElementById('bizNotifPanel');
      if (panel) panel.classList.remove('open');
      bizNotifPanelOpen = false;
    }
  });

  function fetchBizNotifCount() {
    fetch('<?= url("api/business/notifications/count") ?>')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) updateBizBadge(data.unread_count);
      })
      .catch(function() {});
  }

  function fetchBizNotifications() {
    fetch('<?= url("api/business/notifications") ?>?limit=10')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          updateBizBadge(data.unread_count);
          renderBizNotifications(data.notifications);
        }
      })
      .catch(function() {});
  }

  function updateBizBadge(count) {
    var badge = document.getElementById('bizNotifBadge');
    var markAllBtn = document.getElementById('bizNotifMarkAllBtn');
    if (count > 0) {
      badge.textContent = count > 9 ? '9+' : count;
      badge.classList.remove('hidden');
      if (markAllBtn) markAllBtn.classList.remove('hidden');
    } else {
      badge.classList.add('hidden');
      if (markAllBtn) markAllBtn.classList.add('hidden');
    }
  }

  var _noNotifText = <?= json_encode(($_SESSION['language'] ?? 'fr') === 'fr' ? 'Aucune notification' : 'No notifications') ?>;

  function renderBizNotifications(notifications) {
    var list = document.getElementById('bizNotifList');
    if (!notifications.length) {
      list.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i>' + _noNotifText + '</div>';
      return;
    }
    var html = '';
    notifications.forEach(function(n) {
      var unread = n.is_read == 0 ? ' unread' : '';
      var link = n.link ? '<?= url("") ?>' + n.link.replace(/^\//, '') : '#';
      html += '<a href="' + link + '" class="notif-item' + unread + '" onclick="markBizNotifRead(' + n.id + ', event)">'
        + '<div class="notif-item-icon"><i class="fas fa-' + (n.icon || 'bell') + '"></i></div>'
        + '<div class="notif-item-content">'
        + '<div class="notif-item-title">' + escBizHtml(n.title) + '</div>'
        + '<div class="notif-item-msg">' + escBizHtml(n.message) + '</div>'
        + '<div class="notif-item-time">' + formatBizNotifTime(n.created_at) + '</div>'
        + '</div></a>';
    });
    list.innerHTML = html;
  }

  function markBizNotifRead(id, event) {
    fetch('<?= url("api/business/notifications/mark-read") ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) updateBizBadge(data.unread_count);
    })
    .catch(function() {});
  }

  function markAllBizNotifRead() {
    fetch('<?= url("api/business/notifications/mark-all-read") ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        updateBizBadge(0);
        document.querySelectorAll('.notif-item.unread').forEach(function(el) {
          el.classList.remove('unread');
        });
      }
    })
    .catch(function() {});
  }

  function formatBizNotifTime(dateStr) {
    var d = new Date(dateStr);
    var now = new Date();
    var diff = Math.floor((now - d) / 1000);
    var isFr = <?= json_encode(($_SESSION['language'] ?? 'fr') === 'fr') ?>;
    if (diff < 60)     return isFr ? 'À l\'instant' : 'Just now';
    if (diff < 3600)   return Math.floor(diff / 60)  + (isFr ? ' min' : 'm ago');
    if (diff < 86400)  return Math.floor(diff / 3600) + (isFr ? ' h'  : 'h ago');
    if (diff < 604800) return Math.floor(diff / 86400)+ (isFr ? ' j'  : 'd ago');
    return d.toLocaleDateString(isFr ? 'fr-CA' : 'en-CA', { month: 'short', day: 'numeric' });
  }

  function escBizHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ── Browser Notifications API ─────────────────────────────────────────────
  var _bizBrowserNotifPerm = (typeof Notification !== 'undefined') ? Notification.permission : 'denied';
  function _requestBizNotifPerm() {
    if (_bizBrowserNotifPerm === 'default' && typeof Notification !== 'undefined') {
      Notification.requestPermission().then(function(r) { _bizBrowserNotifPerm = r; });
    }
  }
  document.addEventListener('click', _requestBizNotifPerm, { once: true, passive: true });

  function _showBizBrowserNotif(title, body, link) {
    if (_bizBrowserNotifPerm !== 'granted') return;
    try {
      var n = new Notification('OCSAPP — ' + title, {
        body: body,
        icon: '<?= url("assets/images/logo.png") ?>',
        tag: 'ocsapp-business',
        requireInteraction: false
      });
      if (link) {
        n.onclick = function() { window.focus(); window.location.href = '<?= url("") ?>' + link.replace(/^\//, ''); n.close(); };
      }
      setTimeout(function() { try { n.close(); } catch(e) {} }, 8000);
    } catch(e) {}
  }

  var _bizPrevBellCount = -1;

  // Override fetchBizNotifCount to use unified refresh
  function fetchBizNotifCount() {
    if (typeof window._refreshAllBizBadges === 'function') {
      window._refreshAllBizBadges();
      return;
    }
    fetch('<?= url("api/business/notifications/count") ?>')
      .then(function(r) { return r.json(); })
      .then(function(data) { if (data.success) updateBizBadge(data.unread_count); })
      .catch(function() {});
  }

  // Use SSE for instant notifications, fallback to 5s polling (only when authenticated)
  <?php if (!empty($_SESSION['business']['id'])): ?>
  fetchBizNotifCount();
  (function startBizNotifStream() {
    if (typeof EventSource === 'undefined') {
      setInterval(fetchBizNotifCount, 5000);
      return;
    }
    function connect() {
      var es = new EventSource('<?= url("api/business/notifications/stream") ?>');
      es.onmessage = function(e) {
        try {
          var d = JSON.parse(e.data);
          if (typeof d.unread_count !== 'undefined') {
            var n = d.unread_count;
            if (_bizPrevBellCount >= 0 && n > _bizPrevBellCount) {
              if (typeof window.playChimeBiz === 'function') window.playChimeBiz();
              // Show system notification with message text
              fetch('<?= url("api/business/notifications") ?>?limit=1')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                  if (data.success && data.notifications && data.notifications.length) {
                    var notif = data.notifications[0];
                    if (!notif.is_read) _showBizBrowserNotif(notif.title, notif.message, notif.link);
                  }
                }).catch(function() {});
            }
            _bizPrevBellCount = n;
            updateBizBadge(n);
            // Refresh ALL badges
            if (typeof window._refreshAllBizBadges === 'function') window._refreshAllBizBadges();
            if (bizNotifPanelOpen) fetchBizNotifications();
          }
        } catch(err) {}
      };
      es.addEventListener('reconnect', function() { es.close(); connect(); });
      es.onerror = function() { es.close(); setTimeout(connect, 5000); };
    }
    connect();
  })();
  <?php endif; ?>

  // ── Enable Notifications Banner ───────────────────────────────────────────
  (function() {
    if (localStorage.getItem('biz_notif_enabled')) return;
    if (typeof Notification !== 'undefined' && Notification.permission === 'denied') return;
    setTimeout(function() {
      var b = document.getElementById('bizNotifEnableBanner');
      if (b) b.style.display = 'flex';
    }, 2000);
  })();

  window._enableBizNotifs = function() {
    if (typeof _unlockBizAudio === 'function') _unlockBizAudio();
    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
      Notification.requestPermission().then(function(r) { _bizBrowserNotifPerm = r; });
    }
    localStorage.setItem('biz_notif_enabled', '1');
    var b = document.getElementById('bizNotifEnableBanner');
    if (b) b.style.display = 'none';
  };

  window._dismissBizNotifBanner = function(e) {
    e.stopPropagation();
    var b = document.getElementById('bizNotifEnableBanner');
    if (b) b.style.display = 'none';
  };
  </script>

  <!-- Enable Notifications Banner -->
  <div id="bizNotifEnableBanner" onclick="window._enableBizNotifs()" style="display:none;position:fixed;bottom:24px;right:24px;z-index:9999;background:#00b207;color:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 4px 20px rgba(0,0,0,0.2);align-items:center;gap:12px;cursor:pointer;font-size:14px;font-weight:600;max-width:300px;animation:slideInBanner 0.4s ease;">
    <i class="fas fa-bell" style="font-size:18px;flex-shrink:0;"></i>
    <span style="flex:1;">Enable notifications &amp; sound</span>
    <button onclick="window._dismissBizNotifBanner(event)" title="Dismiss" style="background:none;border:none;color:#fff;cursor:pointer;font-size:18px;line-height:1;padding:0;opacity:0.8;">&times;</button>
  </div>
  <style>
  @keyframes slideInBanner { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
  </style>
</body>
</html>
