      </div>
    </main>
  </div>
  <script>
  // Password reminder dismiss
  function dismissPasswordReminder() {
      var banner = document.getElementById('passwordReminderBanner');
      if (banner) {
          banner.style.transition = 'opacity 0.3s';
          banner.style.opacity = '0';
          setTimeout(function() { banner.style.display = 'none'; }, 300);
      }
      fetch('<?= url("supplier/dismiss-password-reminder") ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: '<?= env("CSRF_TOKEN_NAME", "_csrf_token") ?>=<?= $_SESSION[env("CSRF_TOKEN_NAME", "_csrf_token")] ?? "" ?>' });
  }

  // Language switcher
  (function() {
      var langBtn = document.getElementById('supplierLangBtn');
      var langDropdown = document.getElementById('supplierLangDropdown');
      if (!langBtn || !langDropdown) return;

      langBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          langDropdown.classList.toggle('open');
      });

      document.querySelectorAll('.lang-option').forEach(function(option) {
          option.addEventListener('click', function() {
              var newLang = this.dataset.lang;
              fetch('<?= url("set-language") ?>', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                      'X-Requested-With': 'XMLHttpRequest'
                  },
                  body: new URLSearchParams({
                      'language': newLang,
                      '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>': '<?= generateCsrfToken() ?>'
                  })
              })
              .then(function(r) { return r.json(); })
              .then(function(data) {
                  if (data.success) window.location.reload();
              })
              .catch(function(err) {
                  console.error('Language switch error:', err);
              });
          });
      });

      document.addEventListener('click', function(e) {
          if (!e.target.closest('.language-selector')) {
              langDropdown.classList.remove('open');
          }
      });
  })();
  // ---- Notification Bell ----
  var notifPanelOpen = false;

  function toggleNotifPanel() {
      notifPanelOpen = !notifPanelOpen;
      var panel = document.getElementById('notifPanel');
      if (notifPanelOpen) {
          panel.classList.add('open');
          fetchNotifications();
      } else {
          panel.classList.remove('open');
      }
  }

  // Close panel on outside click
  document.addEventListener('click', function(e) {
      if (!e.target.closest('#notifWrapper')) {
          var panel = document.getElementById('notifPanel');
          if (panel) panel.classList.remove('open');
          notifPanelOpen = false;
      }
  });

  function fetchNotifCount() {
      // Use unified badge refresh if available, otherwise fall back to bell-only
      if (typeof window._refreshAllSupplierBadges === 'function') {
          window._refreshAllSupplierBadges();
          return;
      }
      fetch('<?= url("api/supplier/notifications/count") ?>')
          .then(function(r) { return r.json(); })
          .then(function(data) {
              if (data.success) updateBadge(data.unread_count);
          })
          .catch(function() {});
  }

  function fetchNotifications() {
      fetch('<?= url("api/supplier/notifications") ?>?limit=10')
          .then(function(r) { return r.json(); })
          .then(function(data) {
              if (data.success) {
                  updateBadge(data.unread_count);
                  renderNotifications(data.notifications);
              }
          })
          .catch(function() {});
  }

  function updateBadge(count) {
      var badge = document.getElementById('notifBadge');
      var markAllBtn = document.getElementById('notifMarkAllBtn');
      if (count > 0) {
          badge.textContent = count > 9 ? '9+' : count;
          badge.style.display = 'flex';
          if (markAllBtn) markAllBtn.style.display = 'inline';
      } else {
          badge.style.display = 'none';
          if (markAllBtn) markAllBtn.style.display = 'none';
      }
  }

  function renderNotifications(notifications) {
      var list = document.getElementById('notifList');
      if (!notifications.length) {
          list.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i><?= $fr ? 'Aucune notification' : 'No notifications' ?></div>';
          return;
      }
      var html = '';
      notifications.forEach(function(n) {
          var unread = n.is_read == 0 ? ' unread' : '';
          var link = n.link ? '<?= url("") ?>' + n.link.replace(/^\//, '') : '#';
          html += '<a href="' + link + '" class="notif-item' + unread + '" onclick="markNotifRead(' + n.id + ', event)">'
              + '<div class="notif-item-icon"><i class="fas fa-' + (n.icon || 'bell') + '"></i></div>'
              + '<div class="notif-item-content">'
              + '<div class="notif-item-title">' + escHtml(n.title) + '</div>'
              + '<div class="notif-item-msg">' + escHtml(n.message) + '</div>'
              + '<div class="notif-item-time">' + formatNotifTime(n.created_at) + '</div>'
              + '</div></a>';
      });
      list.innerHTML = html;
  }

  function markNotifRead(id, event) {
      fetch('<?= url("api/supplier/notifications/mark-read") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
          if (data.success) updateBadge(data.unread_count);
      })
      .catch(function() {});
  }

  function markAllNotifRead() {
      fetch('<?= url("api/supplier/notifications/mark-all-read") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
          if (data.success) {
              updateBadge(0);
              // Mark all items as read visually
              document.querySelectorAll('.notif-item.unread').forEach(function(el) {
                  el.classList.remove('unread');
              });
          }
      })
      .catch(function() {});
  }

  var _notifLang = '<?= $currentLang ?>';
  function formatNotifTime(dateStr) {
      var d = new Date(dateStr);
      var now = new Date();
      var diff = Math.floor((now - d) / 1000);
      if (_notifLang === 'fr') {
          if (diff < 60)     return 'À l\'instant';
          if (diff < 3600)   return 'il y a ' + Math.floor(diff / 60) + ' min';
          if (diff < 86400)  return 'il y a ' + Math.floor(diff / 3600) + ' h';
          if (diff < 604800) return 'il y a ' + Math.floor(diff / 86400) + ' j';
          return d.toLocaleDateString('fr-CA', { month: 'short', day: 'numeric' });
      }
      if (diff < 60)     return 'Just now';
      if (diff < 3600)   return Math.floor(diff / 60) + 'm ago';
      if (diff < 86400)  return Math.floor(diff / 3600) + 'h ago';
      if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
      return d.toLocaleDateString('en-CA', { month: 'short', day: 'numeric' });
  }

  function escHtml(str) {
      var div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
  }

  // ── Audio: shared unlocked AudioContext ──────────────────────────────────
  var _audioCtx = null;
  var _audioUnlocked = false;

  function _unlockAudio() {
    if (_audioUnlocked) return;
    try {
      _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      // Play a silent buffer to unlock the context on first user gesture
      var buf = _audioCtx.createBuffer(1, 1, 22050);
      var src = _audioCtx.createBufferSource();
      src.buffer = buf;
      src.connect(_audioCtx.destination);
      src.start(0);
      _audioUnlocked = true;
    } catch(e) {}
  }

  // Unlock on any first user interaction
  ['click','touchstart','keydown'].forEach(function(evt) {
    document.addEventListener(evt, _unlockAudio, { once: true, passive: true });
  });

  function playChimeFooter() {
    if (localStorage.getItem('sup_sound_enabled') === 'off') return;
    try {
      if (!_audioCtx) {
        _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      }
      var ctx = _audioCtx;
      function _doChime() {
        [[880, 0], [1047, 0.15], [1319, 0.30]].forEach(function(note) {
          var osc = ctx.createOscillator(), gain = ctx.createGain();
          osc.connect(gain); gain.connect(ctx.destination);
          osc.type = 'sine'; osc.frequency.value = note[0];
          gain.gain.setValueAtTime(0, ctx.currentTime + note[1]);
          gain.gain.linearRampToValueAtTime(0.12, ctx.currentTime + note[1] + 0.04);
          gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + note[1] + 0.5);
          osc.start(ctx.currentTime + note[1]);
          osc.stop(ctx.currentTime + note[1] + 0.55);
        });
      }
      // Must await resume() — starting oscillators on a suspended context fails silently
      if (ctx.state === 'suspended') {
        ctx.resume().then(_doChime).catch(function() {});
      } else {
        _doChime();
      }
    } catch(e) {}
  }

  // ── Enable Notifications Banner ──────────────────────────────────────────
  // Shows once until user clicks — unlocks AudioContext + grants browser notif permission
  (function() {
    if (localStorage.getItem('sup_notif_enabled')) return;
    if (typeof Notification !== 'undefined' && Notification.permission === 'denied') return;
    setTimeout(function() {
      var b = document.getElementById('supNotifEnableBanner');
      if (b) b.style.display = 'flex';
    }, 2000);
  })();

  window._enableSupNotifs = function() {
    _unlockAudio();
    _requestNotifPermission();
    localStorage.setItem('sup_notif_enabled', '1');
    var b = document.getElementById('supNotifEnableBanner');
    if (b) b.style.display = 'none';
  };

  window._dismissSupNotifBanner = function(e) {
    e.stopPropagation();
    var b = document.getElementById('supNotifEnableBanner');
    if (b) b.style.display = 'none';
    // Not saved to localStorage — shows again on next page load
  };

  // ── Browser Notifications API ─────────────────────────────────────────────
  // Shows a system-level popup even when tab is minimised or on another screen
  var _browserNotifPermission = Notification.permission; // 'default','granted','denied'

  function _requestNotifPermission() {
    if (_browserNotifPermission === 'default') {
      Notification.requestPermission().then(function(result) {
        _browserNotifPermission = result;
      });
    }
  }

  // Request on first click so the prompt feels intentional, not intrusive
  document.addEventListener('click', _requestNotifPermission, { once: true, passive: true });

  function _showBrowserNotif(title, body, link) {
    if (_browserNotifPermission !== 'granted') return;
    try {
      var n = new Notification('OCSAPP — ' + title, {
        body: body,
        icon: '<?= url("assets/images/logo.png") ?>',
        tag: 'ocsapp-supplier',   // replaces previous notif so they don't stack
        requireInteraction: false,
        silent: false             // OS sound plays too (platform default)
      });
      if (link) {
        n.onclick = function() {
          window.focus();
          window.location.href = '<?= url("") ?>' + link.replace(/^\//, '');
          n.close();
        };
      }
      // Auto-close after 8 seconds
      setTimeout(function() { try { n.close(); } catch(e) {} }, 8000);
    } catch(e) {}
  }

  var _prevBellCount = -1;

  // Use SSE for instant notifications, fallback to 5s polling
  fetchNotifCount();
  (function startNotifStream() {
    if (typeof EventSource === 'undefined') {
      setInterval(fetchNotifCount, 5000);
      return;
    }
    function connect() {
      var es = new EventSource('<?= url("api/supplier/notifications/stream") ?>');
      es.onmessage = function(e) {
        try {
          var d = JSON.parse(e.data);
          if (typeof d.unread_count !== 'undefined') {
            var n = d.unread_count;
            if (_prevBellCount >= 0 && n > _prevBellCount) {
              playChimeFooter();
              // Show system notification with the actual message text
              fetch('<?= url("api/supplier/notifications") ?>?limit=1')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                  if (data.success && data.notifications && data.notifications.length) {
                    var notif = data.notifications[0];
                    if (!notif.is_read) {
                      _showBrowserNotif(notif.title, notif.message, notif.link);
                    }
                  }
                }).catch(function() {});
            }
            _prevBellCount = n;
            updateBadge(n);
            // Refresh ALL sidebar badges via the unified function
            if (typeof window._refreshAllSupplierBadges === 'function') {
              window._refreshAllSupplierBadges();
            }
            if (notifPanelOpen) fetchNotifications();
          }
        } catch(err) {}
      };
      es.addEventListener('reconnect', function() { es.close(); connect(); });
      es.onerror = function() { es.close(); setTimeout(connect, 5000); };
    }
    connect();
  })();
  </script>

  <!-- Enable Notifications Banner (shown once until user clicks) -->
  <div id="supNotifEnableBanner" onclick="window._enableSupNotifs()" style="display:none;position:fixed;bottom:24px;right:24px;z-index:9999;background:#00b207;color:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 4px 20px rgba(0,0,0,0.2);align-items:center;gap:12px;cursor:pointer;font-size:14px;font-weight:600;max-width:300px;animation:slideInBanner 0.4s ease;">
    <i class="fas fa-bell" style="font-size:18px;flex-shrink:0;"></i>
    <span style="flex:1;"><?= $fr ? 'Activer les notifications et le son' : 'Enable notifications &amp; sound' ?></span>
    <button onclick="window._dismissSupNotifBanner(event)" title="Dismiss" style="background:none;border:none;color:#fff;cursor:pointer;font-size:18px;line-height:1;padding:0;opacity:0.8;">&times;</button>
  </div>
  <style>
  @keyframes slideInBanner {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
  }
  </style>
</body>
</html>
