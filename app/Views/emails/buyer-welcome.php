<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur OCSAPP / Welcome to OCSAPP</title>
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
                                Bienvenue sur OCSAPP ! / Welcome!
                            </h1>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour {{user_first_name}} !
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Merci d'avoir créé votre <strong>compte acheteur</strong> sur OCSAPP. Vous pouvez maintenant commencer vos achats et commander des produits sur notre plateforme !
                            </p>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Votre compte a été créé avec succès et est prêt à l'emploi.
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Vos informations de compte</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Courriel :</strong> {{user_email}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Type de compte :</strong> Acheteur
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Statut :</strong> <span style="color: #00b207; font-weight: 600;">Actif</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Ce qu'il faut faire ensuite</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                                <li>Parcourez des milliers de produits de vendeurs canadiens vérifiés</li>
                                <li>Ajoutez des articles à votre panier et à votre liste de souhaits</li>
                                <li>Profitez d'un paiement sécurisé et d'un traitement fiable</li>
                                <li>Suivez vos commandes en temps réel</li>
                                <li>Bénéficiez d'offres et promotions exclusives</li>
                            </ul>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/login" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Commencer mes achats →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #00b207; background-color: #f0fdf4; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            📋 Important : Conditions d'utilisation
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            En utilisant OCSAPP, vous acceptez nos <a href="https://ocsapp.ca/terms/buyer" style="color: #00b207; text-decoration: none; font-weight: 600;">Conditions d'utilisation</a> et notre <a href="https://ocsapp.ca/privacy" style="color: #00b207; text-decoration: none; font-weight: 600;">Politique de confidentialité</a>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Besoin d'aide pour démarrer ? Notre équipe de soutien est là pour vous :
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                📧 <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a><br>
                                💬 <a href="https://ocsapp.ca/help" style="color: #00b207; text-decoration: none;">Centre d'aide</a>
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
                                Hi {{user_first_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Thank you for creating your <strong>Buyer Account</strong> with OCSAPP. You're now approved to start shopping and purchasing products on our platform!
                            </p>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Your account has been successfully created and is ready to use.
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Your Account Details</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Email:</strong> {{user_email}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Account Type:</strong> Buyer
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Status:</strong> <span style="color: #00b207; font-weight: 600;">Active</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">What's Next?</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                                <li>Browse thousands of products from verified Canadian sellers</li>
                                <li>Add items to your cart and wishlist</li>
                                <li>Enjoy secure checkout and payment processing</li>
                                <li>Track your orders in real-time</li>
                                <li>Get exclusive deals and promotions</li>
                            </ul>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/login" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Start Shopping Now →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #00b207; background-color: #f0fdf4; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            📋 Important: Terms & Conditions
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            By using OCSAPP, you agree to our <a href="https://ocsapp.ca/terms/buyer" style="color: #00b207; text-decoration: none; font-weight: 600;">Terms of Service</a> and <a href="https://ocsapp.ca/privacy" style="color: #00b207; text-decoration: none; font-weight: 600;">Privacy Policy</a>. You can review these documents anytime in your account settings.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Need help getting started? Our support team is here for you:
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                📧 <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a><br>
                                💬 <a href="https://ocsapp.ca/help" style="color: #00b207; text-decoration: none;">Visit Help Center</a>
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
