<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chauffeur en route / Driver Coming for Pickup - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
<tr><td align="center" style="padding:40px 20px;">
<table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">

    <!-- French Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <div style="font-size:42px;margin-bottom:10px;">🚚</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Livreur assigné pour le ramassage</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Veuillez préparer votre commande</p>
        </td>
    </tr>

    <!-- French Body -->
    <tr><td style="padding:40px 30px 20px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Bonjour {{supplier_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            Un chauffeur a été assigné pour récupérer le <strong>bon de commande #{{po_number}}</strong> à votre emplacement. Veuillez vous assurer que la commande est emballée et prête.
        </p>

        <!-- Driver Info FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#e8f5e9;border-radius:8px;margin:0 0 24px;">
        <tr><td style="padding:20px;">
            <p style="margin:0 0 12px;color:#007a05;font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Informations du chauffeur</p>
            <table role="presentation" style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;width:40%;">Nom du chauffeur</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:600;">{{driver_name}}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;">No de bon de commande</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:600;">#{{po_number}}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;">Total de la commande</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:600;">${{order_total}}</td>
                </tr>
                {{#if pickup_notes}}
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;">Notes</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;">{{pickup_notes}}</td>
                </tr>
                {{/if}}
            </table>
        </td></tr>
        </table>

        <!-- Checklist FR -->
        <table role="presentation" style="width:100%;border-collapse:collapse;border-left:4px solid #f59e0b;background:#fffbeb;border-radius:4px;margin:0 0 24px;">
        <tr><td style="padding:16px 20px;">
            <p style="margin:0 0 8px;color:#92400e;font-size:14px;font-weight:600;">Avant l'arrivée du chauffeur :</p>
            <ul style="margin:0;padding-left:20px;color:#78350f;font-size:14px;line-height:1.8;">
                <li>Vérifiez que tous les articles sont emballés et comptés</li>
                <li>Ayez votre numéro de bon de commande prêt pour la vérification</li>
                <li>Assurez-vous que l'accès à votre zone de chargement est dégagé</li>
            </ul>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin-bottom:24px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/orders" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                Voir les détails de la commande
            </a>
        </td></tr>
        </table>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">Pour toute question, contactez-nous à <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a>.</p>
        <p style="margin:16px 0 0;color:#6b7280;font-size:14px;">Cordialement,<br><strong style="color:#1f2937;">L'équipe logistique OCSAPP</strong></p>
    </td></tr>

    <!-- Language Divider -->
    <tr><td style="padding:0 30px;">
        <table role="presentation" style="width:100%;border-collapse:collapse;">
        <tr><td style="padding:24px 0 8px;text-align:center;">
            <hr style="border:none;border-top:2px dashed #e5e7eb;margin:0 0 12px;">
            <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">
                🇬🇧 English version follows below / La version française précède
            </span>
            <hr style="border:none;border-top:2px dashed #e5e7eb;margin:12px 0 0;">
        </td></tr>
        </table>
    </td></tr>

    <!-- English Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <div style="font-size:42px;margin-bottom:10px;">🚚</div>
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Driver Assigned for Pickup</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Please have your order ready</p>
        </td>
    </tr>

    <!-- English Body -->
    <tr><td style="padding:20px 30px 40px;">
        <h2 style="margin:0 0 18px;color:#1f2937;font-size:20px;">Hi {{supplier_name}},</h2>

        <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
            A driver has been assigned to pick up <strong>Purchase Order #{{po_number}}</strong> from your location. Please ensure the order is packed and ready.
        </p>

        <!-- Driver Info EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#e8f5e9;border-radius:8px;margin:0 0 24px;">
        <tr><td style="padding:20px;">
            <p style="margin:0 0 12px;color:#007a05;font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Driver Details</p>
            <table role="presentation" style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;width:40%;">Driver Name</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:600;">{{driver_name}}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;">PO Number</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:600;">#{{po_number}}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;">Order Total</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;font-weight:600;">${{order_total}}</td>
                </tr>
                {{#if pickup_notes}}
                <tr>
                    <td style="padding:4px 0;color:#6b7280;font-size:14px;">Notes</td>
                    <td style="padding:4px 0;color:#111827;font-size:14px;">{{pickup_notes}}</td>
                </tr>
                {{/if}}
            </table>
        </td></tr>
        </table>

        <!-- Checklist EN -->
        <table role="presentation" style="width:100%;border-collapse:collapse;border-left:4px solid #f59e0b;background:#fffbeb;border-radius:4px;margin:0 0 24px;">
        <tr><td style="padding:16px 20px;">
            <p style="margin:0 0 8px;color:#92400e;font-size:14px;font-weight:600;">Before the driver arrives:</p>
            <ul style="margin:0;padding-left:20px;color:#78350f;font-size:14px;line-height:1.8;">
                <li>Ensure all items in the order are packed and counted</li>
                <li>Have your PO number ready for verification</li>
                <li>Keep access to your loading area clear</li>
            </ul>
        </td></tr>
        </table>

        <table role="presentation" style="width:100%;margin-bottom:24px;">
        <tr><td align="center">
            <a href="https://ocsapp.ca/supplier/orders" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00b207 0%,#009206 100%);color:#fff;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;box-shadow:0 4px 12px rgba(0,178,7,0.3);">
                View Order Details
            </a>
        </td></tr>
        </table>

        <p style="margin:0 0 6px;color:#4b5563;font-size:14px;">If you have any questions, contact us at <a href="mailto:info@ocsapp.ca" style="color:#00b207;text-decoration:none;font-weight:600;">info@ocsapp.ca</a>.</p>
        <p style="margin:16px 0 0;color:#6b7280;font-size:14px;">Best regards,<br><strong style="color:#1f2937;">OCSAPP Logistics Team</strong></p>
    </td></tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f9fafb;padding:24px 30px;text-align:center;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;">
            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">&copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.</p>
            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">Courriel automatique - ne pas répondre. / Automated email - do not reply.</p>
            <p style="margin:0;color:#9ca3af;font-size:12px;">
                <a href="https://ocsapp.ca/terms" style="color:#6b7280;text-decoration:none;">Terms</a> &bull;
                <a href="https://ocsapp.ca/privacy" style="color:#6b7280;text-decoration:none;">Privacy</a>
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
