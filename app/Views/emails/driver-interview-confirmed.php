<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entretien confirmé / Interview Confirmed - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Entretien confirmé !</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Équipe de recrutement OCSAPP</p>
        </td>
    </tr>

    <!-- French -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 16px;color:#1f2937;font-size:20px;">Bonjour {{first_name}},</h2>
        <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
            Votre entretien avec l'équipe OCSAPP est confirmé. Nous avons hâte de vous parler !
        </p>

        <!-- Interview time box -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin-bottom:24px;border:2px solid #00b207;">
        <tr><td style="padding:24px;text-align:center;">
            <p style="margin:0 0 6px;font-size:13px;font-weight:600;color:#166534;text-transform:uppercase;letter-spacing:0.5px;">Date et heure de l'entretien</p>
            <p style="margin:0;font-size:22px;font-weight:800;color:#1f2937;">{{interview_time_fr}}</p>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:8px;margin-bottom:24px;">
        <tr><td style="padding:16px 20px;">
            <p style="margin:0;color:#4b5563;font-size:14px;line-height:1.6;">
                Vous recevrez les détails de connexion à l'entretien séparément. En attendant, vous pouvez suivre votre candidature et nous envoyer des messages via votre portail.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 4px;text-align:center;">
            <a href="https://ocsapp.ca/delivery/application-status"
               style="display:inline-block;background:#00b207;color:#fff;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:700;font-size:14px;">
                Voir ma candidature
            </a>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Interview Confirmed!</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">OCSAPP Recruitment Team</p>
        </td>
    </tr>

    <!-- English -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 16px;color:#1f2937;font-size:20px;">Hi {{first_name}},</h2>
        <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
            Your interview with the OCSAPP team is confirmed. We look forward to speaking with you!
        </p>

        <!-- Interview time box EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;margin-bottom:24px;border:2px solid #00b207;">
        <tr><td style="padding:24px;text-align:center;">
            <p style="margin:0 0 6px;font-size:13px;font-weight:600;color:#166534;text-transform:uppercase;letter-spacing:0.5px;">Interview Date & Time</p>
            <p style="margin:0;font-size:22px;font-weight:800;color:#1f2937;">{{interview_time}}</p>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:8px;margin-bottom:24px;">
        <tr><td style="padding:16px 20px;">
            <p style="margin:0;color:#4b5563;font-size:14px;line-height:1.6;">
                You'll receive connection details for the interview separately. In the meantime, you can track your application and message us through your portal.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 20px;text-align:center;">
            <a href="https://ocsapp.ca/delivery/application-status"
               style="display:inline-block;background:#00b207;color:#fff;text-decoration:none;padding:12px 24px;border-radius:8px;font-weight:700;font-size:14px;">
                View My Application
            </a>
        </p>

        <p style="margin:0;color:#6b7280;font-size:14px;">
            Best regards,<br>
            <strong style="color:#1f2937;">OCSAPP Driver Recruitment Team</strong>
        </p>
    </td></tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f9fafb;padding:24px 30px;text-align:center;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;">
            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">&copy; {{current_year}} OCSAPP. All rights reserved.</p>
            <p style="margin:0;color:#9ca3af;font-size:12px;">Automated email - do not reply directly.</p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
