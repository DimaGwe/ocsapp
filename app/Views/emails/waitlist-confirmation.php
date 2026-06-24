<?php
/**
 * Waitlist confirmation email - BILINGUAL (FR + EN).
 * Always renders both languages regardless of the visitor's chosen language.
 * Variables in scope: $firstName, $roleLabelFr, $roleLabelEn, $pos, $refUrl, $email
 */
$refUrlSafe = htmlspecialchars($refUrl ?? '', ENT_QUOTES);
$year       = date('Y');
?>
<!DOCTYPE html>
<html lang="fr-CA">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vous êtes sur la liste - OCSAPP / You're on the list - OCSAPP</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
  <tr>
    <td align="center" style="padding:40px 20px;">
      <table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);">

        <!-- ===================== FRENCH ===================== -->
        <!-- French Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:40px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">Vous êtes sur la liste !</h1>
          </td>
        </tr>

        <!-- French Body -->
        <tr>
          <td style="padding:36px 30px 32px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Bonjour <?= htmlspecialchars($firstName) ?>,</p>
            <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
              Merci de vous être inscrit à la liste d'attente OCSAPP en tant que <strong><?= htmlspecialchars($roleLabelFr) ?></strong>. Vous serez parmi les premiers à recevoir accès à la plateforme lors du lancement officiel.
            </p>

            <!-- Position badge FR -->
            <table role="presentation" style="width:100%;margin:0 0 28px;">
              <tr>
                <td align="center">
                  <div style="display:inline-block;background:linear-gradient(135deg,#00b207,#00d609);color:#fff;font-size:2rem;font-weight:900;padding:14px 36px;border-radius:12px;text-align:center;">
                    #<?= (int) $pos ?>
                    <div style="font-size:.85rem;font-weight:400;opacity:.85;">votre position</div>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Referral FR -->
            <table role="presentation" style="width:100%;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px;">
                  <p style="margin:0 0 8px;font-size:.75rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:1px;">Votre lien de parrainage</p>
                  <p style="margin:0 0 10px;font-size:14px;color:#111;word-break:break-all;">
                    <a href="<?= $refUrlSafe ?>" style="color:#00b207;font-weight:600;"><?= $refUrlSafe ?></a>
                  </p>
                  <p style="margin:0;font-size:.8rem;color:#6b7280;">Partagez ce lien avec vos contacts. Chaque inscription via votre lien est comptée !</p>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 8px;color:#4b5563;font-size:14px;">Nous vous contacterons par courriel dès que la plateforme sera ouverte.</p>
          </td>
        </tr>

        <!-- Language Divider -->
        <tr>
          <td style="padding:0 30px;">
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

        <!-- ===================== ENGLISH ===================== -->
        <!-- English Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:40px 30px;text-align:center;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">You're on the list!</h1>
          </td>
        </tr>

        <!-- English Body -->
        <tr>
          <td style="padding:36px 30px 40px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">Hi <?= htmlspecialchars($firstName) ?>,</p>
            <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
              Thank you for joining the OCSAPP waitlist as a <strong><?= htmlspecialchars($roleLabelEn) ?></strong>. You'll be among the first to get access when we officially launch.
            </p>

            <!-- Position badge EN -->
            <table role="presentation" style="width:100%;margin:0 0 28px;">
              <tr>
                <td align="center">
                  <div style="display:inline-block;background:linear-gradient(135deg,#00b207,#00d609);color:#fff;font-size:2rem;font-weight:900;padding:14px 36px;border-radius:12px;text-align:center;">
                    #<?= (int) $pos ?>
                    <div style="font-size:.85rem;font-weight:400;opacity:.85;">your position</div>
                  </div>
                </td>
              </tr>
            </table>

            <!-- Referral EN -->
            <table role="presentation" style="width:100%;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px;">
                  <p style="margin:0 0 8px;font-size:.75rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:1px;">Your referral link</p>
                  <p style="margin:0 0 10px;font-size:14px;color:#111;word-break:break-all;">
                    <a href="<?= $refUrlSafe ?>" style="color:#00b207;font-weight:600;"><?= $refUrlSafe ?></a>
                  </p>
                  <p style="margin:0;font-size:.8rem;color:#6b7280;">Share this link with your network. Every signup via your link is counted!</p>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 8px;color:#4b5563;font-size:14px;">We'll reach out by email as soon as the platform opens.</p>
          </td>
        </tr>

        <!-- Footer (bilingual) -->
        <tr>
          <td style="background:#f9fafb;padding:24px 30px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">OCSAPP | Grand Montréal, Québec / Greater Montreal, Quebec</p>
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">&copy; <?= $year ?> OCSAPP. Tous droits réservés. / All rights reserved.</p>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              <a href="<?= url('/unsubscribe') ?>?email=<?= urlencode($email) ?>" style="color:#9ca3af;">Se désabonner / Unsubscribe</a>
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
