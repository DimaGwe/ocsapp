<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

$pageTitle = $fr ? 'Portail Distribution - OCSAPP' : 'Business Central - OCSAPP';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $pageTitle ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; color: #1f2937; }
        footer.footer { margin-top: 0; }

        /* ── Hero ── */
        .bc-hero {
            background: linear-gradient(135deg, #0a1628 0%, #0d2137 50%, #071220 100%);
            color: white;
            text-align: center;
            padding: 100px 24px 80px;
            position: relative;
            overflow: hidden;
        }
        .bc-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .bc-hero-badge {
            display: inline-block;
            background: rgba(0,178,7,0.18);
            color: #4ade80;
            border: 1px solid rgba(0,178,7,0.35);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 28px;
        }
        .bc-hero h1 {
            font-size: clamp(30px, 5vw, 54px);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .bc-hero h1 span { color: #4ade80; }
        .bc-hero p {
            font-size: clamp(15px, 2.2vw, 18px);
            opacity: 0.85;
            max-width: 620px;
            margin: 0 auto 40px;
            line-height: 1.65;
        }
        .bc-hero-btns {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .bc-btn-primary {
            background: #00b207;
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(0,178,7,0.4);
        }
        .bc-btn-primary:hover {
            background: #009906;
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,178,7,0.5);
        }
        .bc-btn-secondary {
            background: rgba(255,255,255,0.12);
            color: white;
            border: 2px solid rgba(255,255,255,0.45);
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            backdrop-filter: blur(4px);
        }
        .bc-btn-secondary:hover {
            background: rgba(255,255,255,0.22);
            border-color: white;
            transform: translateY(-2px);
        }

        /* ── Stats bar ── */
        .bc-stats {
            background: white;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            border-bottom: 1px solid #e5e7eb;
        }
        .bc-stat {
            flex: 1;
            min-width: 150px;
            max-width: 240px;
            padding: 28px 16px;
            text-align: center;
            border-right: 1px solid #e5e7eb;
        }
        .bc-stat:last-child { border-right: none; }
        .bc-stat-val {
            font-size: 30px;
            font-weight: 800;
            color: #00b207;
            line-height: 1;
            margin-bottom: 6px;
        }
        .bc-stat-lbl {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }

        /* ── Sections ── */
        .bc-section {
            padding: 80px 24px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .bc-section-title {
            font-size: clamp(22px, 3.5vw, 34px);
            font-weight: 800;
            text-align: center;
            margin-bottom: 12px;
        }
        .bc-section-sub {
            text-align: center;
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 52px;
        }
        .bc-alt-bg { background: #f0fdf4; }

        /* ── Pillars ── */
        .bc-pillars {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            text-align: center;
        }
        .bc-pillar-icon {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: #00b207;
        }
        .bc-pillar h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .bc-pillar p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.65;
        }

        /* ── 3-step flow ── */
        .bc-steps {
            display: flex;
            gap: 0;
            flex-wrap: wrap;
            justify-content: center;
        }
        .bc-step {
            flex: 1;
            min-width: 200px;
            max-width: 280px;
            text-align: center;
            padding: 24px 20px;
            position: relative;
        }
        .bc-step:not(:last-child)::after {
            content: '→';
            position: absolute;
            right: -12px;
            top: 40px;
            font-size: 26px;
            color: #d1d5db;
        }
        .bc-step-num {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #00b207, #009906);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            margin: 0 auto 16px;
            box-shadow: 0 4px 14px rgba(0,178,7,0.3);
        }
        .bc-step h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bc-step p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.55;
        }

        /* ── Feature cards ── */
        .bc-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .bc-feature {
            background: white;
            border-radius: 16px;
            padding: 32px 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bc-feature:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.10);
        }
        .bc-feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 18px;
        }
        .bc-feature h3 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bc-feature p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.65;
        }

        /* ── Social proof ── */
        .bc-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-bottom: 40px;
        }
        .bc-chip {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .bc-quote {
            background: white;
            border-radius: 16px;
            padding: 32px 36px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            text-align: center;
        }
        .bc-quote p {
            font-size: 17px;
            color: #374151;
            font-style: italic;
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .bc-quote span {
            font-size: 13px;
            color: #9ca3af;
            font-weight: 500;
        }

        /* ── FAQ ── */
        .bc-faq { max-width: 720px; margin: 0 auto; }
        .bc-faq-item {
            border-bottom: 1px solid #e5e7eb;
        }
        .bc-faq-item:first-child { border-top: 1px solid #e5e7eb; }
        .bc-faq-q {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
            padding: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            gap: 16px;
            font-family: inherit;
        }
        .bc-faq-q i { color: #00b207; flex-shrink: 0; transition: transform 0.25s; }
        .bc-faq-q.open i { transform: rotate(45deg); }
        .bc-faq-a {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            padding-bottom: 20px;
            display: none;
        }
        .bc-faq-a.open { display: block; }

        /* ── Contact dark box ── */
        .bc-contact-box {
            background: #0a1628;
            color: white;
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }
        .bc-contact-box h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bc-contact-box p {
            color: rgba(255,255,255,0.65);
            font-size: 14px;
            margin-bottom: 28px;
        }
        .bc-contact-links {
            display: flex;
            gap: 24px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .bc-contact-links a {
            color: #4ade80;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .bc-contact-links a:hover { color: #86efac; }

        /* ── Bottom CTA ── */
        .bc-cta {
            background: linear-gradient(135deg, #00b207 0%, #009906 100%);
            color: white;
            text-align: center;
            padding: 80px 24px;
        }
        .bc-cta h2 {
            font-size: clamp(24px, 4vw, 38px);
            font-weight: 800;
            margin-bottom: 16px;
        }
        .bc-cta p {
            font-size: 17px;
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto 36px;
            line-height: 1.6;
        }
        .bc-cta-btns {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .bc-cta-btn-primary {
            background: white;
            color: #00b207;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .bc-cta-btn-primary:hover { background: #f0fdf4; transform: translateY(-2px); }
        .bc-cta-btn-secondary {
            background: rgba(255,255,255,0.18);
            color: white;
            border: 2px solid rgba(255,255,255,0.6);
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .bc-cta-btn-secondary:hover { background: rgba(255,255,255,0.28); transform: translateY(-2px); }

        @media (max-width: 640px) {
            .bc-hero { padding: 70px 20px 60px; }
            .bc-step:not(:last-child)::after { display: none; }
            .bc-contact-box { padding: 36px 24px; }
        }

        /* suppress global header spacing against dark hero */
        .header { margin-bottom: 0; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Hero -->
    <section class="bc-hero">
        <div class="bc-hero-badge">
            <i class="fas fa-truck-fast"></i>
            <?= $fr ? 'Livraison d\'entreprise zéro carbone' : 'Zero Carbon Business Delivery' ?>
        </div>
        <h1>
            <?= $fr ? 'Approvisionnement &amp; Livraison<br>pour <span>Votre Entreprise</span>' : 'Procurement &amp; Delivery<br>for <span>Your Business</span>' ?>
        </h1>
        <p>
            <?= $fr
                ? 'Laissez-nous gérer vos achats et livraisons. Du Costco aux fournitures de bureau - nous récupérons ce dont vous avez besoin et le livrons à votre porte.'
                : 'Let us handle your shopping and delivery. From Costco runs to office supplies - we pick up what you need and deliver it right to your door.' ?>
        </p>
        <div class="bc-hero-btns">
            <a href="<?= url('distribution/register') ?>" class="bc-btn-primary">
                <i class="fas fa-building"></i>
                <?= $fr ? 'Inscrire votre entreprise' : 'Register Your Business' ?>
            </a>
            <a href="<?= url('distribution/login') ?>" class="bc-btn-secondary">
                <i class="fas fa-sign-in-alt"></i>
                <?= $fr ? 'Déjà inscrit? Se connecter' : 'Already Registered? Sign In' ?>
            </a>
        </div>
    </section>

    <!-- Stats -->
    <div class="bc-stats">
        <div class="bc-stat">
            <div class="bc-stat-val">500+</div>
            <div class="bc-stat-lbl"><?= $fr ? 'Entreprises servies' : 'Businesses served' ?></div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-val"><?= $fr ? 'Jour J' : 'Same Day' ?></div>
            <div class="bc-stat-lbl"><?= $fr ? 'Livraison disponible' : 'Delivery available' ?></div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-val">0</div>
            <div class="bc-stat-lbl"><?= $fr ? 'Frais cachés' : 'Hidden fees' ?></div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-val">100%</div>
            <div class="bc-stat-lbl"><?= $fr ? 'Objectif écologique' : 'Eco-delivery goal' ?></div>
        </div>
    </div>

    <!-- Pillars -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Pourquoi choisir OCSAPP?' : 'Why Choose OCSAPP?' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Tout ce dont votre entreprise a besoin, livré.' : 'Everything your business needs, delivered.' ?></p>
        <div class="bc-pillars">
            <div class="bc-pillar">
                <div class="bc-pillar-icon"><i class="fas fa-shopping-cart"></i></div>
                <h3><?= $fr ? 'On fait vos achats' : 'We Shop for You' ?></h3>
                <p><?= $fr ? 'Envoyez votre liste et nous récupérons vos articles dans n\'importe quel magasin - Costco, Walmart, pharmacie et plus.' : 'Send your list and we\'ll pick up from any store - Costco, Walmart, pharmacy, and more.' ?></p>
            </div>
            <div class="bc-pillar">
                <div class="bc-pillar-icon"><i class="fas fa-truck"></i></div>
                <h3><?= $fr ? 'Livraison ponctuelle' : 'On-Time Delivery' ?></h3>
                <p><?= $fr ? 'Livraison le jour même disponible. Vos fournitures arrivent quand vous en avez besoin, sans surprises.' : 'Same-day delivery available. Your supplies arrive when you need them, no surprises.' ?></p>
            </div>
            <div class="bc-pillar">
                <div class="bc-pillar-icon"><i class="fas fa-receipt"></i></div>
                <h3><?= $fr ? 'Tarification transparente' : 'Transparent Pricing' ?></h3>
                <p><?= $fr ? 'Payez par commande. Voyez le coût des articles et nos frais de service à l\'avance. Aucun frais caché.' : 'Pay per order. See item costs and our service fee upfront. Zero hidden fees.' ?></p>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="bc-alt-bg">
        <div class="bc-section">
            <h2 class="bc-section-title"><?= $fr ? 'Comment ça marche' : 'How It Works' ?></h2>
            <p class="bc-section-sub"><?= $fr ? 'En trois étapes simples' : 'Three simple steps' ?></p>
            <div class="bc-steps">
                <div class="bc-step">
                    <div class="bc-step-num">1</div>
                    <h3><?= $fr ? 'Inscrivez-vous' : 'Register' ?></h3>
                    <p><?= $fr ? 'Créez votre compte entreprise gratuit en quelques minutes.' : 'Create your free business account in minutes.' ?></p>
                </div>
                <div class="bc-step">
                    <div class="bc-step-num">2</div>
                    <h3><?= $fr ? 'Soumettez une demande' : 'Submit a Request' ?></h3>
                    <p><?= $fr ? 'Envoyez votre liste d\'achats avec les magasins préférés et les détails de livraison.' : 'Send your shopping list with preferred stores and delivery details.' ?></p>
                </div>
                <div class="bc-step">
                    <div class="bc-step-num">3</div>
                    <h3><?= $fr ? 'Recevez votre livraison' : 'Get Delivered' ?></h3>
                    <p><?= $fr ? 'Nous achetons et livrons. Suivez votre commande en temps réel.' : 'We shop and deliver. Track your order in real time.' ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Tout ce que vous obtenez' : 'Everything You Get' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Des outils conçus pour les entreprises modernes' : 'Tools built for modern businesses' ?></p>
        <div class="bc-features">
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-shopping-bag"></i></div>
                <h3><?= $fr ? 'Achats personnalisés' : 'Personal Shopping' ?></h3>
                <p><?= $fr ? 'Nous achetons exactement ce que vous demandez - marque, taille et quantité précisées.' : 'We buy exactly what you ask for - specific brand, size, and quantity.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-bolt"></i></div>
                <h3><?= $fr ? 'Livraison le jour même' : 'Same-Day Delivery' ?></h3>
                <p><?= $fr ? 'Commandez avant midi, recevez avant la fin de journée. Urgent? Nous nous adaptons.' : 'Order by noon, receive before end of day. Urgent? We\'ll make it work.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#d1fae5;color:#065f46;"><i class="fas fa-leaf"></i></div>
                <h3><?= $fr ? 'Livraison écologique' : 'Eco-Friendly Delivery' ?></h3>
                <p><?= $fr ? 'Notre réseau minimise l\'empreinte carbone. Logistique durable pour des entreprises responsables.' : 'Our network minimizes carbon footprint. Sustainable logistics for conscious businesses.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-chart-line"></i></div>
                <h3><?= $fr ? 'Tableau de bord entreprise' : 'Business Dashboard' ?></h3>
                <p><?= $fr ? 'Suivez toutes vos commandes, historique et dépenses depuis votre portail en ligne.' : 'Track all your orders, history, and spending from your online portal.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-map-marker-alt"></i></div>
                <h3><?= $fr ? 'Suivi en temps réel' : 'Real-Time Tracking' ?></h3>
                <p><?= $fr ? 'Sachez exactement où est votre livraison à chaque étape du processus.' : 'Know exactly where your delivery is at every step of the process.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-headset"></i></div>
                <h3><?= $fr ? 'Support dédié' : 'Dedicated Support' ?></h3>
                <p><?= $fr ? 'Une équipe dédiée pour répondre à vos questions et résoudre tout problème rapidement.' : 'A dedicated team to answer questions and resolve any issues quickly.' ?></p>
            </div>
        </div>
    </div>

    <!-- Social proof -->
    <div class="bc-alt-bg">
        <div class="bc-section">
            <h2 class="bc-section-title"><?= $fr ? 'Des entreprises qui nous font confiance' : 'Businesses That Trust Us' ?></h2>
            <p class="bc-section-sub"><?= $fr ? 'De tous les secteurs, à travers le Canada' : 'Across all industries, across Canada' ?></p>
            <div class="bc-chips">
                <span class="bc-chip"><i class="fas fa-utensils" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Restaurants' : 'Restaurants' ?></span>
                <span class="bc-chip"><i class="fas fa-briefcase" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Bureaux' : 'Offices' ?></span>
                <span class="bc-chip"><i class="fas fa-store" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Commerce de détail' : 'Retail Stores' ?></span>
                <span class="bc-chip"><i class="fas fa-clinic-medical" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Cliniques' : 'Clinics' ?></span>
                <span class="bc-chip"><i class="fas fa-hammer" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Construction' : 'Construction' ?></span>
                <span class="bc-chip"><i class="fas fa-graduation-cap" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Éducation' : 'Education' ?></span>
            </div>
            <div class="bc-quote">
                <p>"<?= $fr ? 'OCSAPP gère toutes nos courses chez Costco. Ça nous fait gagner des heures chaque semaine et la livraison est toujours ponctuelle.' : 'OCSAPP handles all our Costco runs. It saves us hours every week and the delivery is always on time.' ?>"</p>
                <span><?= $fr ? '- Responsable des opérations, Restaurant local' : '- Operations Manager, Local Restaurant' ?></span>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Questions fréquentes' : 'Frequently Asked Questions' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Tout ce que vous devez savoir' : 'Everything you need to know' ?></p>
        <div class="bc-faq">
            <?php
            $faqs = [
                [
                    'q' => $fr ? 'Comment puis-je inscrire mon entreprise?' : 'How do I register my business?',
                    'a' => $fr ? 'Cliquez sur "Inscrire votre entreprise", remplissez le formulaire en quelques minutes et votre compte est actif immédiatement.' : 'Click "Register Your Business", fill out the form in a few minutes, and your account is active right away.',
                ],
                [
                    'q' => $fr ? 'Dans quels magasins faites-vous les achats?' : 'Which stores do you shop at?',
                    'a' => $fr ? 'Nous travaillons avec Costco, Walmart, épiceries, pharmacies, quincailleries et pratiquement tout autre commerce de détail selon votre demande.' : 'We work with Costco, Walmart, grocery stores, pharmacies, hardware stores, and virtually any retail store on request.',
                ],
                [
                    'q' => $fr ? 'Combien ça coûte?' : 'How much does it cost?',
                    'a' => $fr ? 'Vous payez le coût des articles plus des frais de service transparents - aucun abonnement mensuel, aucun frais caché. Le détail des frais vous est communiqué avant confirmation.' : 'You pay the item cost plus transparent service fees - no monthly subscription, no hidden charges. Fee details are shown before you confirm.',
                ],
                [
                    'q' => $fr ? 'Puis-je suivre ma commande en temps réel?' : 'Can I track my order in real time?',
                    'a' => $fr ? 'Oui. Votre tableau de bord affiche les mises à jour en direct de chaque étape - achats, transit, livraison.' : 'Yes. Your dashboard shows live updates at every stage - shopping, transit, delivery.',
                ],
                [
                    'q' => $fr ? 'Que se passe-t-il si un article est indisponible?' : 'What if an item is out of stock?',
                    'a' => $fr ? 'Nous vous contactons immédiatement pour proposer un substitut ou annuler l\'article. Vous avez toujours le dernier mot.' : 'We contact you immediately to suggest a substitute or remove the item. You always have the final say.',
                ],
                [
                    'q' => $fr ? 'Quelles zones sont desservies?' : 'Which areas do you serve?',
                    'a' => $fr ? 'Nous desservons actuellement les zones métropolitaines canadiennes. Contactez-nous pour vérifier la disponibilité dans votre région.' : 'We currently serve Canadian metropolitan areas. Contact us to verify availability in your region.',
                ],
            ];
            foreach ($faqs as $i => $faq): ?>
            <div class="bc-faq-item">
                <button class="bc-faq-q" onclick="bcToggleFaq(this)" type="button">
                    <?= htmlspecialchars($faq['q']) ?>
                    <i class="fas fa-plus"></i>
                </button>
                <div class="bc-faq-a"><?= htmlspecialchars($faq['a']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Contact -->
    <div class="bc-section" style="padding-top:0;">
        <div class="bc-contact-box">
            <h3><?= $fr ? 'Une question? Contactez-nous.' : 'Have a question? Get in touch.' ?></h3>
            <p><?= $fr ? 'Notre équipe répond dans les 24 heures ouvrables.' : 'Our team responds within 24 business hours.' ?></p>
            <div class="bc-contact-links">
                <a href="mailto:info@ocsapp.ca"><i class="fas fa-envelope"></i> info@ocsapp.ca</a>
                <a href="tel:5147463789"><i class="fas fa-phone"></i> 514-746-3789</a>
                <a href="<?= url('home') ?>"><i class="fas fa-globe"></i> ocsapp.ca</a>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <section class="bc-cta">
        <h2><?= $fr ? 'Prêt à simplifier votre approvisionnement?' : 'Ready to Simplify Your Procurement?' ?></h2>
        <p><?= $fr ? 'Rejoignez les entreprises canadiennes qui font confiance à OCSAPP. Créez votre compte gratuitement dès aujourd\'hui.' : 'Join Canadian businesses that trust OCSAPP. Create your free account today.' ?></p>
        <div class="bc-cta-btns">
            <a href="<?= url('distribution/register') ?>" class="bc-cta-btn-primary">
                <i class="fas fa-building"></i>
                <?= $fr ? 'Créer un compte entreprise' : 'Create Business Account' ?>
            </a>
            <a href="<?= url('distribution/login') ?>" class="bc-cta-btn-secondary">
                <i class="fas fa-sign-in-alt"></i>
                <?= $fr ? 'Se connecter' : 'Sign In' ?>
            </a>
        </div>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
    function bcToggleFaq(btn) {
        const answer = btn.nextElementSibling;
        const isOpen = btn.classList.contains('open');
        document.querySelectorAll('.bc-faq-q.open').forEach(b => {
            b.classList.remove('open');
            b.nextElementSibling.classList.remove('open');
        });
        if (!isOpen) {
            btn.classList.add('open');
            answer.classList.add('open');
        }
    }
    </script>
</body>
</html>
