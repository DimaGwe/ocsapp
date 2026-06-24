<?php
$orderNumber = htmlspecialchars($order['order_number'] ?? 'N/A');
$statusColors = [
    'processing' => '#f59e0b',
    'shipped' => '#3b82f6',
    'delivered' => '#00b207',
    'cancelled' => '#ef4444',
];
$statusColor = $statusColors[$new_status] ?? '#6b7280';
$statusLabelsFr = [
    'processing' => 'En traitement',
    'shipped' => 'Expédié',
    'delivered' => 'Livré',
    'cancelled' => 'Annulé',
];
$oldStatusFr = $statusLabelsFr[$old_status] ?? htmlspecialchars($old_status ?? 'N/A');
$newStatusFr = $statusLabelsFr[$new_status] ?? htmlspecialchars($new_status ?? 'N/A');
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de commande / Order Status Update</title>
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
                                Mise à jour de commande / Order Status Update
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
                                Le statut de votre commande a été mis à jour.
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Commande #<?= $orderNumber ?></h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Statut précédent :</strong> <span style="text-transform: capitalize;"><?= $oldStatusFr ?></span>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Nouveau statut :</strong> <span style="color: <?= $statusColor ?>; font-weight: 600; text-transform: capitalize;"><?= $newStatusFr ?></span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <?php if ($new_status === 'shipped'): ?>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            🚚 Votre commande est en route !
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Votre colis a été expédié et est en chemin vers votre adresse de livraison. Suivez-le en cliquant sur le lien ci-dessous.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php elseif ($new_status === 'delivered'): ?>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #00b207; background-color: #f0fdf4; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            ✓ Livraison confirmée !
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Votre commande a été livrée. Nous espérons que vous êtes satisfait de votre achat !
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/<?= $orderNumber ?>" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Voir les détails →
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
                                Your order status has been updated.
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #00b207; font-size: 18px;">Order #<?= $orderNumber ?></h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Previous Status:</strong> <span style="text-transform: capitalize;"><?= htmlspecialchars($old_status ?? 'N/A') ?></span>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>New Status:</strong> <span style="color: <?= $statusColor ?>; font-weight: 600; text-transform: capitalize;"><?= htmlspecialchars($new_status ?? 'N/A') ?></span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <?php if ($new_status === 'shipped'): ?>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            🚚 Your order is on its way!
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Your package has been shipped and is en route to your delivery address. Track it using the link below.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php elseif ($new_status === 'delivered'): ?>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #00b207; background-color: #f0fdf4; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            ✓ Delivered Successfully!
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Your order has been delivered. We hope you enjoy your purchase!
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/orders/<?= $orderNumber ?>" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            View Order Details →
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
