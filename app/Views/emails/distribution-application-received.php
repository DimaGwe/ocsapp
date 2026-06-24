<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande reçue / Application Received - OCSAPP Distribution</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- French Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 36px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width: 160px; height: auto; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700;">Demande reçue !</h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Merci de votre intérêt pour OCSAPP Distribution</p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 20px;">
                                Bonjour {{user_first_name}} !
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Nous avons bien reçu votre demande d'inscription au programme de <strong>distribution OCSAPP</strong> pour <strong>{{company_name}}</strong>. Notre équipe examinera votre dossier et vous contactera sous peu.
                            </p>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Votre compte est actuellement en attente d'approbation. Vous pouvez dès maintenant accéder à votre tableau de bord.
                            </p>

                            <!-- Application Details FR -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 16px;">📋 Détails de la demande</h3>
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600; width: 45%;">Entreprise :</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{company_name}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Numéro NEQ :</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px; font-family: monospace;">{{neq_number}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Courriel :</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{user_email}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Date de la demande :</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{submitted_date}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Statut :</td>
                                                <td style="padding: 8px 0; font-size: 14px;"><span style="color: #d97706; font-weight: 600;">En attente d'examen</span></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- What Happens Next FR -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">⏳ Prochaines étapes</h3>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ol style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 12px;">
                                                <strong>Examen de la demande (1 à 3 jours ouvrables)</strong><br>
                                                <span style="color: #6b7280;">Notre équipe vérifiera votre numéro NEQ, vos documents et les informations de votre entreprise.</span>
                                            </li>
                                            <li style="margin-bottom: 12px;">
                                                <strong>Vérification et conformité</strong><br>
                                                <span style="color: #6b7280;">Nous pourrions vous contacter pour des informations ou documents supplémentaires si nécessaire.</span>
                                            </li>
                                            <li style="margin-bottom: 12px;">
                                                <strong>Notification de décision</strong><br>
                                                <span style="color: #6b7280;">Vous recevrez un courriel de confirmation d'approbation ou de demande d'informations complémentaires.</span>
                                            </li>
                                            <li style="margin-bottom: 0;">
                                                <strong>Activation du compte et accès complet</strong><br>
                                                <span style="color: #6b7280;">Une fois approuvé, votre compte sera activé et vous pourrez soumettre des demandes de distribution.</span>
                                            </li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA FR -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/distribution/dashboard" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(0,178,7,0.3);">
                                            Accéder à mon tableau de bord →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important note FR -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 14px 18px;">
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">
                                            En utilisant OCSAPP, vous acceptez nos <a href="https://ocsapp.ca/terms/business" style="color: #92400e; font-weight: 600;">Conditions d'utilisation</a> et notre <a href="https://ocsapp.ca/privacy" style="color: #92400e; font-weight: 600;">Politique de confidentialité</a>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                📧 <a href="mailto:info@ocsapp.ca" style="color: #00b207; text-decoration: none;">info@ocsapp.ca</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Language Divider -->
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

                    <!-- English Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 36px 30px; text-align: center;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width: 160px; height: auto; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700;">Application Received!</h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Thank you for applying to OCSAPP Distribution</p>
                        </td>
                    </tr>

                    <!-- English Body -->
                    <tr>
                        <td style="padding: 20px 30px 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 20px;">
                                Hi {{user_first_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Thank you for applying to the <strong>OCSAPP Distribution Program</strong> for <strong>{{company_name}}</strong>. Our team will review your application and reach out shortly.
                            </p>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Your account is currently pending approval. You can access your dashboard in the meantime.
                            </p>

                            <!-- Application Details EN -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 16px;">📋 Application Details</h3>
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600; width: 45%;">Company:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{company_name}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">NEQ Number:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px; font-family: monospace;">{{neq_number}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Email:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{user_email}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Submitted:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{submitted_date}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Status:</td>
                                                <td style="padding: 8px 0; font-size: 14px;"><span style="color: #d97706; font-weight: 600;">Pending Review</span></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- What Happens Next EN -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">⏳ What Happens Next?</h3>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ol style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 12px;">
                                                <strong>Application Review (1-3 business days)</strong><br>
                                                <span style="color: #6b7280;">Our team will verify your NEQ number, uploaded documents, and company information.</span>
                                            </li>
                                            <li style="margin-bottom: 12px;">
                                                <strong>Verification & Compliance Check</strong><br>
                                                <span style="color: #6b7280;">We may contact you for additional information or documentation if needed.</span>
                                            </li>
                                            <li style="margin-bottom: 12px;">
                                                <strong>Decision Notification</strong><br>
                                                <span style="color: #6b7280;">You'll receive a confirmation email once your account is approved or if we need more details.</span>
                                            </li>
                                            <li style="margin-bottom: 0;">
                                                <strong>Account Activation & Full Access</strong><br>
                                                <span style="color: #6b7280;">Once approved, your account will be activated and you can begin submitting distribution requests.</span>
                                            </li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA EN -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/distribution/dashboard" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 12px rgba(0,178,7,0.3);">
                                            Go to My Dashboard →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important note EN -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 14px 18px;">
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">
                                            By using OCSAPP, you agree to our <a href="https://ocsapp.ca/terms/business" style="color: #92400e; font-weight: 600;">Terms of Service</a> and <a href="https://ocsapp.ca/privacy" style="color: #92400e; font-weight: 600;">Privacy Policy</a>.
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
