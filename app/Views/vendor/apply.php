<?php
/**
 * Vendor Application Form - OCSAPP
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$inviteCode = get('invite', '');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Application - OCSAPP</title>
    <?= csrfMeta() ?>

    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .application-page {
            min-height: 100vh;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .application-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .app-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .app-header h1 {
            font-size: 36px;
            font-weight: 800;
            color: #00b207;
            margin-bottom: 12px;
        }

        .app-header p {
            font-size: 18px;
            color: #4b5563;
        }

        .app-form {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-grid-full {
            grid-column: 1 / -1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }

        .required {
            color: #ef4444;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="password"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-help {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            cursor: pointer;
        }

        .submit-section {
            background: #f9fafb;
            padding: 24px;
            border-radius: 12px;
            margin-top: 32px;
        }

        .btn {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 178, 7, 0.4);
        }

        .invite-badge {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 32px;
        }

        .invite-badge i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .app-form {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="application-page">
        <div class="application-container">
            <div class="app-header">
                <h1>🤝 Apply to Become a Vendor</h1>
                <p>Join the OCSAPP Vendor Program and grow your business across Canada</p>
            </div>

            <?php if ($inviteCode): ?>
            <div class="invite-badge">
                <i class="fa-solid fa-check-circle"></i>
                <strong>Pre-Approved Invite!</strong> Your application will be fast-tracked.
            </div>
            <?php endif; ?>

            <form action="<?= url('vendor/submit-application') ?>" method="POST" class="app-form">
                <?= csrfField() ?>
                <?php if ($inviteCode): ?>
                    <input type="hidden" name="invite_code" value="<?= htmlspecialchars($inviteCode) ?>">
                <?php endif; ?>

                <!-- Company Information -->
                <div class="form-section">
                    <h3 class="section-title">Company Information</h3>

                    <div class="form-grid">
                        <div class="form-group form-grid-full">
                            <label>Company Name <span class="required">*</span></label>
                            <input type="text" name="company_name" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" name="email" required>
                            <div class="form-help">Your vendor account login email</div>
                        </div>

                        <div class="form-group">
                            <label>Phone Number <span class="required">*</span></label>
                            <input type="tel" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label>Contact Person Name <span class="required">*</span></label>
                            <input type="text" name="contact_person" required placeholder="John Doe">
                        </div>

                        <div class="form-group">
                            <label>Business Registration Number</label>
                            <input type="text" name="business_number" placeholder="123456789RC0001">
                            <div class="form-help">Canadian business registration number</div>
                        </div>

                        <div class="form-group">
                            <label>Tax ID / GST Number</label>
                            <input type="text" name="tax_id">
                        </div>

                        <div class="form-group">
                            <label>Website (optional)</label>
                            <input type="text" name="website" placeholder="https://yourcompany.com">
                        </div>

                        <div class="form-group form-grid-full">
                            <label>Company Description</label>
                            <textarea name="description" placeholder="Tell us about your company, products, and why you want to become a vendor..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Business Address -->
                <div class="form-section">
                    <h3 class="section-title">Business Address</h3>

                    <div class="form-grid">
                        <div class="form-group form-grid-full">
                            <label>Street Address <span class="required">*</span></label>
                            <input type="text" name="address" required placeholder="123 Main Street">
                        </div>

                        <div class="form-group">
                            <label>City <span class="required">*</span></label>
                            <input type="text" name="city" required placeholder="Toronto">
                        </div>

                        <div class="form-group">
                            <label>Province <span class="required">*</span></label>
                            <select name="province" required>
                                <option value="">Select Province</option>
                                <option value="ON">Ontario</option>
                                <option value="QC">Quebec</option>
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

                        <div class="form-group">
                            <label>Postal Code <span class="required">*</span></label>
                            <input type="text" name="postal_code" required placeholder="M5H 2N2">
                        </div>

                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="Canada" readonly>
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="form-section">
                    <h3 class="section-title">Account Security</h3>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" required id="password" minlength="8">
                            <div class="form-help">Minimum 8 characters</div>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="password_confirm" required id="password_confirm" minlength="8">
                        </div>
                    </div>
                </div>

                <!-- Terms & Submit -->
                <div class="submit-section">
                    <div class="checkbox-group" style="margin-bottom: 24px;">
                        <input type="checkbox" id="accept_terms" name="accept_terms" required>
                        <label for="accept_terms" style="margin: 0;">
                            I agree to the <a href="<?= url('terms') ?>" target="_blank" style="color: #00b207;">Terms of Service</a>
                            and <a href="<?= url('privacy') ?>" target="_blank" style="color: #00b207;">Privacy Policy</a>
                            <span class="required">*</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Submit Application
                    </button>

                    <p style="text-align: center; margin-top: 16px; color: #6b7280; font-size: 14px;">
                        Already have an account? <a href="<?= url('vendor/login') ?>" style="color: #00b207; font-weight: 600;">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
    // Password matching validation
    document.getElementById('password_confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;

        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html>
