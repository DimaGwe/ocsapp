<?php
/**
 * Privacy Policy - OCSAPP
 * File: app/Views/legal/privacy.php
 */
$currentLang = $_SESSION['language'] ?? 'fr';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - OCSAPP</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        .legal-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .legal-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #00b207;
        }
        .legal-header h1 {
            color: #1f2937;
            font-size: 32px;
            margin: 0 0 10px;
        }
        .legal-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        .legal-content h2 {
            color: #00b207;
            font-size: 24px;
            margin: 30px 0 15px;
            padding-top: 20px;
        }
        .legal-content h3 {
            color: #374151;
            font-size: 18px;
            margin: 20px 0 10px;
        }
        .legal-content p {
            color: #4b5563;
            line-height: 1.8;
            margin: 12px 0;
        }
        .legal-content ul, .legal-content ol {
            color: #4b5563;
            line-height: 1.8;
            margin: 12px 0;
            padding-left: 30px;
        }
        .legal-content li {
            margin: 8px 0;
        }
        .legal-content strong {
            color: #1f2937;
        }
        .highlight-box {
            background: #f0fdf4;
            border-left: 4px solid #00b207;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #00b207;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="page">
        <div class="legal-container">
            <a href="<?= url('/') ?>" class="back-link">← Back to Home</a>

            <div class="legal-header">
                <h1>Privacy Policy</h1>
                <p>Last Updated: <?= date('F j, Y') ?></p>
            </div>

            <div class="legal-content">
                <div class="highlight-box">
                    <p><strong>Your Privacy Matters</strong></p>
                    <p>OCSAPP is committed to protecting your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your data when you use our platform.</p>
                </div>

                <h2>1. Information We Collect</h2>

                <h3>1.1 Personal Information</h3>
                <p>We collect personal information that you voluntarily provide when you:</p>
                <ul>
                    <li>Create an account (name, email, phone number)</li>
                    <li>Make a purchase (shipping address, billing information)</li>
                    <li>Contact customer support</li>
                    <li>Subscribe to newsletters or promotions</li>
                    <li>Leave reviews or ratings</li>
                </ul>

                <h3>1.2 Business Information (Sellers)</h3>
                <p>For seller accounts, we collect:</p>
                <ul>
                    <li>Business registration details</li>
                    <li>Tax identification numbers</li>
                    <li>Bank account information for payments</li>
                    <li>Business contact information</li>
                    <li>Product and inventory data</li>
                </ul>

                <h3>1.3 Automatically Collected Information</h3>
                <p>When you use our platform, we automatically collect:</p>
                <ul>
                    <li>Device information (IP address, browser type, device type)</li>
                    <li>Usage data (pages visited, time spent, click patterns)</li>
                    <li>Location data (with your permission)</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <p>We use your information to:</p>

                <h3>2.1 Provide Services</h3>
                <ul>
                    <li>Process and fulfill orders</li>
                    <li>Manage your account</li>
                    <li>Process payments and refunds</li>
                    <li>Provide customer support</li>
                    <li>Send order confirmations and updates</li>
                </ul>

                <h3>2.2 Improve Our Platform</h3>
                <ul>
                    <li>Analyze usage patterns and trends</li>
                    <li>Develop new features and services</li>
                    <li>Enhance user experience</li>
                    <li>Conduct research and analytics</li>
                </ul>

                <h3>2.3 Marketing and Communications</h3>
                <ul>
                    <li>Send promotional emails (with your consent)</li>
                    <li>Provide personalized recommendations</li>
                    <li>Send service-related announcements</li>
                    <li>Notify you about account activity</li>
                </ul>

                <h3>2.4 Security and Fraud Prevention</h3>
                <ul>
                    <li>Detect and prevent fraudulent transactions</li>
                    <li>Monitor for suspicious activity</li>
                    <li>Enforce our Terms of Service</li>
                    <li>Comply with legal obligations</li>
                </ul>

                <h2>3. Information Sharing and Disclosure</h2>

                <h3>3.1 With Sellers</h3>
                <p>When you make a purchase, we share necessary information with the seller:</p>
                <ul>
                    <li>Your name and shipping address</li>
                    <li>Contact information for delivery</li>
                    <li>Order details</li>
                </ul>

                <h3>3.2 With Service Providers</h3>
                <p>We share information with trusted third parties who help us operate:</p>
                <ul>
                    <li>Payment processors (Stripe, PayPal, etc.)</li>
                    <li>Shipping and delivery services</li>
                    <li>Email service providers</li>
                    <li>Cloud hosting providers (AWS)</li>
                    <li>Analytics providers</li>
                </ul>

                <h3>3.3 Legal Requirements</h3>
                <p>We may disclose information when required by law or to:</p>
                <ul>
                    <li>Comply with legal processes or government requests</li>
                    <li>Enforce our Terms of Service</li>
                    <li>Protect our rights, property, or safety</li>
                    <li>Prevent fraud or security threats</li>
                </ul>

                <h3>3.4 Business Transfers</h3>
                <p>In the event of a merger, acquisition, or sale of assets, your information may be transferred to the new owner.</p>

                <h2>4. Data Security</h2>
                <p>We implement industry-standard security measures to protect your information:</p>
                <ul>
                    <li>SSL/TLS encryption for data transmission</li>
                    <li>Encrypted storage of sensitive data</li>
                    <li>Secure payment processing (PCI DSS compliant)</li>
                    <li>Regular security audits and updates</li>
                    <li>Access controls and authentication</li>
                    <li>Employee training on data protection</li>
                </ul>

                <div class="highlight-box">
                    <p><strong>Important:</strong> While we strive to protect your information, no method of transmission or storage is 100% secure. We cannot guarantee absolute security.</p>
                </div>

                <h2>5. Your Rights and Choices</h2>

                <h3>5.1 Access and Correction</h3>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal information</li>
                    <li>Correct inaccurate data</li>
                    <li>Update your account details</li>
                    <li>Request a copy of your data</li>
                </ul>

                <h3>5.2 Marketing Opt-Out</h3>
                <ul>
                    <li>Unsubscribe from promotional emails via the link in each email</li>
                    <li>Update your communication preferences in account settings</li>
                    <li>Contact us directly to opt out of marketing communications</li>
                </ul>

                <h3>5.3 Account Deletion</h3>
                <p>You may request account deletion by contacting us. Please note:</p>
                <ul>
                    <li>We may retain certain information for legal and business purposes</li>
                    <li>Order history may be retained for accounting and tax purposes</li>
                    <li>Deleted accounts cannot be recovered</li>
                </ul>

                <h3>5.4 Do Not Track</h3>
                <p>Our platform currently does not respond to "Do Not Track" browser signals.</p>

                <h2>6. Cookies and Tracking Technologies</h2>

                <h3>6.1 What Are Cookies?</h3>
                <p>Cookies are small text files stored on your device that help us provide and improve our services.</p>

                <h3>6.2 Types of Cookies We Use</h3>
                <ul>
                    <li><strong>Essential Cookies:</strong> Required for the platform to function</li>
                    <li><strong>Performance Cookies:</strong> Help us understand how you use our platform</li>
                    <li><strong>Functional Cookies:</strong> Remember your preferences</li>
                    <li><strong>Advertising Cookies:</strong> Deliver relevant advertisements</li>
                </ul>

                <h3>6.3 Managing Cookies</h3>
                <p>You can control cookies through your browser settings. Note that disabling cookies may affect platform functionality.</p>

                <h2>7. Children's Privacy</h2>
                <p>Our platform is not intended for children under 18. We do not knowingly collect information from minors. If you believe a child has provided us with personal information, please contact us immediately.</p>

                <h2>8. International Data Transfers</h2>
                <p>Your information may be transferred to and processed in countries outside of Canada. We ensure appropriate safeguards are in place to protect your data.</p>

                <h2>9. Data Retention</h2>
                <p>We retain your information for as long as necessary to:</p>
                <ul>
                    <li>Provide our services</li>
                    <li>Comply with legal obligations</li>
                    <li>Resolve disputes</li>
                    <li>Enforce our agreements</li>
                </ul>
                <p>Specific retention periods:</p>
                <ul>
                    <li>Account information: Active accounts + 7 years after closure</li>
                    <li>Transaction records: 7 years (tax and accounting requirements)</li>
                    <li>Marketing data: Until you opt out + 30 days</li>
                </ul>

                <h2>10. Third-Party Links</h2>
                <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. Please review their privacy policies before providing any information.</p>

                <h2>11. Canadian Privacy Laws</h2>
                <p>We comply with Canadian privacy laws, including:</p>
                <ul>
                    <li>Personal Information Protection and Electronic Documents Act (PIPEDA)</li>
                    <li>Quebec's Law 25 (Bill 64)</li>
                    <li>Provincial privacy legislation</li>
                </ul>

                <h2>12. Changes to This Privacy Policy</h2>
                <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Last Updated" date. Continued use of the platform after changes constitutes acceptance of the updated policy.</p>

                <h2>13. Contact Us</h2>
                <p>For questions about this Privacy Policy or to exercise your rights, contact us:</p>
                <ul>
                    <li><strong>Privacy Officer:</strong> <a href="mailto:privacy@ocsapp.ca" style="color: #00b207;">privacy@ocsapp.ca</a></li>
                    <li><strong>General Support:</strong> <a href="mailto:support@ocsapp.ca" style="color: #00b207;">support@ocsapp.ca</a></li>
                    <li><strong>Phone:</strong> +1 (809) 555-1234</li>
                    <li><strong>Mail:</strong> OCSAPP Privacy Officer<br>
                        [Address to be provided]<br>
                        Canada
                    </li>
                </ul>

                <div class="highlight-box" style="margin-top: 40px;">
                    <p><strong>Your Consent</strong></p>
                    <p>By using OCSAPP, you consent to the collection, use, and disclosure of your information as described in this Privacy Policy.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
