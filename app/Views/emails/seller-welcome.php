<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de vendeur reçue / Seller Application Received - OCSAPP</title>
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
                                Demande reçue ! / Seller Application Received!
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
                                Merci d'avoir postulé pour devenir <strong>vendeur</strong> sur OCSAPP. Nous avons bien reçu votre demande pour un compte d'entreprise canadienne/québécoise.
                            </p>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Votre demande est actuellement <strong style="color: #f59e0b;">en attente d'approbation</strong> par notre équipe.
                            </p>

                            <!-- Status Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #fbbf24;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">⏳</div>
                                        <h3 style="margin: 0 0 8px; color: #d97706; font-size: 20px;">Statut : En attente d'approbation</h3>
                                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                                            Notre équipe examinera votre demande dans 1 à 2 jours ouvrables
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Application Details -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Détails de votre demande</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Courriel :</strong> {{user_email}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Type de compte :</strong> Vendeur (entreprise canadienne/québécoise)
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Soumis le :</strong> {{submitted_date}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- What Happens Next -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Prochaines étapes</h3>

                            <ol style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                                <li><strong>Processus de vérification :</strong> Notre équipe vérifiera votre enregistrement commercial et vos documents</li>
                                <li><strong>Notification par courriel :</strong> Vous recevrez un courriel une fois votre demande examinée</li>
                                <li><strong>Activation du compte :</strong> Si approuvé, vous pourrez commencer à vendre immédiatement</li>
                                <li><strong>Accès au tableau de bord :</strong> Configurez votre boutique, ajoutez des produits et gérez votre inventaire</li>
                            </ol>

                            <!-- Requirements -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            ⚠️ Conditions d'approbation
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Pour assurer la qualité et la légitimité, tous les comptes vendeurs doivent être des entreprises enregistrées au Canada ou au Québec. Notre équipe vérifiera vos informations commerciales avant l'approbation.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Once Approved -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Une fois approuvé, vous pourrez :</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                                <li>Créer et gérer votre propre vitrine en ligne</li>
                                <li>Lister des produits illimités avec des descriptions détaillées</li>
                                <li>Fixer vos propres prix et niveaux d'inventaire</li>
                                <li>Accéder au tableau de bord vendeur avec analyses et rapports</li>
                                <li>Gérer les commandes et les communications avec les clients</li>
                                <li>Participer aux promotions et offres spéciales</li>
                            </ul>

                            <!-- Terms -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #00b207; background-color: #f0fdf4; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            📋 Important : Conditions générales vendeur
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            En postulant comme vendeur, vous acceptez nos <a href="https://ocsapp.ca/terms/seller" style="color: #00b207; text-decoration: none; font-weight: 600;">Conditions de service vendeur</a>, nos <a href="https://ocsapp.ca/seller/policies" style="color: #00b207; text-decoration: none; font-weight: 600;">Politiques vendeur</a> et notre <a href="https://ocsapp.ca/privacy" style="color: #00b207; text-decoration: none; font-weight: 600;">Politique de confidentialité</a>.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Des questions sur votre demande de vendeur ?
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                📧 <a href="mailto:sellers@ocsapp.ca" style="color: #00b207; text-decoration: none;">sellers@ocsapp.ca</a><br>
                                💬 <a href="https://ocsapp.ca/seller/help" style="color: #00b207; text-decoration: none;">Centre d'aide vendeur</a>
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
                                Thank you for applying to become a <strong>Seller</strong> on OCSAPP. We've received your application for a Canadian/Quebec registered business account.
                            </p>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Your application is currently <strong style="color: #f59e0b;">pending approval</strong> by our team.
                            </p>

                            <!-- Status Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #fbbf24;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">⏳</div>
                                        <h3 style="margin: 0 0 8px; color: #d97706; font-size: 20px;">Application Status: Pending Approval</h3>
                                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                                            Our team will review your application within 1-2 business days
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Application Details -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Your Application Details</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Email:</strong> {{user_email}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Account Type:</strong> Seller (Canadian/Quebec Business)
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Submitted:</strong> {{submitted_date}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- What Happens Next -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">What Happens Next?</h3>

                            <ol style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                                <li><strong>Review Process:</strong> Our team will verify your business registration and documents</li>
                                <li><strong>Email Notification:</strong> You'll receive an email once your application is reviewed</li>
                                <li><strong>Account Activation:</strong> If approved, you can immediately start selling</li>
                                <li><strong>Dashboard Access:</strong> Set up your shop, add products, and manage inventory</li>
                            </ol>

                            <!-- Requirements -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            ⚠️ Approval Requirements
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            To ensure quality and legitimacy, all seller accounts must be Canadian or Quebec registered businesses. Our team will verify your business credentials before approval.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Once Approved -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Once Approved, You Can:</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                                <li>Create and manage your own shop storefront</li>
                                <li>List unlimited products with detailed descriptions</li>
                                <li>Set your own pricing and inventory levels</li>
                                <li>Access seller dashboard with analytics and reports</li>
                                <li>Manage orders and customer communications</li>
                                <li>Participate in promotions and featured deals</li>
                            </ul>

                            <!-- Terms -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #00b207; background-color: #f0fdf4; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            📋 Important: Seller Terms & Conditions
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            By applying as a seller, you agree to our <a href="https://ocsapp.ca/terms/seller" style="color: #00b207; text-decoration: none; font-weight: 600;">Seller Terms of Service</a>, <a href="https://ocsapp.ca/seller/policies" style="color: #00b207; text-decoration: none; font-weight: 600;">Seller Policies</a>, and <a href="https://ocsapp.ca/privacy" style="color: #00b207; text-decoration: none; font-weight: 600;">Privacy Policy</a>. Please review these documents carefully.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Questions about your seller application?
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                📧 <a href="mailto:sellers@ocsapp.ca" style="color: #00b207; text-decoration: none;">sellers@ocsapp.ca</a><br>
                                💬 <a href="https://ocsapp.ca/seller/help" style="color: #00b207; text-decoration: none;">Seller Help Center</a>
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
                                <a href="https://ocsapp.ca/seller/policies" style="color: #6b7280; text-decoration: none;">Seller Policies</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
