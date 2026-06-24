<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature reçue / Application Received - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <div style="font-size:42px;margin-bottom:10px;">📋</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Candidature soumise !</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Portail fournisseur OCSAPP</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour <?= htmlspecialchars($contactPerson) ?>,</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Merci ! Votre candidature pour rejoindre <strong style="color:#00b207;">OCSAPP</strong> en tant que fournisseur a bien été reçue. Notre équipe l'examinera dans les <strong>2 à 3 jours ouvrables</strong>.
        </p>

        <!-- Account details FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin:0 0 20px;border:2px solid #00b207;">
        <tr><td style="padding:22px 24px;">
            <p style="margin:0 0 14px;font-size:14px;font-weight:700;color:#007a05;">📋 Détails de votre compte</p>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;width:45%;">Entreprise :</td>
                    <td style="padding:7px 0;color:#166534;font-size:13px;"><?= htmlspecialchars($companyName) ?></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;">Courriel de connexion :</td>
                    <td style="padding:7px 0;color:#166534;font-size:13px;font-weight:600;"><?= htmlspecialchars($registeredEmail) ?></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;">Code fournisseur :</td>
                    <td style="padding:7px 0;"><code style="background:#1f2937;color:#34d399;padding:3px 8px;border-radius:4px;font-size:13px;"><?= htmlspecialchars($supplierCode) ?></code></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;">Référence :</td>
                    <td style="padding:7px 0;color:#166534;font-size:13px;font-family:monospace;">#<?= htmlspecialchars($applicationId) ?></td>
                </tr>
            </table>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin:0 0 20px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/login"
               style="display:inline-block;padding:14px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:700;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Accéder au portail fournisseur
            </a>
        </td></tr>
        </table>

        <!-- What's next FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:10px;margin:0 0 20px;">
        <tr><td style="padding:20px 22px;">
            <p style="margin:0 0 12px;font-size:14px;font-weight:700;color:#1f2937;">⏳ Prochaines étapes</p>
            <ol style="margin:0;padding-left:18px;color:#4b5563;font-size:13px;line-height:1.9;">
                <li><strong>Examen de votre dossier</strong> - Notre équipe examine votre candidature (1 à 3 jours ouvrables)</li>
                <li><strong>Vérification</strong> - Nous pourrions vous contacter pour des documents supplémentaires</li>
                <li><strong>Décision par courriel</strong> - Vous recevrez notre réponse à <strong><?= htmlspecialchars($registeredEmail) ?></strong></li>
                <li><strong>Activation</strong> - Une fois approuvé, votre accès complet au portail sera activé</li>
            </ol>
        </td></tr>
        </table>

        <!-- Limited access note FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:20px;">
        <tr><td style="padding:14px 18px;">
            <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
                <strong>ℹ️ Accès limité :</strong> Vous pouvez vous connecter dès maintenant avec votre mot de passe. L'accès complet au portail sera activé dès que votre compte aura été approuvé par notre équipe.
            </p>
        </td></tr>
        </table>

        <p style="margin:0;color:#4b5563;font-size:14px;line-height:1.6;">
            Des questions ? Écrivez-nous à <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;">info@ocsapp.ca</a> en mentionnant votre référence <strong>#<?= htmlspecialchars($applicationId) ?></strong>.
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
            <div style="font-size:42px;margin-bottom:10px;">📋</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Application Submitted!</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">OCSAPP Supplier Portal</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hi <?= htmlspecialchars($contactPerson) ?>,</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Thank you! Your application to join <strong style="color:#00b207;">OCSAPP</strong> as a supplier partner has been received. Our team will review it within <strong>2-3 business days</strong>.
        </p>

        <!-- Account details EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin:0 0 20px;border:2px solid #00b207;">
        <tr><td style="padding:22px 24px;">
            <p style="margin:0 0 14px;font-size:14px;font-weight:700;color:#007a05;">📋 Your Account Details</p>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;width:45%;">Company:</td>
                    <td style="padding:7px 0;color:#166534;font-size:13px;"><?= htmlspecialchars($companyName) ?></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;">Login Email:</td>
                    <td style="padding:7px 0;color:#166534;font-size:13px;font-weight:600;"><?= htmlspecialchars($registeredEmail) ?></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;">Supplier Code:</td>
                    <td style="padding:7px 0;"><code style="background:#1f2937;color:#34d399;padding:3px 8px;border-radius:4px;font-size:13px;"><?= htmlspecialchars($supplierCode) ?></code></td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:#374151;font-size:13px;font-weight:600;">Reference:</td>
                    <td style="padding:7px 0;color:#166534;font-size:13px;font-family:monospace;">#<?= htmlspecialchars($applicationId) ?></td>
                </tr>
            </table>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin:0 0 20px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/login"
               style="display:inline-block;padding:14px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:15px;font-weight:700;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Log In to Supplier Portal
            </a>
        </td></tr>
        </table>

        <!-- What's next EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:10px;margin:0 0 20px;">
        <tr><td style="padding:20px 22px;">
            <p style="margin:0 0 12px;font-size:14px;font-weight:700;color:#1f2937;">⏳ What Happens Next</p>
            <ol style="margin:0;padding-left:18px;color:#4b5563;font-size:13px;line-height:1.9;">
                <li><strong>Application Review</strong> - Our team reviews your submission (1-3 business days)</li>
                <li><strong>Verification</strong> - We may reach out for additional documents if needed</li>
                <li><strong>Decision by Email</strong> - You'll hear from us at <strong><?= htmlspecialchars($registeredEmail) ?></strong></li>
                <li><strong>Activation</strong> - Once approved, your full portal access will be enabled</li>
            </ol>
        </td></tr>
        </table>

        <!-- Limited access note EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#fffbeb;border-radius:8px;border-left:4px solid #f59e0b;margin-bottom:20px;">
        <tr><td style="padding:14px 18px;">
            <p style="margin:0;font-size:13px;color:#92400e;line-height:1.6;">
                <strong>ℹ️ Limited Access:</strong> You can log in now using your password. Full portal access will be activated once our team has reviewed and approved your account.
            </p>
        </td></tr>
        </table>

        <p style="margin:0;color:#4b5563;font-size:14px;line-height:1.6;">
            Questions? Email us at <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;">info@ocsapp.ca</a> and include your reference <strong>#<?= htmlspecialchars($applicationId) ?></strong>.
        </p>
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
