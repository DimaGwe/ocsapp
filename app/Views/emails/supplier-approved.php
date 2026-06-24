<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande approuvée / Supplier Application Approved - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <div style="font-size:42px;margin-bottom:10px;">🎉</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Demande approuvée !</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Bienvenue dans le réseau fournisseur OCSAPP</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour {{first_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Excellente nouvelle ! Votre demande en tant que fournisseur pour <strong>{{company_name}}</strong> a été examinée et approuvée. Votre compte fournisseur est maintenant actif.
        </p>

        <!-- Portal Info FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin:0 0 24px;border:2px solid #00b207;">
        <tr><td style="padding:24px;">
            <h3 style="margin:0 0 16px;color:#00b207;font-size:16px;text-align:center;">Vos informations du portail fournisseur</h3>
            <table role="presentation" style="width:100%;">
                <tr>
                    <td style="padding:8px 0;color:#374151;font-size:14px;font-weight:600;width:40%;">Code fournisseur :</td>
                    <td style="padding:8px 0;"><code style="background:#1f2937;color:#34d399;padding:4px 10px;border-radius:4px;font-size:14px;font-family:monospace;">{{supplier_code}}</code></td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#374151;font-size:14px;font-weight:600;">Courriel :</td>
                    <td style="padding:8px 0;color:#00b207;font-size:14px;font-weight:600;">{{email}}</td>
                </tr>
                {{credentials_section}}
            </table>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin-bottom:24px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/login" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Accéder au portail fournisseur
            </a>
        </td></tr>
        </table>

        {{password_warning_section}}

        <h3 style="margin:0 0 14px;color:#1f2937;font-size:16px;">Pour commencer</h3>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:8px;margin-bottom:20px;">
        <tr><td style="padding:18px 20px;">
            <ul style="margin:0;padding-left:20px;color:#4b5563;font-size:14px;line-height:1.9;">
                <li>Connectez-vous à votre portail fournisseur</li>
                <li>Complétez votre profil fournisseur</li>
                <li>Téléchargez votre catalogue de produits</li>
                <li>Commencez à recevoir des bons de commande</li>
            </ul>
        </td></tr>
        </table>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">Besoin d'aide ? Contactez-nous à <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a></p>
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
            <div style="font-size:42px;margin-bottom:10px;">🎉</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Application Approved!</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Welcome to the OCSAPP Supplier Network</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hi {{first_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Great news! Your supplier application for <strong>{{company_name}}</strong> has been reviewed and approved. Your supplier account is now active and ready to use.
        </p>

        <!-- Portal Info EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin:0 0 24px;border:2px solid #00b207;">
        <tr><td style="padding:24px;">
            <h3 style="margin:0 0 16px;color:#00b207;font-size:16px;text-align:center;">Your Supplier Portal Details</h3>
            <table role="presentation" style="width:100%;">
                <tr>
                    <td style="padding:8px 0;color:#374151;font-size:14px;font-weight:600;width:40%;">Supplier Code:</td>
                    <td style="padding:8px 0;"><code style="background:#1f2937;color:#34d399;padding:4px 10px;border-radius:4px;font-size:14px;font-family:monospace;">{{supplier_code}}</code></td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#374151;font-size:14px;font-weight:600;">Email:</td>
                    <td style="padding:8px 0;color:#00b207;font-size:14px;font-weight:600;">{{email}}</td>
                </tr>
                {{credentials_section}}
            </table>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin-bottom:24px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/login" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Log In to Supplier Portal
            </a>
        </td></tr>
        </table>

        {{password_warning_section}}

        <h3 style="margin:0 0 14px;color:#1f2937;font-size:16px;">Getting Started</h3>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:8px;margin-bottom:20px;">
        <tr><td style="padding:18px 20px;">
            <ul style="margin:0;padding-left:20px;color:#4b5563;font-size:14px;line-height:1.9;">
                <li>Log in to your supplier portal</li>
                <li>Complete your supplier profile</li>
                <li>Upload your product catalog</li>
                <li>Start receiving purchase orders</li>
            </ul>
        </td></tr>
        </table>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">Need help? Contact us at <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a></p>
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
