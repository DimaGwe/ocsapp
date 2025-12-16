<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order Has Been Delivered! - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header with Green Gradient (Success/Delivered) -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">✅</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Your Order Has Been Delivered!
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
                                Enjoy your order, {{user_first_name}}! 🎉
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Your order was successfully delivered on <strong>{{delivery_date}}</strong>. We hope you love your items!
                            </p>

                            <!-- Delivery Confirmation Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">📦✓</div>
                                        <h3 style="margin: 0 0 8px; color: #00b207; font-size: 20px;">Delivery Confirmed</h3>
                                        <p style="margin: 0; color: #166534; font-size: 14px;">
                                            Delivered to: {{delivery_address}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Summary -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">📋 Order Summary</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            {{order_items_summary}}
                                        </p>
                                        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 16px 0;">
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 4px 0; color: #6b7280; font-size: 14px;">Subtotal:</td>
                                                <td align="right" style="padding: 4px 0; color: #6b7280; font-size: 14px;">${{order_subtotal}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; color: #6b7280; font-size: 14px;">Delivery Fee:</td>
                                                <td align="right" style="padding: 4px 0; color: #6b7280; font-size: 14px;">${{delivery_fee}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0 0; color: #1f2937; font-size: 16px; font-weight: 700;">Total Paid:</td>
                                                <td align="right" style="padding: 8px 0 0; color: #00b207; font-size: 16px; font-weight: 700;">${{order_total}} CAD</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Review Request -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border-radius: 8px; border-left: 4px solid #eab308; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px; text-align: center;">
                                        <div style="font-size: 42px; margin-bottom: 12px;">⭐⭐⭐⭐⭐</div>
                                        <h3 style="margin: 0 0 12px; color: #854d0e; font-size: 18px;">How was your experience?</h3>
                                        <p style="margin: 0 0 16px; color: #a16207; font-size: 14px; line-height: 1.6;">
                                            Your feedback helps us improve and helps other shoppers make informed decisions
                                        </p>
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/review" style="display: inline-block; padding: 12px 28px; background: #eab308; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 600;">
                                            ⭐ Leave a Review
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Receipt & Reorder CTAs -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/invoice" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            📄 Download Receipt
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/reorder" style="display: inline-block; padding: 14px 32px; background: #6b7280; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                            🔄 Reorder Same Items
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            💬 Need Help?
                                        </p>
                                        <p style="margin: 0 0 16px; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            If there's any issue with your order, we're here to help within 24 hours of delivery
                                        </p>
                                        <a href="https://ocsapp.ca/orders/{{order_id}}/support" style="display: inline-block; padding: 10px 24px; background: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            Report an Issue
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Continue Shopping -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px; text-align: center;">Continue Shopping</h3>

                            <table role="presentation" style="width: 100%;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/deals" style="display: inline-block; padding: 12px 24px; background: #ffffff; color: #00b207; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; border: 2px solid #00b207;">
                                            🔥 View Today's Deals
                                        </a>
                                    </td>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/best-sellers" style="display: inline-block; padding: 12px 24px; background: #ffffff; color: #00b207; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; border: 2px solid #00b207;">
                                            ⭐ Best Sellers
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px; font-weight: 600;">
                                Thank you for choosing OCSAPP! 💚
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                We hope to serve you again soon!
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca" style="color: #6b7280; text-decoration: none;">Shop</a> •
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
