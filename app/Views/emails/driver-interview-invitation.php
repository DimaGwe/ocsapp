<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation entretien / Interview Invitation - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Invitation à un entretien</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Équipe de recrutement OCSAPP</p>
        </td>
    </tr>

    <!-- French -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 16px;color:#1f2937;font-size:20px;">Bonjour {{first_name}},</h2>
        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Bonne nouvelle ! Nous aimerions planifier un entretien pour votre candidature de livreur OCSAPP.
        </p>

        <!-- App ID -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:10px;margin-bottom:24px;border:2px solid #00b207;">
        <tr><td style="padding:16px 20px;">
            <p style="margin:0;font-size:13px;color:#166534;">
                <strong>Candidature :</strong>
                <code style="background:#1f2937;color:#60a5fa;padding:2px 8px;border-radius:4px;font-size:13px;font-family:monospace;">#{{application_id}}</code>
            </p>
        </td></tr>
        </table>

        <!-- Time slots FR -->
        <h3 style="margin:0 0 12px;color:#1f2937;font-size:16px;">Créneaux disponibles</h3>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:20px;">
            {{time_slots_fr}}
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;">
        <tr><td style="padding:14px 18px;">
            <p style="margin:0;color:#166534;font-size:14px;line-height:1.6;">
                Connectez-vous à votre portail pour choisir votre créneau préféré et compléter l'entretien.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 16px;text-align:center;">
            <a href="https://ocsapp.ca/delivery/application-status"
               style="display:inline-block;background:#00b207;color:#fff;text-decoration:none;padding:13px 28px;border-radius:8px;font-weight:700;font-size:15px;">
                Choisir mon créneau
            </a>
        </p>
        <p style="margin:0 0 4px;color:#6b7280;font-size:13px;text-align:center;">ou visitez : <a href="https://ocsapp.ca/delivery/application-status" style="color:#00b207;">ocsapp.ca/delivery/application-status</a></p>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Interview Invitation</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">OCSAPP Recruitment Team</p>
        </td>
    </tr>

    <!-- English -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 16px;color:#1f2937;font-size:20px;">Hi {{first_name}},</h2>
        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Great news! We'd like to schedule an interview for your OCSAPP driver application.
        </p>

        <!-- App ID EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:10px;margin-bottom:24px;border:2px solid #00b207;">
        <tr><td style="padding:16px 20px;">
            <p style="margin:0;font-size:13px;color:#166534;">
                <strong>Application:</strong>
                <code style="background:#1f2937;color:#60a5fa;padding:2px 8px;border-radius:4px;font-size:13px;font-family:monospace;">#{{application_id}}</code>
            </p>
        </td></tr>
        </table>

        <!-- Time slots EN -->
        <h3 style="margin:0 0 12px;color:#1f2937;font-size:16px;">Available Time Slots</h3>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f9fafb;border-radius:10px;margin-bottom:24px;">
        <tr><td style="padding:20px;">
            {{time_slots_en}}
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;">
        <tr><td style="padding:14px 18px;">
            <p style="margin:0;color:#166534;font-size:14px;line-height:1.6;">
                Log in to your portal to select your preferred time and complete your interview booking.
            </p>
        </td></tr>
        </table>

        <p style="margin:0 0 16px;text-align:center;">
            <a href="https://ocsapp.ca/delivery/application-status"
               style="display:inline-block;background:#00b207;color:#fff;text-decoration:none;padding:13px 28px;border-radius:8px;font-weight:700;font-size:15px;">
                Select My Time Slot
            </a>
        </p>
        <p style="margin:0 0 4px;color:#6b7280;font-size:13px;text-align:center;">or visit: <a href="https://ocsapp.ca/delivery/application-status" style="color:#00b207;">ocsapp.ca/delivery/application-status</a></p>

        <p style="margin:28px 0 0;color:#6b7280;font-size:14px;">
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
