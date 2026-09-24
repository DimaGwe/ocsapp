<?php
/**
 * Admin click-to-call bar (Twilio bridge). Included once by admin/layout.php.
 *
 *   ocsCall({phone, name, type, id, email, ticketId})
 *
 * Twilio rings the agent's own phone first ("press 1 to connect"), then the contact.
 * When the call ends, the Log a Call form opens pre-filled and linked to the call.
 */
$__twilioReady = false;
try {
    require_once BASE_PATH . '/app/Helpers/TwilioHelper.php';
    $__twilioReady = \App\Helpers\TwilioHelper::isConfigured();
} catch (\Throwable $e) {
    $__twilioReady = false;
}
?>
<style>
#ocsCallBar { display:none; position:fixed; right:28px; bottom:92px; z-index:9005; width:300px; background:#111827; color:white; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.3); padding:14px 16px; font-size:13px; }
#ocsCallBar .ocb-top { display:flex; align-items:center; gap:10px; }
#ocsCallBar .ocb-icon { width:34px; height:34px; border-radius:50%; background:#00b207; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
#ocsCallBar .ocb-icon.ended { background:#6b7280; }
#ocsCallBar .ocb-name { font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
#ocsCallBar .ocb-phone { font-size:11px; color:rgba(255,255,255,.55); }
#ocsCallBar .ocb-status { margin-top:10px; color:rgba(255,255,255,.85); line-height:1.4; }
#ocsCallBar .ocb-timer { font-variant-numeric:tabular-nums; font-weight:700; color:#4ade80; }
#ocsCallBar .ocb-close { margin-left:auto; background:rgba(255,255,255,.1); border:none; color:white; width:26px; height:26px; border-radius:50%; cursor:pointer; }
@media (max-width: 480px) { #ocsCallBar { right:12px; left:12px; width:auto; } }
</style>

<div id="ocsCallBar" role="status" aria-live="polite">
  <div class="ocb-top">
    <div class="ocb-icon" id="ocbIcon"><i class="fa-solid fa-phone"></i></div>
    <div style="min-width:0;">
      <div class="ocb-name" id="ocbName"></div>
      <div class="ocb-phone" id="ocbPhone"></div>
    </div>
    <button type="button" class="ocb-close" id="ocbClose" title="Hide" onclick="ocsCallBarHide()">&times;</button>
  </div>
  <div class="ocb-status" id="ocbStatus"></div>
</div>

<script>
(function () {
  const READY = <?= $__twilioReady ? 'true' : 'false' ?>;
  let pollTimer = null;
  let current = null;

  const STATUS_TEXT = {
    agent_ringing:   'Calling your phone... answer it, then press 1.',
    agent_answered:  'Press 1 on your phone to connect.',
    contact_ringing: 'Ringing the contact...',
    in_progress:     'Connected',
    completed:       'Call ended',
    no_answer:       'No answer',
    busy:            'Line busy',
    failed:          'Call failed',
    canceled:        'Call canceled',
    agent_no_answer: 'You did not pick up, so the contact was not called.',
    agent_declined:  'Not connected (1 was not pressed).'
  };

  function fmt(s) {
    s = Math.max(0, parseInt(s || 0, 10));
    return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
  }

  function show(name, phone, text, ended) {
    document.getElementById('ocbName').textContent = name || phone || 'Call';
    document.getElementById('ocbPhone').textContent = name ? phone : '';
    document.getElementById('ocbStatus').innerHTML = text;
    document.getElementById('ocbIcon').classList.toggle('ended', !!ended);
    document.getElementById('ocsCallBar').style.display = 'block';
  }

  window.ocsCallBarHide = function () {
    document.getElementById('ocsCallBar').style.display = 'none';
  };

  window.ocsCallReady = READY;

  /**
   * Start a Twilio bridge call. Falls back to the phone's dialer when calling is not set up.
   */
  window.ocsCall = async function (c) {
    c = c || {};
    if (!c.phone) return;

    if (!READY) {
      window.location.href = 'tel:' + c.phone;
      return;
    }
    if (current && !current.final) {
      show(current.name, current.phone, 'A call is already in progress.', false);
      return;
    }

    show(c.name, c.phone, 'Starting call...', false);

    try {
      const res = await fetch('/api/twilio/call', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          phone: c.phone, name: c.name || '', contact_type: c.type || 'unknown',
          contact_id: c.id || 0, email: c.email || '', ticket_id: c.ticketId || 0
        })
      });
      const d = await res.json();

      if (!d.success) {
        const link = d.code === 'no_agent_phone' ? ' <a href="/admin/agent-dashboard" style="color:#4ade80;">Set it now</a>' : '';
        show(c.name, c.phone, (d.error || 'Could not start the call.') + link, true);
        return;
      }

      current = { id: d.call_log_id, name: c.name, phone: c.phone, final: false };
      show(c.name, c.phone, 'Calling your phone (' + d.agent_phone + ')... answer it, then press 1.', false);
      poll();
    } catch (e) {
      show(c.name, c.phone, 'Network error. The call was not started.', true);
    }
  };

  function poll() {
    clearTimeout(pollTimer);
    pollTimer = setTimeout(async () => {
      if (!current) return;
      try {
        const res = await fetch('/api/twilio/call-state?id=' + current.id);
        const d = await res.json();
        if (!d.success) return;

        let text = STATUS_TEXT[d.status] || d.status;
        if (d.status === 'in_progress' && d.elapsed !== null) {
          text = 'Connected <span class="ocb-timer">' + fmt(d.elapsed) + '</span>';
        }
        if (d.status === 'completed' && d.duration !== null) {
          text = 'Call ended <span class="ocb-timer">' + fmt(d.duration) + '</span>';
        }
        show(current.name, current.phone, text, d.final);

        if (d.final) {
          current.final = true;
          // Nobody was reached on the agent side: nothing to log
          if (d.status === 'agent_no_answer' || d.status === 'agent_declined') return;

          setTimeout(() => {
            ocsCallBarHide();
            if (typeof openDispositionModal === 'function') {
              const c = d.contact || {};
              openDispositionModal(c.name || '', c.phone || '', c.type || 'unknown', c.id || 0, c.email || '', {
                callLogId: d.id, direction: 'outbound', outcome: d.suggested_outcome, duration: d.duration
              });
            }
          }, 1200);
          return;
        }
      } catch (e) { /* keep polling */ }
      poll();
    }, 2000);
  }

  // Any element with data-ocs-call="+15145550100" (plus optional data-name/-type/-id/-email/-ticket) becomes a Call button
  document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-ocs-call]');
    if (!el || !READY) return;
    e.preventDefault();
    ocsCall({
      phone: el.dataset.ocsCall, name: el.dataset.name || '', type: el.dataset.type || 'unknown',
      id: parseInt(el.dataset.id || '0', 10), email: el.dataset.email || '', ticketId: parseInt(el.dataset.ticket || '0', 10)
    });
  });
})();
</script>
