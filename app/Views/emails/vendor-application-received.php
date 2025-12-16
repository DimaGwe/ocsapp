<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Application Received - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">📝</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Application Received!
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Thank you for applying to become an OCSAPP vendor
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
                                We've successfully received your vendor application for the OCSAPP marketplace. Our team is excited to review your submission!
                            </p>

                            <!-- Application Details -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #00b207;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 16px; color: #166534; font-size: 18px;">📋 Application Details</h3>

                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Company Name:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{company_name}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Email:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{email}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Application Date:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px;">{{application_date}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #15803d; font-size: 14px; font-weight: 600;">Reference Number:</td>
                                                <td style="padding: 8px 0; color: #166534; font-size: 14px; font-family: monospace;">{{application_id}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- What Happens Next -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">⏳ What Happens Next?</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ol style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li style="margin-bottom: 12px;">
                                                <strong>Application Review (1-3 business days)</strong><br>
                                                <span style="color: #6b7280;">Our vendor onboarding team will carefully review your application and company details</span>
                                            </li>
                                            <li style="margin-bottom: 12px;">
                                                <strong>Verification & Due Diligence</strong><br>
                                                <span style="color: #6b7280;">We may contact you for additional information or documentation if needed</span>
                                            </li>
                                            <li style="margin-bottom: 12px;">
                                                <strong>Decision Notification</strong><br>
                                                <span style="color: #6b7280;">You'll receive an email with our decision within 3-5 business days</span>
                                            </li>
                                            <li style="margin-bottom: 0;">
                                                <strong>Onboarding & Setup</strong><br>
                                                <span style="color: #6b7280;">Once approved, we'll guide you through setting up your vendor account</span>
                                            </li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Important Info Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; margin-bottom: 24px; border: 1px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h4 style="margin: 0 0 12px; color: #92400e; font-size: 16px;">⚠️ Important Information</h4>
                                        <ul style="margin: 0; padding-left: 20px; color: #78350f; font-size: 13px; line-height: 1.8;">
                                            <li>Please check your spam/junk folder for our emails</li>
                                            <li>Keep this reference number for your records: <strong>{{application_id}}</strong></li>
                                            <li>We'll contact you at {{email}} with updates</li>
                                            <li>No action is required from you at this time</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- While You Wait -->
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">📚 While You Wait</h3>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Get a head start by learning more about selling on OCSAPP:
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <ul style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                            <li><a href="https://ocsapp.ca/vendor-central" style="color: #00b207; text-decoration: none; font-weight: 600;">Explore Vendor Central</a> - Learn about our platform</li>
                                            <li><a href="https://ocsapp.ca/help/vendor-guide" style="color: #00b207; text-decoration: none; font-weight: 600;">Vendor Success Guide</a> - Best practices and tips</li>
                                            <li><a href="https://ocsapp.ca/help/fees" style="color: #00b207; text-decoration: none; font-weight: 600;">Fee Structure</a> - Understand our pricing</li>
                                            <li><a href="https://ocsapp.ca/help/product-guidelines" style="color: #00b207; text-decoration: none; font-weight: 600;">Product Guidelines</a> - What you can sell</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Support Info -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="margin: 0 0 12px; color: #1f2937; font-size: 15px; font-weight: 600;">
                                            Have Questions About Your Application?
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            Our vendor support team is here to help!<br>
                                            Email us at <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a><br>
                                            Please include your reference number: <strong>{{application_id}}</strong>
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
                                Thank you for your interest in OCSAPP! 🙏
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Questions? Contact us at <a href="mailto:vendors@ocsapp.ca" style="color: #00b207; text-decoration: none;">vendors@ocsapp.ca</a>
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca" style="color: #6b7280; text-decoration: none;">Visit Website</a> •
                                <a href="https://ocsapp.ca/vendor-central" style="color: #6b7280; text-decoration: none;">Vendor Central</a> •
                                <a href="https://ocsapp.ca/help" style="color: #6b7280; text-decoration: none;">Help Center</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
