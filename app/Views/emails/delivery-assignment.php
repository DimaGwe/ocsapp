<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle assignation de livraison / New Delivery Assignment - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- French Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
                            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Nouvelle livraison</h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Commande #{{order_number}}</p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour {{delivery_person_name}} !
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Vous avez été assigné à une nouvelle livraison. Veuillez consulter les détails ci-dessous et procéder au ramassage et à la livraison.
                            </p>

                            <!-- Order Info Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 42px; margin-bottom: 12px;">📦</div>
                                        <h3 style="margin: 0 0 8px; color: #166534; font-size: 20px;">Commande #{{order_number}}</h3>
                                        <p style="margin: 0; color: #15803d; font-size: 14px;">
                                            Total : ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pickup Location -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">📍 Étape 1 : Lieu de ramassage</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #00b207;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            {{shop_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📌 {{shop_address}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📞 {{shop_phone}}
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px;">
                                            👤 Contact : {{shop_contact_name}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Delivery Location -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🏠 Étape 2 : Lieu de livraison</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #00b207;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            {{customer_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📌 {{delivery_address}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📞 {{customer_phone}}
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px;">
                                            ⏰ Plage horaire : {{delivery_window}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Items -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">📋 Articles de la commande ({{items_count}})</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.8;">
                                            {{order_items_list}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Special Instructions -->
                            {{delivery_instructions}}

                            <!-- Action Buttons -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/delivery/orders/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            ✓ Accepter et commencer la livraison
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/delivery/dashboard" style="display: inline-block; padding: 14px 32px; background: #6b7280; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                            📱 Voir toutes les livraisons
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Guidelines -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">
                                            ⚠️ Directives de livraison importantes
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #92400e; font-size: 13px; line-height: 1.8;">
                                            <li>Vérifiez les détails de la commande au ramassage</li>
                                            <li>Manipulez tous les articles avec soin</li>
                                            <li>Appelez le client si vous ne trouvez pas l'adresse</li>
                                            <li>Marquez la livraison comme complétée dans l'appli</li>
                                            <li>Prenez une photo comme preuve de livraison</li>
                                        </ul>
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

                    <!-- English Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
                            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">New Delivery Assignment</h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Order #{{order_number}}</p>
                        </td>
                    </tr>

                    <!-- English Body -->
                    <tr>
                        <td style="padding: 20px 30px 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Hi {{delivery_person_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                You have been assigned a new delivery. Please review the details below and proceed with pickup and delivery.
                            </p>

                            <!-- Order Info Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 42px; margin-bottom: 12px;">📦</div>
                                        <h3 style="margin: 0 0 8px; color: #166534; font-size: 20px;">Order #{{order_number}}</h3>
                                        <p style="margin: 0; color: #15803d; font-size: 14px;">
                                            Total: ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pickup Location -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">📍 Step 1: Pickup Location</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #00b207;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            {{shop_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📌 {{shop_address}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📞 {{shop_phone}}
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px;">
                                            👤 Contact: {{shop_contact_name}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Delivery Location -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🏠 Step 2: Delivery Location</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #00b207;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            {{customer_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📌 {{delivery_address}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📞 {{customer_phone}}
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px;">
                                            ⏰ Delivery Window: {{delivery_window}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Items -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">📋 Order Items ({{items_count}})</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.8;">
                                            {{order_items_list}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Special Instructions -->
                            {{delivery_instructions}}

                            <!-- Action Buttons -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/delivery/orders/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            ✓ Accept &amp; Start Delivery
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/delivery/dashboard" style="display: inline-block; padding: 14px 32px; background: #6b7280; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                            📱 View All Deliveries
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Guidelines -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">
                                            ⚠️ Important Delivery Guidelines
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #92400e; font-size: 13px; line-height: 1.8;">
                                            <li>Verify order details at pickup</li>
                                            <li>Handle all items with care</li>
                                            <li>Call customer if you can't find the address</li>
                                            <li>Mark delivery as complete in the app</li>
                                            <li>Take a photo proof of delivery</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px; font-weight: 600;">
                                Bonne route et merci de livrer ! / Safe travels and thank you for delivering! 🚚
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                Courriel automatique - ne pas répondre. / Automated email - do not reply.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/delivery/help" style="color: #6b7280; text-decoration: none;">Support</a> •
                                <a href="https://ocsapp.ca/delivery/guidelines" style="color: #6b7280; text-decoration: none;">Guidelines</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
