<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel de vérification / Verification Reminder - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700;">{{title_fr}}</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Portail fournisseur OCSAPP</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour {{contact_person}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.6;">{{message_fr}}</p>

        <!-- Deadline Box -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:{{box_bg}};border-radius:10px;margin:0 0 24px;border:2px solid {{box_border}};">
        <tr><td style="padding:20px;text-align:center;">
            <p style="margin:0 0 6px;color:{{box_color}};font-size:14px;font-weight:600;">Date limite de vérification</p>
            <p style="margin:0;color:{{box_color}};font-size:22px;font-weight:700;">{{deadline_date}}</p>
            <p style="margin:8px 0 0;color:{{box_color}};font-size:14px;">{{days_remaining}} jours restants</p>
        </td></tr>
        </table>

        <p style="margin:0 0 24px;color:#4b5563;font-size:14px;line-height:1.6;">
            Pour compléter votre vérification, assurez-vous d'avoir téléversé les documents requis dans votre portail fournisseur. Notre équipe les examinera et approuvera votre compte.
        </p>

        <table role="presentation" style="width:100%;margin-bottom:24px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/login" style="display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Se connecter au portail
            </a>
        </td></tr>
        </table>

        <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.6;">
            Pour toute question ou si vous avez besoin de plus de temps, contactez-nous à <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;">info@ocsapp.ca</a>
        </p>
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
            <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700;">{{title_en}}</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">OCSAPP Supplier Portal</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hi {{contact_person}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.6;">{{message_en}}</p>

        <!-- Deadline Box -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:{{box_bg}};border-radius:10px;margin:0 0 24px;border:2px solid {{box_border}};">
        <tr><td style="padding:20px;text-align:center;">
            <p style="margin:0 0 6px;color:{{box_color}};font-size:14px;font-weight:600;">Verification Deadline</p>
            <p style="margin:0;color:{{box_color}};font-size:22px;font-weight:700;">{{deadline_date}}</p>
            <p style="margin:8px 0 0;color:{{box_color}};font-size:14px;">{{days_remaining}} days remaining</p>
        </td></tr>
        </table>

        <p style="margin:0 0 24px;color:#4b5563;font-size:14px;line-height:1.6;">
            To complete your verification, ensure you have uploaded the required documents in your supplier portal. Our team will review them and approve your account.
        </p>

        <table role="presentation" style="width:100%;margin-bottom:24px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/login" style="display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Log In to Your Portal
            </a>
        </td></tr>
        </table>

        <p style="margin:0;color:#6b7280;font-size:13px;line-height:1.6;">
            If you have any questions or need more time, please contact us at <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;">info@ocsapp.ca</a>
        </p>
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
