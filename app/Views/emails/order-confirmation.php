<?php
$orderTotal = number_format($order['total'] ?? 0, 2);
$orderNumber = htmlspecialchars($order['order_number'] ?? 'N/A');
$orderDate = date('F j, Y', strtotime($order['created_at'] ?? 'now'));
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande confirmée / Order Confirmed</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00b207 0%, #009206 100%); padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width: 180px; height: auto; margin-bottom: 20px;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                Commande confirmée / Order Confirmed!
                            </h1>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour <?= htmlspecialchars($user['first_name'] ?? 'là') ?> !
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Merci pour votre commande ! Nous l'avons bien reçue et elle est en cours de traitement.
                            </p>

                            <!-- Détails de la commande -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Détails de la commande</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Numéro de commande :</strong> <?= $orderNumber ?>
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Date de commande :</strong> <?= $orderDate ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Statut :</strong> <span style="color: #00b207; font-weight: 600;">En traitement</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Articles commandés -->
                            <?php if (!empty($items)): ?>
                            <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Articles commandés</h3>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr style="background-color: #f9fafb;">
                                        <th style="padding: 12px; text-align: left; color: #6b7280; font-size: 14px; border-bottom: 2px solid #e5e7eb;">Article</th>
                                        <th style="padding: 12px; text-align: center; color: #6b7280; font-size: 14px; border-bottom: 2px solid #e5e7eb;">Qté</th>
                                        <th style="padding: 12px; text-align: right; color: #6b7280; font-size: 14px; border-bottom: 2px solid #e5e7eb;">Prix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td style="padding: 12px; color: #4b5563; font-size: 14px; border-bottom: 1px solid #e5e7eb;">
                                            <?= htmlspecialchars($item['name'] ?? 'Produit') ?>
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
                                    <?php
                                    $subtotal    = (float)($order['subtotal'] ?? 0);
                                    $deliveryFee = (float)($order['delivery_fee'] ?? 0);
                                    $gst         = round($subtotal * 0.05, 2);
                                    $qst         = round($subtotal * 0.09975, 2);
                                    ?>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">Sous-total :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                    <?php if ($deliveryFee > 0): ?>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">Livraison :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($deliveryFee, 2) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">TPS (5%) :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($gst, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">TVQ (9,975%) :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($qst, 2) ?></td>
                                    </tr>
                                    <tr style="border-top: 2px solid #e5e7eb;">
                                        <td colspan="2" style="padding: 16px 12px; text-align: right; color: #1f2937; font-size: 16px; font-weight: 700;">Total :</td>
                                        <td style="padding: 16px 12px; text-align: right; color: #00b207; font-size: 18px; font-weight: 700;">$<?= $orderTotal ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php endif; ?>

                            <!-- Bouton FR -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/<?= $orderNumber ?>" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Suivre ma commande →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Des questions sur votre commande ?
                            </p>
                            <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                📧 <a href="mailto:info@ocsapp.ca" style="color: #00b207; text-decoration: none;">info@ocsapp.ca</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Language Divider -->
                    <tr>
                      <td style="padding: 0 30px;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;">
                          <tr>
                            <td style="padding:24px 0 8px;text-align:center;">
                              <hr style="border:none;border-top:2px dashed #e5e7eb;margin:0 0 12px;">
                              <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">
                                🇬🇧 English version follows below / La version française précède
                              </span>
                              <hr style="border:none;border-top:2px dashed #e5e7eb;margin:12px 0 0;">
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>

                    <!-- English Body -->
                    <tr>
                        <td style="padding: 20px 30px 40px 30px;">
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
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">Subtotal / Sous-total :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                    <?php if ($deliveryFee > 0): ?>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">Delivery / Livraison :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($deliveryFee, 2) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">GST / TPS (5%) :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($gst, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 8px 12px; text-align: right; color: #6b7280; font-size: 14px;">QST / TVQ (9.975%) :</td>
                                        <td style="padding: 8px 12px; text-align: right; color: #4b5563; font-size: 14px;">$<?= number_format($qst, 2) ?></td>
                                    </tr>
                                    <tr style="border-top: 2px solid #e5e7eb;">
                                        <td colspan="2" style="padding: 16px 12px; text-align: right; color: #1f2937; font-size: 16px; font-weight: 700;">Total :</td>
                                        <td style="padding: 16px 12px; text-align: right; color: #00b207; font-size: 18px; font-weight: 700;">$<?= $orderTotal ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php endif; ?>

                            <!-- CTA Button EN -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/<?= $orderNumber ?>" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Track Your Order →
                                        </a>
                                    </td>
                                </tr>
                            </table>

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
                                &copy; <?= date('Y') ?> OCSAPP. Tous droits réservés. / All rights reserved.
                            </p>
                            <p style="margin: 0 0 12px; color: #9ca3af; font-size: 12px;">
                                Courriel automatique — ne pas répondre. / Automated email — do not reply.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                <a href="https://ocsapp.ca/terms" style="color: #6b7280; text-decoration: none;">Terms</a> •
                                <a href="https://ocsapp.ca/privacy" style="color: #6b7280; text-decoration: none;">Privacy</a> •
                                <a href="https://ocsapp.ca/unsubscribe" style="color: #6b7280; text-decoration: none;">Unsubscribe</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
