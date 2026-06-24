<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification courriel / Email Verification - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width: 180px; height: auto; margin-bottom: 20px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700;">
                                Vérifiez votre courriel / Verify Your Email
                            </h1>
                            <p style="margin: 10px 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">
                                OCSAPP Distribution Portal
                            </p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">
                                Bonjour {{user_first_name}} !
                            </h2>
                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Merci de vous être inscrit sur <strong>OCSAPP Distribution</strong>. Entrez le code ci-dessous pour confirmer votre adresse courriel et activer votre compte.
                            </p>

                            <!-- Code box FR -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #00b207; border-radius: 16px; padding: 32px 40px; text-align: center;">
                                            <p style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #15803d; text-transform: uppercase; letter-spacing: 1px;">Votre code de vérification</p>
                                            <div style="font-size: 48px; font-weight: 800; color: #0a1628; letter-spacing: 12px; font-family: 'Courier New', monospace; line-height: 1.1;">{{verification_code}}</div>
                                            <p style="margin: 12px 0 0; font-size: 12px; color: #6b7280;">Expire dans <strong>30 minutes</strong></p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA buttons FR -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="{{magic_link_url_fr}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(0,178,7,0.3);">
                                            &#10003; Vérifier automatiquement
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="{{verify_url_fr}}" style="display: inline-block; padding: 12px 28px; background: #ffffff; color: #00b207; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; border: 2px solid #00b207;">
                                            Entrer le code manuellement →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 14px 18px;">
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">
                                            ⚠️ Si vous n'avez pas demandé cette inscription, ignorez ce message. Aucune action n'est requise.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                📧 <a href="mailto:info@ocsapp.ca" style="color: #00b207; text-decoration: none;">info@ocsapp.ca</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 30px;">
                            <table role="presentation" style="width:100%;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:20px 0 8px;text-align:center;">
                                        <hr style="border:none;border-top:2px dashed #e5e7eb;margin:0 0 12px;">
                                        <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">
                                            🇬🇧 English version follows below / La version française précède
                                        </span>
                                        <hr style="border:none;border-top:2px dashed #e5e7eb;margin:12px 0 0;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- English Body -->
                    <tr>
                        <td style="padding: 20px 30px 40px 30px;">
                            <h2 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">
                                Hi {{user_first_name}}! 👋
                            </h2>
                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Thank you for registering with <strong>OCSAPP Distribution</strong>. Enter the code below to confirm your email address and activate your account.
                            </p>

                            <!-- Code box EN -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #00b207; border-radius: 16px; padding: 32px 40px; text-align: center;">
                                            <p style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #15803d; text-transform: uppercase; letter-spacing: 1px;">Your verification code</p>
                                            <div style="font-size: 48px; font-weight: 800; color: #0a1628; letter-spacing: 12px; font-family: 'Courier New', monospace; line-height: 1.1;">{{verification_code}}</div>
                                            <p style="margin: 12px 0 0; font-size: 12px; color: #6b7280;">Expires in <strong>30 minutes</strong></p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA buttons EN -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="{{magic_link_url_en}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(0,178,7,0.3);">
                                            &#10003; Verify Automatically
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="{{verify_url_en}}" style="display: inline-block; padding: 12px 28px; background: #ffffff; color: #00b207; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; border: 2px solid #00b207;">
                                            Enter code manually →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 14px 18px;">
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">
                                            ⚠️ If you did not request this registration, you can safely ignore this email. No action is required.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                📧 <a href="mailto:info@ocsapp.ca" style="color: #00b207; text-decoration: none;">info@ocsapp.ca</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Courriel automatique - ne pas répondre. / Automated email - do not reply.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/terms" style="color: #6b7280; text-decoration: none;">Terms</a> •
                                <a href="https://ocsapp.ca/privacy" style="color: #6b7280; text-decoration: none;">Privacy</a> •
                                <a href="https://ocsapp.ca/unsubscribe" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
