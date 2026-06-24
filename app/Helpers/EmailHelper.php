<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * EmailHelper - Simple and powerful email sending
 * Supports SMTP, templates, and logging
 */
class EmailHelper
{
    private static $config = null;

    /** @var string|null Optional email type for DB logging (set before calling send) */
    private static $nextEmailType = null;
    /** @var string|null Optional related entity type */
    private static $nextRelatedType = null;
    /** @var int|null Optional related entity ID */
    private static $nextRelatedId = null;

    /**
     * Set metadata for the next email send (used for DB logging).
     * Call this before send() to tag the email with a type and related entity.
     */
    public static function setNextMeta(?string $emailType, ?string $relatedType = null, ?int $relatedId = null): void
    {
        self::$nextEmailType = $emailType;
        self::$nextRelatedType = $relatedType;
        self::$nextRelatedId = $relatedId;
    }

    /**
     * Load email configuration
     */
    private static function loadConfig(): array
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/mail.php';
        }
        return self::$config;
    }

    /**
     * Send email using PHPMailer
     *
     * @param string|array $to Recipient email(s)
     * @param string $subject Email subject
     * @param string $body HTML body
     * @param array $options Additional options (attachments, cc, bcc, etc.)
     * @return bool Success status
     */
    public static function send($to, string $subject, string $body, array $options = []): bool
    {
        $config = self::loadConfig();

        // Sanitize subject to prevent header injection
        $subject = str_replace(["\r", "\n", "\0"], '', $subject);

        // Validate recipient emails
        $emails = is_array($to) ? array_keys($to) : [$to];
        foreach ($emails as $idx => $email) {
            $email = is_numeric($idx) && is_array($to) ? $to[$idx] : $email;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $email)) {
                logger("Invalid email address blocked: {$email}", 'error');
                return false;
            }
        }

        // Test mode - just log and return
        if ($config['test_mode']) {
            self::logToDatabase($to, $subject, $body, 'test_mode');
            return self::logEmail($to, $subject, $body, 'Test mode - not sent');
        }

        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = $config['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp']['username'];
            $mail->Password = $config['smtp']['password'];
            $mail->SMTPSecure = $config['smtp']['encryption'];
            $mail->Port = $config['smtp']['port'];
            $mail->CharSet = $config['defaults']['charset'];

            // Recipients
            $mail->setFrom(
                $options['from_address'] ?? $config['smtp']['from_address'],
                $options['from_name'] ?? $config['smtp']['from_name']
            );

            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    if (is_numeric($email)) {
                        $mail->addAddress($name);
                    } else {
                        $mail->addAddress($email, $name);
                    }
                }
            } else {
                $mail->addAddress($to);
            }

            // Reply-To (validate before adding)
            if (!empty($options['reply_to']) && filter_var($options['reply_to'], FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($options['reply_to']);
            } elseif (!empty($config['defaults']['reply_to']) && filter_var($config['defaults']['reply_to'], FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($config['defaults']['reply_to']);
            }

            // CC & BCC (validate each address)
            if (!empty($options['cc'])) {
                $ccList = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
                foreach ($ccList as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->addCC($email);
                    }
                }
            }

            if (!empty($options['bcc'])) {
                $bccList = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
                foreach ($bccList as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->addBCC($email);
                    }
                }
            }

            // Auto-BCC admin on all outgoing emails (unless admin is already a recipient)
            $adminBcc = $config['admin_email'] ?? '';
            if ($adminBcc && empty($options['no_admin_bcc'])) {
                $recipientEmails = is_array($to) ? array_map(function($k, $v) {
                    return is_numeric($k) ? $v : $k;
                }, array_keys($to), array_values($to)) : [$to];
                if (!in_array($adminBcc, $recipientEmails, true)) {
                    $mail->addBCC($adminBcc);
                }
            }

            // Attachments
            if (!empty($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (is_array($attachment)) {
                        $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
                    } else {
                        $mail->addAttachment($attachment);
                    }
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Plain text alternative
            if (!empty($options['text'])) {
                $mail->AltBody = $options['text'];
            } else {
                $mail->AltBody = strip_tags($body);
            }

            // Send
            $result = $mail->send();

            // Log success
            self::logToDatabase($to, $subject, $body, 'sent');
            if ($config['log']['enabled']) {
                self::logEmail($to, $subject, $body, 'Sent successfully');
            }

            return $result;

        } catch (Exception $e) {
            // Log error
            $errorMsg = "Email sending failed: {$mail->ErrorInfo}";
            logger($errorMsg, 'error');

            self::logToDatabase($to, $subject, $body, 'failed', $errorMsg);
            if ($config['log']['enabled']) {
                self::logEmail($to, $subject, $body, $errorMsg);
            }

            return false;
        }
    }

    /**
     * Send email using template
     *
     * @param string|array $to Recipient email(s)
     * @param string $template Template name (without .php)
     * @param array $data Data to pass to template
     * @param array $options Additional options
     * @return bool Success status
     */
    public static function sendTemplate($to, string $template, array $data = [], array $options = []): bool
    {
        $config = self::loadConfig();

        // Load template
        $templatePath = $config['templates_path'] . $template . '.php';

        if (!file_exists($templatePath)) {
            logger("Email template not found: {$template}", 'error');
            return false;
        }

        // Render template
        // All templates now use placeholder system
        $body = file_get_contents($templatePath);

        // Build comprehensive replacement array
        $replacements = [
            // User placeholders (supports both nested $data['user']['first_name'] and flat $data['user_first_name'])
            '{{user_first_name}}' => htmlspecialchars($data['user']['first_name'] ?? $data['user_first_name'] ?? 'there'),
            '{{user_last_name}}' => htmlspecialchars($data['user']['last_name'] ?? $data['user_last_name'] ?? ''),
            '{{user_email}}' => htmlspecialchars($data['user']['email'] ?? $data['user_email'] ?? ''),

            // Seller placeholders
            '{{seller_first_name}}' => htmlspecialchars($data['seller']['first_name'] ?? 'there'),
            '{{seller_email}}' => htmlspecialchars($data['seller']['email'] ?? ''),

            // Order placeholders
            '{{order_number}}' => htmlspecialchars($data['order']['order_number'] ?? 'N/A'),
            '{{order_total}}' => number_format($data['order']['total'] ?? 0, 2),
            '{{order_date}}' => isset($data['order']['created_at']) ? date('F j, Y', strtotime($data['order']['created_at'])) : date('F j, Y'),
            '{{old_status}}' => htmlspecialchars($data['old_status'] ?? 'N/A'),
            '{{new_status}}' => htmlspecialchars($data['new_status'] ?? 'N/A'),
            '{{cancellation_reason}}' => htmlspecialchars($data['reason'] ?? ''),

            // Product placeholders
            '{{product_name}}' => htmlspecialchars($data['product']['name'] ?? 'Product'),
            '{{product_sku}}' => htmlspecialchars($data['product']['sku'] ?? 'N/A'),
            '{{current_stock}}' => htmlspecialchars($data['current_stock'] ?? '0'),

            // Date placeholders
            '{{current_year}}' => date('Y'),
            '{{submitted_date}}' => date('F j, Y \a\t g:i A'),

            // Notification placeholders
            '{{notification_title}}' => htmlspecialchars($data['notification_title'] ?? ''),
            '{{notification_message}}' => htmlspecialchars($data['notification_message'] ?? ''),
            '{{notification_type}}' => htmlspecialchars($data['notification_type'] ?? ''),
            '{{action_url}}' => htmlspecialchars($data['action_url'] ?? '#'),
        ];

        $body = str_replace(array_keys($replacements), array_values($replacements), $body);


        // Get subject from data or default
        $subject = $data['subject'] ?? $options['subject'] ?? 'Notification from OCSAPP';

        // Replace variables in subject
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $subject = str_replace('{' . $key . '}', $value, $subject);
            }
        }

        return self::send($to, $subject, $body, $options);
    }

    /**
     * Send order confirmation email
     */
    public static function sendOrderConfirmation(array $order, array $items = []): bool
    {
        $config = self::loadConfig();

        if (!$config['notifications']['order_confirmation']['enabled']) {
            return false;
        }

        // Get customer email
        $db = \Database::getConnection();
        $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = :id");
        $stmt->execute(['id' => $order['user_id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            logger("User not found for order confirmation: {$order['user_id']}", 'error');
            return false;
        }

        $subject = str_replace('{order_number}', $order['order_number'],
                    $config['notifications']['order_confirmation']['subject']);

        // Render PHP template via output buffering (template uses PHP, not {{placeholder}} system)
        $templatePath = $config['templates_path'] . 'order-confirmation.php';
        if (!file_exists($templatePath)) {
            logger("Order confirmation template not found", 'error');
            return false;
        }
        ob_start();
        extract(['order' => $order, 'items' => $items, 'user' => $user]);
        include $templatePath;
        $body = ob_get_clean();

        self::setNextMeta('order_confirmation', 'user', (int)($order['user_id'] ?? 0));
        return self::send($user['email'], $subject, $body);
    }

    /**
     * Send order status update email
     */
    public static function sendOrderStatusUpdate(array $order, string $oldStatus, string $newStatus): bool
    {
        $config = self::loadConfig();

        if (!$config['notifications']['order_status_update']['enabled']) {
            return false;
        }

        // Get customer email
        $db = \Database::getConnection();
        $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = :id");
        $stmt->execute(['id' => $order['user_id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $data = [
            'order' => $order,
            'user' => $user,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'subject' => str_replace('{order_number}', $order['order_number'],
                        $config['notifications']['order_status_update']['subject'])
        ];

        self::setNextMeta('order_status_update', 'user', (int)($order['user_id'] ?? 0));
        return self::sendTemplate($user['email'], 'order-status-update', $data);
    }

    /**
     * Send order cancelled email
     */
    public static function sendOrderCancelled(array $order, string $reason = ''): bool
    {
        $config = self::loadConfig();

        if (!$config['notifications']['order_cancelled']['enabled']) {
            return false;
        }

        // Get customer email
        $db = \Database::getConnection();
        $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = :id");
        $stmt->execute(['id' => $order['user_id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $data = [
            'order' => $order,
            'user' => $user,
            'reason' => $reason,
            'subject' => str_replace('{order_number}', $order['order_number'],
                        $config['notifications']['order_cancelled']['subject'])
        ];

        return self::sendTemplate($user['email'], 'order-cancelled', $data);
    }

    /**
     * Send low stock alert to seller
     */
    public static function sendLowStockAlert(int $sellerId, array $product, int $currentStock): bool
    {
        $config = self::loadConfig();

        if (!$config['notifications']['low_stock_alert']['enabled']) {
            return false;
        }

        // Get seller email
        $db = \Database::getConnection();
        $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = :id");
        $stmt->execute(['id' => $sellerId]);
        $seller = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$seller) {
            return false;
        }

        $data = [
            'seller' => $seller,
            'product' => $product,
            'current_stock' => $currentStock,
            'product_name' => $product['name'],
            'subject' => str_replace('{product_name}', $product['name'],
                        $config['notifications']['low_stock_alert']['subject'])
        ];

        return self::sendTemplate($seller['email'], 'low-stock-alert', $data);
    }

    /**
     * Log email for debugging (file-based)
     */
    private static function logEmail($to, string $subject, string $body, string $status): bool
    {
        $config = self::loadConfig();

        if (!$config['log']['enabled']) {
            return false;
        }

        $logEntry = sprintf(
            "[%s] To: %s | Subject: %s | Status: %s\n",
            date('Y-m-d H:i:s'),
            is_array($to) ? implode(', ', array_keys($to)) : $to,
            $subject,
            $status
        );

        $logPath = $config['log']['path'];
        $logDir = dirname($logPath);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        return file_put_contents($logPath, $logEntry, FILE_APPEND) !== false;
    }

    /**
     * Log email to database for archive/history tracking
     */
    private static function logToDatabase($to, string $subject, string $body, string $dbStatus, ?string $errorMsg = null): void
    {
        try {
            $db = \Database::getConnection();

            // Resolve recipient email and name
            if (is_array($to)) {
                $recipientEmail = '';
                $recipientName = '';
                foreach ($to as $key => $val) {
                    if (is_numeric($key)) {
                        $recipientEmail = $val;
                    } else {
                        $recipientEmail = $key;
                        $recipientName = $val;
                    }
                    break; // log first recipient
                }
            } else {
                $recipientEmail = $to;
                $recipientName = null;
            }

            $config = self::loadConfig();

            $stmt = $db->prepare("
                INSERT INTO email_log
                    (recipient_email, recipient_name, subject, body, email_type, status, error_message,
                     related_type, related_id, sender_email, sender_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $recipientEmail,
                $recipientName ?: null,
                $subject,
                $body,
                self::$nextEmailType,
                $dbStatus,
                $errorMsg,
                self::$nextRelatedType,
                self::$nextRelatedId,
                $config['smtp']['from_address'] ?? 'info@ocsapp.ca',
                $config['smtp']['from_name'] ?? 'OCSAPP',
            ]);
        } catch (\Exception $e) {
            // Silently fail — don't break email sending if logging fails
            if (function_exists('logger')) {
                logger("Email DB log failed: " . $e->getMessage(), 'error');
            }
        } finally {
            // Always clear meta after logging
            self::$nextEmailType = null;
            self::$nextRelatedType = null;
            self::$nextRelatedId = null;
        }
    }

    /**
     * Send raw HTML email (alias for send with no extra options)
     */
    public static function sendRaw(string $to, string $subject, string $body): bool
    {
        return self::send($to, $subject, $body);
    }

    /**
     * Send buyer welcome email
     *
     * @param array $user User data (email, first_name, last_name)
     * @return bool Success status
     */
    public static function sendBuyerWelcome(array $user): bool
    {
        $data = [
            'user' => $user,
            'subject' => 'Welcome to OCSAPP - Your Buyer Account is Ready!'
        ];

        self::setNextMeta('buyer_welcome', 'user', (int)($user['id'] ?? 0));
        return self::sendTemplate($user['email'], 'buyer-welcome', $data);
    }

    /**
     * Send seller application received email (pending approval)
     *
     * @param array $user User data (email, first_name, last_name)
     * @return bool Success status
     */
    public static function sendSellerApplicationReceived(array $user): bool
    {
        $data = [
            'user' => $user,
            'subject' => 'Demande de vendeur reçue / Seller Application Received - Pending Review'
        ];

        self::setNextMeta('seller_application_received', 'user', (int)($user['id'] ?? 0));
        return self::sendTemplate($user['email'], 'seller-welcome', $data);
    }

    /**
     * Send seller approval email
     *
     * @param array $user User data (email, first_name, last_name)
     * @return bool Success status
     */
    public static function sendSellerApproved(array $user): bool
    {
        $data = [
            'user' => $user,
            'subject' => 'Compte vendeur approuvé ! / Seller Account Approved - OCSAPP'
        ];

        self::setNextMeta('seller_approved', 'user', (int)($user['id'] ?? 0));
        return self::sendTemplate($user['email'], 'seller-approved', $data);
    }

    /**
     * Send admin notification for new seller application
     *
     * @param array $seller Seller user data
     * @return bool Success status
     */
    public static function sendAdminSellerNotification(array $seller): bool
    {
        $config = self::loadConfig();
        $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';

        $data = [
            'seller' => $seller,
            'seller_first_name' => $seller['first_name'] ?? '',
            'seller_last_name' => $seller['last_name'] ?? '',
            'seller_email' => $seller['email'] ?? '',
            'seller_phone' => $seller['phone'] ?? 'Not provided',
            'subject' => 'Nouvelle demande de vendeur / New Seller Application - Action Required'
        ];

        return self::sendTemplate($adminEmail, 'admin-seller-notification', $data);
    }

    /**
     * Send buyer notification when order is out for delivery
     *
     * @param array $order Order details
     * @param array $driver Driver details
     * @return bool Success status
     */
    public static function sendBuyerOutForDelivery(array $order, array $driver = []): bool
    {
        $buyerEmail = $order['customer_email'] ?? '';
        if (empty($buyerEmail)) {
            logger("Customer email not found for out-for-delivery notification", 'error');
            return false;
        }

        $data = [
            'order' => $order,
            'user_first_name' => $order['customer_first_name'] ?? 'Customer',
            'order_number' => $order['order_number'] ?? 'N/A',
            'order_id' => $order['id'] ?? '',
            'order_total' => number_format($order['total'] ?? 0, 2),
            'delivery_eta' => $order['delivery_eta'] ?? 'Within 30-60 minutes',
            'driver_name' => $driver['name'] ?? 'OCSAPP Delivery Partner',
            'driver_phone' => $driver['phone'] ?? 'Contact support',
            'delivery_address' => $order['delivery_address'] ?? '',
            'order_items_summary' => $order['items_summary'] ?? '',
            'subject' => "Your Order #{$order['order_number']} is On the Way!"
        ];

        return self::sendTemplate($buyerEmail, 'buyer-out-for-delivery', $data);
    }

    /**
     * Send buyer notification when order is delivered
     *
     * @param array $order Order details
     * @return bool Success status
     */
    public static function sendBuyerOrderDelivered(array $order): bool
    {
        $buyerEmail = $order['customer_email'] ?? '';
        if (empty($buyerEmail)) {
            logger("Customer email not found for delivery confirmation", 'error');
            return false;
        }

        $data = [
            'order' => $order,
            'user_first_name' => $order['customer_first_name'] ?? 'Customer',
            'order_number' => $order['order_number'] ?? 'N/A',
            'order_id' => $order['id'] ?? '',
            'order_total' => number_format($order['total'] ?? 0, 2),
            'order_subtotal' => number_format($order['subtotal'] ?? 0, 2),
            'delivery_fee' => number_format($order['delivery_fee'] ?? 0, 2),
            'delivery_date' => $order['delivered_at'] ?? date('F j, Y \a\t g:i A'),
            'delivery_address' => $order['delivery_address'] ?? '',
            'order_items_summary' => $order['items_summary'] ?? '',
            'subject' => "Your Order #{$order['order_number']} Has Been Delivered!"
        ];

        return self::sendTemplate($buyerEmail, 'buyer-order-delivered', $data);
    }

    /**
     * Send vendor notification for new order
     *
     * @param array $vendor Vendor details
     * @param array $order Order details
     * @return bool Success status
     */
    public static function sendVendorNewOrder(array $vendor, array $order): bool
    {
        $vendorEmail = $vendor['email'] ?? '';

        if (empty($vendorEmail)) {
            logger("Vendor email not provided", 'error');
            return false;
        }

        // Calculate vendor-specific totals
        $vendorQuantity = 0;
        $vendorCostTotal = 0;
        $vendorProductsList = '';

        // Build vendor products list (this would be populated from vendor_products join)
        if (!empty($order['vendor_products'])) {
            foreach ($order['vendor_products'] as $product) {
                $vendorQuantity += $product['quantity'] ?? 0;
                $vendorCostTotal += ($product['vendor_cost'] ?? 0) * ($product['quantity'] ?? 0);
                $vendorProductsList .= "• " . ($product['name'] ?? 'Unknown Product') . " (Qty: " . ($product['quantity'] ?? 0) . ")\n";
            }
        }

        $data = [
            'vendor_name' => $vendor['company_name'] ?? 'Vendor',
            'order_number' => $order['order_number'] ?? 'N/A',
            'customer_name' => $order['customer_name'] ?? 'Customer',
            'order_date' => $order['created_at'] ?? date('F j, Y'),
            'order_total' => number_format($order['total'] ?? 0, 2),
            'vendor_products_list' => $vendorProductsList ?: 'No products listed',
            'vendor_quantity' => $vendorQuantity,
            'vendor_cost_total' => number_format($vendorCostTotal, 2),
            'order_id' => $order['id'] ?? 0,
            'subject' => "Nouvelle commande / New Order #{$order['order_number']} - Action Required"
        ];

        logger("Sending vendor order notification to {$vendorEmail} for order #{$order['order_number']}", 'info');

        return self::sendTemplate($vendorEmail, 'vendor-new-order', $data);
    }

    /**
     * Send purchase order created notification to supplier
     *
     * @param array $po Purchase order details
     * @param array $supplier Supplier details
     * @param array $items PO items
     * @return bool Success status
     */
    public static function sendPurchaseOrderCreated(array $po, array $supplier, array $items): bool
    {
        $supplierEmail = $supplier['email'] ?? '';
        if (empty($supplierEmail)) {
            logger("Supplier email not found for PO notification", 'error');
            return false;
        }

        // Build items list
        $itemsList = '';
        foreach ($items as $item) {
            $itemsList .= sprintf(
                "<tr><td style='padding: 12px; border-bottom: 1px solid #e5e7eb;'>%s</td><td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;'>%d</td><td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;'>$%s</td><td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;'>$%s</td></tr>",
                htmlspecialchars($item['product_name'] ?? 'Product'),
                $item['quantity_ordered'] ?? 0,
                number_format($item['unit_cost'] ?? 0, 2),
                number_format($item['total_cost'] ?? 0, 2)
            );
        }

        $body = sprintf("
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #00b207 0%%, #009906 100%%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>New Purchase Order</h1>
                    <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>PO #%s</p>
                </div>

                <div style='background: white; padding: 30px; border: 1px solid #e5e7eb;'>
                    <p style='font-size: 16px; color: #374151; margin-bottom: 20px;'>Hello %s,</p>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6;'>
                        A new purchase order has been created for your review. Please log in to your supplier portal to accept or decline this order.
                    </p>

                    <div style='background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <table style='width: 100%%;'>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>PO Number:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Order Date:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Expected Delivery:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Status:</td>
                                <td style='padding: 8px 0; text-align: right;'><span style='background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;'>%s</span></td>
                            </tr>
                        </table>
                    </div>

                    <h3 style='margin: 24px 0 12px; font-size: 16px; color: #111827;'>Order Items</h3>
                    <table style='width: 100%%; border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 8px;'>
                        <thead>
                            <tr style='background: #f9fafb;'>
                                <th style='padding: 12px; text-align: left; font-size: 12px; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;'>Product</th>
                                <th style='padding: 12px; text-align: center; font-size: 12px; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;'>Quantity</th>
                                <th style='padding: 12px; text-align: right; font-size: 12px; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;'>Unit Price</th>
                                <th style='padding: 12px; text-align: right; font-size: 12px; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            %s
                        </tbody>
                    </table>

                    <div style='margin: 24px 0; padding: 20px; background: #f9fafb; border-radius: 8px;'>
                        <table style='width: 100%%;'>
                            <tr>
                                <td style='padding: 8px 0; font-size: 14px; color: #6b7280;'>Subtotal:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600;'>$%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 14px; color: #6b7280;'>Shipping:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600;'>$%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; font-size: 14px; color: #6b7280;'>Tax:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600;'>$%s</td>
                            </tr>
                            <tr style='border-top: 2px solid #111827;'>
                                <td style='padding: 12px 0; font-size: 18px; color: #111827; font-weight: 700;'>Total:</td>
                                <td style='padding: 12px 0; text-align: right; font-size: 18px; font-weight: 700; color: #00b207;'>$%s</td>
                            </tr>
                        </table>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='https://ocsapp.ca/supplier/orders' style='background: linear-gradient(135deg, #00b207 0%%, #009906 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>View Purchase Order</a>
                    </div>

                    %s
                </div>

                <div style='background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;'>
                    <p style='margin: 0;'>© %d OCSAPP Marketplace. All rights reserved.</p>
                    <p style='margin: 10px 0 0;'>If you have questions, please contact us at info@ocsapp.ca</p>
                </div>
            </div>
        ",
            htmlspecialchars($po['po_number']),
            htmlspecialchars($supplier['name'] ?? $supplier['company_name']),
            htmlspecialchars($po['po_number']),
            date('F d, Y', strtotime($po['order_date'])),
            $po['expected_delivery_date'] ? date('F d, Y', strtotime($po['expected_delivery_date'])) : 'TBD',
            ucfirst($po['status']),
            $itemsList,
            number_format($po['subtotal'], 2),
            number_format($po['shipping_cost'], 2),
            number_format($po['tax_amount'], 2),
            number_format($po['total_amount'], 2),
            !empty($po['notes']) ? '<div style="margin: 20px 0; padding: 16px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;"><p style="margin: 0 0 8px; color: #92400e; font-weight: 600; font-size: 14px;">📝 Notes</p><p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">' . nl2br(htmlspecialchars($po['notes'])) . '</p></div>' : '',
            date('Y')
        );

        $subject = "Nouveau bon de commande #{$po['po_number']} / New Purchase Order #{$po['po_number']} — Action Required";
        self::setNextMeta('purchase_order_created', 'supplier', (int)($po['supplier_id'] ?? 0));
        return self::send($supplierEmail, $subject, $body);
    }

    /**
     * Send purchase order completed notification
     *
     * @param array $po Purchase order details
     * @param array $supplier Supplier details
     * @return bool Success status
     */
    public static function sendPurchaseOrderCompleted(array $po, array $supplier): bool
    {
        // Send to supplier
        $supplierEmail = $supplier['email'] ?? '';
        if (!empty($supplierEmail)) {
            $body = sprintf("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #10b981 0%%, #059669 100%%); color: white; padding: 30px; text-align: center;'>
                        <h1 style='margin: 0; font-size: 28px;'>✓ Purchase Order Completed</h1>
                        <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>PO #%s</p>
                    </div>

                    <div style='background: white; padding: 30px; border: 1px solid #e5e7eb;'>
                        <p style='font-size: 16px; color: #374151; margin-bottom: 20px;'>Hello %s,</p>

                        <p style='font-size: 14px; color: #6b7280; line-height: 1.6;'>
                            Purchase order <strong>%s</strong> has been successfully completed. All items have been received and the order is now closed.
                        </p>

                        <div style='background: #dcfce7; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;'>
                            <table style='width: 100%%;'>
                                <tr>
                                    <td style='padding: 8px 0; color: #166534; font-size: 13px;'>PO Number:</td>
                                    <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #166534;'>%s</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #166534; font-size: 13px;'>Order Date:</td>
                                    <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #166534;'>%s</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #166534; font-size: 13px;'>Completed Date:</td>
                                    <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #166534;'>%s</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #166534; font-size: 13px;'>Total Amount:</td>
                                    <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #166534; font-size: 18px;'>$%s</td>
                                </tr>
                            </table>
                        </div>

                        <p style='font-size: 14px; color: #6b7280; line-height: 1.6; margin-top: 20px;'>
                            Thank you for your prompt service and quality products. We look forward to continuing our partnership.
                        </p>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='https://ocsapp.ca/supplier/orders' style='background: linear-gradient(135deg, #10b981 0%%, #059669 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>View Order History</a>
                        </div>
                    </div>

                    <div style='background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;'>
                        <p style='margin: 0;'>© %d OCSAPP Marketplace. All rights reserved.</p>
                        <p style='margin: 10px 0 0;'>If you have questions, please contact us at info@ocsapp.ca</p>
                    </div>
                </div>
            ",
                htmlspecialchars($po['po_number']),
                htmlspecialchars($supplier['name'] ?? $supplier['company_name']),
                htmlspecialchars($po['po_number']),
                htmlspecialchars($po['po_number']),
                date('F d, Y', strtotime($po['order_date'])),
                date('F d, Y'),
                number_format($po['total_amount'], 2),
                date('Y')
            );

            $subject = "Bon de commande #{$po['po_number']} complété / Purchase Order #{$po['po_number']} Completed";
            self::setNextMeta('purchase_order_completed', 'supplier', (int)($po['supplier_id'] ?? 0));
            self::send($supplierEmail, $subject, $body);
        }

        // Send to admin
        $config = self::loadConfig();
        $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';

        $adminBody = sprintf("
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #00b207 0%%, #009906 100%%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>Purchase Order Completed</h1>
                    <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>PO #%s</p>
                </div>

                <div style='background: white; padding: 30px; border: 1px solid #e5e7eb;'>
                    <p style='font-size: 16px; color: #374151; margin-bottom: 20px;'>Admin Notification,</p>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6;'>
                        Purchase order <strong>%s</strong> from supplier <strong>%s</strong> has been marked as completed. All items have been received and stock has been updated.
                    </p>

                    <div style='background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <table style='width: 100%%;'>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>PO Number:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Supplier:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Order Date:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Completed Date:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Total Amount:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #00b207; font-size: 18px;'>$%s</td>
                            </tr>
                        </table>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='https://ocsapp.ca/admin/purchase-orders/view?id=%d' style='background: linear-gradient(135deg, #00b207 0%%, #009906 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>View Purchase Order</a>
                    </div>
                </div>

                <div style='background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;'>
                    <p style='margin: 0;'>© %d OCSAPP Marketplace - Admin Notification</p>
                </div>
            </div>
        ",
            htmlspecialchars($po['po_number']),
            htmlspecialchars($po['po_number']),
            htmlspecialchars($supplier['name'] ?? $supplier['company_name']),
            htmlspecialchars($po['po_number']),
            htmlspecialchars($supplier['name'] ?? $supplier['company_name']),
            date('F d, Y', strtotime($po['order_date'])),
            date('F d, Y'),
            number_format($po['total_amount'], 2),
            $po['id'],
            date('Y')
        );

        $adminSubject = "PO #{$po['po_number']} Completed - Stock Updated";
        return self::send($adminEmail, $adminSubject, $adminBody);
    }

    /**
     * Send purchase order status update notification to both supplier and admin
     *
     * @param array $po Purchase order details
     * @param array $supplier Supplier details
     * @param string $oldStatus Previous status
     * @param string $newStatus New status
     * @param string $note Optional note/reason
     * @return bool Success status
     */
    public static function sendPurchaseOrderStatusUpdate(array $po, array $supplier, string $oldStatus, string $newStatus, string $note = ''): bool
    {
        $statusLabels = [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'receiving' => 'Accepted / Receiving',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $statusColors = [
            'draft' => ['bg' => '#f3f4f6', 'text' => '#374151'],
            'sent' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
            'receiving' => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'completed' => ['bg' => '#dcfce7', 'text' => '#166534'],
            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
        ];

        $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        $oldLabel = $statusLabels[$oldStatus] ?? ucfirst($oldStatus);
        $color = $statusColors[$newStatus] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];

        $headerColor = $newStatus === 'cancelled' ? '#dc2626' : '#00b207';
        $headerColorEnd = $newStatus === 'cancelled' ? '#b91c1c' : '#009906';

        $statusMessages = [
            'sent' => 'A new purchase order has been sent to you for review. Please log in to accept or decline.',
            'receiving' => 'The supplier has accepted this purchase order. Items are now being prepared for delivery.',
            'completed' => 'This purchase order has been fully received. All items are accounted for.',
            'cancelled' => 'This purchase order has been cancelled.',
        ];
        $message = $statusMessages[$newStatus] ?? "The status of this purchase order has been updated from {$oldLabel} to {$newLabel}.";

        $noteHtml = '';
        if (!empty($note)) {
            $noteHtml = sprintf(
                '<div style="margin: 20px 0; padding: 16px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;"><p style="margin: 0 0 8px; color: #92400e; font-weight: 600; font-size: 14px;">Note</p><p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">%s</p></div>',
                nl2br(htmlspecialchars($note))
            );
        }

        $template = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="background: linear-gradient(135deg, %s 0%%, %s 100%%); color: white; padding: 30px; text-align: center;">
                    <h1 style="margin: 0; font-size: 28px;">Purchase Order Update</h1>
                    <p style="margin: 10px 0 0; font-size: 16px; opacity: 0.9;">PO #%s</p>
                </div>

                <div style="background: white; padding: 30px; border: 1px solid #e5e7eb;">
                    <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">Hello %s,</p>

                    <p style="font-size: 14px; color: #6b7280; line-height: 1.6;">%s</p>

                    <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <table style="width: 100%%;">
                            <tr>
                                <td style="padding: 8px 0; color: #6b7280; font-size: 13px;">PO Number:</td>
                                <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #111827;">%s</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #6b7280; font-size: 13px;">Supplier:</td>
                                <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #111827;">%s</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #6b7280; font-size: 13px;">Previous Status:</td>
                                <td style="padding: 8px 0; text-align: right;">%s</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #6b7280; font-size: 13px;">New Status:</td>
                                <td style="padding: 8px 0; text-align: right;"><span style="background: %s; color: %s; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">%s</span></td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #6b7280; font-size: 13px;">Total Amount:</td>
                                <td style="padding: 8px 0; text-align: right; font-weight: 700; color: #00b207; font-size: 16px;">$%s</td>
                            </tr>
                        </table>
                    </div>

                    %s

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="%s" style="background: linear-gradient(135deg, #00b207 0%%, #009906 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">View Purchase Order</a>
                    </div>
                </div>

                <div style="background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;">
                    <p style="margin: 0;">&copy; %d OCSAPP Marketplace. All rights reserved.</p>
                    <p style="margin: 10px 0 0;">If you have questions, please contact us at info@ocsapp.ca</p>
                </div>
            </div>
        ';

        $supplierName = htmlspecialchars($supplier['name'] ?? $supplier['company_name'] ?? 'Supplier');
        $poNumber = htmlspecialchars($po['po_number']);

        // Send to supplier
        $supplierEmail = $supplier['email'] ?? '';
        if (!empty($supplierEmail)) {
            $supplierBody = sprintf($template,
                $headerColor, $headerColorEnd,
                $poNumber,
                $supplierName,
                $message,
                $poNumber,
                $supplierName,
                $oldLabel,
                $color['bg'], $color['text'], $newLabel,
                number_format($po['total_amount'], 2),
                $noteHtml,
                'https://ocsapp.ca/supplier/orders',
                date('Y')
            );

            $subject = "Bon de commande #{$poNumber} — {$newLabel} / Purchase Order #{$poNumber} — {$newLabel}";
            self::setNextMeta('purchase_order_status_update', 'supplier', (int)($po['supplier_id'] ?? 0));
            self::send($supplierEmail, $subject, $supplierBody);
        }

        // Send to admin
        $config = self::loadConfig();
        $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';

        $adminBody = sprintf($template,
            $headerColor, $headerColorEnd,
            $poNumber,
            'Admin',
            $message . " (Supplier: <strong>{$supplierName}</strong>)",
            $poNumber,
            $supplierName,
            $oldLabel,
            $color['bg'], $color['text'], $newLabel,
            number_format($po['total_amount'], 2),
            $noteHtml,
            'https://ocsapp.ca/admin/purchase-orders/view?id=' . ($po['id'] ?? 0),
            date('Y')
        );

        $adminSubject = "PO #{$poNumber} Status: {$newLabel} - {$supplierName}";
        self::setNextMeta('purchase_order_status_update', 'purchase_order', (int)($po['id'] ?? 0));
        self::send($adminEmail, $adminSubject, $adminBody);

        return true;
    }

    /**
     * Send seller verification submitted notification to admin
     *
     * @param array $seller Seller details
     * @return bool Success status
     */
    public static function sendSellerVerificationSubmitted(array $seller): bool
    {
        $config = self::loadConfig();
        $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';

        $body = sprintf("
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%%, #764ba2 100%%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>Seller Verification Submitted</h1>
                    <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>Action Required</p>
                </div>

                <div style='background: white; padding: 30px; border: 1px solid #e5e7eb;'>
                    <p style='font-size: 16px; color: #374151; margin-bottom: 20px;'>Admin Notification,</p>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6;'>
                        A seller has submitted their verification information for review.
                    </p>

                    <div style='background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                        <h3 style='margin: 0 0 16px; font-size: 16px; color: #111827;'>Seller Information</h3>
                        <table style='width: 100%%;'>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Name:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s %s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Email:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Business Name:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #6b7280; font-size: 13px;'>Submitted:</td>
                                <td style='padding: 8px 0; text-align: right; font-weight: 600; color: #111827;'>%s</td>
                            </tr>
                        </table>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='https://ocsapp.ca/admin/sellers/verification-review?id=%d' style='background: linear-gradient(135deg, #667eea 0%%, #764ba2 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>Review Verification</a>
                    </div>
                </div>

                <div style='background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;'>
                    <p style='margin: 0;'>© %d OCSAPP Marketplace - Admin Notification</p>
                </div>
            </div>
        ",
            htmlspecialchars($seller['first_name'] ?? ''),
            htmlspecialchars($seller['last_name'] ?? ''),
            htmlspecialchars($seller['email']),
            htmlspecialchars($seller['business_name'] ?? 'Not provided'),
            date('F d, Y g:i A'),
            $seller['id'],
            date('Y')
        );

        $subject = "Vérification vendeur soumise / Seller Verification Submitted — Action Required";
        self::setNextMeta('seller_verification_submitted', 'user', (int)($seller['id'] ?? 0));
        return self::send($adminEmail, $subject, $body);
    }

    /**
     * Send seller verification approved notification
     *
     * @param array $seller Seller details
     * @return bool Success status
     */
    public static function sendSellerVerificationApproved(array $seller): bool
    {
        $sellerEmail = $seller['email'] ?? '';
        if (empty($sellerEmail)) {
            logger("Seller email not found for verification approval", 'error');
            return false;
        }

        $body = sprintf("
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #10b981 0%%, #059669 100%%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>🎉 Verification Approved!</h1>
                    <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>Welcome to OCSAPP Marketplace</p>
                </div>

                <div style='background: white; padding: 30px; border: 1px solid #e5e7eb;'>
                    <p style='font-size: 16px; color: #374151; margin-bottom: 20px;'>Hello %s,</p>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6;'>
                        Congratulations! Your seller verification has been approved. You now have full access to the marketplace and can start selling.
                    </p>

                    <div style='background: #dcfce7; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;'>
                        <h3 style='margin: 0 0 12px; font-size: 16px; color: #166534;'>✓ What's Next?</h3>
                        <ul style='margin: 0; padding-left: 20px; color: #166534; font-size: 14px; line-height: 1.8;'>
                            <li>Your shop is now visible on the marketplace</li>
                            <li>You can add products and manage your inventory</li>
                            <li>Start accepting orders from customers</li>
                            <li>Manage deliveries and track sales</li>
                        </ul>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='https://ocsapp.ca/seller/dashboard' style='background: linear-gradient(135deg, #10b981 0%%, #059669 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block; margin-right: 10px;'>Go to Dashboard</a>
                        <a href='https://ocsapp.ca/seller/products' style='background: white; color: #10b981; border: 2px solid #10b981; padding: 12px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>Add Products</a>
                    </div>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6; margin-top: 20px;'>
                        If you have any questions or need assistance getting started, please don't hesitate to contact our support team.
                    </p>
                </div>

                <div style='background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;'>
                    <p style='margin: 0;'>© %d OCSAPP Marketplace. All rights reserved.</p>
                    <p style='margin: 10px 0 0;'>Questions? Contact us at info@ocsapp.ca</p>
                </div>
            </div>
        ",
            htmlspecialchars($seller['first_name'] ?? 'Seller'),
            date('Y')
        );

        $subject = "Compte vendeur vérifié! / Seller Account Verified — OCSAPP";
        self::setNextMeta('seller_verification_approved', 'user', (int)($seller['id'] ?? 0));
        return self::send($sellerEmail, $subject, $body);
    }

    /**
     * Send seller verification rejected notification
     *
     * @param array $seller Seller details
     * @param string $reason Rejection reason
     * @return bool Success status
     */
    public static function sendSellerVerificationRejected(array $seller, string $reason): bool
    {
        $sellerEmail = $seller['email'] ?? '';
        if (empty($sellerEmail)) {
            logger("Seller email not found for verification rejection", 'error');
            return false;
        }

        $body = sprintf("
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #ef4444 0%%, #dc2626 100%%); color: white; padding: 30px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 28px;'>Verification Requires Attention</h1>
                    <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>Additional Information Needed</p>
                </div>

                <div style='background: white; padding: 30px; border: 1px solid #e5e7eb;'>
                    <p style='font-size: 16px; color: #374151; margin-bottom: 20px;'>Hello %s,</p>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6;'>
                        Thank you for submitting your seller verification. Unfortunately, we need some additional information or clarification before we can approve your account.
                    </p>

                    <div style='background: #fee2e2; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444;'>
                        <h3 style='margin: 0 0 12px; font-size: 16px; color: #991b1b;'>Reason for Review:</h3>
                        <p style='margin: 0; color: #991b1b; font-size: 14px; line-height: 1.6;'>%s</p>
                    </div>

                    <div style='background: #fef3c7; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b;'>
                        <h3 style='margin: 0 0 12px; font-size: 16px; color: #92400e;'>What to Do Next:</h3>
                        <ol style='margin: 0; padding-left: 20px; color: #92400e; font-size: 14px; line-height: 1.8;'>
                            <li>Review the reason provided above</li>
                            <li>Update your business information or documents</li>
                            <li>Resubmit your verification for review</li>
                        </ol>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='https://ocsapp.ca/seller/verification' style='background: linear-gradient(135deg, #ef4444 0%%, #dc2626 100%%); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>Update Verification</a>
                    </div>

                    <p style='font-size: 14px; color: #6b7280; line-height: 1.6; margin-top: 20px;'>
                        If you have questions or need clarification, please contact our support team at info@ocsapp.ca. We're here to help!
                    </p>
                </div>

                <div style='background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none;'>
                    <p style='margin: 0;'>© %d OCSAPP Marketplace. All rights reserved.</p>
                    <p style='margin: 10px 0 0;'>Questions? Contact us at info@ocsapp.ca</p>
                </div>
            </div>
        ",
            htmlspecialchars($seller['first_name'] ?? 'Seller'),
            nl2br(htmlspecialchars($reason)),
            date('Y')
        );

        $subject = "Informations requises — Vérification vendeur / Seller Verification — Additional Info Required";
        self::setNextMeta('seller_verification_rejected', 'user', (int)($seller['id'] ?? 0));
        return self::send($sellerEmail, $subject, $body);
    }

    /**
     * Send welcome email to admin-created user with temp credentials
     *
     * @param array $user User data (email, first_name, last_name, role, temp_password)
     * @return bool Success status
     */
    public static function sendAdminCreatedUserWelcome(array $user): bool
    {
        $userEmail = $user['email'] ?? '';
        if (empty($userEmail)) {
            logger("User email not provided for admin-created welcome", 'error');
            return false;
        }

        // Load template
        $templatePath = __DIR__ . '/../Views/emails/admin-user-welcome.php';
        if (!file_exists($templatePath)) {
            logger("Admin user welcome template not found", 'error');
            return false;
        }

        $body = file_get_contents($templatePath);

        // Build replacements
        $replacements = [
            '{{user_first_name}}' => htmlspecialchars($user['first_name'] ?? 'User'),
            '{{user_last_name}}' => htmlspecialchars($user['last_name'] ?? ''),
            '{{user_email}}' => htmlspecialchars($user['email']),
            '{{temp_password}}' => htmlspecialchars($user['temp_password'] ?? ''),
            '{{user_role}}' => ucfirst(htmlspecialchars($user['role'] ?? 'User')),
            '{{current_year}}' => date('Y'),
        ];

        $body = str_replace(array_keys($replacements), array_values($replacements), $body);

        $subject = "Bienvenue sur OCSAPP / Welcome to OCSAPP — Account Created";

        logger("Sending admin-created user welcome email to {$userEmail}", 'info');
        self::setNextMeta('admin_user_welcome', 'user', null);
        return self::send($userEmail, $subject, $body);
    }

    /**
     * Send invoice generated notification to admin (with PDF attachment)
     */
    public static function sendInvoiceGenerated(array $invoice, array $supplier, array $items = [], ?string $pdfPath = null): bool
    {
        $invoiceNumber = $invoice['invoice_number'] ?? 'N/A';
        $supplierName = $supplier['company_name'] ?? $supplier['name'] ?? 'Supplier';
        $poNumber = $invoice['po_number'] ?? 'N/A';
        $total = number_format((float)($invoice['total_amount'] ?? 0), 2);
        $dueDate = isset($invoice['due_date']) ? date('M j, Y', strtotime($invoice['due_date'])) : 'N/A';
        $issueDate = isset($invoice['issue_date']) ? date('M j, Y', strtotime($invoice['issue_date'])) : date('M j, Y');

        // Build items table rows
        $itemsHtml = '';
        if (!empty($items)) {
            foreach ($items as $i => $item) {
                $unitCost = number_format((float)($item['unit_cost'] ?? 0), 2);
                $lineCost = number_format((float)($item['total_cost'] ?? ($item['quantity_ordered'] ?? 0) * ($item['unit_cost'] ?? 0)), 2);
                $itemsHtml .= '<tr>
                    <td style="padding: 10px 14px; border-bottom: 1px solid #f3f4f6; font-size: 14px; color: #374151;">'
                        . htmlspecialchars($item['product_name'] ?? 'Product')
                        . (($item['sku'] ?? '') ? '<br><span style="font-size:12px;color:#9ca3af;">SKU: ' . htmlspecialchars($item['sku']) . '</span>' : '') .
                    '</td>
                    <td style="padding: 10px 14px; border-bottom: 1px solid #f3f4f6; text-align: center; font-size: 14px; color: #374151;">' . ($item['quantity_ordered'] ?? 0) . '</td>
                    <td style="padding: 10px 14px; border-bottom: 1px solid #f3f4f6; text-align: right; font-size: 14px; color: #374151;">$' . $unitCost . '</td>
                    <td style="padding: 10px 14px; border-bottom: 1px solid #f3f4f6; text-align: right; font-size: 14px; font-weight: 600; color: #1f2937;">$' . $lineCost . '</td>
                </tr>';
            }
        }

        $itemsSection = '';
        if ($itemsHtml) {
            $itemsSection = '
            <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr style="background: #f9fafb;">
                        <th style="padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Item</th>
                        <th style="padding: 10px 14px; text-align: center; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Qty</th>
                        <th style="padding: 10px 14px; text-align: right; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Unit Price</th>
                        <th style="padding: 10px 14px; text-align: right; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb;">Total</th>
                    </tr>
                </thead>
                <tbody>' . $itemsHtml . '</tbody>
            </table>';
        }

        $subtotal = number_format((float)($invoice['subtotal'] ?? 0), 2);
        $gst = number_format((float)($invoice['tax_gst'] ?? 0), 2);
        $qst = number_format((float)($invoice['tax_qst'] ?? 0), 2);
        $shipping = number_format((float)($invoice['shipping'] ?? 0), 2);

        $subject = "Nouvelle facture {$invoiceNumber} — {$supplierName} / New Invoice {$invoiceNumber} from {$supplierName}";

        $body = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;background-color:#f5f5f5;">
        <table role="presentation" style="width:100%;border-collapse:collapse;background-color:#f5f5f5;">
        <tr><td align="center" style="padding:40px 20px;">
        <table role="presentation" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

        <!-- Header -->
        <tr><td style="background:linear-gradient(135deg,#00b207 0%,#009906 100%);padding:40px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;">New Invoice Received</h1>
            <p style="margin:10px 0 0;color:rgba(255,255,255,0.9);font-size:16px;">From ' . htmlspecialchars($supplierName) . '</p>
        </td></tr>

        <!-- Body -->
        <tr><td style="padding:40px 30px;">
            <table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-left:4px solid #00b207;border-radius:4px;margin-bottom:24px;">
            <tr><td style="padding:20px;">
                <div style="font-size:28px;font-weight:700;color:#00b207;margin-bottom:4px;">$' . $total . '</div>
                <div style="font-size:14px;color:#6b7280;">Total Amount Due</div>
            </td>
            <td style="padding:20px;text-align:right;">
                <div style="font-size:20px;font-weight:700;color:#1f2937;">' . htmlspecialchars($invoiceNumber) . '</div>
                <div style="font-size:13px;color:#6b7280;">PO: ' . htmlspecialchars($poNumber) . '</div>
            </td></tr></table>

            <table role="presentation" style="width:100%;margin-bottom:20px;">
                <tr>
                    <td style="padding:8px 0;font-size:14px;color:#6b7280;">Supplier:</td>
                    <td style="padding:8px 0;font-size:14px;color:#1f2937;font-weight:600;text-align:right;">' . htmlspecialchars($supplierName) . '</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;font-size:14px;color:#6b7280;">Issue Date:</td>
                    <td style="padding:8px 0;font-size:14px;color:#1f2937;font-weight:500;text-align:right;">' . $issueDate . '</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;font-size:14px;color:#6b7280;">Due Date:</td>
                    <td style="padding:8px 0;font-size:14px;color:#991b1b;font-weight:600;text-align:right;">' . $dueDate . '</td>
                </tr>
            </table>

            ' . $itemsSection . '

            <table role="presentation" style="width:100%;margin:16px 0 24px;border-top:1px solid #e5e7eb;">
                <tr><td style="padding:6px 14px;font-size:14px;color:#6b7280;">Subtotal</td><td style="padding:6px 14px;text-align:right;font-size:14px;font-weight:500;">$' . $subtotal . '</td></tr>
                <tr><td style="padding:6px 14px;font-size:14px;color:#6b7280;">Shipping</td><td style="padding:6px 14px;text-align:right;font-size:14px;font-weight:500;">$' . $shipping . '</td></tr>
                <tr><td style="padding:6px 14px;font-size:14px;color:#6b7280;">GST (5%)</td><td style="padding:6px 14px;text-align:right;font-size:14px;font-weight:500;">$' . $gst . '</td></tr>
                <tr><td style="padding:6px 14px;font-size:14px;color:#6b7280;">QST (9.975%)</td><td style="padding:6px 14px;text-align:right;font-size:14px;font-weight:500;">$' . $qst . '</td></tr>
                <tr style="border-top:2px solid #1f2937;"><td style="padding:10px 14px;font-size:16px;font-weight:700;">Total</td><td style="padding:10px 14px;text-align:right;font-size:16px;font-weight:700;color:#00b207;">$' . $total . '</td></tr>
            </table>

            <!-- CTA -->
            <table role="presentation" style="width:100%;margin-bottom:24px;">
            <tr><td align="center">
                <a href="https://ocsapp.ca/admin/payables/view?id=' . ($invoice['id'] ?? '') . '" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009906 100%);color:#ffffff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                    View Invoice &amp; Pay
                </a>
            </td></tr></table>

            <p style="margin:0;color:#555;font-size:14px;">Best regards,<br><strong>OCSAPP System</strong></p>
        </td></tr>

        <!-- Footer -->
        <tr><td style="background-color:#1a1a1a;padding:25px 30px;text-align:center;border-radius:0 0 12px 12px;">
            <p style="margin:0 0 8px;color:#9ca3af;font-size:12px;">&copy; ' . date('Y') . ' OCSAPP. All rights reserved.</p>
        </td></tr>

        </table>
        </td></tr></table>
        </body></html>';

        $config = self::loadConfig();
        $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';

        $options = [];
        if ($pdfPath && file_exists($pdfPath)) {
            $options['attachments'] = [['path' => $pdfPath, 'name' => $invoiceNumber . '.pdf']];
        }
        // Send directly to admin — no_admin_bcc to avoid double delivery
        $options['no_admin_bcc'] = true;

        self::setNextMeta('invoice_generated', 'invoice', $invoice['id'] ?? null);
        return self::send($adminEmail, $subject, $body, $options);
    }

    /**
     * Send supplier approval welcome email with credentials
     */
    public static function sendSupplierApproved(array $data): bool
    {
        $email = $data['email'] ?? '';
        if (empty($email)) {
            logger("Supplier email not provided for approval", 'error');
            return false;
        }

        $templatePath = __DIR__ . '/../Views/emails/supplier-approved.php';
        if (!file_exists($templatePath)) {
            logger("Supplier approved template not found", 'error');
            return false;
        }

        $body = file_get_contents($templatePath);

        // Build conditional sections based on whether temp password exists
        $hasTempPassword = !empty($data['temp_password']);
        $credentialsSection = '';
        $passwordWarning = '';

        if ($hasTempPassword) {
            $credentialsSection = '
                <tr>
                    <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600;">Temporary Password:</td>
                    <td style="padding: 8px 0;">
                        <code style="background: #1f2937; color: #fbbf24; padding: 6px 12px; border-radius: 6px; font-size: 14px; font-family: monospace; letter-spacing: 1px;">' . htmlspecialchars($data['temp_password']) . '</code>
                    </td>
                </tr>';
            $passwordWarning = '
                <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fef3c7; border-radius: 4px; margin-bottom: 24px;">
                    <tr>
                        <td style="padding: 16px 20px;">
                            <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                <strong>Important:</strong> Please change your password after your first login for security.
                            </p>
                        </td>
                    </tr>
                </table>';
        } else {
            $credentialsSection = '
                <tr>
                    <td style="padding: 8px 0; color: #374151; font-size: 14px; font-weight: 600;">Password:</td>
                    <td style="padding: 8px 0; color: #4b5563; font-size: 14px;">Use the password you created during your application</td>
                </tr>';
        }

        $replacements = [
            '{{first_name}}' => htmlspecialchars($data['first_name'] ?? 'Supplier'),
            '{{last_name}}' => htmlspecialchars($data['last_name'] ?? ''),
            '{{company_name}}' => htmlspecialchars($data['company_name'] ?? ''),
            '{{email}}' => htmlspecialchars($data['email']),
            '{{supplier_code}}' => htmlspecialchars($data['supplier_code'] ?? ''),
            '{{credentials_section}}' => $credentialsSection,
            '{{password_warning_section}}' => $passwordWarning,
            '{{current_year}}' => date('Y'),
        ];

        $body = str_replace(array_keys($replacements), array_values($replacements), $body);
        $subject = "Demande fournisseur approuvée / Supplier Application Approved — OCSAPP";

        logger("Sending supplier approval email to {$email}", 'info');
        self::setNextMeta('supplier_approved', 'supplier', null);
        return self::send($email, $subject, $body);
    }

    /**
     * Send supplier rejection email
     */
    public static function sendSupplierRejected(array $data): bool
    {
        $email = $data['email'] ?? '';
        if (empty($email)) {
            logger("Supplier email not provided for rejection", 'error');
            return false;
        }

        $templatePath = __DIR__ . '/../Views/emails/supplier-rejected.php';
        if (!file_exists($templatePath)) {
            logger("Supplier rejected template not found", 'error');
            return false;
        }

        $body = file_get_contents($templatePath);
        $replacements = [
            '{{first_name}}' => htmlspecialchars($data['first_name'] ?? 'Applicant'),
            '{{last_name}}' => htmlspecialchars($data['last_name'] ?? ''),
            '{{company_name}}' => htmlspecialchars($data['company_name'] ?? ''),
            '{{reason}}' => htmlspecialchars($data['reason'] ?? ''),
            '{{current_year}}' => date('Y'),
        ];

        $body = str_replace(array_keys($replacements), array_values($replacements), $body);

        // Handle optional notes block
        if (!empty($data['notes'])) {
            $body = str_replace('{{#notes}}', '', $body);
            $body = str_replace('{{/notes}}', '', $body);
            $body = str_replace('{{notes}}', htmlspecialchars($data['notes']), $body);
        } else {
            $body = preg_replace('/\{\{#notes\}\}.*?\{\{\/notes\}\}/s', '', $body);
        }

        $subject = "Mise à jour de votre demande / Application Update — OCSAPP";

        logger("Sending supplier rejection email to {$email}", 'info');
        self::setNextMeta('supplier_rejected', 'supplier', null);
        return self::send($email, $subject, $body);
    }

    /**
     * Send supplier info request email
     */
    public static function sendSupplierInfoRequest(array $data): bool
    {
        $email = $data['email'] ?? '';
        if (empty($email)) {
            logger("Supplier email not provided for info request", 'error');
            return false;
        }

        $templatePath = __DIR__ . '/../Views/emails/supplier-info-request.php';
        if (!file_exists($templatePath)) {
            logger("Supplier info request template not found", 'error');
            return false;
        }

        $body = file_get_contents($templatePath);
        $replacements = [
            '{{first_name}}' => htmlspecialchars($data['first_name'] ?? 'Applicant'),
            '{{last_name}}' => htmlspecialchars($data['last_name'] ?? ''),
            '{{company_name}}' => htmlspecialchars($data['company_name'] ?? ''),
            '{{company_name_encoded}}' => rawurlencode($data['company_name'] ?? ''),
            '{{message}}' => nl2br(htmlspecialchars($data['message'] ?? '')),
            '{{current_year}}' => date('Y'),
        ];

        $body = str_replace(array_keys($replacements), array_values($replacements), $body);
        $subject = "Informations requises / Additional Information Required — OCSAPP";

        logger("Sending supplier info request email to {$email}", 'info');
        self::setNextMeta('supplier_info_request', 'supplier', null);
        return self::send($email, $subject, $body, ['replyTo' => 'info@ocsapp.ca']);
    }

    /**
     * Send new message notification to supplier (admin → supplier)
     */
    public static function sendSupplierNewMessage(array $supplier, string $message): bool
    {
        $email = $supplier['email'] ?? '';
        if (empty($email)) {
            return false;
        }

        $templatePath = __DIR__ . '/../Views/emails/supplier-new-message.php';
        if (!file_exists($templatePath)) {
            logger("supplier-new-message template not found", 'error');
            return false;
        }

        $body = file_get_contents($templatePath);
        $replacements = [
            '{{first_name}}'   => htmlspecialchars($supplier['contact_person'] ?? $supplier['name'] ?? 'Supplier'),
            '{{company_name}}' => htmlspecialchars($supplier['company_name'] ?? ''),
            '{{message}}'      => nl2br(htmlspecialchars($message)),
            '{{reply_url}}'    => 'https://ocsapp.ca/supplier/messages',
            '{{current_year}}' => date('Y'),
        ];
        $body = str_replace(array_keys($replacements), array_values($replacements), $body);

        $subject = 'Nouveau message OCSAPP / New Message from OCSAPP';
        self::setNextMeta('supplier_message', 'supplier', (int)($supplier['id'] ?? 0));
        return self::send($email, $subject, $body, ['replyTo' => 'info@ocsapp.ca']);
    }

    /**
     * Send new message notification to admin (supplier → admin)
     */
    public static function sendAdminNewMessage(array $supplier, string $message): bool
    {
        $adminEmail  = 'info@ocsapp.ca';
        $companyName = $supplier['company_name'] ?? $supplier['contact_person'] ?? $supplier['name'] ?? 'Supplier';
        $supplierId  = (int)($supplier['id'] ?? 0);
        $actionUrl   = 'https://ocsapp.ca/admin/suppliers/edit?id=' . $supplierId;
        $preview     = mb_strlen($message) > 200 ? mb_substr($message, 0, 200) . '…' : $message;

        $templatePath = __DIR__ . '/../Views/emails/planner-notification.php';
        if (file_exists($templatePath)) {
            $body = file_get_contents($templatePath);
            $replacements = [
                '{{user_first_name}}'        => 'Admin',
                '{{user_last_name}}'         => '',
                '{{user_email}}'             => $adminEmail,
                '{{notification_title}}'     => "New Message from Supplier: {$companyName}",
                '{{notification_message}}'   => nl2br(htmlspecialchars($preview)),
                '{{notification_type}}'      => 'supplier_message',
                '{{action_url}}'             => $actionUrl,
                '{{current_year}}'           => date('Y'),
            ];
            $body = str_replace(array_keys($replacements), array_values($replacements), $body);
        } else {
            // Fallback plain text
            $body = "<p>New message from supplier <strong>{$companyName}</strong>:</p>"
                  . "<blockquote>" . nl2br(htmlspecialchars($preview)) . "</blockquote>"
                  . "<p><a href='{$actionUrl}'>View Supplier →</a></p>";
        }

        $subject = "Nouveau message fournisseur : {$companyName} / New Supplier Message: {$companyName}";
        self::setNextMeta('supplier_message_admin', 'supplier', $supplierId);
        return self::send($adminEmail, $subject, $body);
    }

    /**
     * Send distribution email verification code.
     * $data keys: first_name, email, verification_code
     */
    public static function sendDistributionVerificationCode(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/distribution-verify-email.php';

        if (!file_exists($templatePath)) {
            error_log('Distribution verify email template not found');
            return false;
        }

        $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');

        $html = file_get_contents($templatePath);
        $html = str_replace('{{user_first_name}}',    htmlspecialchars($data['first_name'] ?? 'there'), $html);
        $html = str_replace('{{verification_code}}',  htmlspecialchars($data['verification_code'] ?? ''), $html);
        $html = str_replace('{{verify_url_fr}}',      $data['verify_url_fr'] ?? $appUrl . '/distribution/verify-email?lang=fr', $html);
        $html = str_replace('{{verify_url_en}}',      $data['verify_url_en'] ?? $appUrl . '/distribution/verify-email?lang=en', $html);
        $html = str_replace('{{magic_link_url_fr}}',  $data['magic_link_url_fr'] ?? '', $html);
        $html = str_replace('{{magic_link_url_en}}',  $data['magic_link_url_en'] ?? '', $html);
        $html = str_replace('{{current_year}}',       date('Y'), $html);

        return self::send(
            $data['email'],
            'Code de vérification / Verification Code - OCSAPP Distribution',
            $html
        );
    }

    /**
     * Send supplier email verification code.
     * $data keys: first_name, email, verification_code
     */
    public static function sendSupplierVerificationCode(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/supplier-verify-email.php';

        if (!file_exists($templatePath)) {
            error_log('Supplier verify email template not found');
            return false;
        }

        $appUrl    = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
        $verifyUrl = $appUrl . '/supplier/verify-email?email=' . urlencode($data['email'] ?? '');

        $html = file_get_contents($templatePath);
        $html = str_replace('{{user_first_name}}',   htmlspecialchars($data['first_name'] ?? 'there'), $html);
        $html = str_replace('{{verification_code}}', htmlspecialchars($data['verification_code'] ?? ''), $html);
        $html = str_replace('{{verify_url}}',        $data['verify_url'] ?? $verifyUrl, $html);
        $html = str_replace('{{verify_url_en}}',     $data['verify_url_en'] ?? ($appUrl . '/supplier/verify-email'), $html);
        $html = str_replace('{{magic_link_url_fr}}', $data['magic_link_url_fr'] ?? '', $html);
        $html = str_replace('{{magic_link_url_en}}', $data['magic_link_url_en'] ?? '', $html);
        $html = str_replace('{{current_year}}',      date('Y'), $html);

        return self::send(
            $data['email'],
            'Code de vérification / Verification Code - OCSAPP Fournisseur',
            $html
        );
    }

    /**
     * Send email verification code for buyer/seller registration.
     * $data keys: first_name, email, verification_code
     */
    public static function sendUserVerificationCode(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/auth-verify-email.php';

        if (!file_exists($templatePath)) {
            error_log('Auth verify email template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{user_first_name}}',   htmlspecialchars($data['first_name'] ?? 'there'), $html);
        $html = str_replace('{{verification_code}}', htmlspecialchars($data['verification_code'] ?? ''), $html);
        $html = str_replace('{{verify_url_fr}}',     $data['verify_url_fr'] ?? $data['verify_url'] ?? 'https://ocsapp.ca/delivery/verify-email', $html);
        $html = str_replace('{{verify_url_en}}',     $data['verify_url_en'] ?? $data['verify_url'] ?? 'https://ocsapp.ca/delivery/verify-email', $html);
        $html = str_replace('{{magic_link_url_fr}}', $data['magic_link_url_fr'] ?? $data['magic_link_url'] ?? '', $html);
        $html = str_replace('{{magic_link_url_en}}', $data['magic_link_url_en'] ?? $data['magic_link_url'] ?? '', $html);
        $html = str_replace('{{current_year}}',      date('Y'), $html);

        return self::send(
            $data['email'],
            'Code de vérification / Verification Code - OCSAPP',
            $html
        );
    }

    /**
     * Send distribution application received confirmation to the applicant.
     * $data keys: first_name, email, company_name, neq_number
     */
    public static function sendDistributionApplicationReceived(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/distribution-application-received.php';

        if (!file_exists($templatePath)) {
            error_log('Distribution application email template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{user_first_name}}', htmlspecialchars($data['first_name'] ?? 'there'), $html);
        $html = str_replace('{{user_email}}',      htmlspecialchars($data['email'] ?? ''), $html);
        $html = str_replace('{{company_name}}',    htmlspecialchars($data['company_name'] ?? ''), $html);
        $html = str_replace('{{neq_number}}',      htmlspecialchars($data['neq_number'] ?? ''), $html);
        $html = str_replace('{{submitted_date}}',  date('F j, Y \a\t g:i A'), $html);
        $html = str_replace('{{current_year}}',    date('Y'), $html);

        return self::send(
            $data['email'],
            'Demande reçue / Application Received - OCSAPP Distribution',
            $html
        );
    }

    /**
     * Send driver application received confirmation
     */
    public static function sendDriverApplicationReceived(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-application-received.php';

        if (!file_exists($templatePath)) {
            error_log('Driver application email template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}', htmlspecialchars($data['first_name']), $html);
        $html = str_replace('{{application_id}}', $data['application_id'], $html);
        $html = str_replace('{{submitted_date}}', date('F j, Y'), $html);
        $html = str_replace('{{email}}', htmlspecialchars($data['email']), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        $tempPassword = $data['temp_password'] ?? null;
        if ($tempPassword) {
            $credFr = '<table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;">'
                    . '<tr><td style="padding:16px 20px;">'
                    . '<p style="margin:0 0 8px;color:#166534;font-size:14px;font-weight:600;">Vos identifiants de connexion</p>'
                    . '<p style="margin:0;color:#374151;font-size:14px;line-height:1.8;">'
                    . '<strong>Connexion :</strong> <a href="https://ocsapp.ca/login" style="color:#00b207;">ocsapp.ca/login</a><br>'
                    . '<strong>Courriel :</strong> ' . htmlspecialchars($data['email']) . '<br>'
                    . '<strong>Mot de passe temporaire :</strong> ' . htmlspecialchars($tempPassword)
                    . '</p><p style="margin:8px 0 0;color:#6b7280;font-size:12px;">Veuillez changer votre mot de passe après votre première connexion.</p>'
                    . '</td></tr></table>';
            $credEn = '<table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;">'
                    . '<tr><td style="padding:16px 20px;">'
                    . '<p style="margin:0 0 8px;color:#166534;font-size:14px;font-weight:600;">Your Login Credentials</p>'
                    . '<p style="margin:0;color:#374151;font-size:14px;line-height:1.8;">'
                    . '<strong>Login:</strong> <a href="https://ocsapp.ca/login" style="color:#00b207;">ocsapp.ca/login</a><br>'
                    . '<strong>Email:</strong> ' . htmlspecialchars($data['email']) . '<br>'
                    . '<strong>Temporary Password:</strong> ' . htmlspecialchars($tempPassword)
                    . '</p><p style="margin:8px 0 0;color:#6b7280;font-size:12px;">Please change your password after your first login.</p>'
                    . '</td></tr></table>';
        } else {
            $credFr = '<p style="margin:0 0 20px;color:#4b5563;font-size:14px;">Vous pouvez suivre votre candidature en vous connectant à votre compte existant.</p>';
            $credEn = '<p style="margin:0 0 20px;color:#4b5563;font-size:14px;">You can track your application status by logging into your existing account.</p>';
        }
        $html = str_replace('{{credentials_block_fr}}', $credFr, $html);
        $html = str_replace('{{credentials_block_en}}', $credEn, $html);

        self::setNextMeta('driver_application_received', null, null);
        return self::send(
            $data['email'],
            'Demande reçue / Application Received - OCSAPP Delivery Team',
            $html
        );
    }

    public static function sendDriverUnderReview(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-under-review.php';
        if (!file_exists($templatePath)) { error_log('driver-under-review template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',      htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{application_id}}',  $data['application_id'] ?? '', $html);
        $html = str_replace('{{current_year}}',    date('Y'), $html);

        return self::send($data['email'], 'Candidature en examen / Your Driver Application is Under Review - OCSAPP', $html);
    }

    public static function sendSupplierAccountRemoved(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/supplier-account-removed.php';
        if (!file_exists($templatePath)) { error_log('supplier-account-removed template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{supplier_name}}', htmlspecialchars($data['supplier_name'] ?? ''), $html);
        $html = str_replace('{{current_year}}',  date('Y'), $html);

        return self::send($data['email'], 'Compte fournisseur OCSAPP supprimé / Your OCSAPP Supplier Account Has Been Removed', $html);
    }

    public static function sendDriverAccountRemoved(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-account-removed.php';
        if (!file_exists($templatePath)) { error_log('driver-account-removed template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',   htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        return self::send($data['email'], 'Compte chauffeur OCSAPP supprimé / Your OCSAPP Driver Account Has Been Removed', $html);
    }

    public static function sendBuyerAccountRemoved(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/buyer-account-removed.php';
        if (!file_exists($templatePath)) { error_log('buyer-account-removed template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',   htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        return self::send($data['email'], 'Compte acheteur OCSAPP supprimé / Your OCSAPP Buyer Account Has Been Removed', $html);
    }

    public static function sendSellerAccountRemoved(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/seller-account-removed.php';
        if (!file_exists($templatePath)) { error_log('seller-account-removed template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',   htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        return self::send($data['email'], 'Compte vendeur OCSAPP supprimé / Your OCSAPP Seller Account Has Been Removed', $html);
    }

    public static function sendDistributionApproved(string $to, array $data, array $attachments = []): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/distribution-application-approved.php';
        if (!file_exists($templatePath)) { error_log('distribution-application-approved template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',    htmlspecialchars($data['first_name']    ?? ''), $html);
        $html = str_replace('{{company_name}}',  htmlspecialchars($data['company_name']  ?? ''), $html);
        $html = str_replace('{{login_url}}',     $data['login_url'] ?? '', $html);
        $html = str_replace('{{current_year}}',  date('Y'), $html);

        return self::send($to, 'Compte de distribution approuvé / Your Distribution Account Has Been Approved — OCSAPP Marketplace', $html, ['attachments' => $attachments]);
    }

    public static function sendBusinessAccountRemoved(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/business-account-removed.php';
        if (!file_exists($templatePath)) { error_log('business-account-removed template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',    htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{company_name}}',  htmlspecialchars($data['company_name'] ?? ''), $html);
        $html = str_replace('{{current_year}}',  date('Y'), $html);

        return self::send($data['email'], 'Compte de distribution OCSAPP supprimé / Your OCSAPP Distribution Account Has Been Removed', $html);
    }

    public static function sendDriverInterviewInvitation(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-interview-invitation.php';
        if (!file_exists($templatePath)) { error_log('driver-interview-invitation template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',      htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{application_id}}',  $data['application_id'] ?? '', $html);
        $html = str_replace('{{current_year}}',    date('Y'), $html);

        $slots = $data['proposed_times'] ?? [];
        $slotsFr = $slotsEn = '';
        $frMonthsInv = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
        $frDaysInv   = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
        foreach ($slots as $slot) {
            $ts        = strtotime($slot);
            $formatted = date('l, F j, Y \a\t g:i A', $ts);
            $formattedFr = $frDaysInv[(int)date('w', $ts)] . ' ' . (int)date('j', $ts) . ' ' . $frMonthsInv[(int)date('n', $ts)] . ' ' . date('Y', $ts) . ' à ' . date('G', $ts) . 'h' . date('i', $ts);
            $slotsEn .= '<p style="margin:0 0 8px;color:#1f2937;font-size:14px;">&#8226; ' . $formatted . '</p>';
            $slotsFr .= '<p style="margin:0 0 8px;color:#1f2937;font-size:14px;">&#8226; ' . $formattedFr . '</p>';
        }
        $html = str_replace('{{time_slots_fr}}', $slotsFr ?: '<p style="color:#6b7280;font-size:14px;">Aucun créneau disponible.</p>', $html);
        $html = str_replace('{{time_slots_en}}', $slotsEn ?: '<p style="color:#6b7280;font-size:14px;">No time slots available.</p>', $html);

        return self::send($data['email'], 'Invitation à un entretien / Interview Invitation - OCSAPP', $html);
    }

    public static function sendDriverInterviewConfirmed(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-interview-confirmed.php';
        if (!file_exists($templatePath)) { error_log('driver-interview-confirmed template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',         htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{interview_time}}',    htmlspecialchars($data['interview_time'] ?? ''), $html);
        $html = str_replace('{{interview_time_fr}}', htmlspecialchars($data['interview_time_fr'] ?? $data['interview_time'] ?? ''), $html);
        $html = str_replace('{{current_year}}',      date('Y'), $html);

        return self::send($data['email'], 'Entretien confirmé / Interview Confirmed - OCSAPP', $html);
    }

    public static function sendDriverApproved(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-approved.php';
        if (!file_exists($templatePath)) { error_log('driver-approved template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',   htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        $useExisting = $data['use_existing'] ?? false;
        $password    = $data['password'] ?? null;
        $email       = $data['email'] ?? '';

        if ($useExisting) {
            $credFr = '<table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;"><tr><td style="padding:16px 20px;"><p style="margin:0;color:#166534;font-size:14px;">Votre compte existant a été activé. <a href="https://ocsapp.ca/login" style="color:#00b207;font-weight:600;">Connectez-vous ici</a>.</p></td></tr></table>';
            $credEn = '<table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;"><tr><td style="padding:16px 20px;"><p style="margin:0;color:#166534;font-size:14px;">Your existing account has been activated. <a href="https://ocsapp.ca/login" style="color:#00b207;font-weight:600;">Log in here</a>.</p></td></tr></table>';
        } else {
            $credFr = '<table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;"><tr><td style="padding:16px 20px;"><p style="margin:0 0 8px;color:#166534;font-size:14px;font-weight:600;">Vos identifiants de connexion</p><p style="margin:0;color:#374151;font-size:14px;line-height:1.8;"><strong>Connexion :</strong> <a href="https://ocsapp.ca/login" style="color:#00b207;">ocsapp.ca/login</a><br><strong>Courriel :</strong> ' . htmlspecialchars($email) . '<br><strong>Mot de passe :</strong> ' . htmlspecialchars($password ?? '') . '</p><p style="margin:8px 0 0;color:#6b7280;font-size:12px;">Veuillez changer votre mot de passe après votre première connexion.</p></td></tr></table>';
            $credEn = '<table role="presentation" style="width:100%;border-collapse:collapse;background:#f0fdf4;border-radius:8px;margin-bottom:24px;border-left:4px solid #00b207;"><tr><td style="padding:16px 20px;"><p style="margin:0 0 8px;color:#166534;font-size:14px;font-weight:600;">Your Login Credentials</p><p style="margin:0;color:#374151;font-size:14px;line-height:1.8;"><strong>Login:</strong> <a href="https://ocsapp.ca/login" style="color:#00b207;">ocsapp.ca/login</a><br><strong>Email:</strong> ' . htmlspecialchars($email) . '<br><strong>Password:</strong> ' . htmlspecialchars($password ?? '') . '</p><p style="margin:8px 0 0;color:#6b7280;font-size:12px;">Please change your password after your first login.</p></td></tr></table>';
        }
        $html = str_replace('{{credentials_block_fr}}', $credFr, $html);
        $html = str_replace('{{credentials_block_en}}', $credEn, $html);

        return self::send($data['email'], 'Bienvenue dans l\'équipe de livraison OCSAPP ! / Welcome to the OCSAPP Delivery Team!', $html);
    }

    /**
     * Notify supplier that a driver has been assigned to pick up their PO.
     * $data keys: supplier_email, supplier_name, driver_name, po_number, order_total, pickup_notes
     */
    public static function sendSupplierDriverAssigned(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/supplier-driver-assigned.php';
        if (!file_exists($templatePath)) {
            error_log('supplier-driver-assigned email template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{supplier_name}}',  htmlspecialchars($data['supplier_name'] ?? ''), $html);
        $html = str_replace('{{driver_name}}',     htmlspecialchars($data['driver_name'] ?? ''), $html);
        $html = str_replace('{{po_number}}',       htmlspecialchars($data['po_number'] ?? ''), $html);
        $html = str_replace('{{order_total}}',     number_format((float)($data['order_total'] ?? 0), 2), $html);
        $html = str_replace('{{current_year}}',    date('Y'), $html);

        if (!empty($data['pickup_notes'])) {
            $notesHtml = '<tr><td style="padding: 4px 0; color: #6b7280; font-size: 14px;">Notes</td>'
                       . '<td style="padding: 4px 0; color: #111827; font-size: 14px;">'
                       . htmlspecialchars($data['pickup_notes']) . '</td></tr>';
            $html = str_replace('{{#if pickup_notes}}', '', $html);
            $html = str_replace('{{pickup_notes}}', htmlspecialchars($data['pickup_notes']), $html);
            $html = str_replace('{{/if}}', '', $html);
        } else {
            // Strip the conditional block
            $html = preg_replace('/\{\{#if pickup_notes\}\}.*?\{\{\/if\}\}/s', '', $html);
        }

        return self::send(
            $data['supplier_email'],
            "Livreur assigné pour ramassage / Driver Assigned for Pickup — BC #{$data['po_number']}",
            $html
        );
    }

    public static function sendDeliveryAssignment(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/delivery-assignment.php';

        if (!file_exists($templatePath)) {
            error_log('Delivery assignment email template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{delivery_person_name}}', htmlspecialchars($data['driver_name'] ?? ''), $html);
        $html = str_replace('{{order_number}}', htmlspecialchars($data['order_number'] ?? ''), $html);
        $html = str_replace('{{order_total}}', number_format($data['order_total'] ?? 0, 2), $html);
        $html = str_replace('{{shop_name}}', htmlspecialchars($data['shop_name'] ?? ''), $html);
        $html = str_replace('{{shop_address}}', htmlspecialchars($data['shop_address'] ?? ''), $html);
        $html = str_replace('{{shop_phone}}', htmlspecialchars($data['shop_phone'] ?? ''), $html);
        $html = str_replace('{{shop_contact_name}}', htmlspecialchars($data['shop_contact'] ?? ''), $html);
        $html = str_replace('{{customer_name}}', htmlspecialchars($data['customer_name'] ?? ''), $html);
        $html = str_replace('{{delivery_address}}', htmlspecialchars($data['delivery_address'] ?? ''), $html);
        $html = str_replace('{{customer_phone}}', htmlspecialchars($data['customer_phone'] ?? ''), $html);
        $html = str_replace('{{delivery_window}}', htmlspecialchars($data['delivery_window'] ?? 'ASAP'), $html);
        $html = str_replace('{{items_count}}', $data['items_count'] ?? '0', $html);
        $html = str_replace('{{order_items_list}}', $data['items_list'] ?? '', $html);
        $html = str_replace('{{order_id}}', $data['delivery_id'] ?? '', $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        // Handle delivery instructions block
        if (!empty($data['instructions'])) {
            $instructionsHtml = '<table role="presentation" style="width: 100%; border-collapse: collapse; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 24px;"><tr><td style="padding: 16px 20px;"><p style="margin: 0 0 4px; color: #92400e; font-size: 14px; font-weight: 600;">Special Instructions</p><p style="margin: 0; color: #92400e; font-size: 14px;">' . htmlspecialchars($data['instructions']) . '</p></td></tr></table>';
            $html = str_replace('{{delivery_instructions}}', $instructionsHtml, $html);
        } else {
            $html = str_replace('{{delivery_instructions}}', '', $html);
        }

        return self::send(
            $data['driver_email'],
            'Nouvelle livraison assignée / New Delivery Assignment — Order #' . ($data['order_number'] ?? ''),
            $html
        );
    }

    /**
     * Send "out for delivery" notification to customer
     */
    public static function sendOutForDelivery(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/buyer-out-for-delivery.php';

        if (!file_exists($templatePath)) {
            error_log('Out for delivery email template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{user_first_name}}', htmlspecialchars($data['customer_name'] ?? ''), $html);
        $html = str_replace('{{order_number}}', htmlspecialchars($data['order_number'] ?? ''), $html);
        $html = str_replace('{{order_id}}', $data['order_id'] ?? '', $html);
        $html = str_replace('{{delivery_eta}}', htmlspecialchars($data['eta'] ?? '30-45 minutes'), $html);
        $html = str_replace('{{driver_name}}', htmlspecialchars($data['driver_name'] ?? ''), $html);
        $html = str_replace('{{driver_phone}}', htmlspecialchars($data['driver_phone'] ?? ''), $html);
        $html = str_replace('{{delivery_address}}', htmlspecialchars($data['delivery_address'] ?? ''), $html);
        $html = str_replace('{{order_items_summary}}', $data['items_summary'] ?? '', $html);
        $html = str_replace('{{order_total}}', number_format($data['order_total'] ?? 0, 2), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        return self::send(
            $data['customer_email'],
            'Your Order is On the Way! - Order #' . ($data['order_number'] ?? ''),
            $html
        );
    }

    /**
     * Send payment confirmation email to supplier
     */
    public static function sendPaymentConfirmation(array $invoice, array $payment, array $supplier): bool
    {
        $supplierEmail = $supplier['email'] ?? '';
        if (empty($supplierEmail)) {
            logger("Supplier email not found for payment confirmation", 'error');
            return false;
        }

        $invoiceNumber = htmlspecialchars($invoice['invoice_number'] ?? '');
        $supplierName = htmlspecialchars($supplier['contact_person'] ?? $supplier['company_name'] ?? 'Supplier');
        $firstName = htmlspecialchars(explode(' ', $supplierName)[0]);
        $paymentAmount = number_format((float)($payment['amount'] ?? 0), 2);
        $paymentNumber = htmlspecialchars($payment['payment_number'] ?? '');
        $paymentMethod = ucfirst(str_replace('_', ' ', $payment['payment_method'] ?? 'other'));
        $paymentDate = date('M j, Y', strtotime($payment['payment_date'] ?? 'now'));
        $refNumber = htmlspecialchars($payment['reference_number'] ?? '');
        $balanceDue = number_format((float)($invoice['balance_due'] ?? 0), 2);
        $totalAmount = number_format((float)($invoice['total_amount'] ?? 0), 2);
        $amountPaid = number_format((float)($invoice['amount_paid'] ?? 0), 2);
        $isPaid = ((float)($invoice['balance_due'] ?? 0)) <= 0.01;

        $statusHtml = $isPaid
            ? "<span style='color:#059669;font-weight:700;'>FULLY PAID</span>"
            : "<span style='color:#dc2626;font-weight:700;'>Balance remaining: \${$balanceDue}</span>";

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #00b207 0%, #008505 100%); padding: 30px; text-align: center; border-radius: 12px 12px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 22px;'>Payment Received</h1>
                <p style='color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px;'>Invoice {$invoiceNumber}</p>
            </div>

            <div style='padding: 30px; background: #f9f9f9;'>
                <p style='font-size: 16px; color: #333;'>Hi {$firstName},</p>
                <p style='font-size: 15px; color: #333; line-height: 1.7;'>
                    A payment has been recorded for your invoice <strong>{$invoiceNumber}</strong>.
                </p>

                <div style='background: white; border-radius: 12px; padding: 20px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'>
                    <div style='text-align:center; margin-bottom:16px;'>
                        <div style='font-size:13px; color:#6b7280;'>Payment Amount</div>
                        <div style='font-size:32px; font-weight:700; color:#059669;'>\${$paymentAmount}</div>
                    </div>
                    <table style='width:100%; font-size:14px; border-collapse:collapse;'>
                        <tr><td style='padding:8px 0; color:#6b7280; border-top:1px solid #f3f4f6;'>Payment #</td><td style='padding:8px 0; text-align:right; font-weight:500; border-top:1px solid #f3f4f6;'>{$paymentNumber}</td></tr>
                        <tr><td style='padding:8px 0; color:#6b7280; border-top:1px solid #f3f4f6;'>Method</td><td style='padding:8px 0; text-align:right; font-weight:500; border-top:1px solid #f3f4f6;'>{$paymentMethod}</td></tr>
                        <tr><td style='padding:8px 0; color:#6b7280; border-top:1px solid #f3f4f6;'>Date</td><td style='padding:8px 0; text-align:right; font-weight:500; border-top:1px solid #f3f4f6;'>{$paymentDate}</td></tr>"
                        . ($refNumber ? "<tr><td style='padding:8px 0; color:#6b7280; border-top:1px solid #f3f4f6;'>Reference</td><td style='padding:8px 0; text-align:right; font-weight:500; border-top:1px solid #f3f4f6;'>{$refNumber}</td></tr>" : "") . "
                        <tr style='border-top:2px solid #e5e7eb;'><td style='padding:10px 0; color:#6b7280; font-weight:600;'>Invoice Total</td><td style='padding:10px 0; text-align:right; font-weight:600;'>\${$totalAmount}</td></tr>
                        <tr><td style='padding:8px 0; color:#6b7280;'>Total Paid</td><td style='padding:8px 0; text-align:right; font-weight:500; color:#059669;'>\${$amountPaid}</td></tr>
                        <tr><td style='padding:8px 0; color:#6b7280;'>Status</td><td style='padding:8px 0; text-align:right;'>{$statusHtml}</td></tr>
                    </table>
                </div>

                <div style='text-align:center; margin: 24px 0;'>
                    <a href='" . url('supplier/invoices') . "' style='display:inline-block; background:#00b207; color:white; padding:12px 32px; text-decoration:none; border-radius:8px; font-weight:bold;'>View Invoices</a>
                </div>

                <p style='font-size: 14px; color: #555; margin-top: 24px;'>
                    Best regards,<br><strong>OCSAPP Team</strong>
                </p>
            </div>

            <div style='background: #333; color: #999; padding: 16px; text-align: center; font-size: 12px; border-radius: 0 0 12px 12px;'>
                <p style='margin: 0;'>&copy; " . date('Y') . " OCSAPP. All rights reserved.</p>
            </div>
        </div>";

        return self::send($supplierEmail, "Payment Received - Invoice {$invoiceNumber}", $body);
    }

    public static function sendDriverTrainingComplete(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/driver-training-complete.php';
        if (!file_exists($templatePath)) { error_log('driver-training-complete template not found'); return false; }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{first_name}}',   htmlspecialchars($data['first_name'] ?? ''), $html);
        $html = str_replace('{{cert_number}}',  htmlspecialchars($data['cert_number'] ?? ''), $html);
        $html = str_replace('{{current_year}}', date('Y'), $html);

        return self::send(
            $data['email'],
            'Formation OCSAPP complétée ! / OCSAPP Training Complete!',
            $html
        );
    }

    public static function sendSupplierAgreementSigned(array $data): bool
    {
        $templatePath = dirname(__DIR__) . '/Views/emails/supplier-agreement-signed.php';
        if (!file_exists($templatePath)) {
            error_log('supplier-agreement-signed template not found');
            return false;
        }

        $html = file_get_contents($templatePath);
        $html = str_replace('{{company_name}}',  htmlspecialchars($data['company_name'] ?? ''), $html);
        $html = str_replace('{{contact_person}}', htmlspecialchars($data['contact_person'] ?? $data['company_name'] ?? ''), $html);
        $html = str_replace('{{signed_at}}',     htmlspecialchars($data['signed_at'] ?? date('Y-m-d H:i')), $html);
        $html = str_replace('{{version}}',       htmlspecialchars((string)($data['version'] ?? 1)), $html);
        $html = str_replace('{{current_year}}',  date('Y'), $html);

        self::setNextMeta('supplier_agreement_signed', 'supplier', $data['supplier_id'] ?? null);
        return self::send(
            $data['email'],
            'Accord signé avec succès / Distribution Agreement Signed - OCSAPP Marketplace',
            $html
        );
    }

    /**
     * Test email configuration
     *
     * @param string $testEmail Email to send test to
     * @return bool Success status
     */
    public static function testConnection(string $testEmail): bool
    {
        $subject = 'OCSAPP — Test de courriel / Email Test';
        $body = '
            <h2>Email Configuration Test</h2>
            <p>If you receive this email, your email configuration is working correctly!</p>
            <p>Sent at: ' . date('Y-m-d H:i:s') . '</p>
        ';

        return self::send($testEmail, $subject, $body);
    }
}
