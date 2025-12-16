<?php
/**
 * Terms of Service - OCSAPP
 * File: app/Views/legal/terms.php
 */
$currentLang = $_SESSION['language'] ?? 'fr';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - OCSAPP</title>

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
                <h1>Terms of Service</h1>
                <p>Last Updated: <?= date('F j, Y') ?></p>
            </div>

            <div class="legal-content">
                <div class="highlight-box">
                    <p><strong>Welcome to OCSAPP!</strong></p>
                    <p>These Terms of Service govern your use of our platform. By accessing or using OCSAPP, you agree to be bound by these terms. Please read them carefully.</p>
                </div>

                <h2>1. Acceptance of Terms</h2>
                <p>By creating an account, accessing, or using OCSAPP ("the Platform"), you agree to comply with and be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>

                <h2>2. Definitions</h2>
                <ul>
                    <li><strong>"Platform"</strong> refers to OCSAPP, including the website, mobile applications, and all associated services.</li>
                    <li><strong>"User"</strong> refers to any person accessing or using the Platform.</li>
                    <li><strong>"Buyer"</strong> refers to users who purchase products through the Platform.</li>
                    <li><strong>"Seller"</strong> refers to registered Canadian or Quebec businesses that list and sell products on the Platform.</li>
                    <li><strong>"Products"</strong> refers to goods listed for sale on the Platform.</li>
                </ul>

                <h2>3. Account Registration</h2>

                <h3>3.1 Buyer Accounts</h3>
                <ul>
                    <li>You must be at least 18 years old to create a buyer account</li>
                    <li>You must provide accurate, current, and complete information</li>
                    <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                    <li>You are responsible for all activities that occur under your account</li>
                    <li>Buyer accounts are activated immediately upon registration</li>
                </ul>

                <h3>3.2 Seller Accounts</h3>
                <ul>
                    <li>Sellers must be registered Canadian or Quebec businesses</li>
                    <li>All seller applications are subject to review and approval</li>
                    <li>Sellers must provide valid business registration documents</li>
                    <li>Sellers must comply with all applicable laws and regulations</li>
                    <li>OCSAPP reserves the right to reject any seller application</li>
                    <li>Seller accounts are activated only after administrative approval</li>
                </ul>

                <h2>4. User Conduct</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Violate any laws, regulations, or third-party rights</li>
                    <li>Post false, inaccurate, misleading, or fraudulent content</li>
                    <li>Engage in any form of harassment, abuse, or threatening behavior</li>
                    <li>Attempt to gain unauthorized access to the Platform or other user accounts</li>
                    <li>Distribute viruses, malware, or other harmful code</li>
                    <li>Use the Platform for any illegal or unauthorized purpose</li>
                    <li>Circumvent any security features or restrictions</li>
                </ul>

                <h2>5. Seller Obligations</h2>

                <h3>5.1 Product Listings</h3>
                <ul>
                    <li>All product information must be accurate and truthful</li>
                    <li>Product images must accurately represent the items being sold</li>
                    <li>Prices must be clearly stated and include all applicable taxes</li>
                    <li>Sellers must maintain accurate inventory levels</li>
                </ul>

                <h3>5.2 Order Fulfillment</h3>
                <ul>
                    <li>Sellers must fulfill orders in a timely manner</li>
                    <li>Sellers must provide tracking information when available</li>
                    <li>Sellers are responsible for packaging products securely</li>
                    <li>Sellers must handle customer inquiries professionally</li>
                </ul>

                <h3>5.3 Prohibited Items</h3>
                <p>Sellers may not list:</p>
                <ul>
                    <li>Illegal or regulated items</li>
                    <li>Counterfeit or unauthorized products</li>
                    <li>Hazardous materials without proper certification</li>
                    <li>Items that violate intellectual property rights</li>
                    <li>Any products prohibited by Canadian law</li>
                </ul>

                <h2>6. Buyer Obligations</h2>
                <ul>
                    <li>Provide accurate shipping and payment information</li>
                    <li>Make timely payments for purchases</li>
                    <li>Accept delivery of ordered products</li>
                    <li>Review products fairly and honestly</li>
                    <li>Contact sellers or support for any issues before disputes</li>
                </ul>

                <h2>7. Payments and Fees</h2>

                <h3>7.1 Buyer Payments</h3>
                <ul>
                    <li>All prices are in Canadian Dollars (CAD)</li>
                    <li>Payment is processed at checkout</li>
                    <li>We accept major credit cards and other approved payment methods</li>
                    <li>Buyers are responsible for all applicable taxes</li>
                </ul>

                <h3>7.2 Seller Fees</h3>
                <ul>
                    <li>OCSAPP may charge commission fees on sales</li>
                    <li>Fee structure will be provided to sellers upon account approval</li>
                    <li>Sellers will receive payment after order completion</li>
                    <li>OCSAPP reserves the right to modify fee structures with notice</li>
                </ul>

                <h2>8. Returns and Refunds</h2>
                <ul>
                    <li>Return policies are set by individual sellers</li>
                    <li>Buyers should review seller return policies before purchase</li>
                    <li>OCSAPP may mediate disputes between buyers and sellers</li>
                    <li>Refunds will be processed according to the seller's stated policy</li>
                    <li>OCSAPP reserves the right to issue refunds in cases of fraud or policy violations</li>
                </ul>

                <h2>9. Intellectual Property</h2>
                <ul>
                    <li>All Platform content, including logos, designs, and software, is owned by OCSAPP</li>
                    <li>Users retain ownership of content they upload</li>
                    <li>By uploading content, users grant OCSAPP a license to use, display, and distribute it</li>
                    <li>Users must not infringe on others' intellectual property rights</li>
                </ul>

                <h2>10. Privacy and Data Protection</h2>
                <p>Your privacy is important to us. Please review our <a href="<?= url('privacy') ?>" style="color: #00b207; text-decoration: none; font-weight: 600;">Privacy Policy</a> to understand how we collect, use, and protect your personal information.</p>

                <h2>11. Dispute Resolution</h2>
                <ul>
                    <li>Users agree to attempt to resolve disputes amicably</li>
                    <li>OCSAPP may provide mediation services</li>
                    <li>Disputes will be governed by the laws of Canada and the Province of Quebec</li>
                    <li>Users agree to binding arbitration for unresolved disputes</li>
                </ul>

                <h2>12. Limitation of Liability</h2>
                <p>OCSAPP is a platform connecting buyers and sellers. We are not responsible for:</p>
                <ul>
                    <li>The quality, safety, or legality of products listed</li>
                    <li>The accuracy of listings or user content</li>
                    <li>The ability of sellers to fulfill orders</li>
                    <li>The ability of buyers to complete transactions</li>
                    <li>Any disputes between buyers and sellers</li>
                    <li>Any indirect, incidental, or consequential damages</li>
                </ul>

                <h2>13. Account Termination</h2>
                <p>OCSAPP reserves the right to:</p>
                <ul>
                    <li>Suspend or terminate accounts for violations of these terms</li>
                    <li>Remove listings that violate our policies</li>
                    <li>Refuse service to anyone for any reason</li>
                    <li>Modify or discontinue the Platform at any time</li>
                </ul>

                <h2>14. Changes to Terms</h2>
                <p>We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting. Continued use of the Platform constitutes acceptance of modified terms.</p>

                <h2>15. Contact Information</h2>
                <p>For questions about these Terms of Service, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:legal@ocsapp.ca" style="color: #00b207;">legal@ocsapp.ca</a></li>
                    <li><strong>Support:</strong> <a href="mailto:support@ocsapp.ca" style="color: #00b207;">support@ocsapp.ca</a></li>
                    <li><strong>Phone:</strong> +1 (809) 555-1234</li>
                </ul>

                <h2>16. Governing Law</h2>
                <p>These Terms of Service shall be governed by and construed in accordance with the laws of Canada and the Province of Quebec, without regard to conflict of law provisions.</p>

                <div class="highlight-box" style="margin-top: 40px;">
                    <p><strong>By using OCSAPP, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
