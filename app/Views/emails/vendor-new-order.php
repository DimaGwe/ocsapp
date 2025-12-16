<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header with Blue Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">📦</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                New Order for Your Products!
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
                                Hi {{vendor_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Great news! A new order containing your products has been placed on OCSAPP. Please review and approve the order at your earliest convenience.
                            </p>

                            <!-- Order Summary Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #1e40af; font-size: 20px;">📋 Order Summary</h3>

                                        <p style="margin: 0 0 8px; color: #1e3a8a; font-size: 14px;">
                                            <strong>Order Number:</strong> {{order_number}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #1e3a8a; font-size: 14px;">
                                            <strong>Customer:</strong> {{customer_name}}
                                        </p>
                                        <p style="margin: 0 0 8px; color: #1e3a8a; font-size: 14px;">
                                            <strong>Order Date:</strong> {{order_date}}
                                        </p>
                                        <p style="margin: 0; color: #1e3a8a; font-size: 14px;">
                                            <strong>Order Total:</strong> ${{order_total}} CAD
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Your Products in this Order -->
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
                                                <td align="right" style="padding: 8px 0 0; color: #3b82f6; font-size: 16px; font-weight: 700;">${{vendor_cost_total}} CAD</td>
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
                                        <a href="https://ocsapp.ca/vendor/order/{{order_id}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
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
                                Thank you for partnering with OCSAPP! 🤝
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Need help? Contact us at <a href="mailto:vendors@ocsapp.ca" style="color: #3b82f6; text-decoration: none;">vendors@ocsapp.ca</a>
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/vendor/dashboard" style="color: #6b7280; text-decoration: none;">Dashboard</a> •
                                <a href="https://ocsapp.ca/vendor/orders" style="color: #6b7280; text-decoration: none;">Orders</a> •
                                <a href="https://ocsapp.ca/vendor-central" style="color: #6b7280; text-decoration: none;">Vendor Central</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
