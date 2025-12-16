<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to OCSAPP Vendor Program</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">🎉</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Welcome to OCSAPP!
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Your vendor account has been created
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Hi {{company_name}}! 👋
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Great news! Your vendor account has been created by our admin team. You can now start managing your products and orders on the OCSAPP platform.
                            </p>

                            <!-- Credentials Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #1e40af; font-size: 20px;">🔐 Your Login Credentials</h3>

                                        <p style="margin: 0 0 12px; color: #1e3a8a; font-size: 14px;">
                                            <strong>Email:</strong> {{email}}
                                        </p>
                                        <p style="margin: 0 0 12px; color: #1e3a8a; font-size: 14px;">
                                            <strong>Password:</strong> {{password}}
                                        </p>
                                        <p style="margin: 0; color: #ef4444; font-size: 13px; font-weight: 600;">
                                            ⚠️ Please change your password after first login!
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Quick Start Guide -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">🚀 Quick Start Guide</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ol style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 12px;">Login to your vendor dashboard</li>
                                            <li style="margin-bottom: 12px;">Complete your vendor profile</li>
                                            <li style="margin-bottom: 12px;">Link your products to your vendor account</li>
                                            <li style="margin-bottom: 0;">Start receiving and approving orders</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Login Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{login_url}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            🔑 Login to Vendor Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support Info -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            Need Help?
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            Our vendor support team is here to help!<br>
                                            Email us at <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
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
                                Welcome to the OCSAPP Vendor Family! 🤝
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Need help? Contact us at <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/vendor/dashboard" style="color: #6b7280; text-decoration: none;">Dashboard</a> •
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
