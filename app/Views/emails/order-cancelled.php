<?php
/**
 * Order cancelled - BILINGUAL (FR first, then EN). Rendered as PHP by EmailHelper::sendOrderCancelled().
 * Rewritten 2026-09-28: it went through sendTemplate(), which does not run PHP (raw source would have
 * been emailed); fr-CA amounts; the "continue shopping" button pointed to a dead /shop URL.
 * The refund wording is unchanged from the previous version.
 * Variables: $order (orders row), $user (email, first_name), $reason.
 */
$fmtMoney = fn($v, bool $fr) => $fr ? number_format((float) $v, 2, ',', "\u{00A0}") . "\u{00A0}$" : '$' . number_format((float) $v, 2);
$orderNo  = htmlspecialchars($order['order_number'] ?? '');
$first    = htmlspecialchars(html_entity_decode((string) ($user['first_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$reasonTx = trim(html_entity_decode((string) ($reason ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$shopUrl  = htmlspecialchars(rtrim(env('APP_URL', 'https://ocsapp.ca'), '/') . '/marketplace-central');
$header = 'background-color:#374151;padding:36px 30px;text-align:center;';
$btn    = 'display:inline-block;background-color:#00b207;color:#fff;font-size:15px;font-weight:700;text-decoration:none;padding:13px 28px;border-radius:10px;';
$box    = 'width:100%;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:0 0 18px;';
$section = function (bool $fr) use ($orderNo, $first, $reasonTx, $order, $fmtMoney, $shopUrl, $header, $btn, $box) {
    $total = $fmtMoney($order['total'] ?? 0, $fr);
    ob_start(); ?>
        <tr><td bgcolor="#374151" style="<?= $header ?>">
          <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:150px;height:auto;margin:0 auto 14px;display:block;">
          <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700;"><?= $fr ? 'Commande annulée' : 'Order cancelled' ?></h1>
        </td></tr>
        <tr><td style="padding:32px 30px 28px;">
          <p style="margin:0 0 14px;color:#374151;font-size:16px;"><?= $fr ? 'Bonjour' : 'Hi' ?><?= $first !== '' ? ' ' . $first : '' ?>,</p>
          <p style="margin:0 0 22px;color:#4b5563;font-size:15px;line-height:1.7;"><?= $fr ? 'Votre commande a été annulée.' : 'Your order has been cancelled.' ?></p>
          <table role="presentation" style="<?= $box ?>"><tr><td style="padding:16px 20px;font-size:14px;color:#374151;line-height:1.8;">
            <strong><?= $fr ? 'Commande' : 'Order' ?> :</strong> #<?= $orderNo ?><br>
            <strong><?= $fr ? 'Montant' : 'Amount' ?> :</strong> <?= $total ?>
            <?php if ($reasonTx !== ''): ?><br><strong><?= $fr ? "Motif d'annulation" : 'Reason' ?> :</strong> <?= htmlspecialchars($reasonTx) ?><?php endif; ?>
          </td></tr></table>
          <table role="presentation" style="<?= $box ?>"><tr><td style="padding:16px 20px;font-size:14px;color:#374151;line-height:1.7;">
            <strong><?= $fr ? 'Remboursement' : 'Refund' ?></strong><br>
            <?= $fr
              ? "Si vous avez déjà été débité, un remboursement complet de {$total} sera traité sur votre mode de paiement d'origine dans un délai de 5 à 7 jours ouvrables."
              : "If you were already charged, a full refund of {$total} will be processed to your original payment method within 5 to 7 business days." ?>
          </td></tr></table>
          <p style="margin:0;text-align:center;"><a href="<?= $shopUrl ?>" style="<?= $btn ?>"><?= $fr ? 'Continuer mes achats' : 'Continue shopping' ?></a></p>
        </td></tr>
    <?php return ob_get_clean();
};
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Commande annulée #<?= $orderNo ?> / Order cancelled #<?= $orderNo ?></title></head>
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
