      </div>
    </main>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('open');
    }

    function toggleAvailability(isOnline) {
      const toggle = document.getElementById('availabilitySwitch');
      const label  = document.getElementById('availabilityLabel');
      const status = isOnline ? 'available' : 'offline';

      // Disable toggle during request to prevent double-clicks
      toggle.disabled = true;
      label.textContent = '...';
      label.className = 'availability-label';

      fetch('<?= url('delivery/availability') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: 'status=' + encodeURIComponent(status)
      })
      .then(r => r.json())
      .then(data => {
        toggle.disabled = false;
        if (data.success) {
          label.textContent = isOnline ? '<?= $fr ? 'En ligne' : 'Online' ?>' : '<?= $fr ? 'Hors ligne' : 'Offline' ?>';
          label.className = 'availability-label' + (isOnline ? ' online' : '');
        } else {
          // Revert the toggle
          toggle.checked = !isOnline;
          label.textContent = isOnline ? '<?= $fr ? 'Hors ligne' : 'Offline' ?>' : '<?= $fr ? 'En ligne' : 'Online' ?>';
          label.className = 'availability-label' + (!isOnline ? ' online' : '');
          if (data.error) {
            alert(data.error);
          }
          if (data.redirect) {
            window.location.href = data.redirect;
          }
        }
      })
      .catch(() => {
        toggle.disabled = false;
        // Revert on network error
        toggle.checked = !isOnline;
        label.textContent = isOnline ? '<?= $fr ? 'Hors ligne' : 'Offline' ?>' : '<?= $fr ? 'En ligne' : 'Online' ?>';
        label.className = 'availability-label' + (!isOnline ? ' online' : '');
      });
    }

    // ── Driver Notification Bell ──────────────────────────────────────────────
    (function() {
      const _driverFr = <?= $fr ? 'true' : 'false' ?>;

      function escHtml(str) {
        return String(str)
          .replace(/&/g,'&amp;').replace(/</g,'&lt;')
          .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }

      function formatNotifTime(dateStr) {
        const d   = new Date(dateStr.replace(' ', 'T') + 'Z');
        const now = new Date();
        const sec = Math.floor((now - d) / 1000);
        if (sec < 60)  return _driverFr ? "À l'instant" : 'Just now';
        if (sec < 3600) {
          const m = Math.floor(sec / 60);
          return _driverFr ? `Il y a ${m} min` : `${m}m ago`;
        }
        if (sec < 86400) {
          const h = Math.floor(sec / 3600);
          return _driverFr ? `Il y a ${h} h` : `${h}h ago`;
        }
        const days = Math.floor(sec / 86400);
        return _driverFr ? `Il y a ${days} j` : `${days}d ago`;
      }

      function renderDriverNotifications(notifications) {
        const list = document.getElementById('driverNotifList');
        const markAllBtn = document.getElementById('driverNotifMarkAllBtn');
        if (!list) return;

        if (!notifications || notifications.length === 0) {
          list.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i>' +
            (escHtml(_driverFr ? 'Aucune notification' : 'No notifications')) + '</div>';
          if (markAllBtn) markAllBtn.style.display = 'none';
          return;
        }

        const hasUnread = notifications.some(n => !n.is_read);
        if (markAllBtn) markAllBtn.style.display = hasUnread ? '' : 'none';

        const iconMap = { urgent: 'urgent', warning: 'warning', info: 'info' };
        const faMap   = { urgent: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };

        list.innerHTML = notifications.map(function(n) {
          const cls    = iconMap[n.type] || 'info';
          const fa     = faMap[n.type]   || 'fa-bell';
          const unread = !n.is_read ? ' unread' : '';
          return '<div class="notif-item' + unread + '" onclick="markDriverNotifRead(' + n.id + ', this)">' +
            '<div class="notif-item-icon ' + cls + '"><i class="fas ' + fa + '"></i></div>' +
            '<div class="notif-item-content">' +
              '<div class="notif-item-msg">' + escHtml(n.message) + '</div>' +
              '<div class="notif-item-time">' + escHtml(formatNotifTime(n.created_at)) + '</div>' +
            '</div>' +
            '</div>';
        }).join('');
      }

      window.fetchDriverNotifications = function() {
        fetch('<?= url('api/driver/notifications/inbox') ?>?limit=10', {
          headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '' }
        })
        .then(function(r) { return r.ok ? r.json() : null; })
        .then(function(data) {
          if (!data || !data.success) return;
          renderDriverNotifications(data.notifications);
          updateDriverBadge(data.unread_count);
        })
        .catch(function() {});
      };

      window.fetchDriverNotifCount = function() {
        fetch('<?= url('api/driver/notifications/count') ?>', {
          headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '' }
        })
        .then(function(r) { return r.ok ? r.json() : null; })
        .then(function(data) { if (data && data.success) updateDriverBadge(data.unread_count); })
        .catch(function() {});
      };

      window.updateDriverBadge = function(count) {
        const badge = document.getElementById('driverNotifBadge');
        if (!badge) return;
        if (count > 0) {
          badge.textContent = count > 99 ? '99+' : count;
          badge.style.display = '';
        } else {
          badge.style.display = 'none';
        }
      };

      window.toggleDriverNotifPanel = function() {
        const panel = document.getElementById('driverNotifPanel');
        if (!panel) return;
        const isOpen = panel.classList.toggle('open');
        if (isOpen) fetchDriverNotifications();
      };

      window.markDriverNotifRead = function(id, el) {
        if (!el || !el.classList.contains('unread')) return;
        el.classList.remove('unread');
        fetch('<?= url('api/driver/notifications/mark-read') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
          },
          body: JSON.stringify({ id: id })
        })
        .then(function(r) { return r.ok ? r.json() : null; })
        .then(function(data) { if (data && data.success) updateDriverBadge(data.unread_count); })
        .catch(function() {});
      };

      window.markAllDriverNotifRead = function() {
        fetch('<?= url('api/driver/notifications/mark-all-read') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
          },
          body: '{}'
        })
        .then(function(r) { return r.ok ? r.json() : null; })
        .then(function(data) {
          if (!data || !data.success) return;
          updateDriverBadge(0);
          document.querySelectorAll('#driverNotifList .notif-item.unread').forEach(function(el) {
            el.classList.remove('unread');
          });
          const markAllBtn = document.getElementById('driverNotifMarkAllBtn');
          if (markAllBtn) markAllBtn.style.display = 'none';
        })
        .catch(function() {});
      };

      // Close panel on outside click
      document.addEventListener('click', function(e) {
        var wrapper = document.getElementById('driverNotifWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
          var panel = document.getElementById('driverNotifPanel');
          if (panel) panel.classList.remove('open');
        }
      });

      // Poll count every 30s
      fetchDriverNotifCount();
      setInterval(fetchDriverNotifCount, 30000);
    })();

    // Language switcher
    (function() {
      var langBtn = document.getElementById('driverLangBtn');
      var langDropdown = document.getElementById('driverLangDropdown');
      if (!langBtn || !langDropdown) return;

      langBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        langDropdown.classList.toggle('open');
      });

      document.addEventListener('click', function() {
        langDropdown.classList.remove('open');
      });

      document.querySelectorAll('#driverLangDropdown .lang-option').forEach(function(option) {
        option.addEventListener('click', function() {
          var newLang = this.dataset.lang;
          fetch('<?= url('set-language') ?>', {
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
          .catch(function() {});
        });
      });
    })();
  </script>
</body>
</html>
