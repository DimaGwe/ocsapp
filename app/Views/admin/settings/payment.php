<?php
/**
 * OCS Admin Payment Gateway Settings
 * File: app/Views/admin/settings/payment.php
 */

$pageTitle = 'Payment Settings';
$currentPage = 'payment-settings';

ob_start();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
}

.page-header p {
    font-size: 14px;
    color: #666;
    margin-top: 4px;
}

.gateway-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

@media (max-width: 1024px) {
    .gateway-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .gateway-cards {
        grid-template-columns: 1fr;
    }
}

.gateway-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 24px;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.gateway-card:hover {
    border-color: #00b207;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.gateway-card.selected {
    border-color: #00b207;
    background: #f0fdf4;
}

.gateway-card.selected::after {
    content: '\f058';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    top: 12px;
    right: 12px;
    color: #00b207;
    font-size: 24px;
}

.gateway-logo {
    height: 48px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
}

.gateway-logo img {
    max-height: 100%;
    max-width: 120px;
}

.gateway-logo i {
    font-size: 36px;
}

.gateway-logo.stripe { color: #635bff; }
.gateway-logo.paypal { color: #003087; }
.gateway-logo.venn { color: #1a1a1a; }

.gateway-name {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.gateway-description {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
}

.gateway-features {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.gateway-feature {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #666;
    margin-bottom: 6px;
}

.gateway-feature i {
    color: #00b207;
    width: 14px;
}

.settings-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 24px;
    margin-bottom: 24px;
}

.settings-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 16px;
    margin-bottom: 24px;
    border-bottom: 1px solid #e5e7eb;
}

.settings-card-header .icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.settings-card-header .icon.stripe { background: #ede9fe; color: #635bff; }
.settings-card-header .icon.paypal { background: #dbeafe; color: #003087; }
.settings-card-header .icon.venn { background: #f3f4f6; color: #1a1a1a; }

.settings-card-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
}

.settings-card-header p {
    font-size: 13px;
    color: #666;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.form-label .required {
    color: #dc2626;
}

.form-description {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
}

.form-input.monospace {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 13px;
}

.toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toggle {
    position: relative;
    width: 48px;
    height: 26px;
    background: #e5e7eb;
    border-radius: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.toggle::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.toggle.active {
    background: #00b207;
}

.toggle.active::after {
    left: 25px;
}

.toggle-label {
    font-size: 14px;
    font-weight: 500;
    color: #1a1a1a;
}

.mode-selector {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.mode-option {
    flex: 1;
    padding: 14px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

.mode-option:hover {
    border-color: #00b207;
}

.mode-option.selected {
    border-color: #00b207;
    background: #f0fdf4;
}

.mode-option input {
    display: none;
}

.mode-option .mode-name {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 4px;
}

.mode-option .mode-description {
    font-size: 12px;
    color: #666;
}

.mode-option.test .mode-name { color: #b45309; }
.mode-option.live .mode-name { color: #059669; }

.alert {
    padding: 14px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.alert i {
    margin-top: 2px;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.btn-group {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    font-family: inherit;
}

.btn-primary {
    background: #00b207;
    color: white;
}

.btn-primary:hover {
    background: #009906;
}

.btn-secondary {
    background: #f3f4f6;
    color: #666;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.hidden {
    display: none !important;
}

/* Venn.ca specific styling */
.venn-logo-text {
    font-size: 32px;
    font-weight: 800;
    color: #1a1a1a;
    letter-spacing: -1px;
}
</style>

<div class="page-header">
    <div>
        <h1>Payment Gateway Settings</h1>
        <p>Configure payment processing for Distribution Portal orders</p>
    </div>
</div>

<?php if ($flash = getFlash('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<?php if ($flash = getFlash('error')): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('admin/settings/payment/save') ?>">
    <?= csrfField() ?>

    <!-- Gateway Selection -->
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #1a1a1a;">
        <i class="fas fa-credit-card" style="color: #00b207; margin-right: 8px;"></i>
        Select Payment Gateway
    </h3>

    <div class="gateway-cards">
        <!-- Stripe -->
        <div class="gateway-card <?= ($settings['active_gateway'] ?? 'stripe') === 'stripe' ? 'selected' : '' ?>"
             onclick="selectGateway('stripe')">
            <input type="radio" name="active_gateway" value="stripe"
                   <?= ($settings['active_gateway'] ?? 'stripe') === 'stripe' ? 'checked' : '' ?> style="display:none;">
            <div class="gateway-logo stripe">
                <i class="fab fa-stripe"></i>
            </div>
            <div class="gateway-name">Stripe</div>
            <div class="gateway-description">
                Industry-leading payment processing with support for all major credit cards.
            </div>
            <div class="gateway-features">
                <div class="gateway-feature"><i class="fas fa-check"></i> Credit/Debit Cards</div>
                <div class="gateway-feature"><i class="fas fa-check"></i> Apple Pay & Google Pay</div>
                <div class="gateway-feature"><i class="fas fa-check"></i> 2.9% + $0.30 per transaction</div>
            </div>
        </div>

        <!-- PayPal -->
        <div class="gateway-card <?= ($settings['active_gateway'] ?? '') === 'paypal' ? 'selected' : '' ?>"
             onclick="selectGateway('paypal')">
            <input type="radio" name="active_gateway" value="paypal"
                   <?= ($settings['active_gateway'] ?? '') === 'paypal' ? 'checked' : '' ?> style="display:none;">
            <div class="gateway-logo paypal">
                <i class="fab fa-paypal"></i>
            </div>
            <div class="gateway-name">PayPal</div>
            <div class="gateway-description">
                Trusted worldwide payment platform with buyer and seller protection.
            </div>
            <div class="gateway-features">
                <div class="gateway-feature"><i class="fas fa-check"></i> PayPal Balance</div>
                <div class="gateway-feature"><i class="fas fa-check"></i> Credit/Debit Cards</div>
                <div class="gateway-feature"><i class="fas fa-check"></i> 2.9% + $0.30 per transaction</div>
            </div>
        </div>

        <!-- Venn.ca -->
        <div class="gateway-card <?= ($settings['active_gateway'] ?? '') === 'venn' ? 'selected' : '' ?>"
             onclick="selectGateway('venn')">
            <input type="radio" name="active_gateway" value="venn"
                   <?= ($settings['active_gateway'] ?? '') === 'venn' ? 'checked' : '' ?> style="display:none;">
            <div class="gateway-logo venn">
                <span class="venn-logo-text">venn</span>
            </div>
            <div class="gateway-name">Venn.ca</div>
            <div class="gateway-description">
                Canadian payment processor with competitive rates and local support.
            </div>
            <div class="gateway-features">
                <div class="gateway-feature"><i class="fas fa-check"></i> Canadian-based</div>
                <div class="gateway-feature"><i class="fas fa-check"></i> Interac e-Transfer</div>
                <div class="gateway-feature"><i class="fas fa-check"></i> Competitive CAD rates</div>
            </div>
        </div>
    </div>

    <!-- Stripe Settings -->
    <div class="settings-card gateway-settings" id="stripe-settings"
         style="<?= ($settings['active_gateway'] ?? 'stripe') !== 'stripe' ? 'display:none;' : '' ?>">
        <div class="settings-card-header">
            <div class="icon stripe"><i class="fab fa-stripe-s"></i></div>
            <div>
                <h3>Stripe Configuration</h3>
                <p>Enter your Stripe API credentials</p>
            </div>
        </div>

        <!-- Mode Selector -->
        <div class="mode-selector">
            <label class="mode-option test <?= ($settings['stripe_mode'] ?? 'test') === 'test' ? 'selected' : '' ?>">
                <input type="radio" name="stripe_mode" value="test"
                       <?= ($settings['stripe_mode'] ?? 'test') === 'test' ? 'checked' : '' ?>>
                <div class="mode-name">Test Mode</div>
                <div class="mode-description">For development & testing</div>
            </label>
            <label class="mode-option live <?= ($settings['stripe_mode'] ?? '') === 'live' ? 'selected' : '' ?>">
                <input type="radio" name="stripe_mode" value="live"
                       <?= ($settings['stripe_mode'] ?? '') === 'live' ? 'checked' : '' ?>>
                <div class="mode-name">Live Mode</div>
                <div class="mode-description">Process real payments</div>
            </label>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Test Publishable Key</label>
                <input type="text" name="stripe_test_publishable_key" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['stripe_test_publishable_key'] ?? '') ?>"
                       placeholder="pk_test_...">
            </div>
            <div class="form-group">
                <label class="form-label">Test Secret Key</label>
                <input type="password" name="stripe_test_secret_key" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['stripe_test_secret_key'] ?? '') ?>"
                       placeholder="sk_test_...">
            </div>
            <div class="form-group">
                <label class="form-label">Live Publishable Key</label>
                <input type="text" name="stripe_live_publishable_key" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['stripe_live_publishable_key'] ?? '') ?>"
                       placeholder="pk_live_...">
            </div>
            <div class="form-group">
                <label class="form-label">Live Secret Key</label>
                <input type="password" name="stripe_live_secret_key" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['stripe_live_secret_key'] ?? '') ?>"
                       placeholder="sk_live_...">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Webhook Secret</label>
                <p class="form-description">Required for payment confirmations. Get this from Stripe Dashboard > Webhooks</p>
                <input type="password" name="stripe_webhook_secret" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['stripe_webhook_secret'] ?? '') ?>"
                       placeholder="whsec_...">
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Webhook URL:</strong><br>
                <code style="background: rgba(0,0,0,0.1); padding: 4px 8px; border-radius: 4px;">
                    <?= url('distribution/pay/webhook') ?>
                </code>
            </div>
        </div>
    </div>

    <!-- PayPal Settings -->
    <div class="settings-card gateway-settings" id="paypal-settings"
         style="<?= ($settings['active_gateway'] ?? '') !== 'paypal' ? 'display:none;' : '' ?>">
        <div class="settings-card-header">
            <div class="icon paypal"><i class="fab fa-paypal"></i></div>
            <div>
                <h3>PayPal Configuration</h3>
                <p>Enter your PayPal API credentials</p>
            </div>
        </div>

        <!-- Mode Selector -->
        <div class="mode-selector">
            <label class="mode-option test <?= ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' ?>">
                <input type="radio" name="paypal_mode" value="sandbox"
                       <?= ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'checked' : '' ?>>
                <div class="mode-name">Sandbox Mode</div>
                <div class="mode-description">For development & testing</div>
            </label>
            <label class="mode-option live <?= ($settings['paypal_mode'] ?? '') === 'live' ? 'selected' : '' ?>">
                <input type="radio" name="paypal_mode" value="live"
                       <?= ($settings['paypal_mode'] ?? '') === 'live' ? 'checked' : '' ?>>
                <div class="mode-name">Live Mode</div>
                <div class="mode-description">Process real payments</div>
            </label>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Sandbox Client ID</label>
                <input type="text" name="paypal_sandbox_client_id" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['paypal_sandbox_client_id'] ?? '') ?>"
                       placeholder="Sandbox Client ID">
            </div>
            <div class="form-group">
                <label class="form-label">Sandbox Secret</label>
                <input type="password" name="paypal_sandbox_secret" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['paypal_sandbox_secret'] ?? '') ?>"
                       placeholder="Sandbox Secret">
            </div>
            <div class="form-group">
                <label class="form-label">Live Client ID</label>
                <input type="text" name="paypal_live_client_id" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['paypal_live_client_id'] ?? '') ?>"
                       placeholder="Live Client ID">
            </div>
            <div class="form-group">
                <label class="form-label">Live Secret</label>
                <input type="password" name="paypal_live_secret" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['paypal_live_secret'] ?? '') ?>"
                       placeholder="Live Secret">
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                Get your API credentials from <a href="https://developer.paypal.com/dashboard/" target="_blank" style="color: inherit; text-decoration: underline;">PayPal Developer Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Venn.ca Settings -->
    <div class="settings-card gateway-settings" id="venn-settings"
         style="<?= ($settings['active_gateway'] ?? '') !== 'venn' ? 'display:none;' : '' ?>">
        <div class="settings-card-header">
            <div class="icon venn"><span style="font-weight: 800; font-size: 14px;">V</span></div>
            <div>
                <h3>Venn.ca Configuration</h3>
                <p>Enter your Venn.ca API credentials</p>
            </div>
        </div>

        <!-- Mode Selector -->
        <div class="mode-selector">
            <label class="mode-option test <?= ($settings['venn_mode'] ?? 'test') === 'test' ? 'selected' : '' ?>">
                <input type="radio" name="venn_mode" value="test"
                       <?= ($settings['venn_mode'] ?? 'test') === 'test' ? 'checked' : '' ?>>
                <div class="mode-name">Test Mode</div>
                <div class="mode-description">For development & testing</div>
            </label>
            <label class="mode-option live <?= ($settings['venn_mode'] ?? '') === 'live' ? 'selected' : '' ?>">
                <input type="radio" name="venn_mode" value="live"
                       <?= ($settings['venn_mode'] ?? '') === 'live' ? 'checked' : '' ?>>
                <div class="mode-name">Live Mode</div>
                <div class="mode-description">Process real payments</div>
            </label>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Test API Key</label>
                <input type="text" name="venn_test_api_key" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['venn_test_api_key'] ?? '') ?>"
                       placeholder="Test API Key">
            </div>
            <div class="form-group">
                <label class="form-label">Test API Secret</label>
                <input type="password" name="venn_test_api_secret" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['venn_test_api_secret'] ?? '') ?>"
                       placeholder="Test API Secret">
            </div>
            <div class="form-group">
                <label class="form-label">Live API Key</label>
                <input type="text" name="venn_live_api_key" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['venn_live_api_key'] ?? '') ?>"
                       placeholder="Live API Key">
            </div>
            <div class="form-group">
                <label class="form-label">Live API Secret</label>
                <input type="password" name="venn_live_api_secret" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['venn_live_api_secret'] ?? '') ?>"
                       placeholder="Live API Secret">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Merchant ID</label>
                <input type="text" name="venn_merchant_id" class="form-input monospace"
                       value="<?= htmlspecialchars($settings['venn_merchant_id'] ?? '') ?>"
                       placeholder="Your Venn.ca Merchant ID">
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                Get your API credentials from your <a href="https://venn.ca" target="_blank" style="color: inherit; text-decoration: underline;">Venn.ca merchant dashboard</a>
            </div>
        </div>
    </div>

    <!-- Interac e-Transfer Settings (always visible — not gateway-dependent) -->
    <div class="settings-card" id="interac-settings">
        <div class="settings-card-header">
            <div class="icon" style="background: #fef3c7; color: #d97706;">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <h3>Interac e-Transfer</h3>
                <p>Configure Interac e-Transfer payment option for B2C marketplace checkout</p>
            </div>
        </div>

        <div class="alert alert-info" style="margin-bottom: 20px;">
            <i class="fas fa-info-circle"></i>
            <div>
                Interac e-Transfer is a manual payment option. Customers place their order and send you an e-Transfer.
                Use the <strong>"Mark as Paid"</strong> button in Orders Management to confirm receipt.
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">e-Transfer Email <span class="required">*</span></label>
                <p class="form-description">The email address customers should send Interac e-Transfers to</p>
                <input type="email" name="interac_email" class="form-input"
                       value="<?= htmlspecialchars($settings['interac_email'] ?? '') ?>"
                       placeholder="payments@yourcompany.ca">
            </div>
            <div class="form-group full-width">
                <label class="form-label">Customer Instructions</label>
                <p class="form-description">Instructions shown to customers on the checkout success page</p>
                <textarea name="interac_instructions" class="form-input" rows="3"
                          style="resize: vertical; min-height: 80px;"
                          placeholder="Please send an Interac e-Transfer to the email above with your order number as the message."><?= htmlspecialchars($settings['interac_instructions'] ?? 'Please send an Interac e-Transfer to the email above with your order number as the message.') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="btn-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Payment Settings
        </button>
        <a href="<?= url('admin/settings') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Settings
        </a>
    </div>
</form>

<script>
function selectGateway(gateway) {
    // Update card selection
    document.querySelectorAll('.gateway-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');

    // Update radio button
    document.querySelector(`input[name="active_gateway"][value="${gateway}"]`).checked = true;

    // Show/hide settings panels
    document.querySelectorAll('.gateway-settings').forEach(panel => {
        panel.style.display = 'none';
    });
    document.getElementById(`${gateway}-settings`).style.display = 'block';
}

// Mode selector
document.querySelectorAll('.mode-option').forEach(option => {
    option.addEventListener('click', function() {
        const siblings = this.parentElement.querySelectorAll('.mode-option');
        siblings.forEach(s => s.classList.remove('selected'));
        this.classList.add('selected');
    });
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
