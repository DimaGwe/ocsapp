<?php
/**
 * OCSAPP Phone window: browser softphone (Twilio Voice JS SDK).
 * Opened as a small separate window so calls survive page loads in the main admin window.
 * Talks to admin pages over BroadcastChannel('ocs-phone'): receives {type:'dial'}, sends {type:'alive'|'incoming'}.
 *
 * Vars: $agentName, $softphoneReady, $twilioNumber
 */
$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OCSAPP Phone</title>
  <?= csrfMeta() ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    :root { --green:#00b207; --dark:#111827; --muted:#6b7280; --line:#e5e7eb; --red:#dc2626; }
    * { box-sizing:border-box; }
    body { margin:0; font-family:'Segoe UI', Arial, sans-serif; background:#f3f4f6; color:var(--dark); min-height:100vh; display:flex; flex-direction:column; }
    header { background:var(--dark); color:white; padding:14px 16px; display:flex; align-items:center; gap:10px; }
    header .title { font-weight:700; font-size:15px; }
    header .sub { font-size:11px; color:rgba(255,255,255,.55); }
    .pill { margin-left:auto; border:none; border-radius:20px; padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; }
    .pill.on { background:#dcfce7; color:#166534; }
    .pill.off { background:#374151; color:#e5e7eb; }
    .pill .dot { width:8px; height:8px; border-radius:50%; background:currentColor; }
    main { flex:1; padding:16px; display:flex; flex-direction:column; gap:14px; }
    .card { background:white; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:16px; }
    .msg { font-size:12px; color:var(--muted); text-align:center; min-height:16px; }
    .msg.err { color:var(--red); }
    .num-input { width:100%; font-size:22px; text-align:center; padding:10px; border:1px solid var(--line); border-radius:10px; letter-spacing:.5px; }
    .keypad { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px; }
    .key { padding:12px 0; font-size:18px; font-weight:600; background:#f9fafb; border:1px solid var(--line); border-radius:10px; cursor:pointer; }
    .key small { display:block; font-size:9px; color:var(--muted); font-weight:500; letter-spacing:1px; }
    .key:active { background:#e5e7eb; }
    .btn { border:none; border-radius:30px; padding:13px 18px; font-size:15px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:8px; }
    .btn-call { background:var(--green); color:white; width:100%; margin-top:12px; }
    .btn-hang { background:var(--red); color:white; }
    .btn-ghost { background:#f3f4f6; color:var(--dark); }
    .btn:disabled { opacity:.5; cursor:not-allowed; }
    .who { text-align:center; }
    .who .avatar { width:64px; height:64px; border-radius:50%; background:var(--dark); color:white; font-size:24px; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; }
    .who .name { font-size:18px; font-weight:700; }
    .who .number { font-size:13px; color:var(--muted); margin-top:2px; }
    .who .state { font-size:13px; margin-top:10px; color:var(--muted); }
    .timer { font-variant-numeric:tabular-nums; font-weight:700; color:var(--green); font-size:20px; }
    .row { display:flex; gap:10px; justify-content:center; margin-top:16px; flex-wrap:wrap; }
    .ring { animation:ring 1s infinite; }
    @keyframes ring { 0%,100% { transform:rotate(0) } 25% { transform:rotate(-12deg) } 75% { transform:rotate(12deg) } }
    .outcomes { display:grid; grid-template-columns:repeat(2,1fr); gap:6px; margin-top:10px; }
    .oc { padding:9px 6px; border:2px solid var(--line); border-radius:9px; background:white; font-size:12px; font-weight:700; color:var(--muted); cursor:pointer; }
    .oc.sel { border-color:var(--green); color:#166534; background:#f0fdf4; }
    textarea { width:100%; margin-top:10px; padding:9px; border:1px solid var(--line); border-radius:9px; font-family:inherit; font-size:13px; resize:none; }
    .hidden { display:none !important; }
    .tiny { font-size:11px; color:var(--muted); text-align:center; }
  </style>
</head>
<body>
<header>
  <i class="fa-solid fa-headset" style="font-size:20px;color:var(--green);"></i>
  <div>
    <div class="title">OCSAPP Phone</div>
    <div class="sub"><?= htmlspecialchars($agentName) ?> · <?= htmlspecialchars($twilioNumber) ?></div>
  </div>
  <button type="button" class="pill off" id="presenceBtn" onclick="togglePresence()"><span class="dot"></span><span id="presenceLabel">Offline</span></button>
</header>

<main>
  <?php if (!$softphoneReady): ?>
    <div class="card"><p class="msg err">The browser phone is not set up on this server yet.</p></div>
  <?php else: ?>

  <!-- Idle: dial pad -->
  <div class="card" id="viewIdle">
    <input type="tel" class="num-input" id="dialInput" placeholder="Enter a number" autocomplete="off">
    <div class="keypad" id="keypadIdle"></div>
    <button type="button" class="btn btn-call" id="dialBtn" onclick="dialTyped()"><i class="fa-solid fa-phone"></i> Call</button>
    <p class="tiny" style="margin-top:10px;">Customers see <?= htmlspecialchars($twilioNumber) ?>. Keep this window open to receive calls.</p>
  </div>

  <!-- Incoming -->
  <div class="card hidden" id="viewIncoming">
    <div class="who">
      <div class="avatar ring"><i class="fa-solid fa-phone-volume"></i></div>
      <div class="name" id="inName"></div>
      <div class="number" id="inNumber"></div>
      <div class="state">Incoming call</div>
    </div>
    <div class="row">
      <button type="button" class="btn btn-hang" onclick="declineIncoming()"><i class="fa-solid fa-phone-slash"></i> Decline</button>
      <button type="button" class="btn btn-call" style="width:auto;margin-top:0;" onclick="answerIncoming()"><i class="fa-solid fa-phone"></i> Answer</button>
    </div>
  </div>

  <!-- In call (outgoing or answered) -->
  <div class="card hidden" id="viewCall">
    <div class="who">
      <div class="avatar" id="callAvatar"><i class="fa-solid fa-user"></i></div>
      <div class="name" id="callName"></div>
      <div class="number" id="callNumber"></div>
      <div class="state" id="callState">Calling...</div>
    </div>
    <div class="row">
      <button type="button" class="btn btn-ghost" id="muteBtn" onclick="toggleMute()"><i class="fa-solid fa-microphone"></i> Mute</button>
      <button type="button" class="btn btn-ghost" onclick="document.getElementById('keypadCall').classList.toggle('hidden')"><i class="fa-solid fa-table-cells"></i> Keypad</button>
    </div>
    <div class="keypad hidden" id="keypadCall"></div>
    <div class="row">
      <button type="button" class="btn btn-hang" style="width:100%;" onclick="hangUp()"><i class="fa-solid fa-phone-slash"></i> Hang up</button>
    </div>
  </div>

  <!-- Wrap-up -->
  <div class="card hidden" id="viewWrap">
    <div class="who">
      <div class="name" id="wrapName"></div>
      <div class="number" id="wrapInfo"></div>
    </div>
    <div class="outcomes" id="outcomes"></div>
    <textarea id="wrapNote" rows="2" placeholder="Quick note (optional)"></textarea>
    <div class="row">
      <button type="button" class="btn btn-ghost" onclick="finishWrap(false)">Skip</button>
      <button type="button" class="btn btn-call" style="width:auto;margin-top:0;" id="wrapSave" onclick="finishWrap(true)">Save outcome</button>
    </div>
  </div>

  <p class="msg" id="msg"></p>
  <?php endif; ?>
</main>

<?php if ($softphoneReady): ?>
<script src="https://cdn.jsdelivr.net/npm/@twilio/voice-sdk@2.18.5/dist/twilio.min.js"></script>
<script>
(function () {
  const CSRF_NAME = <?= json_encode($csrfName) ?>;
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const channel = 'BroadcastChannel' in window ? new BroadcastChannel('ocs-phone') : null;

  let device = null, online = false, call = null, incoming = null, heartbeat = null, timerInt = null;
  let current = null;   // {id, name, phone, type, contactId, email, direction, answeredAt}

  const OUTCOMES = [
    ['resolved', 'Resolved'], ['follow_up', 'Follow-up'], ['no_answer', 'No answer'], ['voicemail', 'Left voicemail'],
    ['wrong_number', 'Wrong number'], ['transferred', 'Transferred'], ['callback_scheduled', 'Callback'], ['other', 'Other']
  ];

  const $ = id => document.getElementById(id);
  function msg(text, err) { $('msg').textContent = text || ''; $('msg').className = 'msg' + (err ? ' err' : ''); }
  function show(view) { ['viewIdle', 'viewIncoming', 'viewCall', 'viewWrap'].forEach(v => $(v).classList.toggle('hidden', v !== view)); }
  function fmt(s) { s = Math.max(0, Math.floor(s)); return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0'); }
  function prettyPhone(p) { const d = String(p || '').replace(/\D/g, ''); return d.length === 11 && d[0] === '1' ? '+1 (' + d.substr(1, 3) + ') ' + d.substr(4, 3) + '-' + d.substr(7) : (p || ''); }

  async function api(url, body) {
    const opts = body === undefined ? {} : { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify(body) };
    const res = await fetch(url, opts);
    return res.json();
  }

  // ---------------------------------------------------------- keypads
  function buildKeypad(el, onKey) {
    const keys = [['1', ''], ['2', 'ABC'], ['3', 'DEF'], ['4', 'GHI'], ['5', 'JKL'], ['6', 'MNO'], ['7', 'PQRS'], ['8', 'TUV'], ['9', 'WXYZ'], ['*', ''], ['0', '+'], ['#', '']];
    el.innerHTML = keys.map(([k, s]) => `<button type="button" class="key" data-k="${k}">${k}<small>${s || '&nbsp;'}</small></button>`).join('');
    el.addEventListener('click', e => { const b = e.target.closest('.key'); if (b) onKey(b.dataset.k); });
  }
  buildKeypad($('keypadIdle'), k => { $('dialInput').value += k; $('dialInput').focus(); });
  buildKeypad($('keypadCall'), k => { if (call) call.sendDigits(k); });
  $('dialInput').addEventListener('keydown', e => { if (e.key === 'Enter') dialTyped(); });

  // ---------------------------------------------------------- device + presence
  async function goOnline() {
    msg('Connecting...');
    const t = await api('/api/twilio/token');
    if (!t.success) { msg(t.error || 'Could not get a phone token.', true); return; }

    device = new Twilio.Device(t.token, { codecPreferences: ['opus', 'pcmu'], closeProtection: 'A call is in progress. Leave anyway?', logLevel: 'warn' });
    device.on('registered', () => { setOnline(true); msg('Online. Calls to the OCSAPP number ring here.'); });
    device.on('unregistered', () => setOnline(false));
    device.on('error', e => msg((e && e.message) ? e.message : 'Phone error', true));
    device.on('incoming', onIncoming);
    device.on('tokenWillExpire', async () => { const r = await api('/api/twilio/token'); if (r.success) device.updateToken(r.token); });
    await device.register();

    if ('Notification' in window && Notification.permission === 'default') { try { Notification.requestPermission(); } catch (e) {} }
  }

  async function goOffline() {
    if (call) { msg('Hang up first.', true); return; }
    if (device) { device.destroy(); device = null; }
    setOnline(false);
    await api('/api/twilio/presence', { online: 0 });
    msg('Offline. Incoming calls will not ring here.');
  }

  function setOnline(on) {
    online = on;
    $('presenceBtn').className = 'pill ' + (on ? 'on' : 'off');
    $('presenceLabel').textContent = on ? 'Online' : 'Offline';
    clearInterval(heartbeat);
    if (on) {
      api('/api/twilio/presence', { online: 1 });
      heartbeat = setInterval(() => api('/api/twilio/presence', { online: 1 }), 30000);
    }
    announce();
  }

  window.togglePresence = () => online ? goOffline() : goOnline();

  // Tell admin pages we exist (so their Call buttons send dials here)
  function announce() { if (channel) channel.postMessage({ type: 'alive', online, busy: !!(call || incoming) }); }
  setInterval(announce, 4000);

  window.addEventListener('pagehide', () => {
    if (!online) return;
    const fd = new FormData(); fd.append('online', '0'); fd.append(CSRF_NAME, CSRF);
    navigator.sendBeacon('/api/twilio/presence', fd);
  });

  // ---------------------------------------------------------- outgoing
  async function dial(c) {
    if (!device) { await goOnline(); if (!device) return; }
    if (call || incoming) { msg('Finish the current call first.', true); return; }

    msg('');
    const d = await api('/api/twilio/call', { mode: 'browser', phone: c.phone, name: c.name || '', contact_type: c.type || 'unknown', contact_id: c.id || 0, email: c.email || '', ticket_id: c.ticketId || 0 });
    if (!d.success) { msg(d.error || 'Could not start the call.', true); return; }

    current = { id: d.call_log_id, name: d.name || c.name || '', phone: d.to, type: d.contact_type || c.type || 'unknown', contactId: d.contact_id || c.id || 0, email: c.email || '', direction: 'outbound', answeredAt: null };
    showCall('Calling...');

    try {
      call = await device.connect({ params: { To: d.to, CallLogId: String(d.call_log_id) } });
    } catch (e) {
      msg((e && e.message) || 'Microphone blocked or call failed.', true); call = null; show('viewIdle'); return;
    }
    wireCall(call);
    call.on('ringing', () => { $('callState').textContent = 'Ringing...'; });
  }

  window.dialTyped = function () {
    const v = $('dialInput').value.trim();
    if (!v) return;
    dial({ phone: v });
  };

  // ---------------------------------------------------------- incoming
  function onIncoming(c) {
    if (call || incoming) { c.reject(); return; }   // already busy: let it ring the next agent
    incoming = c;
    const p = c.customParameters || new Map();
    current = { id: parseInt(p.get('callLogId') || '0', 10), name: p.get('callerName') || '', phone: p.get('callerNumber') || c.parameters.From || '', type: 'unknown', contactId: 0, email: '', direction: 'inbound', answeredAt: null };

    $('inName').textContent = current.name || prettyPhone(current.phone);
    $('inNumber').textContent = current.name ? prettyPhone(current.phone) : '';
    show('viewIncoming');
    announce();
    if (channel) channel.postMessage({ type: 'incoming', name: current.name, number: prettyPhone(current.phone) });
    if ('Notification' in window && Notification.permission === 'granted' && document.hidden) {
      try { new Notification('Incoming OCSAPP call', { body: current.name || prettyPhone(current.phone) }); } catch (e) {}
    }

    c.on('cancel', () => { incoming = null; current = null; show('viewIdle'); msg('Missed call (caller hung up or another agent answered).'); announce(); });
    c.on('disconnect', () => { if (incoming === c) { incoming = null; show('viewIdle'); announce(); } });
  }

  window.answerIncoming = function () {
    if (!incoming) return;
    call = incoming; incoming = null;
    wireCall(call);
    call.accept();
    showCall('Connecting...');
  };

  window.declineIncoming = function () {
    if (!incoming) return;
    incoming.reject(); incoming = null; current = null;
    show('viewIdle'); announce();
  };

  // ---------------------------------------------------------- active call
  function showCall(state) {
    $('callName').textContent = current.name || prettyPhone(current.phone);
    $('callNumber').textContent = current.name ? prettyPhone(current.phone) : '';
    $('callState').textContent = state;
    $('muteBtn').innerHTML = '<i class="fa-solid fa-microphone"></i> Mute';
    $('keypadCall').classList.add('hidden');
    show('viewCall');
    announce();
  }

  function wireCall(c) {
    c.on('accept', () => {
      current.answeredAt = Date.now();
      clearInterval(timerInt);
      timerInt = setInterval(() => { $('callState').innerHTML = 'Connected <span class="timer">' + fmt((Date.now() - current.answeredAt) / 1000) + '</span>'; }, 500);
    });
    c.on('disconnect', () => endCall());
    c.on('cancel', () => endCall());
    c.on('reject', () => endCall());
    c.on('error', e => msg((e && e.message) || 'Call error', true));
  }

  window.hangUp = function () { if (call) call.disconnect(); else endCall(); };

  window.toggleMute = function () {
    if (!call) return;
    const m = !call.isMuted();
    call.mute(m);
    $('muteBtn').innerHTML = m ? '<i class="fa-solid fa-microphone-slash"></i> Unmute' : '<i class="fa-solid fa-microphone"></i> Mute';
  };

  function endCall() {
    if (!current) { show('viewIdle'); return; }
    clearInterval(timerInt);
    const secs = current.answeredAt ? (Date.now() - current.answeredAt) / 1000 : 0;
    call = null;
    announce();
    if (!current.id) { current = null; show('viewIdle'); return; }

    $('wrapName').textContent = current.name || prettyPhone(current.phone);
    $('wrapInfo').textContent = current.answeredAt ? 'Call ended · ' + fmt(secs) : 'Not answered';
    $('wrapNote').value = '';
    const preset = current.answeredAt ? '' : 'no_answer';
    $('outcomes').innerHTML = OUTCOMES.map(([v, l]) => `<button type="button" class="oc${v === preset ? ' sel' : ''}" data-v="${v}">${l}</button>`).join('');
    show('viewWrap');
  }

  $('outcomes').addEventListener('click', e => {
    const b = e.target.closest('.oc'); if (!b) return;
    document.querySelectorAll('.oc').forEach(x => x.classList.toggle('sel', x === b));
  });

  window.finishWrap = async function (save) {
    if (save && current) {
      const sel = document.querySelector('.oc.sel');
      if (!sel) { msg('Pick an outcome, or Skip to do it later from the Call Log.', true); return; }
      const fd = new FormData();
      fd.append(CSRF_NAME, CSRF);
      fd.append('call_log_id', current.id);
      fd.append('direction', current.direction);
      fd.append('contact_type', current.type || 'unknown');
      fd.append('contact_id', current.contactId || 0);
      fd.append('contact_name', current.name || '');
      fd.append('contact_phone', current.phone || '');
      fd.append('contact_email', current.email || '');
      fd.append('outcome', sel.dataset.v);
      fd.append('notes', $('wrapNote').value.trim());
      $('wrapSave').disabled = true;
      try {
        const r = await (await fetch('/admin/call-log/store', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }, body: fd })).json();
        msg(r.success ? 'Outcome saved.' : (r.error || 'Could not save the outcome.'), !r.success);
      } catch (e) { msg('Network error saving the outcome.', true); }
      $('wrapSave').disabled = false;
    } else {
      msg('Saved to the Call Log without an outcome. You can add it there later.');
    }
    current = null;
    show('viewIdle');
    announce();
  };

  // ---------------------------------------------------------- dial requests from admin pages
  if (channel) {
    channel.onmessage = e => {
      const m = e.data || {};
      if (m.type === 'dial' && m.c) { window.focus(); dial(m.c); }
      if (m.type === 'ping') announce();
    };
  }

  // Start online right away; pick up a dial queued by the page that opened this window
  goOnline().then(() => {
    try {
      const pending = JSON.parse(localStorage.getItem('ocsPendingDial') || 'null');
      localStorage.removeItem('ocsPendingDial');
      if (pending && Date.now() - pending.at < 30000) dial(pending.c);
    } catch (e) {}
  });
})();
</script>
<?php endif; ?>
</body>
</html>
