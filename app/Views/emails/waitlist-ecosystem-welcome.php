<!DOCTYPE html>
<html lang="fr-CA">
<head>
  <meta charset="UTF-8">
  <title><?= $fr ? "Voici ce que vous venez de rejoindre - OCSAPP" : "Here's what you just joined - OCSAPP" ?></title>
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
            <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700;">
              <?= $fr ? 'Voici ce que vous venez de rejoindre' : "Here's what you just joined" ?>
            </h1>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:40px 30px;">
            <p style="margin:0 0 16px;color:#374151;font-size:16px;">
              <?= $fr ? "Bonjour {$firstName}," : "Hi {$firstName}," ?>
            </p>
            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              <?= $fr
                ? "Vous êtes sur la liste d'attente OCSAPP - mais avant le lancement, on voulait vous montrer exactement à quoi vous venez de vous joindre."
                : "You're on the OCSAPP waitlist - but before launch day comes, we wanted to show you exactly what you're stepping into." ?>
            </p>
            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              <?= $fr
                ? "OCSAPP, ce n'est pas seulement une appli pour magasiner. C'est une économie locale connectée - et selon votre inscription en tant que <strong>{$roleLabel}</strong>, vous allez y jouer un rôle bien précis :"
                : "OCSAPP isn't just an app for buying things. It's a connected local economy - and based on your signup as a <strong>{$roleLabel}</strong>, you're about to play a specific part in it:" ?>
            </p>

            <!-- Ecosystem list -->
            <table role="presentation" style="width:100%;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;margin-bottom:24px;">
              <tr>
                <td style="padding:24px;">
                  <table role="presentation" style="width:100%;border-collapse:collapse;">
                    <tr>
                      <td style="padding:0 0 14px;vertical-align:top;width:34px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#00b207;margin-top:6px;"></div>
                      </td>
                      <td style="padding:0 0 14px;color:#374151;font-size:14px;line-height:1.6;">
                        <?= $fr
                          ? "<strong>Les acheteurs</strong> trouvent des produits locaux et se les font livrer rapidement"
                          : "<strong>Buyers</strong> find local products and get them delivered fast" ?>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:0 0 14px;vertical-align:top;width:34px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#00b207;margin-top:6px;"></div>
                      </td>
                      <td style="padding:0 0 14px;color:#374151;font-size:14px;line-height:1.6;">
                        <?= $fr
                          ? "<strong>Les vendeurs</strong> ouvrent une boutique et rejoignent de nouveaux clients sans avoir à bâtir leur propre site web"
                          : "<strong>Sellers</strong> open a storefront and reach new customers without building their own website" ?>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:0 0 14px;vertical-align:top;width:34px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#00b207;margin-top:6px;"></div>
                      </td>
                      <td style="padding:0 0 14px;color:#374151;font-size:14px;line-height:1.6;">
                        <?= $fr
                          ? "<strong>Les fournisseurs</strong> approvisionnent la chaîne, en connectant leur inventaire directement aux vendeurs et à la place de marché"
                          : "<strong>Suppliers</strong> stock the shelves - connecting their inventory directly to sellers and the marketplace" ?>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:0 0 14px;vertical-align:top;width:34px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#00b207;margin-top:6px;"></div>
                      </td>
                      <td style="padding:0 0 14px;color:#374151;font-size:14px;line-height:1.6;">
                        <?= $fr
                          ? "<strong>Les livreurs</strong> font rouler le réseau de livraison qui relie le tout"
                          : "<strong>Drivers</strong> run the delivery network that ties it all together" ?>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:0;vertical-align:top;width:34px;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#00b207;margin-top:6px;"></div>
                      </td>
                      <td style="padding:0;color:#374151;font-size:14px;line-height:1.6;">
                        <?= $fr
                          ? "<strong>Les clients d'affaires</strong> utilisent OCSAPP Distribution pour commander à grande échelle pour leur entreprise"
                          : "<strong>Business clients</strong> use OCSAPP Distribution to order at scale for their company" ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 24px;color:#4b5563;font-size:15px;line-height:1.7;">
              <?= $fr
                ? "Ce qui rend ça différent : ce ne sont pas cinq applications séparées collées ensemble. C'est un seul écosystème - une commande d'un acheteur peut être approvisionnée par un fournisseur, mise en vente par un vendeur, et livrée par un livreur, le tout sans quitter la plateforme."
                : "Here's the part that makes it different: these aren't five separate apps bolted together. They're one ecosystem - a buyer's order can be stocked by a supplier, listed by a seller, and delivered by a driver, all without anyone leaving the platform." ?>
            </p>

            <!-- Role-specific callout -->
            <table role="presentation" style="width:100%;background:#ecfdf3;border:1.5px solid #b7ebc6;border-radius:10px;margin-bottom:28px;">
              <tr>
                <td style="padding:20px 22px;">
                  <p style="margin:0 0 6px;font-size:.72rem;font-weight:700;color:#00733f;text-transform:uppercase;letter-spacing:1px;">
                    <?= $fr ? "Ce que ça signifie pour vous" : "What this means for you" ?>
                  </p>
                  <p style="margin:0;color:#1f2937;font-size:14px;line-height:1.65;">
