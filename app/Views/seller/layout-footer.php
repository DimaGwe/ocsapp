    </main>
  </div>
</div>
<script>
(function () {
  var CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
  var T = <?= json_encode($fr ? [
      'none' => 'Aucune notification', 'now' => "À l'instant",
  ] : [
      'none' => 'No notifications', 'now' => 'Just now',
  ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>;
  var FR = <?= $fr ? 'true' : 'false' ?>;

  // Language switch (FR / EN)
  document.querySelectorAll('.sp-lang .lang-option').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (btn.classList.contains('active')) return;
      fetch(<?= json_encode(url('set-language')) ?>, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ language: btn.dataset.lang, <?= json_encode(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>: CSRF })
      }).then(function (r) { return r.json(); })
        .then(function (d) { if (d.success) window.location.reload(); })
        .catch(function () {});
    });
  });

  // Notifications
  var panelOpen = false;
  window.toggleNotifPanel = function () {
    panelOpen = !panelOpen;
    document.getElementById('notifPanel').classList.toggle('open', panelOpen);
    if (panelOpen) fetchNotifications();
  };
  document.addEventListener('click', function (e) {
    if (!e.target.closest('#notifWrapper')) {
      document.getElementById('notifPanel').classList.remove('open');
      panelOpen = false;
    }
  });
  function post(url, body) {
    return fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF }, body: JSON.stringify(body || {}) })
      .then(function (r) { return r.json(); });
  }
  function updateBadge(count) {
    var b = document.getElementById('notifBadge'), all = document.getElementById('notifMarkAllBtn');
    if (count > 0) { b.textContent = count > 9 ? '9+' : count; b.style.display = 'flex'; all.style.display = 'inline'; }
    else { b.style.display = 'none'; all.style.display = 'none'; }
  }
  function updateMsgBadge(count) {
    var b = document.getElementById('msgNavBadge');
    if (!b) return;
    if (count > 0) { b.textContent = count > 99 ? '99+' : count; b.classList.remove('hidden'); } else { b.classList.add('hidden'); }
  }
  function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }
  function ago(str) {
    var d = new Date(String(str).replace(' ', 'T')), diff = Math.floor((Date.now() - d) / 1000);
    if (isNaN(diff) || diff < 60) return T.now;
    if (diff < 3600) return FR ? 'il y a ' + Math.floor(diff / 60) + ' min' : Math.floor(diff / 60) + ' min ago';
    if (diff < 86400) return FR ? 'il y a ' + Math.floor(diff / 3600) + ' h' : Math.floor(diff / 3600) + ' h ago';
    if (diff < 604800) return FR ? 'il y a ' + Math.floor(diff / 86400) + ' j' : Math.floor(diff / 86400) + ' d ago';
    return d.toLocaleDateString(FR ? 'fr-CA' : 'en-CA', { month: 'short', day: 'numeric' });
  }
  function fetchCount() {
    fetch(<?= json_encode(url('api/seller/notifications/count')) ?>).then(function (r) { return r.json(); })
      .then(function (d) { if (d.success) { updateBadge(d.unread_count); updateMsgBadge(d.unread_msg_count); } }).catch(function () {});
  }
  function fetchNotifications() {
    fetch(<?= json_encode(url('api/seller/notifications')) ?> + '?limit=10').then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d.success) return;
        updateBadge(d.unread_count);
        var list = document.getElementById('notifList');
        if (!d.notifications.length) { list.innerHTML = '<div class="sp-notif-empty">' + T.none + '</div>'; return; }
        list.innerHTML = d.notifications.map(function (n) {
          var link = n.link ? <?= json_encode(url('')) ?> + String(n.link).replace(/^\//, '') : '#';
          return '<a href="' + esc(link) + '" class="sp-notif-item' + (n.is_read == 0 ? ' unread' : '') + '" data-id="' + (+n.id) + '">'
            + '<div class="sp-notif-ico"><i class="fa-solid fa-' + esc(n.icon || 'bell') + '"></i></div>'
            + '<div><div class="sp-notif-title">' + esc(n.title) + '</div><div class="sp-notif-msg">' + esc(n.message) + '</div>'
            + '<div class="sp-notif-time">' + esc(ago(n.created_at)) + '</div></div></a>';
        }).join('');
        list.querySelectorAll('.sp-notif-item').forEach(function (a) {
          a.addEventListener('click', function () { post(<?= json_encode(url('api/seller/notifications/mark-read')) ?>, { id: +a.dataset.id }).catch(function () {}); });
        });
      }).catch(function () {});
  }
  window.markAllNotifRead = function () {
    post(<?= json_encode(url('api/seller/notifications/mark-all-read')) ?>).then(function (d) {
      if (d.success) { updateBadge(0); document.querySelectorAll('.sp-notif-item.unread').forEach(function (el) { el.classList.remove('unread'); }); }
    }).catch(function () {});
  };
  fetchCount();
  setInterval(fetchCount, 30000);
})();
</script>
</body>
</html>
