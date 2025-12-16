<?php
/**
 * OCSAPP Seller Central Page
 * Public landing page for seller partnerships
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
    <title>Seller Central - OCSAPP</title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .seller-central-page {
            min-height: 100vh;
            padding: 60px 20px 80px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .seller-hero {
            max-width: 1200px;
            margin: 0 auto 60px;
            text-align: center;
        }

        .seller-hero h1 {
            font-size: 48px;
            font-weight: 800;
            color: #00b207;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .seller-hero p {
            font-size: 20px;
            color: #4b5563;
            margin-bottom: 40px;
        }

        .seller-cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .seller-cta-btn {
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .seller-cta-primary {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
        }

        .seller-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 178, 7, 0.4);
        }

        .seller-cta-secondary {
            background: white;
            color: #00b207;
            border: 2px solid #00b207;
        }

        .seller-cta-secondary:hover {
            background: #00b207;
            color: white;
        }

        .seller-info-section {
            max-width: 1000px;
            margin: 0 auto 60px;
            background: white;
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .seller-info-section h2 {
            font-size: 32px;
            color: #00b207;
            margin-bottom: 30px;
            text-align: center;
        }

        .seller-info-section h3 {
            font-size: 24px;
            color: #1f2937;
            margin: 25px 0 15px;
            font-weight: 600;
        }

        .seller-info-section p {
            font-size: 17px;
            color: #4b5563;
            line-height: 1.9;
            margin-bottom: 20px;
            text-align: justify;
        }

        .seller-info-section .moto {
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

        .seller-info-section .cta-text {
            text-align: center;
            font-size: 20px;
            color: #00b207;
            font-weight: 600;
            margin-top: 30px;
        }

        .seller-features {
            max-width: 1200px;
            margin: 0 auto 60px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .seller-feature-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .seller-feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .seller-feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
        }

        .seller-feature-card h3 {
            font-size: 24px;
            color: #00b207;
            margin-bottom: 15px;
        }

        .seller-feature-card p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.8;
        }

        .seller-stats {
            max-width: 1200px;
            margin: 80px auto;
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            border-radius: 20px;
            padding: 60px 40px;
            color: white;
            text-align: center;
        }

        .seller-stats h2 {
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
            .seller-hero h1 {
                font-size: 32px;
            }

            .seller-hero p {
                font-size: 16px;
            }

            .seller-cta-buttons {
                flex-direction: column;
            }

            .seller-cta-btn {
                width: 100%;
            }

            .seller-info-section {
                padding: 30px 20px;
            }

            .seller-info-section h2 {
                font-size: 26px;
            }

            .seller-info-section h3 {
                font-size: 20px;
            }

            .seller-info-section p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="seller-central-page">
        <div class="seller-hero">
            <h1>🏪 Seller Central</h1>
            <p>Open your online store on OCSAPP and reach customers across Canada</p>

            <div class="seller-cta-buttons">
                <a href="<?= url('seller/login') ?>" class="seller-cta-btn seller-cta-primary">
                    <i class="fas fa-sign-in-alt"></i> Seller Login
                </a>
                <a href="<?= url('seller/register') ?>" class="seller-cta-btn seller-cta-secondary">
                    <i class="fas fa-store"></i> Become a Seller
                </a>
            </div>
        </div>

        <!-- Get to Know Us Section -->
        <div class="seller-info-section">
            <h2>Get to Know Us</h2>

            <h3>Sell on OCSAPP</h3>
            <p>
                The OCSapp platform promotes a wide range of offerings that prioritize ease and time saving
                solutions for consumers and businesses. Sell on OCSAPP; create an account to open an online
                store on the platform, and launch or increase your online presence instantly. The OCSapp platform
                provides a range of tools and features including a shop dashboard to view sales activity,
                analytics and insights, and reports to support you.
            </p>

            <p>
                Enjoy our services and leverage OCSapp technology to take your business from physical to
                virtual offering your clients and OCSapp users the benefit of visiting and purchasing from your
                online store on the OCSapp platform. The platform integrates key aspects of your business,
                including product or service information updates, stock picture uploads, pricing, inventory
                management, order processing, and delivery tracking through its user-friendly administrative
                portal which you can access and manage on your mobile phone. If you don't have the time, OCSapp
                can manage your shop for you.
            </p>

            <p>
                The OCSapp platform provides your clients and OCSapp users immediate access to your
                company profile, products, and services from anywhere with an internet connection, and ensures
                deliveries through its zero-carbon footprint distribution service.
            </p>

            <p>
                OCSapp is revolutionizing convenience by making buying and selling online very convenient
                and fun.
            </p>

            <p>
                Join OCSapp as a seller and offer the best virtual customer service experience to your
                customers.
            </p>

            <div class="moto">
                "Convenience is Priceless!"
            </div>

            <p class="cta-text">
                Find a more convenient way; register and find out more
            </p>
        </div>

        <!-- Features Grid -->
        <div class="seller-features">
            <div class="seller-feature-card">
                <span class="seller-feature-icon">🏪</span>
                <h3>Your Own Online Store</h3>
                <p>Create and customize your branded storefront on OCSAPP. Showcase your products and build your online presence instantly.</p>
            </div>

            <div class="seller-feature-card">
                <span class="seller-feature-icon">📊</span>
                <h3>Shop Dashboard & Analytics</h3>
                <p>View sales activity, track performance metrics, and access detailed reports. Make data-driven decisions to grow your business.</p>
            </div>

            <div class="seller-feature-card">
                <span class="seller-feature-icon">📱</span>
                <h3>Mobile Management</h3>
                <p>Manage your entire store from your mobile phone. Update products, process orders, and track inventory on the go.</p>
            </div>

            <div class="seller-feature-card">
                <span class="seller-feature-icon">📦</span>
                <h3>Inventory Management</h3>
                <p>Upload product images, update pricing, manage stock levels, and keep your catalog fresh with our easy-to-use tools.</p>
            </div>

            <div class="seller-feature-card">
                <span class="seller-feature-icon">🚚</span>
                <h3>Zero-Carbon Delivery</h3>
                <p>OCSAPP handles deliveries through our zero-carbon footprint distribution service. Your customers get eco-friendly shipping.</p>
            </div>

            <div class="seller-feature-card">
                <span class="seller-feature-icon">🤝</span>
                <h3>Optional Store Management</h3>
                <p>Don't have time? Let OCSAPP manage your shop for you. We'll handle the day-to-day operations while you focus on your business.</p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="seller-stats">
            <h2>Join Thousands of Successful Sellers</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">10,000+</span>
                    <span class="stat-label">Active Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Seller Stores</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">$5M+</span>
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
