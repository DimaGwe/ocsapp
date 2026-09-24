<?php
/**
 * Waitlist beta invite email - BILINGUAL (FR first, then EN).
 * Sent from Admin > Waitlist ("Send Account Invite"). Carries the personal
 * account-creation link (only works with the invited email) and the role's onboarding guide.
 * Variables in scope (from AdminWaitlistController::sendInvite): $firstName, $email,
 * $roleLabelFr, $roleLabelEn, $inviteUrl, $guideUrl, $unsubUrl
 */
$inviteSafe = htmlspecialchars($inviteUrl ?? '', ENT_QUOTES);
$guideSafe  = htmlspecialchars($guideUrl ?? '', ENT_QUOTES);
$unsubSafe  = htmlspecialchars($unsubUrl ?? '', ENT_QUOTES);
$emailSafe  = htmlspecialchars($email ?? '', ENT_QUOTES);
$year       = date('Y');

// bgcolor + background-color fallbacks: Outlook desktop ignores linear-gradient
$headerStyle = 'background-color:#00b207;background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:40px 30px;text-align:center;';
$btnStyle    = 'display:inline-block;background-color:#00b207;color:#fff;font-size:16px;font-weight:700;text-decoration:none;padding:14px 30px;border-radius:10px;';
$btn2Style   = 'display:inline-block;background-color:#ffffff;color:#00920a;font-size:14px;font-weight:700;text-decoration:none;padding:11px 24px;border-radius:10px;border:2px solid #00b207;';
$boxStyle    = 'width:100%;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;margin-bottom:24px;';
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Votre accès OCSAPP est prêt / Your OCSAPP access is ready</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
  <tr>
    <td align="center" style="padding:40px 20px;">
      <table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);">

        <!-- ===================== FRENCH ===================== -->
        <tr>
          <td bgcolor="#00b207" style="<?= $headerStyle ?>border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Votre accès est prêt !</h1>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 30px 28px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Bonjour <?= htmlspecialchars($firstName) ?>,</p>
            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              Bonne nouvelle : une place s'est libérée dans la version bêta d'OCSAPP. Vous pouvez maintenant créer votre compte <strong><?= htmlspecialchars($roleLabelFr) ?></strong>.
            </p>
            <p style="margin:0 0 24px;text-align:center;"><a href="<?= $inviteSafe ?>" style="<?= $btnStyle ?>">Créer mon compte</a></p>
            <table role="presentation" style="<?= $boxStyle ?>">
              <tr>
                <td style="padding:18px 20px;font-size:13px;color:#4b5563;line-height:1.6;">
                  Ce lien est personnel. Pendant la période bêta, votre compte doit être créé avec l'adresse courriel <strong><?= $emailSafe ?></strong>.
                </td>
              </tr>
            </table>
            <p style="margin:0 0 12px;color:#4b5563;font-size:14px;line-height:1.7;">Avant de commencer, consultez votre guide d'accueil : les étapes pour démarrer, les frais et ce qu'il faut préparer.</p>
            <p style="margin:0;"><a href="<?= $guideSafe ?>" style="<?= $btn2Style ?>">Voir mon guide d'accueil</a></p>
          </td>
        </tr>

        <!-- Language divider -->
        <tr>
          <td style="padding:0 30px;">
            <table role="presentation" style="width:100%;border-collapse:collapse;">
              <tr>
                <td style="padding:24px 0 8px;text-align:center;">
                  <hr style="border:none;border-top:2px dashed #e5e7eb;margin:0 0 12px;">
                  <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">English version below / Version anglaise ci-dessous</span>
                  <hr style="border:none;border-top:2px dashed #e5e7eb;margin:12px 0 0;">
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- ===================== ENGLISH ===================== -->
        <tr>
          <td bgcolor="#00b207" style="<?= $headerStyle ?>">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Your access is ready!</h1>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 30px 36px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Hi <?= htmlspecialchars($firstName) ?>,</p>
            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              Good news: a spot has opened up in the OCSAPP beta. You can now create your <strong><?= htmlspecialchars($roleLabelEn) ?></strong> account.
            </p>
            <p style="margin:0 0 24px;text-align:center;"><a href="<?= $inviteSafe ?>" style="<?= $btnStyle ?>">Create my account</a></p>
            <table role="presentation" style="<?= $boxStyle ?>">
              <tr>
                <td style="padding:18px 20px;font-size:13px;color:#4b5563;line-height:1.6;">
                  This link is personal. During beta, your account must be created with the email address <strong><?= $emailSafe ?></strong>.
                </td>
              </tr>
            </table>
            <p style="margin:0 0 12px;color:#4b5563;font-size:14px;line-height:1.7;">Before you start, have a look at your onboarding guide: how to get started, what it costs and what to have ready.</p>
            <p style="margin:0;"><a href="<?= $guideSafe ?>" style="<?= $btn2Style ?>">View my onboarding guide</a></p>
          </td>
        </tr>

        <!-- Footer (bilingual, CASL sender identification) -->
        <tr>
          <td style="background:#f9fafb;padding:24px 30px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">OCSAPP Inc. | Siège social : Laval, Québec (H7H) / Registered office: Laval, Quebec (H7H)</p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">Questions : <a href="mailto:info@ocsapp.ca" style="color:#9ca3af;">info@ocsapp.ca</a></p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">Vous recevez ce courriel à la suite de votre inscription à la liste d'attente OCSAPP. / You are receiving this email because you joined the OCSAPP waitlist.</p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">&copy; <?= $year ?> OCSAPP Inc. Tous droits réservés. / All rights reserved.</p>
            <p style="margin:0;font-size:12px;color:#9ca3af;"><a href="<?= $unsubSafe ?>" style="color:#9ca3af;">Se désabonner / Unsubscribe</a></p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
