      </div>
    </main>
  </div>
  <script>
  // Language switcher
  (function() {
      var langBtn = document.getElementById('sellerLangBtn');
      var langDropdown = document.getElementById('sellerLangDropdown');
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

  document.addEventListener('click', function(e) {
      if (!e.target.closest('#notifWrapper')) {
          var panel = document.getElementById('notifPanel');
          if (panel) panel.classList.remove('open');
          notifPanelOpen = false;
      }
  });

  function fetchNotifCount() {
      fetch('<?= url("api/seller/notifications/count") ?>')
          .then(function(r) { return r.json(); })
          .then(function(data) {
              if (data.success) {
                  updateBadge(data.unread_count);
                  updateMsgBadge(data.unread_msg_count);
              }
          })
          .catch(function() {});
  }

  function fetchNotifications() {
      fetch('<?= url("api/seller/notifications") ?>?limit=10')
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
      if (!badge) return;
      if (count > 0) {
          badge.textContent = count > 9 ? '9+' : count;
          badge.style.display = 'flex';
          if (markAllBtn) markAllBtn.style.display = 'inline';
      } else {
          badge.style.display = 'none';
          if (markAllBtn) markAllBtn.style.display = 'none';
      }
  }

  function updateMsgBadge(count) {
      var badge = document.getElementById('msgNavBadge');
      if (!badge) return;
      if (count > 0) {
          badge.textContent = count > 99 ? '99+' : count;
          badge.classList.remove('hidden');
      } else {
          badge.classList.add('hidden');
      }
  }

  function renderNotifications(notifications) {
      var list = document.getElementById('notifList');
      if (!notifications.length) {
          list.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i>No notifications</div>';
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
      fetch('<?= url("api/seller/notifications/mark-read") ?>', {
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
      fetch('<?= url("api/seller/notifications/mark-all-read") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
          if (data.success) {
              updateBadge(0);
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

  fetchNotifCount();
  setInterval(fetchNotifCount, 10000);
  </script>
</body>
</html>
