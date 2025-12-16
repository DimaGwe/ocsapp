<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order is On the Way! - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header with Blue Gradient (On the Way) -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">🚚</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Your Order is On the Way!
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Order #{{order_number}}
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Great news, {{user_first_name}}! 🎉
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Your order has been picked up by our delivery driver and is on its way to you!
                            </p>

                            <!-- Delivery Status Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">🚚💨</div>
                                        <h3 style="margin: 0 0 8px; color: #1e40af; font-size: 20px;">Out for Delivery</h3>
                                        <p style="margin: 0 0 16px; color: #1e3a8a; font-size: 14px;">
                                            Estimated arrival: {{delivery_eta}}
                                        </p>
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/track" style="display: inline-block; padding: 10px 24px; background: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            📍 Track Your Order
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Delivery Driver Info -->
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

                            <!-- Delivery Address -->
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

                            <!-- Order Summary -->
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

                            <!-- Preparation Tips -->
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

                            <!-- Track Order CTA -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/track" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            📍 Track Your Delivery Live
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Help Section -->
                            <p style="margin: 0; color: #6b7280; font-size: 13px; text-align: center; line-height: 1.6;">
                                Questions or concerns? We're here to help!<br>
                                📧 <a href="mailto:support@ocsapp.ca" style="color: #00b207; text-decoration: none;">support@ocsapp.ca</a> •
                                📞 +1 (809) 555-1234
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px; font-weight: 600;">
                                Thank you for shopping with OCSAPP! 🛒
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. All rights reserved.
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
