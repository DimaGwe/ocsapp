<?php
/**
 * Founding status granted - BILINGUAL (FR first, then EN). Transactional, sent once per waitlist row.
 * Confirms the founding number and the person's Founders' Wall choice (Law 25: they can change or
 * withdraw it at any time through the no-login preferences link).
 * Sent from FoundersWallHelper::onFoundingGranted(). Variables in scope (all plain text, escaped here):
 * $firstName, $programFr, $programEn, $number, $wallShown (bool), $shownAs, $manageUrl
 */
$first     = htmlspecialchars($firstName ?? '', ENT_QUOTES);
$as        = htmlspecialchars($shownAs ?? '', ENT_QUOTES);
$progFr    = htmlspecialchars($programFr ?? '', ENT_QUOTES);
$progEn    = htmlspecialchars($programEn ?? '', ENT_QUOTES);
$num       = (int) ($number ?? 0);
$manage    = htmlspecialchars($manageUrl ?? '', ENT_QUOTES);
$year      = date('Y');

// bgcolor + background-color fallbacks: Outlook desktop ignores linear-gradient
$headerStyle = 'background-color:#00b207;background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:40px 30px;text-align:center;';
$badgeStyle  = 'display:inline-block;margin-top:14px;background:rgba(255,255,255,.18);color:#fff;font-size:14px;font-weight:700;padding:6px 16px;border-radius:999px;';
$btn2Style   = 'display:inline-block;background-color:#ffffff;color:#00920a;font-size:14px;font-weight:700;text-decoration:none;padding:11px 24px;border-radius:10px;border:2px solid #00b207;';
$boxStyle    = 'width:100%;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;margin-bottom:24px;';
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $progFr ?> n° <?= $num ?> / <?= $progEn ?> #<?= $num ?></title>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Bienvenue parmi les fondateurs !</h1>
            <span style="<?= $badgeStyle ?>"><?= $progFr ?> n° <?= $num ?></span>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 30px 28px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Bonjour <?= $first ?>,</p>
            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              Vous êtes officiellement <strong><?= $progFr ?> n° <?= $num ?></strong> d'OCSAPP. Merci de faire partie de nos tout premiers membres.
            </p>
            <table role="presentation" style="<?= $boxStyle ?>">
              <tr>
                <td style="padding:18px 20px;font-size:14px;color:#4b5563;line-height:1.6;">
                  <strong style="color:#1f2937;">Mur des fondateurs</strong><br>
                  <?php if ($wallShown): ?>
                    Lors de votre inscription, vous avez accepté d'apparaître sur le Mur des fondateurs sous le nom <strong><?= $as ?></strong>. Vous pouvez modifier ou retirer ce choix en tout temps.
                  <?php else: ?>
                    Lors de votre inscription, vous avez choisi de ne pas afficher votre nom sur le Mur des fondateurs. Votre place y apparaît de façon anonyme, sous la forme <strong><?= $progFr ?> n° <?= $num ?></strong>, sans votre nom ni votre ville. Si vous changez d'avis, vous pouvez afficher votre nom en tout temps.
                  <?php endif; ?>
                </td>
              </tr>
            </table>
            <p style="margin:0;"><a href="<?= $manage ?>" style="<?= $btn2Style ?>">Gérer mon choix pour le Mur des fondateurs</a></p>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Welcome to the founders!</h1>
            <span style="<?= $badgeStyle ?>"><?= $progEn ?> #<?= $num ?></span>
          </td>
        </tr>
        <tr>
          <td style="padding:36px 30px 36px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Hi <?= $first ?>,</p>
            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              You are officially OCSAPP <strong><?= $progEn ?> #<?= $num ?></strong>. Thank you for being one of our very first members.
            </p>
            <table role="presentation" style="<?= $boxStyle ?>">
              <tr>
                <td style="padding:18px 20px;font-size:14px;color:#4b5563;line-height:1.6;">
                  <strong style="color:#1f2937;">Founders' Wall</strong><br>
                  <?php if ($wallShown): ?>
                    When you signed up, you agreed to appear on the Founders' Wall as <strong><?= $as ?></strong>. You can change or withdraw this choice at any time.
                  <?php else: ?>
                    When you signed up, you chose not to show your name on the Founders' Wall. Your spot appears there anonymously, as <strong><?= $progEn ?> #<?= $num ?></strong>, without your name or city. If you change your mind, you can show your name at any time.
                  <?php endif; ?>
                </td>
              </tr>
            </table>
            <p style="margin:0;"><a href="<?= $manage ?>" style="<?= $btn2Style ?>">Manage my Founders' Wall choice</a></p>
          </td>
        </tr>

        <!-- Footer (bilingual, sender identification) -->
        <tr>
          <td style="background:#f9fafb;padding:24px 30px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">OCSAPP Inc. | Siège social : Laval, Québec (H7H) / Registered office: Laval, Quebec (H7H)</p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">Questions : <a href="mailto:info@ocsapp.ca" style="color:#9ca3af;">info@ocsapp.ca</a></p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">Courriel transactionnel lié à votre statut de fondateur. / Transactional email about your founder status.</p>
            <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; <?= $year ?> OCSAPP Inc. Tous droits réservés. / All rights reserved.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
