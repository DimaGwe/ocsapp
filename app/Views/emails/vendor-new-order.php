<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande / New Order Received - OCSAPP</title>
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
                                Nouvelle commande ! / New Order for Your Products!
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Commande / Order #{{order_number}}
                            </p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour {{vendor_name}} !
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Bonne nouvelle ! Une nouvelle commande contenant vos produits a été passée sur OCSAPP. Veuillez la réviser et l'approuver dès que possible.
                            </p>

                            <!-- Order Summary Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 20px;">📋 Résumé de la commande</h3>

                                        <p style="margin: 0 0 8px; color: #15803d; font-size: 14px;">
                                            <strong>Numéro de commande :</strong> {{order_number}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #15803d; font-size: 14px;">
                                            <strong>Client :</strong> {{customer_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #15803d; font-size: 14px;">
                                            <strong>Date de commande :</strong> {{order_date}}
                                        </p>
                                        <p style="margin: 0; color: #15803d; font-size: 14px;">
                                            <strong>Total de la commande :</strong> ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Your Products -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🏷️ Vos produits dans cette commande</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            {{vendor_products_list}}
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 4px 0; color: #6b7280; font-size: 14px;">Votre quantité :</td>
                                                <td align="right" style="padding: 4px 0; color: #6b7280; font-size: 14px;">{{vendor_quantity}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0 0; color: #1f2937; font-size: 16px; font-weight: 700;">Votre coût total :</td>
                                                <td align="right" style="padding: 8px 0 0; color: #00b207; font-size: 16px; font-weight: 700;">${{vendor_cost_total}} CAD</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Required -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <h3 style="margin: 0 0 12px; color: #92400e; font-size: 18px;">⚠️ Action requise</h3>
                                        <p style="margin: 0 0 16px; color: #a16207; font-size: 14px; line-height: 1.6;">
                                            Veuillez réviser et approuver cette commande dans les 24 heures pour garantir une exécution dans les délais
                                        </p>
                                        <a href="https://ocsapp.ca/vendor/dashboard" style="display: inline-block; padding: 12px 28px; background: #f59e0b; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 600;">
                                            📊 Accéder au tableau de bord vendeur
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Quick Actions -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/vendor/order/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            ✅ Approuver la commande
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/vendor/order/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: #6b7280; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                            📋 Voir tous les détails
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important Notes -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            📝 Remarques importantes
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            Si vous ne pouvez pas exécuter cette commande, veuillez la rejeter avec un motif dans les plus brefs délais.<br>
                                            Cela nous aide à trouver des fournisseurs alternatifs et à maintenir la satisfaction des clients.
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
                                Hi {{vendor_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Great news! A new order containing your products has been placed on OCSAPP. Please review and approve the order at your earliest convenience.
                            </p>

                            <!-- Order Summary Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 20px;">📋 Order Summary</h3>

                                        <p style="margin: 0 0 8px; color: #15803d; font-size: 14px;">
                                            <strong>Order Number:</strong> {{order_number}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #15803d; font-size: 14px;">
                                            <strong>Customer:</strong> {{customer_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #15803d; font-size: 14px;">
                                            <strong>Order Date:</strong> {{order_date}}
                                        </p>
                                        <p style="margin: 0; color: #15803d; font-size: 14px;">
                                            <strong>Order Total:</strong> ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Your Products -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🏷️ Your Products in this Order</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            {{vendor_products_list}}
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 4px 0; color: #6b7280; font-size: 14px;">Your Quantity:</td>
                                                <td align="right" style="padding: 4px 0; color: #6b7280; font-size: 14px;">{{vendor_quantity}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0 0; color: #1f2937; font-size: 16px; font-weight: 700;">Your Cost Total:</td>
                                                <td align="right" style="padding: 8px 0 0; color: #00b207; font-size: 16px; font-weight: 700;">${{vendor_cost_total}} CAD</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Required -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <h3 style="margin: 0 0 12px; color: #92400e; font-size: 18px;">⚠️ Action Required</h3>
                                        <p style="margin: 0 0 16px; color: #a16207; font-size: 14px; line-height: 1.6;">
                                            Please review and approve this order within 24 hours to ensure timely fulfillment
                                        </p>
                                        <a href="https://ocsapp.ca/vendor/dashboard" style="display: inline-block; padding: 12px 28px; background: #f59e0b; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 600;">
                                            📊 Go to Vendor Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Quick Actions -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/vendor/order/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            ✅ Approve Order
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/vendor/order/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: #6b7280; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                            📋 View Full Order Details
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important Notes -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            📝 Important Notes
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            If you're unable to fulfill this order, please reject it with a reason as soon as possible.<br>
                                            This helps us find alternative suppliers and maintain customer satisfaction.
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
                                Merci de votre partenariat avec OCSAPP ! / Thank you for partnering with OCSAPP! 🤝
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Besoin d'aide ? / Need help? <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/vendor/dashboard" style="color: #6b7280; text-decoration: none;">Dashboard</a> •
                                <a href="https://ocsapp.ca/vendor/orders" style="color: #6b7280; text-decoration: none;">Orders</a> •
                                <a href="https://ocsapp.ca/vendor-central" style="color: #6b7280; text-decoration: none;">Vendor Central</a> •
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
