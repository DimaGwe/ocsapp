<?php
/**
 * Waitlist confirmation email - BILINGUAL (FR + EN).
 * Always renders both languages regardless of the visitor's chosen language.
 * Variables in scope (from WaitlistController::sendConfirmation): $firstName, $roleLabelFr, $roleLabelEn,
 * $pos (per role), $role, $refUrl, $refCode, $email, $unsubUrl, $central [path, FR name, EN name] or null, $centralUrl
 *
 * Headers carry bgcolor + background-color fallbacks: Outlook desktop ignores linear-gradient,
 * which would otherwise leave the white heading on a white background.
 */
$refUrlSafe   = htmlspecialchars($refUrl ?? '', ENT_QUOTES);
$refCodeSafe  = htmlspecialchars($refCode ?? '', ENT_QUOTES);
$unsubUrlSafe = htmlspecialchars($unsubUrl ?? '', ENT_QUOTES);
$centralSafe  = !empty($centralUrl) ? htmlspecialchars($centralUrl, ENT_QUOTES) : '';
$year         = date('Y');

// Positions are numbered per role ("#1 among sellers")
$rolePlural = [
    'buyer'    => ['les acheteurs', 'buyers'],
    'seller'   => ['les vendeurs', 'sellers'],
    'supplier' => ['les fournisseurs', 'suppliers'],
    'driver'   => ['les livreurs', 'drivers'],
    'business' => ['les entreprises', 'businesses'],
    'partner'  => ['les partenaires', 'partners'],
][$role ?? ''] ?? null;
$posLabelFr = $rolePlural ? 'votre position parmi ' . $rolePlural[0] : 'votre position';
$posLabelEn = $rolePlural ? 'your position among ' . $rolePlural[1] : 'your position';

