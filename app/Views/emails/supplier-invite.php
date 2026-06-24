<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation fournisseur / Supplier Invitation - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <div style="font-size:42px;margin-bottom:10px;">✉️</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Vous êtes invité !</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Portail fournisseur OCSAPP</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour,</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Vous avez été invité à rejoindre <strong style="color:#00b207;">OCSAPP</strong> en tant que partenaire fournisseur. Nous sommes ravis de vous accueillir dans notre réseau de livraison d'épicerie zéro émission !
        </p>

        <!-- Benefits FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin:0 0 24px;border:2px solid #00b207;">
        <tr><td style="padding:22px 24px;">
            <p style="margin:0 0 12px;font-size:14px;font-weight:700;color:#007a05;">En tant que fournisseur OCSAPP, vous pourrez :</p>
            <ul style="margin:0;padding-left:20px;color:#166534;font-size:14px;line-height:2;">
                <li>Lister vos produits dans notre catalogue en ligne</li>
                <li>Recevoir et gérer des bons de commande</li>
                <li>Suivre l'exécution des commandes et les livraisons</li>
                <li>Accéder à vos analyses et rapports fournisseur</li>
            </ul>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin:0 0 20px;">
        <tr><td align="center">
            <a href="<?= htmlspecialchars($inviteUrl) ?>"
               style="display:inline-block;padding:16px 44px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:700;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Accepter l'invitation
            </a>
        </td></tr>
        </table>

        <!-- Expiry notice FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:20px;">
        <tr><td style="padding:14px 18px;">
            <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
                <strong>⏰ Important :</strong> Ce lien d'invitation expirera dans <strong>7 jours</strong>. Veuillez compléter votre inscription avant cette date.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 16px;font-size:12px;color:#9ca3af;line-height:1.6;">
            Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
            <a href="<?= htmlspecialchars($inviteUrl) ?>" style="color:#00b207;word-break:break-all;"><?= htmlspecialchars($inviteUrl) ?></a>
        </p>

        <p style="margin:0;color:#6b7280;font-size:14px;">Des questions ? Écrivez-nous à <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;">info@ocsapp.ca</a></p>
    </td></tr>

    <!-- Language Divider -->
    <tr><td style="padding:0 30px;">
        <table role="presentation" style="width:100%;border-collapse:collapse;">
        <tr><td style="padding:24px 0 8px;text-align:center;">
            <hr style="border:none;border-top:2px dashed #e5e7eb;margin:0 0 12px;">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">
                🇬🇧 English version follows below / La version française précède
            </span>
            <hr style="border:none;border-top:2px dashed #e5e7eb;margin:12px 0 0;">
        </td></tr>
        </table>
    </td></tr>

    <!-- English Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <div style="font-size:42px;margin-bottom:10px;">✉️</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">You're Invited!</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">OCSAPP Supplier Portal</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hello,</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            You've been invited to join <strong style="color:#00b207;">OCSAPP</strong> as a supplier partner. We're excited to have you in our zero-emission grocery delivery network!
        </p>

        <!-- Benefits EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin:0 0 24px;border:2px solid #00b207;">
        <tr><td style="padding:22px 24px;">
            <p style="margin:0 0 12px;font-size:14px;font-weight:700;color:#007a05;">As an OCSAPP supplier, you'll be able to:</p>
            <ul style="margin:0;padding-left:20px;color:#166534;font-size:14px;line-height:2;">
                <li>List your products in our online marketplace catalog</li>
                <li>Receive and manage purchase orders</li>
                <li>Track order fulfillment and deliveries</li>
                <li>Access your supplier analytics and reports</li>
            </ul>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin:0 0 20px;">
        <tr><td align="center">
            <a href="<?= htmlspecialchars($inviteUrl) ?>"
               style="display:inline-block;padding:16px 44px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:700;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Accept Invitation
            </a>
        </td></tr>
        </table>

        <!-- Expiry notice EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:20px;">
        <tr><td style="padding:14px 18px;">
            <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
                <strong>⏰ Note:</strong> This invitation link will expire in <strong>7 days</strong>. Please complete your registration before then.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 16px;font-size:12px;color:#9ca3af;line-height:1.6;">
            If the button doesn't work, copy and paste this link into your browser:<br>
            <a href="<?= htmlspecialchars($inviteUrl) ?>" style="color:#00b207;word-break:break-all;"><?= htmlspecialchars($inviteUrl) ?></a>
        </p>

        <p style="margin:0 0 6px;color:#6b7280;font-size:14px;">Questions? Contact us at <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;">info@ocsapp.ca</a></p>
        <p style="margin:16px 0 0;color:#6b7280;font-size:14px;">Best regards,<br><strong style="color:#1f2937;">The OCSAPP Team</strong></p>
    </td></tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f9fafb;padding:24px 30px;text-align:center;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;">
            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">&copy; <?= date('Y') ?> OCSAPP. Tous droits réservés. / All rights reserved.</p>
            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">Courriel automatique - ne pas répondre. / Automated email - do not reply.</p>
            <p style="margin:0;color:#9ca3af;font-size:12px;">
                <a href="https://ocsapp.ca/terms" style="color:#6b7280;text-decoration:none;">Terms</a> &bull;
                <a href="https://ocsapp.ca/privacy" style="color:#6b7280;text-decoration:none;">Privacy</a>
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