<?php switch ($role):
  case 'buyer': ?>
                    <?= $fr
                      ? "En tant qu'<strong>acheteur</strong>, vous serez parmi les premiers à magasiner localement sur OCSAPP au lancement - produits, livraison rapide, et un réseau qui fait vivre votre communauté."
                      : "As a <strong>buyer</strong>, you'll be among the first to shop local on OCSAPP at launch - products, fast delivery, and a network that supports your local community." ?>
<?php break; case 'seller': ?>
                    <?= $fr
                      ? "En tant que <strong>vendeur</strong>, vous pourrez ouvrir votre boutique sur OCSAPP dès le lancement - une vitrine prête à l'emploi, sans frais de démarrage à payer."
                      : "As a <strong>seller</strong>, you'll be able to open your storefront on OCSAPP right at launch - a ready-to-go shop front, with no setup costs to get started." ?>
<?php break; case 'supplier': ?>
                    <?= $fr
                      ? "En tant que <strong>fournisseur</strong>, vous serez invité à connecter votre inventaire au réseau OCSAPP dès le lancement - une nouvelle voie directe vers les vendeurs et les acheteurs locaux."
                      : "As a <strong>supplier</strong>, you'll be invited to connect your inventory to the OCSAPP network right at launch - a new, direct path to local sellers and buyers." ?>
<?php break; case 'driver': ?>
                    <?= $fr
                      ? "En tant que <strong>livreur</strong>, vous serez parmi les premiers invités à faire partie du réseau de livraison OCSAPP - des trajets flexibles, dans votre secteur."
                      : "As a <strong>driver</strong>, you'll be among the first invited to join the OCSAPP delivery network - flexible routes, right in your area." ?>
<?php break; case 'business': ?>
                    <?= $fr
                      ? "En tant que <strong>client d'affaires</strong>, vous serez parmi les premiers à découvrir OCSAPP Distribution - une façon simple de commander à grande échelle auprès de fournisseurs locaux."
                      : "As a <strong>business client</strong>, you'll be among the first to explore OCSAPP Distribution - a simple way to order at scale from local suppliers." ?>
<?php break; default: ?>
                    <?= $fr
                      ? "Vous serez parmi les premiers à découvrir comment OCSAPP fonctionne pour vous, dès le lancement."
                      : "You'll be among the first to see how OCSAPP works for you, right from launch." ?>
<?php endswitch; ?>
                  </p>
                </td>
              </tr>
            </table>

            <table role="presentation" style="width:100%;margin:0 0 8px;">
              <tr>
                <td align="center">
                  <a href="<?= url('/') ?>" style="display:inline-block;padding:15px 40px;background:#00b207;color:#fff;font-size:1rem;font-weight:700;border-radius:50px;text-decoration:none;">
                    <?= $fr ? "Voir comment tout se connecte" : "See how it all connects" ?>
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:24px 0 0;color:#4b5563;font-size:14px;line-height:1.6;text-align:center;">
              <?= $fr
                ? "On vous tient au courant à mesure que le lancement approche."
                : "We'll keep you posted as launch gets closer." ?>
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f9fafb;padding:24px 30px;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;">OCSAPP | <?= $fr ? 'Grand Montréal, Québec' : 'Greater Montreal, Quebec' ?></p>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              <a href="<?= url('/unsubscribe') ?>?email=<?= urlencode($email) ?>" style="color:#9ca3af;">
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
