<?php
$productName = htmlspecialchars($product['name'] ?? 'Product');
$sku = htmlspecialchars($product['sku'] ?? 'N/A');
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte stock bas / Low Stock Alert - OCSAPP</title>
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
                                Alerte stock bas / Low Stock Alert
                            </h1>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 24px;">
                                Bonjour <?= htmlspecialchars($seller['first_name'] ?? 'là') ?>,
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                L'un de vos produits est presque épuisé et nécessite votre attention.
                            </p>

                            <!-- Product Alert Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fffbeb; border-radius: 8px; margin-bottom: 24px; border: 2px solid #fbbf24;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #f59e0b; font-size: 18px;">Détails du produit</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Nom du produit :</strong> <?= $productName ?>
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>UGS :</strong> <?= $sku ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Stock actuel :</strong> <span style="color: #ef4444; font-weight: 700; font-size: 18px;"><?= $current_stock ?></span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Needed -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            📢 Action requise
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Le stock de votre produit est en dessous du seuil. Veuillez réapprovisionner rapidement pour éviter la rupture de stock et les pertes de ventes potentielles.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/seller/inventory" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Mettre à jour l'inventaire →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Besoin d'aide pour la gestion des stocks ?
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
                                Hi <?= htmlspecialchars($seller['first_name'] ?? 'there') ?>,
                            </h2>

                            <p style="margin: 0 0 16px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                One of your products is running low on stock and needs your attention.
                            </p>

                            <!-- Product Alert Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fffbeb; border-radius: 8px; margin-bottom: 24px; border: 2px solid #fbbf24;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #f59e0b; font-size: 18px;">Product Details</h3>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>Product Name:</strong> <?= $productName ?>
                                        </p>
                                        <p style="margin: 0 0 8px; color: #6b7280; font-size: 14px;">
                                            <strong>SKU:</strong> <?= $sku ?>
                                        </p>
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">
                                            <strong>Current Stock:</strong> <span style="color: #ef4444; font-weight: 700; font-size: 18px;"><?= $current_stock ?></span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Needed -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 4px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                            📢 Action Required
                                        </p>
                                        <p style="margin: 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                            Your product stock is below the threshold. Please restock soon to avoid running out and potentially losing sales.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="https://ocsapp.ca/seller/inventory" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);">
                                            Update Inventory →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                Need assistance with inventory management?
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
