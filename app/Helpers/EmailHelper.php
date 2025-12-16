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
        
        // Test mode - just log and return
        if ($config['test_mode']) {
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
            
            // Reply-To
            if (!empty($options['reply_to'])) {
                $mail->addReplyTo($options['reply_to']);
            } elseif (!empty($config['defaults']['reply_to'])) {
                $mail->addReplyTo($config['defaults']['reply_to']);
            }
            
            // CC & BCC
            if (!empty($options['cc'])) {
                if (is_array($options['cc'])) {
                    foreach ($options['cc'] as $email) {
                        $mail->addCC($email);
                    }
                } else {
                    $mail->addCC($options['cc']);
                }
            }
            
            if (!empty($options['bcc'])) {
                if (is_array($options['bcc'])) {
                    foreach ($options['bcc'] as $email) {
                        $mail->addBCC($email);
                    }
                } else {
                    $mail->addBCC($options['bcc']);
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
            if ($config['log']['enabled']) {
                self::logEmail($to, $subject, $body, 'Sent successfully');
            }
            
            return $result;
            
        } catch (Exception $e) {
            // Log error
            $errorMsg = "Email sending failed: {$mail->ErrorInfo}";
            logger($errorMsg, 'error');
            
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
            // User placeholders
            '{{user_first_name}}' => htmlspecialchars($data['user']['first_name'] ?? 'there'),
            '{{user_last_name}}' => htmlspecialchars($data['user']['last_name'] ?? ''),
            '{{user_email}}' => htmlspecialchars($data['user']['email'] ?? ''),

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
        
        $data = [
            'order' => $order,
            'items' => $items,
            'user' => $user,
            'subject' => str_replace('{order_number}', $order['order_number'], 
                        $config['notifications']['order_confirmation']['subject'])
        ];
        
        return self::sendTemplate($user['email'], 'order-confirmation', $data);
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
     * Log email for debugging
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
            'subject' => 'Seller Application Received - Pending Approval'
        ];

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
            'subject' => 'Congratulations! Your Seller Account is Approved'
        ];

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
            'subject' => 'New Seller Application - Action Required'
        ];

        return self::sendTemplate($adminEmail, 'admin-seller-notification', $data);
    }

    /**
     * Send delivery assignment notification to delivery person
     *
     * @param array $delivery Delivery details
     * @param array $order Order details
     * @return bool Success status
     */
    public static function sendDeliveryAssignment(array $delivery, array $order): bool
    {
        $deliveryEmail = $delivery['email'] ?? '';
        if (empty($deliveryEmail)) {
            logger("Delivery person email not provided", 'error');
            return false;
        }

        $data = [
            'order' => $order,
            'delivery_person_name' => $delivery['first_name'] ?? 'Delivery Partner',
            'order_number' => $order['order_number'] ?? 'N/A',
            'order_total' => number_format($order['total'] ?? 0, 2),
            'order_id' => $order['id'] ?? '',
            'shop_name' => $order['shop_name'] ?? 'OCS Store',
            'shop_address' => $order['shop_address'] ?? '',
            'shop_phone' => $order['shop_phone'] ?? '',
            'shop_contact_name' => $order['shop_contact'] ?? '',
            'customer_name' => $order['customer_name'] ?? '',
            'delivery_address' => $order['delivery_address'] ?? '',
            'customer_phone' => $order['customer_phone'] ?? '',
            'delivery_window' => $order['delivery_window'] ?? 'ASAP',
            'items_count' => $order['items_count'] ?? '0',
            'order_items_list' => $order['items_list'] ?? '',
            'delivery_instructions' => !empty($order['delivery_instructions'])
                ? '<table role="presentation" style="width: 100%; border-collapse: collapse; background: #fef3c7; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #f59e0b;">
                    <tr><td style="padding: 16px 20px;">
                        <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">📝 Special Instructions</p>
                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.6;">' . htmlspecialchars($order['delivery_instructions']) . '</p>
                    </td></tr></table>'
                : '',
            'subject' => "New Delivery Assignment - Order #{$order['order_number']}"
        ];

        return self::sendTemplate($deliveryEmail, 'delivery-assignment', $data);
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
            'driver_name' => $driver['name'] ?? 'OCS Delivery Partner',
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
            'subject' => "New Order #{$order['order_number']} - Action Required"
        ];

        logger("Sending vendor order notification to {$vendorEmail} for order #{$order['order_number']}", 'info');

        return self::sendTemplate($vendorEmail, 'vendor-new-order', $data);
    }

    /**
     * Test email configuration
     *
     * @param string $testEmail Email to send test to
     * @return bool Success status
     */
    public static function testConnection(string $testEmail): bool
    {
        $subject = 'OCSAPP - Email Test';
        $body = '
            <h2>Email Configuration Test</h2>
            <p>If you receive this email, your email configuration is working correctly!</p>
            <p>Sent at: ' . date('Y-m-d H:i:s') . '</p>
        ';

        return self::send($testEmail, $subject, $body);
    }
}