<!DOCTYPE html>
<html lang="fr-CA">
<head>
  <meta charset="UTF-8">
  <title><?= $fr ? 'OCSAPP est maintenant ouvert !' : 'OCSAPP is now live!' ?></title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;">
<table role="presentation" style="width:100%;border-collapse:collapse;background:#f5f5f5;">
  <tr>
    <td align="center" style="padding:40px 20px;">
      <table role="presentation" style="max-width:600px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#00b207 0%,#009206 100%);padding:40px 30px;text-align:center;border-radius:12px 12px 0 0;">
            <img src="https://ocsapp.ca/assets/images/logo.png" alt="OCSAPP" style="max-width:160px;height:auto;margin-bottom:16px;">
            <h1 style="margin:0;color:#fff;font-size:26px;font-weight:700;">
              <?= $fr ? 'C\'est l\'heure - OCSAPP est ouvert !' : 'It\'s time - OCSAPP is live!' ?>
            </h1>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:40px 30px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">
              <?= $fr ? "Bonjour {$firstName}," : "Hi {$firstName}," ?>
            </p>
            <p style="margin:0 0 20px;color:#4b5563;font-size:15px;line-height:1.7;">
              <?= $fr
                ? "La bonne nouvelle que vous attendiez : OCSAPP est maintenant officiellement ouvert ! Vous avez rejoint notre liste d'attente en tant que <strong>{$roleLabel}</strong> et votre accès est prêt."
                : "The news you've been waiting for: OCSAPP is now officially open! You joined our waitlist as a <strong>{$roleLabel}</strong> and your access is ready." ?>
            </p>

            <table role="presentation" style="width:100%;margin:0 0 28px;">
              <tr>
                <td align="center">
                  <a href="<?= url('/') ?>" style="display:inline-block;padding:15px 40px;background:#00b207;color:#fff;font-size:1rem;font-weight:700;border-radius:50px;text-decoration:none;">
                    <?= $fr ? 'Accéder à OCSAPP' : 'Go to OCSAPP' ?>
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 8px;color:#4b5563;font-size:14px;">
              <?= $fr
                ? 'Merci d\'avoir fait partie des premiers membres de la communauté OCSAPP.'
                : 'Thank you for being one of the first members of the OCSAPP community.' ?>
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f9fafb;padding:24px 30px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">OCSAPP | <?= $fr ? 'Grand Montréal, Québec' : 'Greater Montreal, Quebec' ?></p>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              <a href="<?= url('/unsubscribe') ?>?email=<?= urlencode($entry['email']) ?>" style="color:#9ca3af;">
                <?= $fr ? 'Se désabonner' : 'Unsubscribe' ?>
              </a>
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
