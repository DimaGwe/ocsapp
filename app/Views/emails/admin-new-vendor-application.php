<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Vendor Application Received</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 650px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <div style="font-size: 64px; margin-bottom: 16px;">🔔</div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                New Vendor Application
                            </h1>
                            <p style="margin: 8px 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                A new vendor has applied to join OCSAPP
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                A new vendor application has been submitted and is awaiting your review in the admin panel.
                            </p>

                            <!-- Application Summary -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; margin-bottom: 24px; border: 2px solid #3b82f6;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <h3 style="margin: 0 0 20px; color: #1e40af; font-size: 20px;">📋 Application Summary</h3>

                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 10px 0; color: #1e3a8a; font-size: 14px; font-weight: 600; width: 35%;">Company Name:</td>
                                                <td style="padding: 10px 0; color: #1e40af; font-size: 15px; font-weight: 700;">{{company_name}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Contact Person:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">{{contact_person}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Email:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">
                                                    <a href="mailto:{{email}}" style="color: #2563eb; text-decoration: none;">{{email}}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Phone:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">{{phone}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Location:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">{{city}}, {{province}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Business Number:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">{{business_number}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Website:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">
                                                    {{website}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Application Date:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px;">{{application_date}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e3a8a; font-size: 14px; font-weight: 600;">Application ID:</td>
                                                <td style="padding: 10px 0; border-top: 1px solid #bfdbfe; color: #1e40af; font-size: 14px; font-family: monospace;">{{vendor_id}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Company Description -->
                            <?php if (!empty($description)): ?>
                            <div style="margin-bottom: 24px;">
                                <h3 style="margin: 0 0 12px; color: #1f2937; font-size: 18px;">💼 Company Description</h3>
                                <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; border-left: 4px solid #00b207;">
                                    <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6; font-style: italic;">
                                        "{{description}}"
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Full Address -->
                            <div style="margin-bottom: 24px;">
                                <h3 style="margin: 0 0 12px; color: #1f2937; font-size: 18px;">📍 Business Address</h3>
                                <div style="background-color: #f9fafb; padding: 16px; border-radius: 8px; font-size: 14px; color: #4b5563; line-height: 1.6;">
                                    {{address}}<br>
                                    {{city}}, {{province}} {{postal_code}}<br>
                                    Canada
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center" style="padding: 24px 0;">
                                        <a href="{{admin_vendor_url}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3); margin: 0 8px;">
                                            ✅ Review & Approve
                                        </a>
                                        <a href="{{admin_vendors_url}}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3); margin: 0 8px;">
                                            📋 View All Applications
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Review Checklist -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 8px; margin-bottom: 24px; border: 1px solid #f59e0b;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h4 style="margin: 0 0 12px; color: #92400e; font-size: 16px;">📝 Review Checklist</h4>
                                        <ul style="margin: 0; padding-left: 20px; color: #78350f; font-size: 13px; line-height: 1.8;">
                                            <li>Verify business registration number and tax ID</li>
                                            <li>Check company website and online presence</li>
                                            <li>Review company description for policy compliance</li>
                                            <li>Confirm contact information is valid</li>
                                            <li>Assess product category fit with OCSAPP marketplace</li>
                                            <li>Check for duplicate or existing vendor accounts</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Statistics -->
                            <div style="background-color: #f0fdf4; padding: 20px; border-radius: 8px; border: 1px solid #00b207; text-align: center;">
                                <p style="margin: 0; color: #166534; font-size: 13px;">
                                    <strong>Pending Applications:</strong> {{pending_count}} |
                                    <strong>Total Vendors:</strong> {{total_vendors}} |
                                    <strong>Active Vendors:</strong> {{active_vendors}}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 12px; color: #6b7280; font-size: 13px;">
                                This is an automated notification from the OCSAPP Admin System
                            </p>
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP Admin Panel. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="{{admin_dashboard_url}}" style="color: #6b7280; text-decoration: none;">Admin Dashboard</a> •
                                <a href="{{admin_vendors_url}}" style="color: #6b7280; text-decoration: none;">Vendor Management</a> •
                                <a href="{{admin_settings_url}}" style="color: #6b7280; text-decoration: none;">Settings</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
