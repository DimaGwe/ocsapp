<?php

namespace App\Controllers;

// Include payment gateway helper
require_once __DIR__ . '/../Helpers/PaymentGatewayHelper.php';

/**
 * DistributionPaymentController - Handles payment for distribution requests
 * Supports Stripe, PayPal, and Venn.ca payment gateways
 */
class DistributionPaymentController
{
    private $db;
    private $activeGateway;

    public function __construct()
    {
        $this->db = \Database::getConnection();
        $this->activeGateway = getActivePaymentGateway();
    }

    /**
     * Display payment page for a distribution request
     * GET /distribution/pay?token=xxx
     */
    public function showPaymentPage(): void
    {
        // PayPal appends its own ?token= on cancel/return redirects, overwriting ours.
        // Parse the raw query string to always get the first (our) token value.
        preg_match('/(?:^|&)token=([^&]+)/', $_SERVER['QUERY_STRING'] ?? '', $m);
        $token = isset($m[1]) ? urldecode($m[1]) : ($_GET['token'] ?? '');

        if (empty($token)) {
            $this->showError('Invalid payment link.');
            return;
        }

        try {
            // Get request with valid token
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name, u.last_name, u.phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.payment_link_token = ?
                AND dr.status = 'awaiting_payment'
            ");
            $stmt->execute([$token]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                $this->showError('Payment link is invalid or has already been used.');
                return;
            }

            // Check if link has expired
            if (strtotime($request['payment_link_expires_at']) < time()) {
                $this->showError('This payment link has expired. Please contact us to request a new one.');
                return;
            }

            // Get catalog items with supplier info for grouped display
            $stmt = $this->db->prepare("
                SELECT dri.*,
                       COALESCE(dri.product_name, sp.product_name) as product_name,
                       COALESCE(dri.product_sku,  sp.sku)          as sku,
                       s.id                                         as supplier_id,
                       COALESCE(s.company_name, s.name, 'Unknown Supplier') as supplier_name
                FROM distribution_request_items dri
                LEFT JOIN supplier_products sp ON dri.product_id = sp.id
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE dri.distribution_request_id = ?
                ORDER BY supplier_name, product_name
            ");
            $stmt->execute([$request['id']]);
            $catalogItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get shopping items
            $stmt = $this->db->prepare("SELECT * FROM distribution_shopping_items WHERE distribution_request_id = ?");
            $stmt->execute([$request['id']]);
            $shoppingItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get gateway-specific config
            $gatewayConfig = $this->getGatewayPublicConfig();

            view('distribution.payment', [
                'request' => $request,
                'catalogItems' => $catalogItems,
                'shoppingItems' => $shoppingItems,
                'token' => $token,
                'activeGateway' => $this->activeGateway,
                'gatewayConfig' => $gatewayConfig
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution payment page error: ' . $e->getMessage());
            $this->showError('An error occurred. Please try again later.');
        }
    }

    /**
     * Get public gateway configuration (safe to expose to frontend)
     */
    private function getGatewayPublicConfig(): array
    {
        switch ($this->activeGateway) {
            case 'stripe':
                $config = getStripeConfig();
                return [
                    'publishable_key' => $config['publishable_key'],
                    'mode' => $config['mode']
                ];

            case 'paypal':
                $config = getPayPalConfig();
                return [
                    'client_id' => $config['client_id'],
                    'mode' => $config['mode']
                ];

            case 'venn':
                $config = getVennConfig();
                return [
                    'merchant_id' => $config['merchant_id'],
                    'mode' => $config['mode']
                ];

            default:
                return [];
        }
    }

    /**
     * Create payment session (gateway-agnostic)
     * POST /distribution/pay/create-session
     */
    public function createCheckoutSession(): void
    {
        header('Content-Type: application/json');
        // CSRF verification using the existing verifyCsrfToken helper
        $csrfName  = env('CSRF_TOKEN_NAME', '_csrf_token');
        $csrfToken = $_POST[$csrfName] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!verifyCsrfToken($csrfToken)) {
            echo json_encode(['error' => 'Security token mismatch. Please refresh the page and try again.']);
            return;
        }

        $token = $_POST['token'] ?? '';

        if (empty($token)) {
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        try {
            // Get request
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.payment_link_token = ?
                AND dr.status = 'awaiting_payment'
            ");
            $stmt->execute([$token]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                echo json_encode(['error' => 'Invalid payment link']);
                return;
            }

            // Check expiry
            if (strtotime($request['payment_link_expires_at']) < time()) {
                echo json_encode(['error' => 'Payment link has expired']);
                return;
            }

            // Route to appropriate gateway
            switch ($this->activeGateway) {
                case 'stripe':
                    $this->createStripeSession($request, $token);
                    break;

                case 'paypal':
                    $this->createPayPalOrder($request, $token);
                    break;

                case 'venn':
                    $this->createVennSession($request, $token);
                    break;

                default:
                    echo json_encode(['error' => 'No payment gateway configured']);
            }

        } catch (\PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            echo json_encode(['error' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Create Stripe Checkout Session
     */
    private function createStripeSession(array $request, string $token): void
    {
        try {
            $config = getStripeConfig();
            \Stripe\Stripe::setApiKey($config['secret_key']);

            $lineItems = [[
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => [
                        'name' => 'Distribution Request #' . $request['request_number'],
                        'description' => $request['request_name'] . ' - ' . $request['company_name'],
                    ],
                    'unit_amount' => (int)($request['total_amount'] * 100),
                ],
                'quantity' => 1,
            ]];

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $request['email'],
                'client_reference_id' => $request['id'],
                'metadata' => [
                    'distribution_request_id' => $request['id'],
                    'request_number' => $request['request_number'],
                    'payment_token' => $token
                ],
                'success_url' => url('distribution/pay/success?session_id={CHECKOUT_SESSION_ID}&token=' . $token . '&gateway=stripe'),
                'cancel_url' => url('distribution/pay?token=' . $token . '&cancelled=1'),
            ]);

            echo json_encode(['sessionId' => $session->id, 'gateway' => 'stripe']);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log('Stripe error: ' . $e->getMessage());
            echo json_encode(['error' => 'Payment processing error. Please try again.']);
        }
    }

    /**
     * Create PayPal Order
     */
    private function createPayPalOrder(array $request, string $token): void
    {
        try {
            $config = getPayPalConfig();

            // Get access token
            $accessToken = $this->getPayPalAccessToken($config);

            if (!$accessToken) {
                echo json_encode(['error' => 'Failed to connect to PayPal']);
                return;
            }

            // Create order
            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $request['id'],
                    'description' => 'Distribution Request #' . $request['request_number'],
                    'custom_id' => $token,
                    'amount' => [
                        'currency_code' => 'CAD',
                        'value' => number_format($request['total_amount'], 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'brand_name' => 'OCSAPP Distribution',
                    'return_url' => url('distribution/pay/success?token=' . $token . '&gateway=paypal'),
                    'cancel_url' => url('distribution/pay?token=' . $token . '&cancelled=1')
                ]
            ];

            $ch = curl_init($config['base_url'] . '/v2/checkout/orders');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken
                ],
                CURLOPT_POSTFIELDS => json_encode($orderData)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $order = json_decode($response, true);

                // Find approval URL
                $approvalUrl = null;
                foreach ($order['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approvalUrl = $link['href'];
                        break;
                    }
                }

                echo json_encode([
                    'orderId' => $order['id'],
                    'approvalUrl' => $approvalUrl,
                    'gateway' => 'paypal'
                ]);
            } else {
                error_log('PayPal error: ' . $response);
                echo json_encode(['error' => 'Failed to create PayPal order']);
            }

        } catch (\Exception $e) {
            error_log('PayPal error: ' . $e->getMessage());
            echo json_encode(['error' => 'Payment processing error. Please try again.']);
        }
    }

    /**
     * Get PayPal access token
     */
    private function getPayPalAccessToken(array $config): ?string
    {
        $ch = curl_init($config['base_url'] . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_USERPWD => $config['client_id'] . ':' . $config['secret'],
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    /**
     * Create Venn.ca Session
     */
    private function createVennSession(array $request, string $token): void
    {
        try {
            $config = getVennConfig();

            // Venn.ca API integration
            // Note: This is a placeholder - implement based on Venn.ca API documentation
            $paymentData = [
                'merchant_id' => $config['merchant_id'],
                'amount' => $request['total_amount'],
                'currency' => 'CAD',
                'reference' => $request['request_number'],
                'description' => 'Distribution Request #' . $request['request_number'],
                'customer_email' => $request['email'],
                'return_url' => url('distribution/pay/success?token=' . $token . '&gateway=venn'),
                'cancel_url' => url('distribution/pay?token=' . $token . '&cancelled=1'),
                'metadata' => [
                    'distribution_request_id' => $request['id'],
                    'payment_token' => $token
                ]
            ];

            // Create HMAC signature
            $signature = hash_hmac('sha256', json_encode($paymentData), $config['api_secret']);

            $ch = curl_init($config['base_url'] . '/v1/payments/create');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-API-Key: ' . $config['api_key'],
                    'X-Signature: ' . $signature
                ],
                CURLOPT_POSTFIELDS => json_encode($paymentData)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                $result = json_decode($response, true);
                echo json_encode([
                    'paymentUrl' => $result['payment_url'] ?? null,
                    'sessionId' => $result['session_id'] ?? null,
                    'gateway' => 'venn'
                ]);
            } else {
                error_log('Venn.ca error: ' . $response);
                echo json_encode(['error' => 'Failed to create payment session']);
            }

        } catch (\Exception $e) {
            error_log('Venn.ca error: ' . $e->getMessage());
            echo json_encode(['error' => 'Payment processing error. Please try again.']);
        }
    }

    /**
     * Handle successful payment return
     * GET /distribution/pay/success
     */
    public function paymentSuccess(): void
    {
        // PayPal appends its own ?token= on return, making the URL have two token params.
        // First token = our payment link token, last token = PayPal's order ID.
        preg_match('/(?:^|&)token=([^&]+)/', $_SERVER['QUERY_STRING'] ?? '', $m);
        $ourToken     = isset($m[1]) ? urldecode($m[1]) : ($_GET['token'] ?? '');
        $paypalToken  = $_GET['token'] ?? ''; // PHP gives last value = PayPal order ID
        $gateway      = $_GET['gateway'] ?? $this->activeGateway;

        if (empty($ourToken)) {
            $this->showError('Invalid payment confirmation.');
            return;
        }

        try {
            // Route to appropriate verification
            switch ($gateway) {
                case 'stripe':
                    $this->verifyStripePayment($ourToken);
                    break;

                case 'paypal':
                    $this->verifyPayPalPayment($ourToken, $paypalToken);
                    break;

                case 'venn':
                    $this->verifyVennPayment($ourToken);
                    break;

                default:
                    $this->showError('Unknown payment gateway');
            }

        } catch (\Exception $e) {
            error_log('Payment verification error: ' . $e->getMessage());
            $this->showError('An error occurred processing your payment confirmation.');
        }
    }

    /**
     * Verify Stripe payment
     */
    private function verifyStripePayment(string $token): void
    {
        $sessionId = $_GET['session_id'] ?? '';

        if (empty($sessionId)) {
            $this->showError('Invalid payment confirmation.');
            return;
        }

        $config = getStripeConfig();
        \Stripe\Stripe::setApiKey($config['secret_key']);

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                $this->showError('Payment was not completed.');
                return;
            }

            $this->completePayment($token, 'stripe', $session->payment_intent);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log('Stripe verification error: ' . $e->getMessage());
            $this->showError('Could not verify payment. Please contact support.');
        }
    }

