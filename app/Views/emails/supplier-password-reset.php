<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe / Password Reset - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Réinitialisation de mot de passe</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Portail fournisseur OCSAPP</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour {{first_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Nous avons reçu une demande de réinitialisation du mot de passe pour votre compte fournisseur OCSAPP. Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe.
        </p>

        <table role="presentation" style="width:100%;margin:0 0 24px;">
        <tr><td align="center">
            <a href="{{reset_url}}" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Réinitialiser mon mot de passe
            </a>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;border-collapse:collapse;border-left:4px solid #f59e0b;background:#fffbeb;border-radius:4px;margin:0 0 24px;">
        <tr><td style="padding:16px;">
            <p style="margin:0;color:#92400e;font-size:14px;line-height:1.6;">
                <strong>Ce lien expire dans 1 heure.</strong> Si vous n'avez pas demandé de réinitialisation, ignorez ce courriel. Votre mot de passe ne sera pas modifié.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 8px;color:#4b5563;font-size:14px;line-height:1.6;">Si le bouton ci-dessus ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
        <p style="margin:0 0 24px;color:#00b207;font-size:13px;word-break:break-all;">{{reset_url}}</p>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">Pour toute question, contactez-nous à <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a>.</p>
        <p style="margin:16px 0 0;color:#6b7280;font-size:14px;">Cordialement,<br><strong style="color:#1f2937;">L'équipe OCSAPP</strong></p>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Password Reset</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">OCSAPP Supplier Portal</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hi {{first_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            We received a request to reset your password for your OCSAPP Supplier Portal account. Click the button below to set a new password.
        </p>

        <table role="presentation" style="width:100%;margin:0 0 24px;">
        <tr><td align="center">
            <a href="{{reset_url}}" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Reset My Password
            </a>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;border-collapse:collapse;border-left:4px solid #f59e0b;background:#fffbeb;border-radius:4px;margin:0 0 24px;">
        <tr><td style="padding:16px;">
            <p style="margin:0;color:#92400e;font-size:14px;line-height:1.6;">
                <strong>This link expires in 1 hour.</strong> If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 8px;color:#4b5563;font-size:14px;line-height:1.6;">If the button above doesn't work, copy and paste this link into your browser:</p>
        <p style="margin:0 0 24px;color:#00b207;font-size:13px;word-break:break-all;">{{reset_url}}</p>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">If you have any questions, contact us at <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a>.</p>
        <p style="margin:16px 0 0;color:#6b7280;font-size:14px;">Best regards,<br><strong style="color:#1f2937;">The OCSAPP Team</strong></p>
    </td></tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f9fafb;padding:24px 30px;text-align:center;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;">
            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">&copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.</p>
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
