<?php

/**
 * PaymentGatewayHelper - Get payment gateway configuration
 */

/**
 * Get all payment settings from database
 */
function getPaymentSettings(): array
{
    static $settings = null;

    if ($settings !== null) {
        return $settings;
    }

    try {
        $db = \Database::getConnection();

        // Check if table exists
        $stmt = $db->query("SHOW TABLES LIKE 'payment_settings'");
        if (!$stmt->fetch()) {
            return [
                'active_gateway' => 'stripe',
                'stripe_mode' => 'test'
            ];
        }

        $stmt = $db->query("SELECT setting_key, setting_value FROM payment_settings");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;

    } catch (\PDOException $e) {
        error_log('Payment settings error: ' . $e->getMessage());
        return [
            'active_gateway' => 'stripe',
            'stripe_mode' => 'test'
        ];
    }
}

/**
 * Get active payment gateway
 */
function getActivePaymentGateway(): string
{
    $settings = getPaymentSettings();
    return $settings['active_gateway'] ?? 'stripe';
}

/**
 * Get Stripe configuration based on mode
 */
function getStripeConfig(): array
{
    $settings = getPaymentSettings();
    $mode = $settings['stripe_mode'] ?? 'test';

    if ($mode === 'live') {
        return [
            'publishable_key' => $settings['stripe_live_publishable_key'] ?? '',
            'secret_key' => $settings['stripe_live_secret_key'] ?? '',
            'webhook_secret' => $settings['stripe_webhook_secret'] ?? '',
            'mode' => 'live'
        ];
    }

    return [
        'publishable_key' => $settings['stripe_test_publishable_key'] ?? '',
        'secret_key' => $settings['stripe_test_secret_key'] ?? '',
        'webhook_secret' => $settings['stripe_webhook_secret'] ?? '',
        'mode' => 'test'
    ];
}

/**
 * Get PayPal configuration based on mode
 */
function getPayPalConfig(): array
{
    $settings = getPaymentSettings();
    $mode = $settings['paypal_mode'] ?? 'sandbox';

    if ($mode === 'live') {
        return [
            'client_id' => $settings['paypal_live_client_id'] ?? '',
            'secret' => $settings['paypal_live_secret'] ?? '',
            'mode' => 'live',
            'base_url' => 'https://api.paypal.com'
        ];
    }

    return [
        'client_id' => $settings['paypal_sandbox_client_id'] ?? '',
        'secret' => $settings['paypal_sandbox_secret'] ?? '',
        'mode' => 'sandbox',
        'base_url' => 'https://api.sandbox.paypal.com'
    ];
}

/**
 * Get Venn.ca configuration based on mode
 */
function getVennConfig(): array
{
    $settings = getPaymentSettings();
    $mode = $settings['venn_mode'] ?? 'test';

    if ($mode === 'live') {
        return [
            'api_key' => $settings['venn_live_api_key'] ?? '',
            'api_secret' => $settings['venn_live_api_secret'] ?? '',
            'merchant_id' => $settings['venn_merchant_id'] ?? '',
            'mode' => 'live',
            'base_url' => 'https://api.venn.ca'
        ];
    }

    return [
        'api_key' => $settings['venn_test_api_key'] ?? '',
        'api_secret' => $settings['venn_test_api_secret'] ?? '',
        'merchant_id' => $settings['venn_merchant_id'] ?? '',
        'mode' => 'test',
        'base_url' => 'https://sandbox.api.venn.ca'
    ];
}
