<?php
/**
 * Home Profile (/account/home, HomeProfileController::index)
 * Guardian: invite teens (13 to 17), card that pays member orders, members' orders (supervision).
 * Member: who manages their Home Profile and what that means.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
require __DIR__ . '/partials/account-helpers.php';
$user = user() ?? [];
$accountActive = 'home';
$isMember = !empty($isMember);
$members = $members ?? [];
$memberOrders = array_map('acct_plain_row', $memberOrders ?? []);
$card = $card ?? [];
$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');

$acctTitle   = $fr ? 'Profil Maison' : 'Home Profile';
$acctHeading = $acctTitle;
$acctCrumb   = [$acctTitle, 'fa-house-user'];
$acctSub     = $isMember
    ? ($fr ? 'Votre compte fait partie du Profil Maison de votre parent ou tuteur.' : "Your account is part of your parent's or guardian's Home Profile.")
    : ($fr ? 'Étendez votre compte à vos jeunes de 13 à 17 ans, avec supervision.' : 'Extend your account to your kids aged 13 to 17, with supervision.');
$acctAction  = '';
require __DIR__ . '/partials/account-top.php';

$maxBirth = (new DateTime('today'))->modify('-13 years')->format('Y-m-d');
$minBirth = (new DateTime('today'))->modify('-18 years')->modify('+1 day')->format('Y-m-d');
?>
<?php if ($isMember): ?>
                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-house-user acct-h-ico"></i> <?= $fr ? 'Votre Profil Maison' : 'Your Home Profile' ?></h2>
                        <?php if (!empty($guardian)): ?>
                            <p><?= $fr ? 'Géré par' : 'Managed by' ?> <strong><?= htmlspecialchars(trim(html_entity_decode($guardian['guardian_first_name'] . ' ' . $guardian['guardian_last_name'], ENT_QUOTES))) ?></strong>.</p>
                        <?php endif; ?>
                        <ul class="acct-muted" style="margin:10px 0 0 18px;line-height:1.8;">
                            <li><?= $fr ? 'Vos commandes sont payées avec la carte de votre parent ou tuteur.' : "Your orders are paid with your parent's or guardian's card." ?></li>
                            <li><?= $fr ? 'Votre parent ou tuteur reçoit un avis à chaque commande et peut en suivre la livraison.' : 'Your parent or guardian is notified of every order and can track its delivery.' ?></li>
                            <li><?= $fr ? 'Les produits réservés aux 18 ans et plus ne sont pas offerts sur votre compte.' : 'Products for ages 18+ are not available on your account.' ?></li>
                            <li><?= $fr ? 'À 18 ans, vous pourrez créer votre propre compte OCSAPP.' : 'At 18, you can create your own OCSAPP account.' ?></li>
                        </ul>
                    </section>
<?php else: ?>
    <?php if (empty($canBeGuardian)): ?>
                    <section class="acct-card acct-panel">
                        <p class="acct-muted"><?= $fr ? 'Votre compte doit être actif et vérifié pour gérer un Profil Maison.' : 'Your account must be active and verified to manage a Home Profile.' ?></p>
                    </section>
    <?php else: ?>
                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-circle-info acct-h-ico"></i> <?= $fr ? 'Comment ça marche' : 'How it works' ?></h2>
                        <ul class="acct-muted" style="margin:0 0 0 18px;line-height:1.8;">
                            <li><?= $fr ? 'Invitez un jeune de 13 à 17 ans; il crée son compte à partir du lien reçu par courriel.' : 'Invite a teen aged 13 to 17; they create their account from the emailed link.' ?></li>
                            <li><?= $fr ? 'Ses commandes sont payées avec votre carte ci-dessous, et vous en êtes responsable.' : 'Their orders are paid with your card below, and you are responsible for them.' ?></li>
                            <li><?= $fr ? 'Vous recevez un courriel à chaque commande et pouvez suivre la livraison.' : 'You get an email for every order and can track the delivery.' ?></li>
                            <li><?= $fr ? 'Les produits réservés aux 18 ans et plus sont bloqués sur son compte.' : 'Products for ages 18+ are blocked on their account.' ?></li>
                        </ul>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-credit-card acct-h-ico"></i> <?= $fr ? 'Carte pour les commandes des membres' : 'Card for member orders' ?></h2>
                        <?php if (!empty($card['card_last4'])): ?>
                            <p><?= htmlspecialchars(ucfirst((string) $card['card_brand'])) ?> •••• <?= htmlspecialchars($card['card_last4']) ?></p>
                        <?php else: ?>
                            <p class="acct-muted"><?= $fr ? 'Aucune carte enregistrée. Vos membres ne peuvent pas commander tant que vous n’en ajoutez pas une.' : "No card saved. Your members can't order until you add one." ?></p>
                        <?php endif; ?>
                        <?php if (!empty($stripeKey)): ?>
                            <div id="hpCardElement" style="padding:12px;border:1px solid #d1d5db;border-radius:8px;margin:12px 0;max-width:460px;"></div>
                            <button type="button" id="hpSaveCard" class="acct-btn acct-btn-primary"><?= !empty($card['card_last4']) ? ($fr ? 'Remplacer la carte' : 'Replace card') : ($fr ? 'Enregistrer la carte' : 'Save card') ?></button>
                            <div id="hpCardMsg" class="acct-flash" hidden role="status" style="margin-top:12px;"></div>
                        <?php else: ?>
                            <p class="acct-dim"><?= $fr ? 'Le paiement par carte n’est pas encore configuré.' : 'Card payments are not configured yet.' ?></p>
                        <?php endif; ?>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-user-plus acct-h-ico"></i> <?= $fr ? 'Inviter un membre' : 'Invite a member' ?></h2>
                        <form class="acct-form" method="POST" action="<?= url('account/home/invite') ?>">
                            <?= csrfField() ?>
                            <div class="acct-form-row">
                                <label><?= $fr ? 'Prénom' : 'First name' ?><input type="text" name="first_name" required maxlength="100" autocomplete="off"></label>
                                <label><?= $fr ? 'Courriel du jeune' : "Teen's email" ?><input type="email" name="email" required maxlength="255" autocomplete="off"></label>
                            </div>
                            <label><?= $fr ? 'Date de naissance (13 à 17 ans)' : 'Date of birth (13 to 17)' ?><input type="date" name="birth_date" required min="<?= $minBirth ?>" max="<?= $maxBirth ?>"></label>
                            <p class="acct-dim"><?= $fr
                                ? "En envoyant cette invitation, vous confirmez être le parent ou le tuteur de ce jeune et consentez à la création de son compte et à la collecte de ses renseignements personnels selon notre Politique de confidentialité."
                                : 'By sending this invite, you confirm you are this teen’s parent or guardian and consent to the creation of their account and the collection of their personal information under our Privacy Policy.' ?></p>
                            <button type="submit" class="acct-btn acct-btn-primary"><i class="fas fa-paper-plane"></i> <?= $fr ? 'Envoyer l’invitation' : 'Send invite' ?></button>
                        </form>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-users acct-h-ico"></i> <?= $fr ? 'Membres' : 'Members' ?></h2>
                        <?php if (empty($members)): ?>
                            <p class="acct-muted"><?= $fr ? 'Aucun membre pour le moment.' : 'No members yet.' ?></p>
                        <?php else: ?>
                            <div class="acct-order-list">
                            <?php foreach ($members as $m): ?>
                                <?php $age = \App\Helpers\HomeProfileHelper::ageOn((string) $m['birth_date']); ?>
                                <div class="acct-order">
                                    <div class="acct-order-main">
                                        <strong><?= htmlspecialchars($m['first_name']) ?><?= $age !== null ? ' (' . $age . ($fr ? ' ans' : '') . ')' : '' ?></strong>
                                        <span><?= htmlspecialchars($m['member_email'] ?? $m['invite_email']) ?></span>
                                    </div>
                                    <span class="acct-badge acct-badge-<?= $m['status'] === 'active' ? 'delivered' : 'pending' ?>"><?= $m['status'] === 'active' ? ($fr ? 'Actif' : 'Active') : ($fr ? 'Invitation envoyée' : 'Invite sent') ?></span>
                                    <form method="POST" action="<?= url('account/home/remove') ?>" onsubmit="return confirm(<?= htmlspecialchars(json_encode($fr ? 'Retirer ce membre ? Son compte sera suspendu.' : 'Remove this member? Their account will be suspended.')) ?>);">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="link_id" value="<?= (int) $m['id'] ?>">
                                        <button type="submit" class="acct-btn acct-btn-danger acct-btn-sm"><?= $m['status'] === 'active' ? ($fr ? 'Retirer' : 'Remove') : ($fr ? 'Annuler' : 'Cancel') ?></button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="acct-card acct-panel">
                        <h2><i class="fas fa-box acct-h-ico"></i> <?= $fr ? 'Commandes des membres' : 'Member orders' ?></h2>
                        <?php if (empty($memberOrders)): ?>
                            <p class="acct-muted"><?= $fr ? 'Aucune commande pour le moment.' : 'No orders yet.' ?></p>
                        <?php else: ?>
                            <div class="acct-order-list">
                            <?php foreach ($memberOrders as $order): ?>
                                <a class="acct-order" href="<?= url('account/home/order') ?>?id=<?= (int) $order['id'] ?>">
                                    <div class="acct-order-main">
                                        <strong><?= htmlspecialchars($order['member_first_name']) ?> · <?= $fr ? 'Commande ' : 'Order ' ?>#<?= htmlspecialchars($order['order_number']) ?></strong>
                                        <span><?= htmlspecialchars($order['shop_name'] ?? ($fr ? 'Commerce' : 'Shop')) ?> · <?= acct_date($order['created_at'], $fr) ?></span>
                                    </div>
                                    <span class="acct-badge acct-badge-<?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars(acct_status((string) $order['status'], $fr)) ?></span>
                                    <span class="acct-order-total"><?= acct_money($order['total'], $fr) ?></span>
                                    <i class="fas fa-chevron-right acct-order-go"></i>
                                </a>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>

        <?php if (!empty($stripeKey)): ?>
<script src="https://js.stripe.com/v3/"></script>
<script>
(function () {
    var stripe = Stripe(<?= json_encode($stripeKey) ?>);
    var card = stripe.elements().create('card');
    card.mount('#hpCardElement');
    var CSRF = <?= json_encode(csrfToken()) ?>, CSRF_NAME = <?= json_encode($csrfName) ?>;
    var btn = document.getElementById('hpSaveCard'), msg = document.getElementById('hpCardMsg');
    function show(text, ok) { msg.hidden = false; msg.textContent = text; msg.className = 'acct-flash ' + (ok ? 'acct-flash-ok' : 'acct-flash-err'); }
    function post(url, data) {
        var body = new URLSearchParams(data); body.append(CSRF_NAME, CSRF);
        return fetch(url, { method: 'POST', body: body, credentials: 'same-origin' }).then(function (r) { return r.json(); });
    }
    btn.addEventListener('click', function () {
        btn.disabled = true;
        post(<?= json_encode(url('account/home/card/intent')) ?>, {}).then(function (d) {
            if (!d.success) throw new Error(d.message || '');
            return stripe.confirmCardSetup(d.client_secret, { payment_method: { card: card } });
        }).then(function (res) {
            if (res.error) throw new Error(res.error.message);
            return post(<?= json_encode(url('account/home/card/save')) ?>, { setup_intent_id: res.setupIntent.id });
        }).then(function (d) {
            if (!d.success) throw new Error(d.message || '');
            show(<?= json_encode($fr ? 'Carte enregistrée.' : 'Card saved.') ?>, true);
            setTimeout(function () { location.reload(); }, 1000);
        }).catch(function (e) {
            show(e.message || <?= json_encode($fr ? 'Une erreur est survenue.' : 'Something went wrong.') ?>, false);
            btn.disabled = false;
        });
    });
})();
</script>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/partials/account-bottom.php'; ?>
