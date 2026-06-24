<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class SettingsController {

    public function __construct() {
        // Super admin only - system settings are restricted
        AuthMiddleware::superAdmin();
    }

    // Settings Index - View all settings grouped by category
    public function index(): void {
        try {
            $db = \Database::getConnection();

            // Exclude 'integrations' - those API keys live on their own masked page
            $stmt = $db->query("SELECT * FROM settings WHERE category != 'integrations' ORDER BY category, id");
            $allSettings = $stmt->fetchAll();

            // Group by category
            $settingsByCategory = [];
            foreach ($allSettings as $setting) {
                $settingsByCategory[$setting['category']][] = $setting;
            }

            view('admin.settings.index', [
                'settingsByCategory' => $settingsByCategory,
            ]);

        } catch (\PDOException $e) {
            logger("Settings index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading settings');
            back();
        }
    }

    // Payment Gateway Settings Page
    public function payment(): void {
        try {
            $db = \Database::getConnection();

            // Check if payment_settings table exists
            $stmt = $db->query("SHOW TABLES LIKE 'payment_settings'");
            if (!$stmt->fetch()) {
                // Create table if it doesn't exist
                $db->exec("
                    CREATE TABLE payment_settings (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        setting_key VARCHAR(100) UNIQUE NOT NULL,
                        setting_value TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");

                // Insert defaults
                $db->exec("INSERT INTO payment_settings (setting_key, setting_value) VALUES ('active_gateway', 'stripe')");
                $db->exec("INSERT INTO payment_settings (setting_key, setting_value) VALUES ('stripe_mode', 'test')");
                $db->exec("INSERT INTO payment_settings (setting_key, setting_value) VALUES ('paypal_mode', 'sandbox')");
                $db->exec("INSERT INTO payment_settings (setting_key, setting_value) VALUES ('venn_mode', 'test')");
            }

            // Get all payment settings
            $stmt = $db->query("SELECT setting_key, setting_value FROM payment_settings");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }

            view('admin.settings.payment', [
                'settings' => $settings
            ]);

        } catch (\PDOException $e) {
            logger("Payment settings error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading payment settings');
            redirect(url('admin/settings'));
        }
    }

    // Save Payment Gateway Settings
    public function savePayment(): void {
        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }

        try {
            $db = \Database::getConnection();

            // Settings to save
            $settingsToSave = [
                'active_gateway',
                'stripe_mode',
                'stripe_test_publishable_key',
                'stripe_test_secret_key',
                'stripe_live_publishable_key',
                'stripe_live_secret_key',
                'stripe_webhook_secret',
                'paypal_mode',
                'paypal_sandbox_client_id',
                'paypal_sandbox_secret',
                'paypal_live_client_id',
                'paypal_live_secret',
                'venn_mode',
                'venn_test_api_key',
                'venn_test_api_secret',
                'venn_live_api_key',
                'venn_live_api_secret',
                'venn_merchant_id',
                'interac_email',
                'interac_instructions',
            ];

            foreach ($settingsToSave as $key) {
                $value = post($key, '');

                // Don't overwrite password fields if empty (keep existing value)
                if (empty($value) && in_array($key, [
                    'stripe_test_secret_key', 'stripe_live_secret_key', 'stripe_webhook_secret',
                    'paypal_sandbox_secret', 'paypal_live_secret',
                    'venn_test_api_secret', 'venn_live_api_secret'
                ])) {
                    continue;
                }

                // Insert or update
                $stmt = $db->prepare("
                    INSERT INTO payment_settings (setting_key, setting_value)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute([$key, $value]);
            }

            setFlash('success', 'Payment settings saved successfully');
            redirect(url('admin/settings/payment'));

        } catch (\PDOException $e) {
            logger("Payment settings save error: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to save payment settings');
            back();
        }
    }

    // Update Settings
    public function update(): void {
        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        try {
            $db = \Database::getConnection();
            $db->beginTransaction();

            // Get all settings from form
            $settings = post('settings', []);

            foreach ($settings as $key => $value) {
                // Get setting type
                $stmt = $db->prepare("SELECT type FROM settings WHERE `key` = ?");
                $stmt->execute([$key]);
                $setting = $stmt->fetch();

                if (!$setting) continue;

                // Handle boolean values
                if ($setting['type'] === 'boolean') {
                    $value = isset($settings[$key]) ? 'true' : 'false';
                }

                // Handle image uploads
                if ($setting['type'] === 'image' && isset($_FILES['images']['tmp_name'][$key]) && $_FILES['images']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                    $uploadDir = BASE_PATH . '/public/uploads/settings/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $extension = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $filename = $key . '_' . time() . '.' . $extension;
                    $filepath = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $filepath)) {
                        // Delete old image if exists
                        $stmt = $db->prepare("SELECT value FROM settings WHERE `key` = ?");
                        $stmt->execute([$key]);
                        $oldValue = $stmt->fetch()['value'] ?? null;

                        if ($oldValue && file_exists(BASE_PATH . '/public/' . $oldValue)) {
                            unlink(BASE_PATH . '/public/' . $oldValue);
                        }

                        // Store relative path from public/
                        $value = 'uploads/settings/' . $filename;
                    }
                }

                // Update setting
                $stmt = $db->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
                $stmt->execute([$value, $key]);
            }

            $db->commit();

            setFlash('success', 'Settings updated successfully');
            redirect(url('admin/settings'));

        } catch (\PDOException $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            logger("Settings update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to update settings');
            back();
        }
    }

    /* ============================================================
     * INTEGRATIONS (API keys: Anthropic, Gemini, ...)
     * Stored in the generic `settings` table, category = 'integrations'.
     * Keys are masked on display and never echoed back in full.
     * ============================================================ */

    // Definitions for every integration key surfaced in the UI
    private function integrationDefs(): array {
        return [
            'anthropic' => [
                'title' => 'Anthropic (Claude)',
                'desc'  => 'Powers the Content Creator chat and caption writing.',
                'icon'  => 'fa-robot',
                'link'  => 'https://console.anthropic.com/',
                'keys'  => [
                    ['key' => 'anthropic_api_key', 'label' => 'API Key', 'placeholder' => 'sk-ant-...'],
                ],
            ],
            'gemini' => [
                'title' => 'Google Gemini ("Nano Banana")',
                'desc'  => 'Image generation in the Content Creator.',
                'icon'  => 'fa-image',
                'link'  => 'https://aistudio.google.com/apikey',
                'keys'  => [
                    ['key' => 'gemini_api_key', 'label' => 'API Key', 'placeholder' => 'AIza...'],
                ],
            ],
        ];
    }

    private function maskKey(?string $val): string {
        $val = (string)$val;
        if ($val === '') return '';
        $len = strlen($val);
        if ($len <= 4) return str_repeat('•', $len);
        return str_repeat('•', 8) . substr($val, -4);
    }

    // Integrations page
    public function integrations(): void {
        try {
            $db = \Database::getConnection();

            // Ensure each integration key exists as a row (auto-seed, like payment())
            $defs = $this->integrationDefs();
            $insert = $db->prepare("INSERT IGNORE INTO settings (`key`, `category`, `label`, `description`, `value`, `type`) VALUES (?, 'integrations', ?, ?, '', 'text')");
            foreach ($defs as $group) {
                foreach ($group['keys'] as $k) {
                    $insert->execute([$k['key'], $group['title'] . ' - ' . $k['label'], $group['desc']]);
                }
            }

            // Load current values (mask secrets; never send full value to the view)
            $stmt = $db->query("SELECT `key`, `value` FROM settings WHERE category = 'integrations'");
            $stored = [];
            foreach ($stmt->fetchAll() as $row) {
                $stored[$row['key']] = [
                    'is_set' => ($row['value'] !== null && $row['value'] !== ''),
                    'masked' => $this->maskKey($row['value']),
                ];
            }

            view('admin.settings.integrations', [
                'defs'   => $defs,
                'stored' => $stored,
            ]);

        } catch (\Throwable $e) {
            logger("Integrations page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading integrations');
            redirect(url('admin/settings'));
        }
    }

    // Save integration keys
    public function saveIntegrations(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }

        try {
            $db = \Database::getConnection();
            $upd = $db->prepare("UPDATE settings SET `value` = ? WHERE `key` = ? AND category = 'integrations'");

            foreach ($this->integrationDefs() as $group) {
                foreach ($group['keys'] as $k) {
                    $submitted = trim((string)post($k['key'], ''));
                    // Empty submission = leave existing value untouched (don't wipe a saved key)
                    if ($submitted === '') continue;
                    // Ignore the masked placeholder being re-submitted unchanged
                    if (strpos($submitted, '•') !== false) continue;
                    $upd->execute([$submitted, $k['key']]);
                }
            }

            setFlash('success', 'Integration keys saved.');
        } catch (\Throwable $e) {
            logger("Integrations save error: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to save integration keys.');
        }
        redirect(url('admin/settings/integrations'));
    }

    // Test an integration's connection (AJAX, JSON)
    public function testIntegration(): void {
        header('Content-Type: application/json');
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken((string)$token)) {
            echo json_encode(['ok' => false, 'message' => 'Session expired - refresh and try again.']);
            return;
        }

        $provider = post('provider', request('provider', ''));
        $result = ['ok' => false, 'message' => 'Unknown provider.'];

        if ($provider === 'anthropic') {
            $result = $this->pingAnthropic(setting('anthropic_api_key', ''));
        } elseif ($provider === 'gemini') {
            $result = $this->pingGemini(setting('gemini_api_key', ''));
        }

        echo json_encode($result);
    }

    private function pingAnthropic(string $key): array {
        if (!$key) return ['ok' => false, 'message' => 'No key saved yet.'];
        $payload = json_encode([
            'model' => 'claude-haiku-4-5-20251001',
            'max_tokens' => 1,
            'messages' => [['role' => 'user', 'content' => 'ping']],
        ]);
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-api-key: ' . $key, 'anthropic-version: 2023-06-01'],
            CURLOPT_TIMEOUT => 20,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($err) return ['ok' => false, 'message' => 'Connection error: ' . $err];
        if ($code === 200) return ['ok' => true, 'message' => 'Connected - Claude is responding.'];
        $data = json_decode($resp, true);
        return ['ok' => false, 'message' => $data['error']['message'] ?? ('Failed (HTTP ' . $code . ')')];
    }

    private function pingGemini(string $key): array {
        if (!$key) return ['ok' => false, 'message' => 'No key saved yet.'];
        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($key));
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($err) return ['ok' => false, 'message' => 'Connection error: ' . $err];
        if ($code === 200) return ['ok' => true, 'message' => 'Connected - Gemini key is valid.'];
        $data = json_decode($resp, true);
        return ['ok' => false, 'message' => $data['error']['message'] ?? ('Failed (HTTP ' . $code . ')')];
    }
}