    /**
     * Verify PayPal payment
     */
    private function verifyPayPalPayment(string $ourToken, string $paypalOrderToken = ''): void
    {
        // Use the explicitly passed PayPal order token; fall back to $_GET last value
        if (empty($paypalOrderToken)) {
            $paypalOrderToken = $_GET['token'] ?? '';
        }

        error_log('[PayPal] verifyPayPalPayment — ourToken=' . substr($ourToken, 0, 8) . '... paypalOrderToken=' . $paypalOrderToken);

        if (empty($paypalOrderToken)) {
            error_log('[PayPal] Missing PayPal order token in return URL. QUERY_STRING=' . ($_SERVER['QUERY_STRING'] ?? ''));
            $this->showError('Payment could not be verified — missing order reference. Please contact support.');
            return;
        }

        $config = getPayPalConfig();
        $accessToken = $this->getPayPalAccessToken($config);

        if (!$accessToken) {
            error_log('[PayPal] Failed to obtain access token. client_id=' . substr($config['client_id'] ?? '', 0, 8) . '... mode=' . ($config['mode'] ?? 'unknown'));
            $this->showError('Could not connect to PayPal to verify your payment. Please contact support.');
            return;
        }

        // Capture the payment using PayPal's order ID
        $captureUrl = $config['base_url'] . '/v2/checkout/orders/' . $paypalOrderToken . '/capture';
        error_log('[PayPal] Attempting capture: ' . $captureUrl);

        $ch = curl_init($captureUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
                'PayPal-Request-Id: dist-' . $ourToken, // idempotency key
            ],
            CURLOPT_POSTFIELDS => '{}'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log('[PayPal] Capture response HTTP ' . $httpCode . ': ' . $response);

        if ($httpCode >= 200 && $httpCode < 300) {
            $order = json_decode($response, true);

            if (($order['status'] ?? '') === 'COMPLETED') {
                $paymentToken = !empty($ourToken) ? $ourToken : ($order['purchase_units'][0]['custom_id'] ?? '');
                $captureId    = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? $order['id'];
                $this->completePayment($paymentToken, 'paypal', $captureId);
            } else {
                $paypalStatus = $order['status'] ?? 'unknown';
                error_log('[PayPal] Order not COMPLETED — status=' . $paypalStatus);
                $this->showError('Payment was not completed (status: ' . htmlspecialchars($paypalStatus) . '). Please try again or contact support.');
            }
        } else {
            $errorData  = json_decode($response, true);
            $errorName  = $errorData['name'] ?? 'UNKNOWN';
            $errorMsg   = $errorData['message'] ?? $response;
            error_log('[PayPal] Capture failed HTTP ' . $httpCode . ' — ' . $errorName . ': ' . $errorMsg);

            // Surface a useful message for known errors
            $userMessage = match($errorName) {
                'INSTRUMENT_DECLINED'   => 'Your PayPal payment was declined. Please try a different payment method or add funds to your PayPal account.',
                'ORDER_ALREADY_CAPTURED'=> 'This payment has already been processed. Please check your order status.',
                'CURRENCY_NOT_SUPPORTED'=> 'CAD currency is not supported by this PayPal account. Please contact support.',
                'ORDER_NOT_APPROVED'    => 'Payment was not approved. Please try again.',
                default                 => 'PayPal error (' . htmlspecialchars($errorName) . '): ' . htmlspecialchars($errorMsg),
            };
            $this->showError($userMessage);
        }
    }

