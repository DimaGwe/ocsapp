<?php
/** Status-change forms (.js-status-form) on the seller order list and detail pages. Needs: $fr. */
?>
<script>
(function () {
  var T = <?= json_encode($fr ? [
      'confirmCancel' => 'Annuler cette commande ? Le client sera avisé.',
      'failed'        => "Le statut n'a pas pu être modifié. Rechargez la page et réessayez.",
      'transition'    => "Ce changement de statut n'est plus possible. Rechargez la page.",
  ] : [
      'confirmCancel' => 'Cancel this order? The customer will be notified.',
      'failed'        => "The status couldn't be changed. Reload the page and try again.",
      'transition'    => 'This status change is no longer possible. Reload the page.',
  ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>;
  document.querySelectorAll('.js-status-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var status = form.querySelector('[name="status"]').value;
      if (status === 'cancelled' && !confirm(T.confirmCancel)) return;
      var btn = form.querySelector('button[type="submit"]');
      btn.disabled = true;
      fetch(form.action, { method: 'POST', body: new FormData(form) })
        .then(function (r) { return r.json(); })
        .then(function (d) {
          if (d.success) { window.location.reload(); return; }
          // The endpoint answers in English; show our own bilingual text
          alert(d.code === 'payment_required' ? d.message : (/Cannot change order status/.test(d.message || '') ? T.transition : T.failed));
          btn.disabled = false;
        })
        .catch(function () { alert(T.failed); btn.disabled = false; });
    });
  });
})();
</script>
