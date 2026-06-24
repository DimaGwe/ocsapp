<?php
/**
 * Become a Seller - Account Upgrade Page
 * For logged-in buyers who want to become sellers
 * File: app/Views/account/become-seller.php
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$user = $user ?? [];
$flash = getFlash('success') ?? getFlash('error') ?? null;
$flashType = getFlash('success') ? 'success' : 'error';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Seller - OCSAPP</title>
    <?= csrfMeta() ?>

    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { height: 100%; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; flex: 1; }
        .footer { margin-top: auto; }

        .page-header {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
        }
        .page-header h1 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .page-header h1 i { color: #f59e0b; }
        .page-header p { color: #666; font-size: 15px; }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-title i { color: #00b207; }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: #f0fdf4;
            border-radius: 10px;
            border: 1px solid #bbf7d0;
        }
        .benefit-icon {
            width: 40px;
            height: 40px;
            background: #00b207;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .benefit-text h4 { font-size: 14px; color: #1a1a1a; margin-bottom: 4px; }
        .benefit-text p { font-size: 12px; color: #666; line-height: 1.4; }

        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-label .required { color: #dc2626; }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #00b207;
            box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
        }
        .form-textarea { min-height: 100px; resize: vertical; }
        .form-hint { font-size: 12px; color: #888; margin-top: 6px; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: #fefce8;
            border: 1px solid #fef08a;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #00b207;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .checkbox-group label {
            font-size: 13px;
            color: #555;
            line-height: 1.5;
        }
        .checkbox-group label a { color: #00b207; }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
        }
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .back-link:hover { color: #00b207; }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .current-status {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="container">
        <a href="<?= url('account') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Account
        </a>

        <div class="page-header">
            <h1><i class="fas fa-store"></i> Become a Seller</h1>
            <p>Upgrade your account to start selling on OCSAPP</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flashType ?>">
                <i class="fas fa-<?= $flashType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <div class="current-status">
            <i class="fas fa-user"></i>
            <span>You're currently logged in as <strong><?= htmlspecialchars($user['first_name'] ?? 'User') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?></strong> (<?= htmlspecialchars($user['email'] ?? '') ?>)</span>
        </div>

        <!-- Benefits -->
        <div class="card">
            <h2 class="card-title"><i class="fas fa-gift"></i> Seller Benefits</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-store"></i></div>
                    <div class="benefit-text">
                        <h4>Your Own Store</h4>
                        <p>Create a branded storefront on OCSAPP</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="benefit-text">
                        <h4>Sales Analytics</h4>
                        <p>Track performance with detailed reports</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-truck"></i></div>
                    <div class="benefit-text">
                        <h4>Delivery Included</h4>
                        <p>Zero-carbon delivery service for orders</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon"><i class="fas fa-mobile-alt"></i></div>
                    <div class="benefit-text">
                        <h4>Mobile Management</h4>
                        <p>Manage your store from anywhere</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Form -->
        <form action="<?= url('account/become-seller') ?>" method="POST" class="card" id="sellerForm">
            <?= csrfField() ?>

            <h2 class="card-title"><i class="fas fa-building"></i> Business Information</h2>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Business Name <span class="required">*</span></label>
                    <input type="text" name="business_name" class="form-input" required
                           placeholder="Your Business Name">
                </div>
                <div class="form-group">
                    <label class="form-label">Business Type <span class="required">*</span></label>
                    <select name="business_type" class="form-select" required>
                        <option value="">Select type...</option>
                        <option value="sole_proprietor">Sole Proprietor</option>
                        <option value="partnership">Partnership</option>
                        <option value="corporation">Corporation</option>
                        <option value="cooperative">Cooperative</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Business Registration Number</label>
                    <input type="text" name="business_registration" class="form-input"
                           placeholder="e.g., NEQ or BN">
                    <p class="form-hint">Quebec NEQ or Federal Business Number (optional for sole proprietors)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">GST/HST Number</label>
                    <input type="text" name="tax_number" class="form-input"
                           placeholder="e.g., 123456789RT0001">
                    <p class="form-hint">Required if registered for GST/HST</p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Business Address <span class="required">*</span></label>
                <input type="text" name="business_address" class="form-input" required
                       placeholder="Street address">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City <span class="required">*</span></label>
                    <input type="text" name="city" class="form-input" required
                           placeholder="City">
                </div>
                <div class="form-group">
                    <label class="form-label">Province <span class="required">*</span></label>
                    <select name="province" class="form-select" required>
                        <option value="">Select province...</option>
                        <option value="QC">Quebec</option>
                        <option value="ON">Ontario</option>
                        <option value="BC">British Columbia</option>
                        <option value="AB">Alberta</option>
                        <option value="MB">Manitoba</option>
                        <option value="SK">Saskatchewan</option>
                        <option value="NS">Nova Scotia</option>
                        <option value="NB">New Brunswick</option>
                        <option value="NL">Newfoundland and Labrador</option>
                        <option value="PE">Prince Edward Island</option>
                        <option value="NT">Northwest Territories</option>
                        <option value="YT">Yukon</option>
                        <option value="NU">Nunavut</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Postal Code <span class="required">*</span></label>
                    <input type="text" name="postal_code" class="form-input" required
                           placeholder="A1A 1A1" pattern="[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d">
                </div>
                <div class="form-group">
                    <label class="form-label">Business Phone <span class="required">*</span></label>
                    <input type="tel" name="business_phone" class="form-input" required
                           placeholder="+1 (514) 555-0000">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">What products do you want to sell? <span class="required">*</span></label>
                <textarea name="product_description" class="form-textarea" required
                          placeholder="Describe the types of products you plan to sell on OCSAPP..."></textarea>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="agree_terms" required>
                <label for="terms">
                    I confirm that I am authorized to register this business on OCSAPP. I agree to the
                    <a href="<?= url('terms') ?>" target="_blank">Seller Terms of Service</a> and
                    <a href="<?= url('privacy') ?>" target="_blank">Privacy Policy</a>.
                    I understand that my application will be reviewed and I will be notified of the decision.
                </label>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-paper-plane"></i>
                Submit Seller Application
            </button>
        </form>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        document.getElementById('sellerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        });
    </script>
</body>
</html>
