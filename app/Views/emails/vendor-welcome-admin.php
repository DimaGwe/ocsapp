<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue / Welcome to OCSAPP Vendor Program</title>
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
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Bienvenue sur OCSAPP ! / Welcome to OCSAPP!
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Votre compte vendeur a été créé / Your vendor account has been created
                            </p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour {{company_name}} !
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Excellente nouvelle ! Votre compte vendeur a été créé par notre équipe administrative. Vous pouvez maintenant gérer vos produits et commandes sur la plateforme OCSAPP.
                            </p>

                            <!-- Credentials Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 20px;">🔐 Vos identifiants de connexion</h3>

                                        <p style="margin: 0 0 12px; color: #15803d; font-size: 14px;">
                                            <strong>Courriel :</strong> {{email}}
                                        </p>
                                        <p style="margin: 0 0 12px; color: #15803d; font-size: 14px;">
                                            <strong>Mot de passe :</strong> {{password}}
                                        </p>
                                        <p style="margin: 0; color: #ef4444; font-size: 13px; font-weight: 600;">
                                            ⚠️ Veuillez changer votre mot de passe après la première connexion !
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Quick Start -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🚀 Guide de démarrage rapide</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ol style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 12px;">Connectez-vous à votre tableau de bord vendeur</li>
                                            <li style="margin-bottom: 12px;">Complétez votre profil vendeur</li>
                                            <li style="margin-bottom: 12px;">Associez vos produits à votre compte vendeur</li>
                                            <li style="margin-bottom: 0;">Commencez à recevoir et approuver des commandes</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Login Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{login_url}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            🔑 Accéder au tableau de bord vendeur
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            Besoin d'aide ?
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            Notre équipe d'assistance vendeur est là pour vous aider !<br>
                                            Écrivez-nous à <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Language Divider -->
                    <tr>
                      <td style="padding: 0 30px;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;">
                          <tr>
                            <td style="padding:24px 0 8px;text-align:center;">
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
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Hi {{company_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Great news! Your vendor account has been created by our admin team. You can now start managing your products and orders on the OCSAPP platform.
                            </p>

                            <!-- Credentials Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 20px;">🔐 Your Login Credentials</h3>

                                        <p style="margin: 0 0 12px; color: #15803d; font-size: 14px;">
                                            <strong>Email:</strong> {{email}}
                                        </p>
                                        <p style="margin: 0 0 12px; color: #15803d; font-size: 14px;">
                                            <strong>Password:</strong> {{password}}
                                        </p>
                                        <p style="margin: 0; color: #ef4444; font-size: 13px; font-weight: 600;">
                                            ⚠️ Please change your password after first login!
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Quick Start -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🚀 Quick Start Guide</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ol style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 12px;">Login to your vendor dashboard</li>
                                            <li style="margin-bottom: 12px;">Complete your vendor profile</li>
                                            <li style="margin-bottom: 12px;">Link your products to your vendor account</li>
                                            <li style="margin-bottom: 0;">Start receiving and approving orders</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Login Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{login_url}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            🔑 Login to Vendor Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            Need Help?
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            Our vendor support team is here to help!<br>
                                            Email us at <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px; font-weight: 600;">
                                Bienvenue dans la famille OCSAPP ! / Welcome to the OCSAPP Vendor Family! 🤝
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Need help? <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Courriel automatique — ne pas répondre. / Automated email — do not reply.
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