    /**
     * Verify Venn.ca payment
     */
    private function verifyVennPayment(string $token): void
    {
        $sessionId = $_GET['session_id'] ?? '';

        $config = getVennConfig();

        // Verify payment status with Venn.ca
        $ch = curl_init($config['base_url'] . '/v1/payments/' . $sessionId . '/status');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'X-API-Key: ' . $config['api_key']
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $result = json_decode($response, true);

            if (($result['status'] ?? '') === 'completed') {
                $this->completePayment($token, 'venn', $result['transaction_id'] ?? $sessionId);
            } else {
                $this->showError('Payment was not completed.');
            }
        } else {
            error_log('Venn.ca verification error: ' . $response);
            $this->showError('Failed to verify payment.');
        }
    }

    /**
     * Complete payment processing
     */
    private function completePayment(string $token, string $gateway, string $transactionId): void
    {
        try {
            // Get request with full billing info
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name,
                       COALESCE(bp.billing_street, bp.delivery_street) AS bill_street,
                       COALESCE(bp.billing_city, bp.delivery_city) AS bill_city,
                       COALESCE(bp.billing_province, bp.delivery_province) AS bill_province,
                       COALESCE(bp.billing_postal_code, bp.delivery_postal_code) AS bill_postal,
                       u.email, u.first_name, u.last_name, u.phone AS u_phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.payment_link_token = ?
            ");
            $stmt->execute([$token]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                $this->showError('Request not found.');
                return;
            }

            // Only update if still awaiting payment (idempotency guard)
            if ($request['status'] === 'awaiting_payment') {
                $this->db->beginTransaction();

                // Update request to paid
                $stmt = $this->db->prepare("
                    UPDATE distribution_requests
                    SET status = 'paid',
                        payment_status = 'paid',
                        paid_at = NOW(),
                        payment_intent_id = ?,
                        payment_method = ?,
                        payment_link_token = NULL,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$transactionId, $gateway, $request['id']]);

                // Log status change
                $this->logStatusChange($request['id'], 'awaiting_payment', 'paid', 'Payment received via ' . ucfirst($gateway));

                // Generate documents (distribution_documents table)
                $this->generateDocuments($request['id']);

                // Update or create invoice record (distribution_invoices — shown in business portal)
                $existingInv = $this->db->prepare("SELECT id FROM distribution_invoices WHERE distribution_request_id = ? LIMIT 1");
                $existingInv->execute([$request['id']]);
                $existingInvRow = $existingInv->fetch(\PDO::FETCH_ASSOC);
                if ($existingInvRow) {
                    // Invoice was pre-created when payment link was sent — mark it paid
                    $this->db->prepare("
                        UPDATE distribution_invoices
                        SET status = 'paid', paid_at = NOW(), updated_at = NOW()
                        WHERE id = ?
                    ")->execute([$existingInvRow['id']]);
                } else {
                    // No invoice yet — create one as paid
                    $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
                    $taxTotal = (float)($request['gst_amount'] ?? 0) + (float)($request['qst_amount'] ?? 0);
                    $taxRate = $request['subtotal'] > 0 ? round($taxTotal / $request['subtotal'] * 100, 2) : 14.98;
                    $this->db->prepare("
                        INSERT INTO distribution_invoices
                        (distribution_request_id, business_profile_id, invoice_number,
                         billing_company_name, billing_contact_name, billing_email, billing_phone,
                         billing_street, billing_city, billing_province, billing_postal_code, billing_country,
                         subtotal, tax_rate, tax_amount, delivery_fee, total_amount,
                         invoice_date, due_date, status, paid_at, sent_at, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', ?, ?, ?, ?, ?, CURDATE(), CURDATE(), 'paid', NOW(), NOW(), NOW(), NOW())
                    ")->execute([
                        $request['id'],
                        $request['business_profile_id'],
                        $invoiceNumber,
                        $request['company_name'],
                        trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? '')),
                        $request['email'],
                        $request['bp_phone'] ?? $request['u_phone'] ?? '',
                        $request['bill_street'],
                        $request['bill_city'],
                        $request['bill_province'],
                        $request['bill_postal'],
                        (float)($request['subtotal'] ?? 0),
                        $taxRate,
                        $taxTotal,
                        (float)($request['delivery_fee'] ?? 0),
                        (float)($request['total_amount'] ?? 0),
                    ]);
                }

                $this->db->commit();

                // Admin bell notification — payment received
                \App\Helpers\NotificationHelper::add(
                    'payment',
                    '💳 Payment Received — ' . $request['company_name'],
                    "Business \"{$request['company_name']}\" paid \$" . number_format((float)($request['total_amount'] ?? 0), 2) . " CAD for distribution request #{$request['request_number']} via " . ucfirst($gateway) . ".",
                    ['link' => '/admin/distribution/view?id=' . $request['id'], 'icon' => 'credit-card', 'priority' => 'high']
                );

                // Business bell — payment confirmed
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    (int)$request['business_profile_id'],
                    'payment',
                    '✅ Payment Confirmed — #' . $request['request_number'],
                    'Your payment of $' . number_format((float)($request['total_amount'] ?? 0), 2) . ' CAD has been received. Our team is now preparing your order.',
                    'distribution/requests/show?id=' . $request['id']
                );

                // Send confirmation email to business
                $this->sendPaymentConfirmationEmail($request);

                // Trigger driver assignment if all suppliers are already ready
                $notReady = $this->db->prepare("
                    SELECT COUNT(*) FROM purchase_orders
                    WHERE distribution_request_id = ?
                      AND status NOT IN ('ready_for_pickup','picked_up','completed','cancelled')
                ");
                $notReady->execute([$request['id']]);
                if ((int)$notReady->fetchColumn() === 0) {
                    \App\Controllers\AdminDistributionController::autoAssignDistributionDriver((int)$request['id'], $this->db);
                    logger("Payment received for DR #{$request['request_number']} — all suppliers already ready, auto-assign triggered.", 'info');
                }
            }

            // Redirect to clean done URL so language switching doesn't re-trigger verification
            header('Location: ' . url('distribution/pay/done?id=' . $request['id']));
            exit;

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Payment completion error: ' . $e->getMessage());
            $this->showError('An error occurred processing your payment.');
        }
    }

    /**
     * Handle webhooks for various gateways
     * POST /distribution/pay/webhook
     */
    public function webhook(): void
    {
        $gateway = $_GET['gateway'] ?? $this->activeGateway;

        switch ($gateway) {
            case 'stripe':
                $this->handleStripeWebhook();
                break;

            case 'paypal':
                $this->handlePayPalWebhook();
                break;

            case 'venn':
                $this->handleVennWebhook();
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Unknown gateway']);
        }
    }

    /**
     * Handle Stripe webhook
     */
    private function handleStripeWebhook(): void
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        $config = getStripeConfig();

        try {
            \Stripe\Stripe::setApiKey($config['secret_key']);

            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $config['webhook_secret']
            );

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                $token = $session->metadata->payment_token ?? null;

                if ($token) {
                    $this->completePaymentFromWebhook($token, 'stripe', $session->payment_intent);
                }
            }

            http_response_code(200);
            echo json_encode(['status' => 'success']);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle PayPal webhook — validates HMAC-SHA256 signature before processing
     */
    private function handlePayPalWebhook(): void
    {
        $payload         = @file_get_contents('php://input');
        $webhookId       = env('PAYPAL_WEBHOOK_ID', '');
        $transmissionId  = $_SERVER['HTTP_PAYPAL_TRANSMISSION_ID']   ?? '';
        $transmissionTime= $_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME']  ?? '';
        $transmissionSig = $_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG']   ?? '';
        $certUrl         = $_SERVER['HTTP_PAYPAL_CERT_URL']           ?? '';

        if (!empty($webhookId)) {
            if (empty($transmissionId) || empty($transmissionTime) || empty($transmissionSig) || empty($certUrl)) {
                error_log("DistributionPayPal webhook: missing signature headers from IP " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                http_response_code(401);
                echo json_encode(['error' => 'Missing webhook signature']);
                return;
            }

            if (!preg_match('#^https://api(?:\.sandbox)?\.paypal\.com/#', $certUrl)) {
                error_log("DistributionPayPal webhook: invalid cert URL '{$certUrl}'");
                http_response_code(401);
                echo json_encode(['error' => 'Invalid webhook source']);
                return;
            }

            $expectedSig = base64_encode(hash_hmac(
                'sha256',
                sprintf('%s|%s|%s|%u', $transmissionId, $transmissionTime, $webhookId, crc32($payload)),
                $webhookId,
                true
            ));

            if (!hash_equals($expectedSig, $transmissionSig)) {
                error_log("DistributionPayPal webhook: signature mismatch from IP " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                http_response_code(401);
                echo json_encode(['error' => 'Invalid webhook signature']);
                return;
            }
        }

        $event = json_decode($payload, true);

        if (($event['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
            $capture  = $event['resource'] ?? [];
            $customId = $capture['custom_id'] ?? null;

            if ($customId) {
                $this->completePaymentFromWebhook($customId, 'paypal', $capture['id'] ?? '');
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    }

    /**
     * Handle Venn.ca webhook
     */
    private function handleVennWebhook(): void
    {
        $payload = @file_get_contents('php://input');
        $event = json_decode($payload, true);

        if (($event['event'] ?? '') === 'payment.completed') {
            $token = $event['metadata']['payment_token'] ?? null;

            if ($token) {
                $this->completePaymentFromWebhook($token, 'venn', $event['transaction_id'] ?? '');
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    }

    /**
     * Complete payment from webhook
     */
    private function completePaymentFromWebhook(string $token, string $gateway, string $transactionId): void
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM distribution_requests WHERE payment_link_token = ?");
            $stmt->execute([$token]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request || $request['status'] !== 'awaiting_payment') {
                return;
            }

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'paid',
                    payment_status = 'paid',
                    paid_at = NOW(),
                    payment_intent_id = ?,
                    payment_method = ?,
                    payment_link_token = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$transactionId, $gateway, $request['id']]);

            $this->logStatusChange($request['id'], 'awaiting_payment', 'paid', 'Payment received via ' . ucfirst($gateway) . ' webhook');
            $this->generateDocuments($request['id']);

            $this->db->commit();

            // Fetch full request data for invoice + email
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name,
                       COALESCE(bp.billing_street, bp.delivery_street) AS bill_street,
                       COALESCE(bp.billing_city, bp.delivery_city) AS bill_city,
                       COALESCE(bp.billing_province, bp.delivery_province) AS bill_province,
                       COALESCE(bp.billing_postal_code, bp.delivery_postal_code) AS bill_postal,
                       u.email, u.first_name, u.last_name, u.phone AS u_phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ?
            ");
            $stmt->execute([$request['id']]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Update or create invoice record (distribution_invoices) on webhook payment
            if ($request) {
                $existingInv = $this->db->prepare("SELECT id FROM distribution_invoices WHERE distribution_request_id = ? LIMIT 1");
                $existingInv->execute([$request['id']]);
                $existingInvRow = $existingInv->fetch(\PDO::FETCH_ASSOC);
                if ($existingInvRow) {
                    $this->db->prepare("
                        UPDATE distribution_invoices
                        SET status = 'paid', paid_at = NOW(), updated_at = NOW()
                        WHERE id = ?
                    ")->execute([$existingInvRow['id']]);
                } else {
                    $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
                    $taxTotal = (float)($request['gst_amount'] ?? 0) + (float)($request['qst_amount'] ?? 0);
                    $taxRate = $request['subtotal'] > 0 ? round($taxTotal / $request['subtotal'] * 100, 2) : 14.98;
                    $this->db->prepare("
                        INSERT INTO distribution_invoices
                        (distribution_request_id, business_profile_id, invoice_number,
                         billing_company_name, billing_contact_name, billing_email, billing_phone,
                         billing_street, billing_city, billing_province, billing_postal_code, billing_country,
                         subtotal, tax_rate, tax_amount, delivery_fee, total_amount,
                         invoice_date, due_date, status, paid_at, sent_at, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', ?, ?, ?, ?, ?, CURDATE(), CURDATE(), 'paid', NOW(), NOW(), NOW(), NOW())
                    ")->execute([
                        $request['id'],
                        $request['business_profile_id'],
                        $invoiceNumber,
                        $request['company_name'],
                        trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? '')),
                        $request['email'],
                        $request['bp_phone'] ?? $request['u_phone'] ?? '',
                        $request['bill_street'],
                        $request['bill_city'],
                        $request['bill_province'],
                        $request['bill_postal'],
                        (float)($request['subtotal'] ?? 0),
                        $taxRate,
                        $taxTotal,
                        (float)($request['delivery_fee'] ?? 0),
                        (float)($request['total_amount'] ?? 0),
                    ]);
                }
            }

            if ($request) {
                // Admin bell notification — payment received via webhook
                \App\Helpers\NotificationHelper::add(
                    'payment',
                    '💳 Payment Received — ' . $request['company_name'],
                    "Business \"{$request['company_name']}\" paid \$" . number_format((float)($request['total_amount'] ?? 0), 2) . " CAD for distribution request #{$request['request_number']} via " . ucfirst($gateway) . " (webhook).",
                    ['link' => '/admin/distribution/view?id=' . $request['id'], 'icon' => 'credit-card', 'priority' => 'high']
                );

                // Business bell — payment confirmed (webhook path)
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    (int)$request['business_profile_id'],
                    'payment',
                    '✅ Payment Confirmed — #' . $request['request_number'],
                    'Your payment of $' . number_format((float)($request['total_amount'] ?? 0), 2) . ' CAD has been received. Our team is now preparing your order.',
                    'distribution/requests/show?id=' . $request['id']
                );

                $this->sendPaymentConfirmationEmail($request);
            }

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Webhook payment error: ' . $e->getMessage());
        }
    }

    /**
     * Generate Invoice, PO, and SO documents
     */
    private function generateDocuments(int $requestId): void
    {
        try {
            $invoiceNumber = $this->getNextDocumentNumber('invoice');
            $poNumber = $this->getNextDocumentNumber('purchase_order');
            $soNumber = $this->getNextDocumentNumber('sales_order');

            $stmt = $this->db->prepare("
                INSERT INTO distribution_documents
                (distribution_request_id, type, document_number)
                VALUES (?, 'invoice', ?)
            ");
            $stmt->execute([$requestId, $invoiceNumber]);

            $stmt = $this->db->prepare("
                INSERT INTO distribution_documents
                (distribution_request_id, type, document_number)
                VALUES (?, 'purchase_order', ?)
            ");
            $stmt->execute([$requestId, $poNumber]);

            $stmt = $this->db->prepare("
                INSERT INTO distribution_documents
                (distribution_request_id, type, document_number)
                VALUES (?, 'sales_order', ?)
            ");
            $stmt->execute([$requestId, $soNumber]);

        } catch (\PDOException $e) {
            error_log('Document generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get next document number in sequence
     */
    private function getNextDocumentNumber(string $type): string
    {
        $year = date('Y');
        $prefix = match($type) {
            'invoice' => 'INV',
            'purchase_order' => 'PO',
            'sales_order' => 'SO',
            default => 'DOC'
        };

        $stmt = $this->db->prepare("
            SELECT last_number FROM document_sequences
            WHERE type = ? AND year = ?
            FOR UPDATE
        ");
        $stmt->execute([$type, $year]);
        $sequence = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($sequence) {
            $nextNumber = $sequence['last_number'] + 1;
            $stmt = $this->db->prepare("
                UPDATE document_sequences SET last_number = last_number + 1
                WHERE type = ? AND year = ?
            ");
            $stmt->execute([$type, $year]);
        } else {
            $nextNumber = 1;
            $stmt = $this->db->prepare("
                INSERT INTO document_sequences (type, year, last_number)
                VALUES (?, ?, 1)
            ");
            $stmt->execute([$type, $year]);
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $nextNumber);
    }

    /**
     * Log status change
     */
    private function logStatusChange(int $requestId, string $fromStatus, string $toStatus, string $notes = ''): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, notes, created_at)
                VALUES (?, ?, ?, 'system', ?, NOW())
            ");
            $stmt->execute([$requestId, $fromStatus, $toStatus, $notes]);
        } catch (\PDOException $e) {
            error_log('Status history log error: ' . $e->getMessage());
        }
    }

    /**
     * Send payment confirmation email
     */
    private function sendPaymentConfirmationEmail(array $request): void
    {
        try {
            $subject = "Payment Received - Order #{$request['request_number']}";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #00b207; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Payment Confirmed!</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>Thank you for your payment! Your order <strong>#{$request['request_number']}</strong> has been confirmed.</p>

                    <div style='background: white; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #333;'>Order Summary</h3>
                        <p><strong>Request:</strong> {$request['request_name']}</p>
                        <p><strong>Amount Paid:</strong> $" . number_format($request['total_amount'], 2) . " CAD</p>
                        <p><strong>Payment Date:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                    </div>

                    <div style='background: #d1fae5; border-radius: 8px; padding: 15px; margin: 20px 0;'>
                        <p style='margin: 0; color: #065f46;'>
                            <strong>What's Next?</strong><br>
                            Our team will now begin procuring your items. You'll receive updates as your order progresses.
                        </p>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . url('distribution/requests/show?id=' . $request['id']) . "' style='display: inline-block; background: #00b207; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                            View Order Details
                        </a>
                    </div>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Payment confirmation email error: ' . $e->getMessage());
        }
    }

    /**
     * Show payment done/success page by request ID (safe to reload, no re-verification)
     * GET /distribution/pay/done?id=X
     */
    public function paymentDone(): void
    {
        $requestId = (int)($_GET['id'] ?? 0);

        if (!$requestId) {
            $this->showError('Invalid request.');
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, bp.status AS business_status,
                       u.id AS user_id, u.email, u.first_name, u.last_name, u.status AS user_status
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status = 'paid'
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                $this->showError('Payment confirmation not found.');
                return;
            }

            // Restore business session if it was lost during the payment gateway redirect
            if (!isset($_SESSION['user']['id'], $_SESSION['business']['id'])) {
                $_SESSION['user'] = [
                    'id'         => $request['user_id'],
                    'email'      => $request['email'],
                    'first_name' => $request['first_name'],
                    'last_name'  => $request['last_name'],
                    'role'       => 'business',
                    'status'     => $request['user_status'],
                ];
                $_SESSION['business'] = [
                    'id'           => $request['business_profile_id'],
                    'company_name' => $request['company_name'],
                    'user_id'      => $request['user_id'],
                    'status'       => $request['business_status'],
                ];
                // Preserve language — default to English if not already set
                if (!isset($_SESSION['language'])) {
                    $_SESSION['language'] = 'en';
                }
            }

            view('distribution.payment-success', ['request' => $request]);

        } catch (\PDOException $e) {
            error_log('Payment done page error: ' . $e->getMessage());
            $this->showError('An error occurred loading your payment confirmation.');
        }
    }

    /**
     * Notify each supplier linked to a distribution request that payment has been
     * confirmed and they should prepare their goods for driver pickup.
     */
    /**
     * Show error page
     */
    private function showError(string $message): void
    {
        view('distribution.payment-error', [
            'message' => $message
        ]);
    }
}
