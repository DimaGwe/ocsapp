<?php
/**
 * OCSAPP Vendor Central Page
 * Public landing page for vendor partnerships
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Central - OCSAPP</title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .vendor-central-page {
            min-height: 100vh;
            padding: 60px 20px 80px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .vendor-hero {
            max-width: 1200px;
            margin: 0 auto 60px;
            text-align: center;
        }

        .vendor-hero h1 {
            font-size: 48px;
            font-weight: 800;
            color: #00b207;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .vendor-hero p {
            font-size: 20px;
            color: #4b5563;
            margin-bottom: 40px;
        }

        .vendor-cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .vendor-cta-btn {
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .vendor-cta-primary {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
        }

        .vendor-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 178, 7, 0.4);
        }

        .vendor-cta-secondary {
            background: white;
            color: #00b207;
            border: 2px solid #00b207;
        }

        .vendor-cta-secondary:hover {
            background: #00b207;
            color: white;
        }

        .vendor-info-section {
            max-width: 1000px;
            margin: 0 auto 60px;
            background: white;
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .vendor-info-section h2 {
            font-size: 32px;
            color: #00b207;
            margin-bottom: 30px;
            text-align: center;
        }

        .vendor-info-section h3 {
            font-size: 24px;
            color: #1f2937;
            margin: 25px 0 15px;
            font-weight: 600;
        }

        .vendor-info-section p {
            font-size: 17px;
            color: #4b5563;
            line-height: 1.9;
            margin-bottom: 20px;
            text-align: justify;
        }

        .vendor-info-section .moto {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            font-size: 24px;
            font-weight: 700;
            font-style: italic;
        }

        .vendor-info-section .cta-text {
            text-align: center;
            font-size: 20px;
            color: #00b207;
            font-weight: 600;
            margin-top: 30px;
        }

        .vendor-features {
            max-width: 1200px;
            margin: 0 auto 60px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .vendor-feature-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .vendor-feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .vendor-feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
        }

        .vendor-feature-card h3 {
            font-size: 24px;
            color: #00b207;
            margin-bottom: 15px;
        }

        .vendor-feature-card p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.8;
        }

        .vendor-stats {
            max-width: 1200px;
            margin: 80px auto;
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            border-radius: 20px;
            padding: 60px 40px;
            color: white;
            text-align: center;
        }

        .vendor-stats h2 {
            font-size: 36px;
            margin-bottom: 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: 800;
            display: block;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 18px;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .vendor-hero h1 {
                font-size: 32px;
            }

            .vendor-hero p {
                font-size: 16px;
            }

            .vendor-cta-buttons {
                flex-direction: column;
            }

            .vendor-cta-btn {
                width: 100%;
            }

            .vendor-info-section {
                padding: 30px 20px;
            }

            .vendor-info-section h2 {
                font-size: 26px;
            }

            .vendor-info-section h3 {
                font-size: 20px;
            }

            .vendor-info-section p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="vendor-central-page">
        <div class="vendor-hero">
            <h1>🤝 Vendor Central</h1>
            <p>Partner with OCSAPP and grow your business across Canada</p>

            <div class="vendor-cta-buttons">
                <a href="<?= url('vendor/login') ?>" class="vendor-cta-btn vendor-cta-primary">
                    <i class="fas fa-sign-in-alt"></i> Vendor Login
                </a>
                <a href="<?= url('vendor/apply') ?>" class="vendor-cta-btn vendor-cta-secondary">
                    <i class="fas fa-clipboard-list"></i> Apply Now
                </a>
            </div>
        </div>

        <!-- Get to Know Us Section -->
        <div class="vendor-info-section">
            <h2>Get to Know Us</h2>

            <h3>OCSAPP Vendor Central</h3>
            <p>
                OCSapp welcomes all licensed Manufacturers, Retailers, Service providers, Importers,
                Independent Vendors, and Dropshippers registered in Canada and Quebec to join our digital
                transformation initiatives via OCSAPP Vendor Central portal.
            </p>

            <p>
                With OCSAPP Vendor Central; it is more than a partnership, it's a "partner-fit". Within this "partner-fit"
                you have time to focus on your core business and OCSAPP does the rest. It offers you the
                opportunity to pivot and expand by letting OCSAPP purchase and store your products in high
                volume, prioritize the product's online presence on the OCSapp platform, and handle all aspects
                of the sale process including marketing, customer service, pricing, and distribution.
            </p>

            <p>
                The OCSapp platform provides vendors with a range of tools and features to support fulfillment
                management. The logistic process is streamlined with access to update product information,
                inventory, pricing, and orders. A vendor dashboard is also integrated to display product sales
                activity, analytics and insights, and additional reports to facilitate the fulfillment of OCSAPP purchase
                orders.
            </p>

            <div class="moto">
                "Convenience is Priceless!"
            </div>

            <p class="cta-text">
                Find a more convenient way; register and find out more
            </p>
        </div>

        <!-- Features Grid -->
        <div class="vendor-features">
            <div class="vendor-feature-card">
                <span class="vendor-feature-icon">📊</span>
                <h3>Real-Time Statistics</h3>
                <p>Track your product performance, sales, and revenue with live analytics dashboards. Know exactly how your products are performing on our platform.</p>
            </div>

            <div class="vendor-feature-card">
                <span class="vendor-feature-icon">✅</span>
                <h3>Order Approval Control</h3>
                <p>Review and approve orders before fulfillment. Maintain complete control over your supply chain and inventory management.</p>
            </div>

            <div class="vendor-feature-card">
                <span class="vendor-feature-icon">🚚</span>
                <h3>Streamlined Logistics</h3>
                <p>Let OCSAPP handle customer delivery. Focus on what you do best - supplying quality products. We'll take care of the rest.</p>
            </div>

            <div class="vendor-feature-card">
                <span class="vendor-feature-icon">💰</span>
                <h3>Transparent Payments</h3>
                <p>Get paid on time with transparent invoicing. Track every transaction and payment with complete visibility.</p>
            </div>

            <div class="vendor-feature-card">
                <span class="vendor-feature-icon">📱</span>
                <h3>Mobile-Friendly Portal</h3>
                <p>Manage your vendor account from anywhere. Our mobile-optimized portal lets you approve orders and check stats on the go.</p>
            </div>

            <div class="vendor-feature-card">
                <span class="vendor-feature-icon">🤝</span>
                <h3>Dedicated Support</h3>
                <p>Get help when you need it. Our vendor support team is available to assist with any questions or issues.</p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="vendor-stats">
            <h2>Why Partner With OCSAPP?</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">10,000+</span>
                    <span class="stat-label">Active Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Vendor Partners</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">$2M+</span>
                    <span class="stat-label">Monthly Sales</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Canadian Owned</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
