<?php
$orderTotal = number_format($order['total'] ?? 0, 2);
$orderNumber = htmlspecialchars($order['order_number'] ?? 'N/A');
$orderDate = date('F j, Y', strtotime($order['created_at'] ?? 'now'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                ✓ Order Confirmed!
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Hi <?= htmlspecialchars($user['first_name'] ?? 'there') ?>! 👋
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Thank you for your order! We've received your order and are processing it now.
                            </p>

                            <!-- Order Details Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Order Details</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Order Number:</strong> <?= $orderNumber ?>
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Order Date:</strong> <?= $orderDate ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Status:</strong> <span style="color: #00b207; font-weight: 600;">Processing</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Items -->
                            <?php if (!empty($items)): ?>
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Order Items</h3>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr style="background-color: #f9fafb;">
                                        <th style="padding: 12px; text-align: left; color: #6b7280; font-size: 14px; border-bottom: 2px solid #e5e7eb;">Item</th>
                                        <th style="padding: 12px; text-align: center; color: #6b7280; font-size: 14px; border-bottom: 2px solid #e5e7eb;">Qty</th>
                                        <th style="padding: 12px; text-align: right; color: #6b7280; font-size: 14px; border-bottom: 2px solid #e5e7eb;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td style="padding: 12px; color: #4b5563; font-size: 14px; border-bottom: 1px solid #e5e7eb;">
                                            <?= htmlspecialchars($item['name'] ?? 'Product') ?>
                                        </td>
                                        <td style="padding: 12px; text-align: center; color: #4b5563; font-size: 14px; border-bottom: 1px solid #e5e7eb;">
                                            <?= htmlspecialchars($item['quantity'] ?? 1) ?>
                                        </td>
                                        <td style="padding: 12px; text-align: right; color: #4b5563; font-size: 14px; border-bottom: 1px solid #e5e7eb;">
                                            $<?= number_format($item['price'] ?? 0, 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="padding: 16px 12px; text-align: right; color: #1f2937; font-size: 16px; font-weight: 700;">
                                            Total:
                                        </td>
                                        <td style="padding: 16px 12px; text-align: right; color: #00b207; font-size: 18px; font-weight: 700;">
                                            $<?= $orderTotal ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php endif; ?>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/<?= $orderNumber ?>" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Track Your Order →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Help Section -->
                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Questions about your order?
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                📧 <a href="mailto:info@ocsapp.ca" style="color: #00b207; text-decoration: none;">info@ocsapp.ca</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                &copy; <?= date('Y') ?> OCSAPP. All rights reserved.
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                This is an automated email. Please do not reply to this message.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/terms" style="color: #6b7280; text-decoration: none;">Terms</a> •
                                <a href="https://ocsapp.ca/privacy" style="color: #6b7280; text-decoration: none;">Privacy</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
