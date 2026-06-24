<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/PaymentGatewayHelper.php';

/**
 * PaymentController - Handles B2C marketplace payment processing
 * Supports Stripe (card), PayPal, and Interac e-Transfer
 */
class PaymentController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Create payment session for checkout
     * POST /payment/create-session
     * Called via AJAX from checkout form after order creation
     */
    public function createCheckoutSession(): void
    {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Please login to continue']);
            return;
        }

        // Rate limit: max 10 payment session attempts per 10 minutes
        if (!rateLimit('payment_session', 10, 600)) {
            http_response_code(429);
            echo json_encode(['error' => 'Too many payment attempts. Please wait before trying again.']);
            return;
        }

        $orderIds = $_SESSION['pending_order_ids'] ?? [];
        $paymentMethod = post('payment_method', '');

        if (empty($orderIds)) {
            echo json_encode(['error' => 'No pending orders found']);
            return;
        }

        try {
            // Get order totals
            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
            $stmt = $this->db->prepare("
                SELECT id, order_number, total, shop_id
                FROM orders
                WHERE id IN ($placeholders) AND user_id = ?
            ");
            $params = array_merge($orderIds, [userId()]);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($orders)) {
                echo json_encode(['error' => 'Orders not found']);
                return;
            }

            $grandTotal = array_sum(array_column($orders, 'total'));
            $orderNumbers = implode(', ', array_column($orders, 'order_number'));

            switch ($paymentMethod) {
                case 'card':
                    $this->createStripeSession($orders, $grandTotal, $orderNumbers);
                    break;
                case 'paypal':
                    $this->createPayPalOrder($orders, $grandTotal, $orderNumbers);
                    break;
                default:
                    echo json_encode(['error' => 'Invalid payment method']);
            }

        } catch (\Exception $e) {
            logger("Payment session creation error: " . $e->getMessage(), 'error');
            echo json_encode(['error' => 'Failed to create payment session. Please try again.']);
        }
    }

    /**
     * Create Stripe Checkout Session
     */
    private function createStripeSession(array $orders, float $grandTotal, string $orderNumbers): void
    {
        $config = getStripeConfig();

        if (empty($config['secret_key'])) {
            echo json_encode(['error' => 'Card payments are not configured. Please contact support.']);
            return;
        }

        try {
            \Stripe\Stripe::setApiKey($config['secret_key']);

            $user = $_SESSION['user'] ?? [];
            $orderIds = array_column($orders, 'id');

            $lineItems = [[
                'price_data' => [
                    'currency' => 'cad',
                    'product_data' => [
                        'name' => 'OCS Marketplace Order',
                        'description' => 'Order(s): ' . $orderNumbers,
                    ],
                    'unit_amount' => (int)round($grandTotal * 100),
                ],
                'quantity' => 1,
            ]];

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $user['email'] ?? null,
                'client_reference_id' => implode(',', $orderIds),
                'metadata' => [
                    'order_ids' => implode(',', $orderIds),
                    'order_numbers' => $orderNumbers,
                    'user_id' => userId()
                ],
                'success_url' => url('payment/success?session_id={CHECKOUT_SESSION_ID}&gateway=stripe'),
                'cancel_url' => url('payment/cancel'),
            ]);

            // Store session ID on orders
            $stmt = $this->db->prepare("
                UPDATE orders SET payment_intent_id = ?, payment_gateway = 'stripe'
                WHERE id IN (" . implode(',', array_fill(0, count($orderIds), '?')) . ")
            ");
            $stmt->execute(array_merge([$session->id], $orderIds));

            echo json_encode([
                'redirect' => $session->url,
                'gateway' => 'stripe'
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            logger('Stripe session error: ' . $e->getMessage(), 'error');
            echo json_encode(['error' => 'Card payment processing error. Please try again.']);
        }
    }

    /**
     * Create PayPal Order
     */
    private function createPayPalOrder(array $orders, float $grandTotal, string $orderNumbers): void
    {
        $config = getPayPalConfig();

        if (empty($config['client_id']) || empty($config['secret'])) {
            echo json_encode(['error' => 'PayPal payments are not configured. Please contact support.']);
            return;
        }

        try {
            $accessToken = $this->getPayPalAccessToken($config);

            if (!$accessToken) {
                echo json_encode(['error' => 'Failed to connect to PayPal. Please try again.']);
                return;
            }

            $orderIds = array_column($orders, 'id');

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => implode(',', $orderIds),
                    'description' => 'OCS Marketplace - Order(s): ' . $orderNumbers,
                    'custom_id' => implode(',', $orderIds),
                    'amount' => [
                        'currency_code' => 'CAD',
                        'value' => number_format($grandTotal, 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'brand_name' => 'OCS Marketplace',
                    'return_url' => url('payment/success?gateway=paypal'),
                    'cancel_url' => url('payment/cancel')
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
                $paypalOrder = json_decode($response, true);

                // Find approval URL
                $approvalUrl = null;
                foreach ($paypalOrder['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approvalUrl = $link['href'];
                        break;
                    }
                }

                // Store PayPal order ID on our orders
                $stmt = $this->db->prepare("
                    UPDATE orders SET payment_intent_id = ?, payment_gateway = 'paypal'
                    WHERE id IN (" . implode(',', array_fill(0, count($orderIds), '?')) . ")
                ");
                $stmt->execute(array_merge([$paypalOrder['id']], $orderIds));

                echo json_encode([
                    'redirect' => $approvalUrl,
                    'gateway' => 'paypal'
                ]);
            } else {
                logger('PayPal create order error: ' . $response, 'error');
                echo json_encode(['error' => 'Failed to create PayPal payment. Please try again.']);
            }

        } catch (\Exception $e) {
            logger('PayPal error: ' . $e->getMessage(), 'error');
            echo json_encode(['error' => 'PayPal payment processing error. Please try again.']);
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
     * Handle successful payment return
     * GET /payment/success
     */
    public function success(): void
    {
        $gateway = get('gateway', '');

        try {
            switch ($gateway) {
                case 'stripe':
                    $this->verifyStripePayment();
                    break;
                case 'paypal':
                    $this->verifyPayPalPayment();
                    break;
                default:
                    setFlash('error', 'Invalid payment confirmation.');
                    redirect(url('/'));
            }
        } catch (\Exception $e) {
            logger('Payment verification error: ' . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred verifying your payment. Please contact support.');
            redirect(url('/'));
        }
    }

    /**
     * Verify Stripe payment and complete orders
     */
    private function verifyStripePayment(): void
    {
        $sessionId = get('session_id', '');

        if (empty($sessionId)) {
            setFlash('error', 'Invalid payment confirmation.');
            redirect(url('/'));
            return;
        }

        $config = getStripeConfig();
        \Stripe\Stripe::setApiKey($config['secret_key']);

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                setFlash('error', 'Payment was not completed. Please try again.');
                redirect(url('checkout'));
                return;
            }

            // Get order IDs from metadata
            $orderIdsStr = $session->metadata->order_ids ?? '';
            $orderIds = array_filter(array_map('intval', explode(',', $orderIdsStr)));

            if (empty($orderIds)) {
                // Fallback: find orders by payment_intent_id
                $stmt = $this->db->prepare("SELECT id FROM orders WHERE payment_intent_id = ?");
                $stmt->execute([$sessionId]);
                $orderIds = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'id');
            }

            if (!empty($orderIds)) {
                $this->completePayment($orderIds, 'stripe', $session->payment_intent);
            }

            // Redirect to checkout success with first order number
            $stmt = $this->db->prepare("SELECT order_number FROM orders WHERE id = ?");
            $stmt->execute([$orderIds[0] ?? 0]);
            $orderNumber = $stmt->fetchColumn();

            unset($_SESSION['pending_order_ids']);
            unset($_SESSION['cart']);

            redirect(url('checkout/success?order=' . $orderNumber . '&paid=1'));

        } catch (\Stripe\Exception\ApiErrorException $e) {
            logger('Stripe verification error: ' . $e->getMessage(), 'error');
            setFlash('error', 'Could not verify payment. Please contact support.');
            redirect(url('/'));
        }
    }

    /**
     * Verify PayPal payment and complete orders
     */
    private function verifyPayPalPayment(): void
    {
        $paypalToken = get('token', '');

        if (empty($paypalToken)) {
            setFlash('error', 'Invalid PayPal payment confirmation.');
            redirect(url('/'));
            return;
        }

        $config = getPayPalConfig();
        $accessToken = $this->getPayPalAccessToken($config);

        if (!$accessToken) {
            setFlash('error', 'Failed to verify PayPal payment. Please contact support.');
            redirect(url('/'));
            return;
        }

        // Capture the PayPal payment
        $ch = curl_init($config['base_url'] . '/v2/checkout/orders/' . $paypalToken . '/capture');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ],
            CURLOPT_POSTFIELDS => '{}'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $paypalOrder = json_decode($response, true);

            if ($paypalOrder['status'] === 'COMPLETED') {
                $customId = $paypalOrder['purchase_units'][0]['custom_id'] ?? '';
                $orderIds = array_filter(array_map('intval', explode(',', $customId)));
                $captureId = $paypalOrder['purchase_units'][0]['payments']['captures'][0]['id'] ?? $paypalOrder['id'];

                if (empty($orderIds)) {
                    // Fallback: find orders by PayPal order ID
                    $stmt = $this->db->prepare("SELECT id FROM orders WHERE payment_intent_id = ?");
                    $stmt->execute([$paypalToken]);
                    $orderIds = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'id');
                }

                if (!empty($orderIds)) {
                    $this->completePayment($orderIds, 'paypal', $captureId);
                }

                // Redirect to success
                $stmt = $this->db->prepare("SELECT order_number FROM orders WHERE id = ?");
                $stmt->execute([$orderIds[0] ?? 0]);
                $orderNumber = $stmt->fetchColumn();

                unset($_SESSION['pending_order_ids']);
                unset($_SESSION['cart']);

                redirect(url('checkout/success?order=' . $orderNumber . '&paid=1'));
                return;
            }
        }

        logger('PayPal capture failed: ' . ($response ?? 'no response'), 'error');
        setFlash('error', 'PayPal payment could not be completed. Please try again.');
        redirect(url('checkout'));
    }

    /**
     * Complete payment - update orders, assign delivery
     */
    private function completePayment(array $orderIds, string $gateway, string $transactionId): void
    {
        try {
            $this->db->beginTransaction();

            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));

            // Update orders to paid + confirmed
            $stmt = $this->db->prepare("
                UPDATE orders
                SET payment_status = 'paid',
                    payment_gateway = ?,
                    payment_intent_id = ?,
                    status = 'confirmed',
                    updated_at = NOW()
                WHERE id IN ($placeholders)
                AND payment_status = 'pending'
            ");
            $stmt->execute(array_merge([$gateway, $transactionId], $orderIds));
            $updatedCount = $stmt->rowCount(); // 0 if already processed (prevents double-fire)

            // Log status changes
            foreach ($orderIds as $orderId) {
                try {
                    $stmt = $this->db->prepare("
                        INSERT INTO delivery_status_history
                        (order_id, old_status, new_status, changed_by, notes, created_at)
                        VALUES (?, 'pending', 'confirmed', NULL, ?, NOW())
                    ");
                    $stmt->execute([$orderId, 'Payment received via ' . ucfirst($gateway)]);
                } catch (\PDOException $e) {
                    logger("Status history log failed: " . $e->getMessage(), 'warning');
                }
            }

            $this->db->commit();

            // Send order confirmation emails + admin bell notifications
            // Only fires when rows were actually updated (prevents duplicate on webhook + redirect)
            if ($updatedCount > 0) {
                foreach ($orderIds as $orderId) {
                    try {
                        $orderStmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
                        $orderStmt->execute([$orderId]);
                        $confirmedOrder = $orderStmt->fetch(\PDO::FETCH_ASSOC);

                        if ($confirmedOrder) {
                            // Fetch order items (product_name stored directly in order_items)
                            $itemsStmt = $this->db->prepare("
                                SELECT product_name as name, quantity, price
                                FROM order_items WHERE order_id = ?
                            ");
                            $itemsStmt->execute([$orderId]);
                            $orderItems = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

                            // Email confirmation to buyer
                            \App\Helpers\EmailHelper::sendOrderConfirmation($confirmedOrder, $orderItems);

                            // Admin bell notification
                            \App\Helpers\NotificationHelper::newOrder($confirmedOrder);

                            // Email notification to the shop owner (seller)
                            $shopStmt = $this->db->prepare("
                                SELECT u.email,
                                       COALESCE(u.business_name, s.name) AS company_name
                                FROM shops s
                                JOIN users u ON s.seller_id = u.id
                                WHERE s.id = ?
                            ");
                            $shopStmt->execute([$confirmedOrder['shop_id']]);
                            $shopSeller = $shopStmt->fetch(\PDO::FETCH_ASSOC);

                            if ($shopSeller && !empty($shopSeller['email'])) {
                                $custStmt = $this->db->prepare(
                                    "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = ?"
                                );
                                $custStmt->execute([$confirmedOrder['user_id']]);
                                $customer = $custStmt->fetch(\PDO::FETCH_ASSOC);

                                $confirmedOrder['customer_name'] = $customer['full_name'] ?? 'Customer';
                                $confirmedOrder['vendor_products'] = array_map(function ($item) {
                                    return [
                                        'name'        => $item['name'],
                                        'quantity'    => $item['quantity'],
                                        'vendor_cost' => $item['price'],
                                    ];
                                }, $orderItems);

                                \App\Helpers\EmailHelper::sendVendorNewOrder($shopSeller, $confirmedOrder);
                            }
                        }
                    } catch (\Exception $e) {
                        logger("Order confirmation notification failed for #{$orderId}: " . $e->getMessage(), 'warning');
                    }
                }
            }

            // Auto-assign delivery for each order (after commit)
            foreach ($orderIds as $orderId) {
                try {
                    $this->autoAssignDelivery($orderId);
                } catch (\Exception $e) {
                    logger("Delivery auto-assignment failed for order #{$orderId}: " . $e->getMessage(), 'warning');
                }
            }

            logger("Payment completed for orders [" . implode(',', $orderIds) . "] via {$gateway}, txn: {$transactionId}", 'info');

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger("Payment completion error: " . $e->getMessage(), 'error');
            throw $e;
        }
    }

    /**
     * Auto-assign delivery driver to an order
     */
    private function autoAssignDelivery(int $orderId): void
    {
        // Get order shop info
        $stmt = $this->db->prepare("SELECT shop_id, total FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order || !$order['shop_id']) return;

        // Check if already assigned
        $stmt = $this->db->prepare("SELECT id FROM delivery_assignments WHERE order_id = ?");
        $stmt->execute([$orderId]);
        if ($stmt->fetch()) return;

        // Find available driver
        $stmt = $this->db->prepare("
            SELECT u.id
            FROM users u
            LEFT JOIN delivery_assignments da ON u.id = da.driver_id
                AND da.status IN ('assigned', 'accepted', 'picked_up', 'on_the_way')
            WHERE u.role = 'delivery' AND u.is_active = 1
            GROUP BY u.id
            ORDER BY COUNT(da.id) ASC
            LIMIT 1
        ");
        $stmt->execute();
        $driver = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($driver) {
            $appConfig = require BASE_PATH . '/config/app.php';
            $assignDeliveryFee = (float)($appConfig['delivery_fee'] ?? 5.00);
            $stmt = $this->db->prepare("
                INSERT INTO delivery_assignments
                (order_id, driver_id, shop_id, status, delivery_fee, assigned_at, created_at)
                VALUES (?, ?, ?, 'assigned', ?, NOW(), NOW())
            ");
            $stmt->execute([$orderId, $driver['id'], $order['shop_id'], $assignDeliveryFee]);
            logger("Order #{$orderId} auto-assigned to driver #{$driver['id']} after payment", 'info');
        }
    }

    /**
     * Handle payment cancellation
     * GET /payment/cancel
     */
    public function cancel(): void
    {
        $pendingOrderIds = $_SESSION['pending_order_ids'] ?? [];

        if (!empty($pendingOrderIds)) {
            try {
                $placeholders = implode(',', array_fill(0, count($pendingOrderIds), '?'));

                // Restore stock for each cancelled order item before deleting
                $itemsStmt = $this->db->prepare("
                    SELECT oi.product_id, oi.shop_id, oi.quantity
                    FROM order_items oi
                    JOIN orders o ON oi.order_id = o.id
                    WHERE oi.order_id IN ($placeholders) AND o.payment_status = 'pending'
                ");
                $itemsStmt->execute($pendingOrderIds);
                $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $this->db->prepare("
                        UPDATE shop_inventory
                        SET stock_quantity = stock_quantity + ?
                        WHERE product_id = ? AND shop_id = ?
                    ")->execute([$item['quantity'], $item['product_id'], $item['shop_id']]);
                }

                // Delete order items first
                $this->db->prepare("DELETE FROM order_items WHERE order_id IN ($placeholders)")
                    ->execute($pendingOrderIds);

                // Delete orders
                $this->db->prepare("DELETE FROM orders WHERE id IN ($placeholders) AND payment_status = 'pending'")
                    ->execute($pendingOrderIds);

                logger("Cancelled pending orders and restored stock: " . implode(',', $pendingOrderIds), 'info');
            } catch (\PDOException $e) {
                logger("Error cleaning up cancelled orders: " . $e->getMessage(), 'error');
            }

            unset($_SESSION['pending_order_ids']);
        }

        setFlash('info', 'Payment was cancelled. Your cart items are still available.');
        redirect(url('checkout'));
    }

    /**
     * Handle webhooks from payment gateways
     * POST /payment/webhook
     */
    public function webhook(): void
    {
        $gateway = get('gateway', '');

        switch ($gateway) {
            case 'stripe':
                $this->handleStripeWebhook();
                break;
            case 'paypal':
                $this->handlePayPalWebhook();
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
                $orderIdsStr = $session->metadata->order_ids ?? '';
                $orderIds = array_filter(array_map('intval', explode(',', $orderIdsStr)));

                if (!empty($orderIds)) {
                    // Check if not already completed
                    $stmt = $this->db->prepare("SELECT id FROM orders WHERE id = ? AND payment_status = 'pending'");
                    $stmt->execute([$orderIds[0]]);
                    if ($stmt->fetch()) {
                        $this->completePayment($orderIds, 'stripe', $session->payment_intent);
                    }
                }
            }

            http_response_code(200);
            echo json_encode(['status' => 'success']);

        } catch (\Exception $e) {
            logger('Stripe webhook error: ' . $e->getMessage(), 'error');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle PayPal webhook
     */
    private function handlePayPalWebhook(): void
    {
        $payload = @file_get_contents('php://input');

        // Verify PayPal webhook signature
        $webhookId = env('PAYPAL_WEBHOOK_ID', '');
        $transmissionId = $_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'] ?? '';
        $transmissionTime = $_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'] ?? '';
        $transmissionSig = $_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG'] ?? '';
        $certUrl = $_SERVER['HTTP_PAYPAL_CERT_URL'] ?? '';

        if (!empty($webhookId)) {
            // Require all signature headers to be present
            if (empty($transmissionId) || empty($transmissionTime) || empty($transmissionSig) || empty($certUrl)) {
                error_log("PayPal webhook: missing signature headers from IP " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                http_response_code(401);
                echo json_encode(['error' => 'Missing webhook signature']);
                return;
            }

            // Validate cert URL is from PayPal (prevent SSRF / header spoofing)
            if (!preg_match('#^https://api(?:\.sandbox)?\.paypal\.com/#', $certUrl)) {
                error_log("PayPal webhook: invalid cert URL '{$certUrl}' from IP " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                http_response_code(401);
                echo json_encode(['error' => 'Invalid webhook source']);
                return;
            }

            // Verify via PayPal's official verify-webhook-signature API (RSA-SHA256)
            $paypalConfig = getPayPalConfig();
            $accessToken  = $this->getPayPalAccessToken($paypalConfig);

            if (!$accessToken) {
                error_log("PayPal webhook: could not obtain access token for verification");
                http_response_code(500);
                echo json_encode(['error' => 'Webhook verification unavailable']);
                return;
            }

            $verifyBody = json_encode([
                'auth_algo'         => $_SERVER['HTTP_PAYPAL_AUTH_ALGO'] ?? 'SHA256withRSA',
                'cert_url'          => $certUrl,
                'transmission_id'   => $transmissionId,
                'transmission_sig'  => $transmissionSig,
                'transmission_time' => $transmissionTime,
                'webhook_id'        => $webhookId,
                'webhook_event'     => json_decode($payload, true),
            ]);

            $ch = curl_init($paypalConfig['base_url'] . '/v1/notifications/verify-webhook-signature');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $verifyBody,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ],
            ]);
            $verifyResponse = curl_exec($ch);
            curl_close($ch);

            $verifyData = json_decode($verifyResponse, true);
            if (($verifyData['verification_status'] ?? '') !== 'SUCCESS') {
                error_log("PayPal webhook: verification failed — status=" . ($verifyData['verification_status'] ?? 'null') . " IP=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                http_response_code(401);
                echo json_encode(['error' => 'Invalid webhook signature']);
                return;
            }
        }

        $event = json_decode($payload, true);
        if (!$event) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            return;
        }

        if (($event['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
            $capture = $event['resource'] ?? [];
            $customId = $capture['custom_id'] ?? '';
            $orderIds = array_filter(array_map('intval', explode(',', $customId)));

            if (!empty($orderIds)) {
                // Verify order amount matches PayPal capture amount
                $captureAmount = (float) ($capture['amount']['value'] ?? 0);
                $stmt = $this->db->prepare("SELECT id, total FROM orders WHERE id = ? AND payment_status = 'pending'");
                $stmt->execute([$orderIds[0]]);
                $order = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($order && abs((float)$order['total'] - $captureAmount) < 0.01) {
                    $this->completePayment($orderIds, 'paypal', $capture['id'] ?? '');
                } elseif ($order) {
                    error_log("PayPal webhook: amount mismatch for order {$orderIds[0]}. Expected: {$order['total']}, Got: {$captureAmount}");
                }
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    }
}