$headerStyle = 'background-color:#00b207;background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:40px 30px;text-align:center;';
$badgeStyle  = 'display:inline-block;background-color:#00b207;background:linear-gradient(135deg,#00b207,#00d609);color:#fff;font-size:32px;font-weight:900;padding:14px 36px;border-radius:12px;text-align:center;';
$labelStyle  = 'margin:0 0 8px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:1px;';
$codeStyle   = "margin:0 0 16px;font-size:22px;font-weight:800;color:#111;letter-spacing:3px;font-family:'Courier New',monospace;";
$btnStyle    = 'display:inline-block;background-color:#00b207;color:#fff;font-size:14px;font-weight:700;text-decoration:none;padding:12px 26px;border-radius:8px;';
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenue sur la liste d'attente OCSAPP / Welcome to the OCSAPP waitlist</title>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Vous êtes sur la liste !</h1>
          </td>
        </tr>

        <tr>
          <td style="padding:36px 30px 32px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Bonjour <?= htmlspecialchars($firstName) ?>,</p>
            <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
              Merci pour votre inscription à la liste d'attente OCSAPP à titre de <strong><?= htmlspecialchars($roleLabelFr) ?></strong>. Vous serez parmi les premiers à obtenir l'accès lors du lancement officiel.
            </p>

            <table role="presentation" style="width:100%;margin:0 0 28px;">
              <tr>
                <td align="center">
                  <div style="<?= $badgeStyle ?>">
                    #<?= (int) $pos ?>
                    <div style="font-size:14px;font-weight:400;opacity:.85;"><?= $posLabelFr ?></div>
                  </div>
                </td>
              </tr>
            </table>

            <table role="presentation" style="width:100%;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px;">
                  <p style="<?= $labelStyle ?>">Votre code de parrainage</p>
                  <p style="<?= $codeStyle ?>"><?= $refCodeSafe ?></p>
                  <p style="<?= $labelStyle ?>">Votre lien de parrainage</p>
                  <p style="margin:0 0 10px;font-size:14px;color:#111;word-break:break-all;">
                    <a href="<?= $refUrlSafe ?>" style="color:#00b207;font-weight:600;"><?= $refUrlSafe ?></a>
                  </p>
                  <p style="margin:0;font-size:13px;color:#6b7280;">Vous connaissez des gens qui aimeraient se joindre à OCSAPP ? Partagez votre lien ou votre code et aidez-nous à faire grandir le réseau local.</p>
                </td>
              </tr>
            </table>

            <?php if ($centralSafe): ?>
            <p style="margin:0 0 14px;color:#4b5563;font-size:14px;line-height:1.7;">En attendant, découvrez ce qu'OCSAPP vous réserve sur <?= htmlspecialchars($central[1]) ?>.</p>
            <p style="margin:0 0 24px;"><a href="<?= $centralSafe ?>" style="<?= $btnStyle ?>">Découvrir <?= htmlspecialchars($central[1]) ?></a></p>
            <?php endif; ?>

            <p style="margin:0;color:#4b5563;font-size:14px;">Nous vous écrirons par courriel dès que votre accès sera prêt.</p>
          </td>
        </tr>

        <!-- Language divider -->
        <tr>
          <td style="padding:0 30px;">
            <table role="presentation" style="width:100%;border-collapse:collapse;">
              <tr>
                <td style="padding:24px 0 8px;text-align:center;">
                  <hr style="border:none;border-top:2px dashed #e5e7eb;margin:0 0 12px;">
                  <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">
                    English version below / Version anglaise ci-dessous
                  </span>
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
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">You're on the list!</h1>
          </td>
        </tr>

        <tr>
          <td style="padding:36px 30px 40px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Hi <?= htmlspecialchars($firstName) ?>,</p>
            <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
              Thank you for joining the OCSAPP waitlist as a <strong><?= htmlspecialchars($roleLabelEn) ?></strong>. You'll be among the first to get access when we officially launch.
            </p>

            <table role="presentation" style="width:100%;margin:0 0 28px;">
              <tr>
                <td align="center">
                  <div style="<?= $badgeStyle ?>">
                    #<?= (int) $pos ?>
                    <div style="font-size:14px;font-weight:400;opacity:.85;"><?= $posLabelEn ?></div>
                  </div>
                </td>
              </tr>
            </table>

            <table role="presentation" style="width:100%;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px;">
                  <p style="<?= $labelStyle ?>">Your referral code</p>
                  <p style="<?= $codeStyle ?>"><?= $refCodeSafe ?></p>
                  <p style="<?= $labelStyle ?>">Your referral link</p>
                  <p style="margin:0 0 10px;font-size:14px;color:#111;word-break:break-all;">
                    <a href="<?= $refUrlSafe ?>" style="color:#00b207;font-weight:600;"><?= $refUrlSafe ?></a>
                  </p>
                  <p style="margin:0;font-size:13px;color:#6b7280;">Know people who would like to join OCSAPP? Share your link or code and help us grow the local network.</p>
                </td>
              </tr>
            </table>

            <?php if ($centralSafe): ?>
            <p style="margin:0 0 14px;color:#4b5563;font-size:14px;line-height:1.7;">In the meantime, see what OCSAPP has in store for you on <?= htmlspecialchars($central[2]) ?>.</p>
            <p style="margin:0 0 24px;"><a href="<?= $centralSafe ?>" style="<?= $btnStyle ?>">Explore <?= htmlspecialchars($central[2]) ?></a></p>
            <?php endif; ?>

            <p style="margin:0;color:#4b5563;font-size:14px;">We'll email you as soon as your access is ready.</p>
          </td>
        </tr>

        <!-- Footer (bilingual, CASL sender identification) -->
        <tr>
          <td style="background:#f9fafb;padding:24px 30px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">OCSAPP Inc. | Siège social : Laval, Québec (H7H) / Registered office: Laval, Quebec (H7H)</p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">Questions : <a href="mailto:info@ocsapp.ca" style="color:#9ca3af;">info@ocsapp.ca</a></p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">Vous recevez ce courriel à la suite de votre inscription à la liste d'attente OCSAPP. / You are receiving this email because you joined the OCSAPP waitlist.</p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">&copy; <?= $year ?> OCSAPP Inc. Tous droits réservés. / All rights reserved.</p>
            <?php if ($unsubUrlSafe): ?>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              <a href="<?= $unsubUrlSafe ?>" style="color:#9ca3af;">Se désabonner / Unsubscribe</a>
            </p>
            <?php endif; ?>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
