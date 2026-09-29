<?php
/**
 * Order status update - BILINGUAL (FR first, then EN). Rendered as PHP by EmailHelper::sendOrderStatusUpdate().
 * Rewritten 2026-09-28: it used to go through sendTemplate(), which does not run PHP, so the raw
 * template source would have been emailed; status labels were for statuses that no longer exist
 * ("shipped"); the button linked to a dead /orders/{number} URL.
 * Variables: $order (orders row), $user (email, first_name), $old_status, $new_status.
 */
$fmtMoney = fn($v, bool $fr) => $fr ? number_format((float) $v, 2, ',', "\u{00A0}") . "\u{00A0}$" : '$' . number_format((float) $v, 2);
$orderNo  = htmlspecialchars($order['order_number'] ?? '');
$first    = htmlspecialchars(html_entity_decode((string) ($user['first_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$pickup   = ($order['fulfillment_type'] ?? '') === 'pickup';
$link     = htmlspecialchars(rtrim(env('APP_URL', 'https://ocsapp.ca'), '/') . '/account/orders/detail?id=' . (int) ($order['id'] ?? 0));
$labels = [
    'pending'          => ['En attente', 'Pending'],
    'confirmed'        => ['Confirmée', 'Confirmed'],
    'processing'       => ['En préparation', 'Being prepared'],
    'ready'            => ['Prête', 'Ready'],
    'out_for_delivery' => ['En livraison', 'Out for delivery'],
    'delivered'        => [$pickup ? 'Ramassée' : 'Livrée', $pickup ? 'Picked up' : 'Delivered'],
    'cancelled'        => ['Annulée', 'Cancelled'],
];
$messages = [
    'confirmed'        => ['Votre paiement est confirmé et votre commande a été transmise au commerce.', 'Your payment is confirmed and your order has been sent to the shop.'],
    'processing'       => ['Le commerce prépare votre commande.', 'The shop is preparing your order.'],
    'ready'            => $pickup
        ? ['Votre commande est prête à ramasser au commerce.', 'Your order is ready for pickup at the shop.']
        : ['Votre commande est prête. Un livreur ODA viendra la chercher sous peu.', 'Your order is ready. An ODA driver will pick it up shortly.'],
    'out_for_delivery' => ['Votre commande est en route vers vous.', 'Your order is on its way to you.'],
    'delivered'        => $pickup
        ? ['Votre commande a été ramassée. Merci de faire vos achats locaux avec OCSAPP !', 'Your order has been picked up. Thank you for shopping local with OCSAPP!']
        : ['Votre commande a été livrée. Merci de faire vos achats locaux avec OCSAPP !', 'Your order has been delivered. Thank you for shopping local with OCSAPP!'],
];
[$labelFr, $labelEn] = $labels[$new_status] ?? [htmlspecialchars((string) $new_status), htmlspecialchars((string) $new_status)];
[$msgFr, $msgEn]     = $messages[$new_status] ?? ['Le statut de votre commande a changé.', 'Your order status has changed.'];
$header = 'background-color:#00b207;background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;';
$btn    = 'display:inline-block;background-color:#00b207;color:#fff;font-size:15px;font-weight:700;text-decoration:none;padding:13px 28px;border-radius:10px;';
$box    = 'width:100%;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:0 0 22px;';
$section = function (bool $fr) use ($orderNo, $first, $labelFr, $labelEn, $msgFr, $msgEn, $order, $fmtMoney, $link, $header, $btn, $box) {
    ob_start(); ?>
        <tr><td bgcolor="#00b207" style="<?= $header ?>">
          <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:150px;height:auto;margin:0 auto 14px;display:block;">
          <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700;"><?= $fr ? 'Mise à jour de votre commande' : 'Your order update' ?></h1>
        </td></tr>
        <tr><td style="padding:32px 30px 28px;">
          <p style="margin:0 0 14px;color:#374151;font-size:16px;"><?= $fr ? 'Bonjour' : 'Hi' ?><?= $first !== '' ? ' ' . $first : '' ?>,</p>
          <p style="margin:0 0 22px;color:#4b5563;font-size:15px;line-height:1.7;"><?= $fr ? $msgFr : $msgEn ?></p>
          <table role="presentation" style="<?= $box ?>"><tr><td style="padding:16px 20px;font-size:14px;color:#374151;line-height:1.8;">
            <strong><?= $fr ? 'Commande' : 'Order' ?> :</strong> #<?= $orderNo ?><br>
            <strong><?= $fr ? 'Statut' : 'Status' ?> :</strong> <span style="color:#00920a;font-weight:700;"><?= $fr ? $labelFr : $labelEn ?></span><br>
            <strong><?= $fr ? 'Total' : 'Total' ?> :</strong> <?= $fmtMoney($order['total'] ?? 0, $fr) ?>
          </td></tr></table>
          <p style="margin:0;text-align:center;"><a href="<?= $link ?>" style="<?= $btn ?>"><?= $fr ? 'Voir ma commande' : 'View my order' ?></a></p>
        </td></tr>
    <?php return ob_get_clean();
};
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Commande #<?= $orderNo ?> : <?= $labelFr ?> / Order #<?= $orderNo ?>: <?= $labelEn ?></title></head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;"><tr><td align="center" style="padding:36px 16px;">
  <table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.08);">
    <?= $section(true) ?>
    <tr><td style="padding:0 30px;"><hr style="border:none;border-top:2px dashed #e5e7eb;margin:8px 0 10px;">
      <p style="text-align:center;margin:0 0 10px;font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:1.5px;text-transform:uppercase;">English version below / Version anglaise ci-dessous</p></td></tr>
    <?= $section(false) ?>
    <tr><td style="background:#f9fafb;padding:22px 30px;border-top:1px solid #e5e7eb;text-align:center;">
      <p style="margin:0 0 6px;font-size:12px;color:#9ca3af;">OCSAPP Inc. | Siège social : Laval, Québec (H7H) / Registered office: Laval, Quebec (H7H)</p>
      <p style="margin:0 0 6px;font-size:12px;color:#9ca3af;">Questions : <a href="mailto:info@ocsapp.ca" style="color:#9ca3af;">info@ocsapp.ca</a></p>
      <p style="margin:0;font-size:12px;color:#9ca3af;">Courriel transactionnel au sujet de votre commande. / Transactional email about your order.</p>
    </td></tr>
  </table>
</td></tr></table>
</body>
</html>
