<!DOCTYPE html>
<html lang="fr-CA">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résumé des gains hebdomadaires / Weekly Earnings Summary - OCSAPP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

                    <!-- French Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
                            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Résumé des gains</h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">{{week_period}}</p>
                        </td>
                    </tr>

                    <!-- French Body -->
                    <tr>
                        <td style="padding: 40px 30px 20px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 20px;">
                                Bonjour {{first_name}},
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Voici votre résumé des gains pour la semaine écoulée. Continuez votre excellent travail !
                            </p>

                            <!-- Stats Grid -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px; background: #dcfce7; border-radius: 10px 0 0 0; text-align: center; width: 50%; border-right: 2px solid white;">
                                        <div style="font-size: 32px; font-weight: 700; color: #166534;">{{deliveries_completed}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Livraisons</div>
                                    </td>
                                    <td style="padding: 16px; background: #f0fdf4; border-radius: 0 10px 0 0; text-align: center; width: 50%;">
                                        <div style="font-size: 32px; font-weight: 700; color: #00b207;">{{net_earnings}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Gains nets</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px; background: #faf5ff; border-radius: 0 0 0 10px; text-align: center; border-right: 2px solid white;">
                                        <div style="font-size: 32px; font-weight: 700; color: #7c3aed;">{{gross_earnings}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Gains bruts</div>
                                    </td>
                                    <td style="padding: 16px; background: #fef3c7; border-radius: 0 0 10px 0; text-align: center;">
                                        <div style="font-size: 32px; font-weight: 700; color: #d97706;">{{tips}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Pourboires</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pending Balance -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 10px; border: 2px solid #f97316; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="font-size: 14px; color: #9a3412; font-weight: 600; margin-bottom: 8px;">SOLDE EN ATTENTE</div>
                                        <div style="font-size: 36px; font-weight: 700; color: #ea580c;">{{pending_balance}}</div>
                                        <div style="font-size: 13px; color: #9a3412; margin-top: 8px;">Votre solde en attente sera versé lors du prochain cycle de paiement.</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Breakdown -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: #f9fafb; border-radius: 10px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #374151; font-size: 16px;">Détail</h3>
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 6px 0; color: #6b7280; font-size: 14px;">Gains bruts</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #1f2937; font-size: 14px;">{{gross_earnings}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #6b7280; font-size: 14px;">Commission plateforme (20 %)</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #ef4444; font-size: 14px;">-{{commission}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #6b7280; font-size: 14px;">Pourboires</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #d97706; font-size: 14px;">+{{tips}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-top: 1px solid #e5e7eb; padding-top: 8px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #1f2937; font-size: 15px; font-weight: 700;">Gains nets</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #00b207; font-size: 15px;">{{net_earnings}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Connectez-vous à votre <a href="https://ocsapp.ca/delivery/earnings" style="color: #00b207; font-weight: 600; text-decoration: none;">portail chauffeur</a> pour tous les détails.
                            </p>

                            <p style="margin: 16px 0 0; color: #6b7280; font-size: 14px;">
                                Cordialement,<br>
                                <strong style="color: #1f2937;">L'équipe OCSAPP</strong>
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

                    <!-- English Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:36px 30px;text-align:center;">
                            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
                            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Weekly Earnings Summary</h1>
                            <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">{{week_period}}</p>
                        </td>
                    </tr>

                    <!-- English Body -->
                    <tr>
                        <td style="padding: 20px 30px 40px 30px;">
                            <h2 style="margin: 0 0 20px; color: #1f2937; font-size: 20px;">
                                Hi {{first_name}},
                            </h2>

                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Here's your earnings summary for the past week. Keep up the great work!
                            </p>

                            <!-- Stats Grid -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px; background: #dcfce7; border-radius: 10px 0 0 0; text-align: center; width: 50%; border-right: 2px solid white;">
                                        <div style="font-size: 32px; font-weight: 700; color: #166534;">{{deliveries_completed}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Deliveries</div>
                                    </td>
                                    <td style="padding: 16px; background: #f0fdf4; border-radius: 0 10px 0 0; text-align: center; width: 50%;">
                                        <div style="font-size: 32px; font-weight: 700; color: #00b207;">{{net_earnings}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Net Earnings</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px; background: #faf5ff; border-radius: 0 0 0 10px; text-align: center; border-right: 2px solid white;">
                                        <div style="font-size: 32px; font-weight: 700; color: #7c3aed;">{{gross_earnings}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Gross Earnings</div>
                                    </td>
                                    <td style="padding: 16px; background: #fef3c7; border-radius: 0 0 10px 0; text-align: center;">
                                        <div style="font-size: 32px; font-weight: 700; color: #d97706;">{{tips}}</div>
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 4px;">Tips</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pending Balance -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 10px; border: 2px solid #f97316; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="font-size: 14px; color: #9a3412; font-weight: 600; margin-bottom: 8px;">PENDING BALANCE</div>
                                        <div style="font-size: 36px; font-weight: 700; color: #ea580c;">{{pending_balance}}</div>
                                        <div style="font-size: 13px; color: #9a3412; margin-top: 8px;">Your pending balance will be paid in the next payout cycle.</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Commission Breakdown -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background: #f9fafb; border-radius: 10px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 12px; color: #374151; font-size: 16px;">Breakdown</h3>
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 6px 0; color: #6b7280; font-size: 14px;">Gross Earnings</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #1f2937; font-size: 14px;">{{gross_earnings}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #6b7280; font-size: 14px;">Platform Commission (20%)</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #ef4444; font-size: 14px;">-{{commission}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #6b7280; font-size: 14px;">Tips</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 600; color: #d97706; font-size: 14px;">+{{tips}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-top: 1px solid #e5e7eb; padding-top: 8px;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #1f2937; font-size: 15px; font-weight: 700;">Net Earnings</td>
                                                <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #00b207; font-size: 15px;">{{net_earnings}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Log in to your <a href="https://ocsapp.ca/delivery/earnings" style="color: #00b207; font-weight: 600; text-decoration: none;">driver portal</a> for full details.
                            </p>

                            <p style="margin: 16px 0 0; color: #6b7280; font-size: 14px;">
                                Best regards,<br>
                                <strong style="color: #1f2937;">The OCSAPP Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 30px; text-align: center; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 6px; color: #9ca3af; font-size: 12px;">
                                &copy; {{current_year}} OCSAPP. Tous droits réservés. / All rights reserved.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                Courriel automatique - ne pas répondre. / Automated email - do not reply.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
