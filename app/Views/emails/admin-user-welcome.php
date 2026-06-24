<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur OCSAPP / Welcome to OCSAPP — Account Created</title>
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
                            <p style="margin: 10px 0 0; color: rgba(255,255,255,0.9); font-size: 16px;">
                                Votre compte a été créé / Your account has been created
                            </p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour {{user_first_name}} !
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Un administrateur a créé un compte pour vous sur OCSAPP. Vous pouvez maintenant vous connecter et commencer à utiliser la plateforme.
                            </p>

                            <!-- Login Credentials Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; margin: 24px 0; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #00b207; font-size: 18px; text-align: center;">
                                            <span style="font-size: 24px;">&#128273;</span> Vos identifiants de connexion
                                        </h3>
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600; width: 40%;">Courriel :</td>
                                                <td style="padding: 8px 0; color: #00b207; font-size: 14px; font-weight: 600;">{{user_email}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600;">Mot de passe temporaire :</td>
                                                <td style="padding: 8px 0;">
                                                    <code style="background: #1f2937; color: #fbbf24; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-family: monospace; letter-spacing: 1px;">{{temp_password}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600;">Type de compte :</td>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px;">{{user_role}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/login" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Accéder à mon compte
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Warning -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fef3c7; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">
                                            &#9888;&#65039; Important : Changez votre mot de passe
                                        </p>
                                        <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                            Pour des raisons de sécurité, veuillez changer votre mot de passe immédiatement après votre première connexion. Allez dans <strong>Paramètres du compte &rarr; Changer le mot de passe</strong> pour le mettre à jour.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Getting Started -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">Pour commencer</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 15px; line-height: 1.8;">
                                <li>Connectez-vous avec les identifiants ci-dessus</li>
                                <li>Changez votre mot de passe temporaire pour quelque chose de sécurisé</li>
                                <li>Complétez vos informations de profil</li>
                                <li>Explorez les fonctionnalités de la plateforme</li>
                            </ul>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Besoin d'aide ? Notre équipe de soutien est là pour vous :
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                &#128231; <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a>
                            </p>
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
                                Hi {{user_first_name}}!
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                An administrator has created an account for you on OCSAPP. You can now log in and start using the platform.
                            </p>

                            <!-- Login Credentials Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 12px; margin: 24px 0; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #00b207; font-size: 18px; text-align: center;">
                                            <span style="font-size: 24px;">&#128273;</span> Your Login Credentials
                                        </h3>
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600; width: 40%;">Email:</td>
                                                <td style="padding: 8px 0; color: #00b207; font-size: 14px; font-weight: 600;">{{user_email}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600;">Temporary Password:</td>
                                                <td style="padding: 8px 0;">
                                                    <code style="background: #1f2937; color: #fbbf24; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-family: monospace; letter-spacing: 1px;">{{temp_password}}</code>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600;">Account Type:</td>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px;">{{user_role}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/login" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Log In to Your Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Warning -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fef3c7; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">
                                            &#9888;&#65039; Important: Change Your Password
                                        </p>
                                        <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                            For security reasons, please change your password immediately after your first login. Go to <strong>Account Settings &rarr; Change Password</strong> to update it.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Getting Started -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">Getting Started</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 15px; line-height: 1.8;">
                                <li>Log in using the credentials above</li>
                                <li>Change your temporary password to something secure</li>
                                <li>Complete your profile information</li>
                                <li>Explore the platform features</li>
                            </ul>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Need help? Our support team is here for you:
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                &#128231; <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a>
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
                                Courriel automatique — ne pas répondre. / Automated email — do not reply.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/terms" style="color: #6b7280; text-decoration: none;">Terms</a> &bull;
                                <a href="https://ocsapp.ca/privacy" style="color: #6b7280; text-decoration: none;">Privacy</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
