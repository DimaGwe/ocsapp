<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Seller Application - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header with Orange Gradient (Admin Alert) -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">🔔</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                New Seller Application
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Action Required: Seller Approval
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                A new seller has registered and is awaiting approval. Please review the application details below:
                            </p>

                            <!-- Seller Details Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px; border: 2px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #f59e0b; font-size: 18px;">👤 Seller Information</h3>

                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px;">
                                            <strong>Name:</strong> {{seller_first_name}} {{seller_last_name}}
                                        </p>
                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px;">
                                            <strong>Email:</strong> <a href="mailto:{{seller_email}}" style="color: #00b207; text-decoration: none;">{{seller_email}}</a>
                                        </p>
                                        <p style="margin: 0 0 10px; color: #1f2937; font-size: 15px;">
                                            <strong>Phone:</strong> {{seller_phone}}
                                        </p>
                                        <p style="margin: 0; color: #1f2937; font-size: 15px;">
                                            <strong>Registered:</strong> {{submitted_date}}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Status Alert Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 8px; margin-bottom: 32px; border-left: 4px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">
                                            ⏳ Current Status: Pending Approval
                                        </p>
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">
                                            This seller cannot list products or access their seller dashboard until you approve their application.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Buttons -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Take Action:</h3>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://ocsapp.ca/admin/sellers" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            ✅ Review & Approve
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/admin/sellers" style="display: inline-block; padding: 14px 32px; background: #6b7280; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                            📋 View All Sellers
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 18px;">What to Review:</h3>

                            <ul style="margin: 0 0 24px; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                <li>Verify seller identity and contact information</li>
                                <li>Check if business information is legitimate</li>
                                <li>Review any submitted business documents</li>
                                <li>Ensure compliance with marketplace policies</li>
                                <li>Approve or reject the application</li>
                            </ul>

                            <!-- Quick Stats Box (Optional) -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; border: 1px solid #bbf7d0;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 4px; color: #166534; font-size: 12px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">
                                            📊 Quick Reminder
                                        </p>
                                        <p style="margin: 0; color: #166534; font-size: 13px; line-height: 1.6;">
                                            Approved sellers receive instant notification and can start listing products immediately.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP Admin Panel
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                This email was sent to admin@ocsapp.ca
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/admin" style="color: #6b7280; text-decoration: none;">Admin Dashboard</a> •
                                <a href="https://ocsapp.ca/admin/settings" style="color: #6b7280; text-decoration: none;">Settings</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
