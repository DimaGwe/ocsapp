<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande en route / Order On the Way - OCSAPP</title>
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
                                Votre commande est en route ! / On the Way!
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
                                Bonne nouvelle, {{user_first_name}} !
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Votre commande a été prise en charge par notre livreur et est en route vers vous !
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">🚚💨</div>
                                        <h3 style="margin: 0 0 8px; color: #166534; font-size: 20px;">En cours de livraison</h3>
                                        <p style="margin: 0 0 16px; color: #15803d; font-size: 14px;">
                                            Arrivée estimée : {{delivery_eta}}
                                        </p>
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/track" style="display: inline-block; padding: 10px 24px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            📍 Suivre ma commande
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🚗 Votre livreur</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 16px; font-weight: 600;">
                                            {{driver_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📞 {{driver_phone}}
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 13px; font-style: italic;">
                                            N'hésitez pas à appeler si vous avez des questions sur votre livraison
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">📍 Adresse de livraison</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; line-height: 1.6;">
                                            {{delivery_address}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">📦 Résumé de votre commande</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            {{order_items_summary}}
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600;">
                                            Total : ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; border-left: 4px solid #00b207; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #166534; font-size: 14px; font-weight: 600;">
                                            💡 Préparez-vous à recevoir votre commande
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #166534; font-size: 13px; line-height: 1.8;">
                                            <li>Assurez-vous que quelqu'un est disponible pour réceptionner la commande</li>
                                            <li>Gardez votre téléphone à portée en cas d'appel du livreur</li>
                                            <li>Préparez le paiement si vous payez à la livraison</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/track" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            📍 Suivre ma livraison en direct
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 13px; text-align: center; line-height: 1.6;">
                                Questions ou préoccupations ? Nous sommes là pour vous aider !<br>
                                📧 <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a>
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
                                Great news, {{user_first_name}}! 🎉
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Your order has been picked up by our delivery driver and is on its way to you!
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">🚚💨</div>
                                        <h3 style="margin: 0 0 8px; color: #166534; font-size: 20px;">Out for Delivery</h3>
                                        <p style="margin: 0 0 16px; color: #15803d; font-size: 14px;">
                                            Estimated arrival: {{delivery_eta}}
                                        </p>
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/track" style="display: inline-block; padding: 10px 24px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            📍 Track Your Order
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🚗 Your Delivery Driver</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 16px; font-weight: 600;">
                                            {{driver_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px;">
                                            📞 {{driver_phone}}
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 13px; font-style: italic;">
                                            Feel free to call if you have any questions about your delivery
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">📍 Delivery Address</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0; color: #1f2937; font-size: 14px; line-height: 1.6;">
                                            {{delivery_address}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">📦 Your Order Summary</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            {{order_items_summary}}
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                                        <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600;">
                                            Total: ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; border-left: 4px solid #00b207; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #166534; font-size: 14px; font-weight: 600;">
                                            💡 Prepare for Delivery
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #166534; font-size: 13px; line-height: 1.8;">
                                            <li>Ensure someone is available to receive the order</li>
                                            <li>Keep your phone nearby in case the driver calls</li>
                                            <li>Have payment ready if paying on delivery</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/track" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            📍 Track Your Delivery Live
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 13px; text-align: center; line-height: 1.6;">
                                Questions or concerns? We're here to help!<br>
                                📧 <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a>
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
                                <a href="https://ocsapp.ca/orders" style="color: #6b7280; text-decoration: none;">My Orders</a> •
                                <a href="https://ocsapp.ca/support" style="color: #6b7280; text-decoration: none;">Support</a> •
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
