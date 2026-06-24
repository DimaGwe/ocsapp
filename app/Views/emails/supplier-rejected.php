<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre demande / Application Update - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#6b7280 0%,#4b5563 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Mise à jour de votre demande</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">Concernant votre demande de fournisseur</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour {{first_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Merci de l'intérêt que vous portez à OCSAPP en tant que fournisseur. Après examen de votre demande pour <strong>{{company_name}}</strong>, nous ne pouvons pas l'approuver pour le moment.
        </p>

        <table role="presentation" style="width:100%;border-collapse:collapse;border-left:4px solid #ef4444;background:#fef2f2;border-radius:4px;margin:0 0 24px;">
        <tr><td style="padding:20px;">
            <p style="margin:0 0 8px;color:#991b1b;font-size:14px;font-weight:600;">Motif :</p>
            <p style="margin:0;color:#7f1d1d;font-size:14px;line-height:1.6;">{{reason}}</p>
            {{#notes}}
            <p style="margin:12px 0 0;color:#7f1d1d;font-size:14px;line-height:1.6;">{{notes}}</p>
            {{/notes}}
        </td></tr>
        </table>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Si vous pensez que cette décision a été prise par erreur, ou si vous disposez de documents supplémentaires pour appuyer votre candidature, n'hésitez pas à nous contacter. Nous serons heureux de reconsidérer votre demande.
        </p>

        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:8px;margin-bottom:20px;">
        <tr><td style="padding:20px;text-align:center;">
            <p style="margin:0 0 8px;color:#374151;font-size:14px;font-weight:600;">Des questions ? Contactez-nous :</p>
            <p style="margin:0;color:#4b5563;font-size:14px;">
                <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a>
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">Nous apprécions votre intérêt pour OCSAPP et vous souhaitons le meilleur.</p>
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
        <td style="background:linear-gradient(135deg,#6b7280 0%,#4b5563 100%);padding:36px 30px;text-align:center;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Application Update</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:14px;">Regarding your supplier application</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hi {{first_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Thank you for your interest in becoming a supplier on OCSAPP. After reviewing your application for <strong>{{company_name}}</strong>, we are unable to approve it at this time.
        </p>

        <table role="presentation" style="width:100%;border-collapse:collapse;border-left:4px solid #ef4444;background:#fef2f2;border-radius:4px;margin:0 0 24px;">
        <tr><td style="padding:20px;">
            <p style="margin:0 0 8px;color:#991b1b;font-size:14px;font-weight:600;">Reason:</p>
            <p style="margin:0;color:#7f1d1d;font-size:14px;line-height:1.6;">{{reason}}</p>
            {{#notes}}
            <p style="margin:12px 0 0;color:#7f1d1d;font-size:14px;line-height:1.6;">{{notes}}</p>
            {{/notes}}
        </td></tr>
        </table>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            If you believe this decision was made in error, or if you have additional documentation to support your application, please don't hesitate to reach out to us. We'd be happy to reconsider.
        </p>

        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:8px;margin-bottom:20px;">
        <tr><td style="padding:20px;text-align:center;">
            <p style="margin:0 0 8px;color:#374151;font-size:14px;font-weight:600;">Have questions? Contact us:</p>
            <p style="margin:0;color:#4b5563;font-size:14px;">
                <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a>
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">We appreciate your interest in OCSAPP and wish you the best.</p>
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
