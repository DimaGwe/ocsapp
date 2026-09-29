<?php
/**
 * Buyer addresses (/account/addresses, AccountController::addresses)
 * Updated 2026-09-28: Marché Central theme via the shared account partials, bilingual EN/FR (was
 * English, including the modal and JS messages). Same AJAX endpoints and form field names.
 * The controller's JSON messages are English, so the page shows its own bilingual text.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$addresses = $addresses ?? [];
require __DIR__ . '/partials/account-helpers.php';
$addresses = array_map('acct_plain_row', $addresses);
$user = user() ?? [];
$accountActive = 'addresses';
$typeLabels = ['home' => [$fr ? 'Domicile' : 'Home', 'fa-house'], 'work' => [$fr ? 'Travail' : 'Work', 'fa-briefcase'], 'other' => [$fr ? 'Autre' : 'Other', 'fa-location-dot']];
$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');

$acctTitle   = $fr ? 'Mes adresses' : 'My addresses';
$acctHeading = $acctTitle;
$acctCrumb   = [$acctTitle, 'fa-location-dot'];
$acctSub     = $fr ? 'Vos adresses de livraison, pour un paiement plus rapide.' : 'Your delivery addresses, for a faster checkout.';
$acctAction  = '<button type="button" class="acct-btn acct-btn-primary" data-open-address><i class="fas fa-plus"></i> ' . ($fr ? 'Ajouter une adresse' : 'Add address') . '</button>';
require __DIR__ . '/partials/account-top.php';
?>
                    <div id="flashMsg" class="acct-flash" hidden role="status"></div>

                    <?php if (empty($addresses)): ?>
                        <section class="acct-card acct-panel">
                            <div class="acct-empty">
                                <i class="fas fa-location-dot"></i>
                                <p><?= $fr ? "Aucune adresse enregistrée. Ajoutez votre adresse de livraison pour passer commande plus vite." : 'No addresses saved. Add your delivery address to speed up checkout.' ?></p>
                                <button type="button" class="acct-btn acct-btn-primary" data-open-address><i class="fas fa-plus"></i> <?= $fr ? 'Ajouter une adresse' : 'Add address' ?></button>
                            </div>
                        </section>
                    <?php else: ?>
                        <div class="acct-addr-grid">
                            <?php foreach ($addresses as $addr): ?>
                                <?php [$typeLabel, $typeIcon] = $typeLabels[$addr['type'] ?? 'home'] ?? $typeLabels['other']; ?>
                                <article class="acct-card acct-addr<?= !empty($addr['is_default']) ? ' is-default' : '' ?>">
                                    <div class="acct-addr-top">
                                        <span class="acct-addr-type"><i class="fas <?= $typeIcon ?>"></i> <?= $typeLabel ?></span>
                                        <?php if (!empty($addr['is_default'])): ?><span class="acct-badge acct-badge-delivered"><?= $fr ? 'Par défaut' : 'Default' ?></span><?php endif; ?>
                                    </div>
                                    <strong><?= htmlspecialchars($addr['name'] ?? '') ?></strong>
                                    <p>
                                        <?= htmlspecialchars($addr['address_line_1'] ?? '') ?>
                                        <?php if (!empty($addr['address_line_2'])): ?><br><?= htmlspecialchars($addr['address_line_2']) ?><?php endif; ?>
                                        <br><?= htmlspecialchars(trim(($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['postal_code'] ?? ''), ', ')) ?>
                                    </p>
                                    <?php if (!empty($addr['phone'])): ?><p class="acct-dim"><i class="fas fa-phone"></i> <?= htmlspecialchars($addr['phone']) ?></p><?php endif; ?>
                                    <div class="acct-addr-actions">
                                        <button type="button" class="acct-btn acct-btn-ghost acct-btn-sm" data-edit-address='<?= htmlspecialchars(json_encode($addr), ENT_QUOTES) ?>'><i class="fas fa-pen"></i> <?= $fr ? 'Modifier' : 'Edit' ?></button>
                                        <?php if (empty($addr['is_default'])): ?>
                                            <button type="button" class="acct-btn acct-btn-ghost acct-btn-sm" data-default-address="<?= (int) $addr['id'] ?>"><?= $fr ? 'Définir par défaut' : 'Set as default' ?></button>
                                        <?php endif; ?>
                                        <button type="button" class="acct-btn acct-btn-danger acct-btn-sm" data-delete-address="<?= (int) $addr['id'] ?>" aria-label="<?= $fr ? 'Supprimer' : 'Delete' ?>"><i class="fas fa-trash"></i></button>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
<?php require __DIR__ . '/partials/account-bottom.php'; ?>

<div class="acct-modal-overlay" id="addressModal" aria-hidden="true">
    <div class="acct-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="acct-modal-head">
            <h2 id="modalTitle"><?= $fr ? 'Ajouter une adresse' : 'Add address' ?></h2>
            <button type="button" class="acct-modal-close" data-close-address aria-label="<?= $fr ? 'Fermer' : 'Close' ?>">&times;</button>
        </div>
        <form id="addressForm" class="acct-form">
            <input type="hidden" name="<?= htmlspecialchars($csrfName) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" id="addressId" name="id" value="">
            <label><?= $fr ? 'Nom complet' : 'Full name' ?> *<input type="text" name="name" id="fieldName" required autocomplete="name"></label>
            <label><?= $fr ? "Type d'adresse" : 'Address type' ?>
                <select name="type" id="fieldType">
                    <?php foreach ($typeLabels as $v => [$l]): ?><option value="<?= $v ?>"><?= $l ?></option><?php endforeach; ?>
                </select>
            </label>
            <label><?= $fr ? 'Adresse' : 'Address line 1' ?> *<input type="text" name="address_line_1" id="fieldAddr1" required autocomplete="address-line1"></label>
            <label><?= $fr ? 'Appartement, bureau (facultatif)' : 'Address line 2 (optional)' ?><input type="text" name="address_line_2" id="fieldAddr2" autocomplete="address-line2"></label>
            <div class="acct-form-row">
                <label><?= $fr ? 'Ville' : 'City' ?> *<input type="text" name="city" id="fieldCity" required autocomplete="address-level2"></label>
                <label><?= $fr ? 'Province' : 'Province' ?><input type="text" name="state" id="fieldState" autocomplete="address-level1" placeholder="QC"></label>
            </div>
            <div class="acct-form-row">
                <label><?= $fr ? 'Code postal' : 'Postal code' ?><input type="text" name="postal_code" id="fieldPostal" autocomplete="postal-code"></label>
                <label><?= $fr ? 'Téléphone' : 'Phone' ?> *<input type="tel" name="phone" id="fieldPhone" required autocomplete="tel"></label>
            </div>
            <label class="acct-check"><input type="checkbox" name="is_default" id="fieldDefault" value="1"> <?= $fr ? 'Utiliser comme adresse par défaut' : 'Set as default address' ?></label>
            <button type="submit" class="acct-btn acct-btn-primary acct-btn-block"><?= $fr ? "Enregistrer l'adresse" : 'Save address' ?></button>
        </form>
    </div>
</div>

<script>
(function () {
    var CSRF = <?= json_encode(csrfToken()) ?>, CSRF_NAME = <?= json_encode($csrfName) ?>;
    var T = <?= json_encode($fr ? [
        'add' => 'Ajouter une adresse', 'edit' => "Modifier l'adresse", 'saved' => 'Adresse enregistrée.',
        'deleted' => 'Adresse supprimée.', 'confirmDelete' => 'Supprimer cette adresse ?',
        'error' => "L'adresse n'a pas pu être enregistrée.", 'network' => 'Erreur réseau. Veuillez réessayer.',
        'required' => 'Veuillez remplir tous les champs obligatoires.', 'login' => 'Veuillez vous connecter.',
        'token' => 'Votre session a expiré. Rechargez la page.', 'notFound' => 'Adresse introuvable.',
    ] : [
        'add' => 'Add address', 'edit' => 'Edit address', 'saved' => 'Address saved.',
        'deleted' => 'Address deleted.', 'confirmDelete' => 'Delete this address?',
        'error' => "The address couldn't be saved.", 'network' => 'Network error. Please try again.',
        'required' => 'Please fill in all required fields.', 'login' => 'Please log in.',
        'token' => 'Your session expired. Reload the page.', 'notFound' => 'Address not found.',
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>;
    // Controller messages are English; map the known ones
    var serverMsg = function (m) {
        return ({ 'Please fill in all required fields': T.required, 'Please login': T.login, 'Invalid token': T.token, 'Address not found': T.notFound })[m] || T.error;
    };
    var modal = document.getElementById('addressModal'), form = document.getElementById('addressForm');

    function flash(msg, ok) {
        var el = document.getElementById('flashMsg');
        el.textContent = msg;
        el.className = 'acct-flash ' + (ok ? 'acct-flash-ok' : 'acct-flash-err');
        el.hidden = false;
        setTimeout(function () { el.hidden = true; }, 4000);
    }
    function open(addr) {
        form.reset();
        document.getElementById('modalTitle').textContent = addr ? T.edit : T.add;
        document.getElementById('addressId').value = addr ? addr.id : '';
        if (addr) {
            [['fieldName', 'name'], ['fieldAddr1', 'address_line_1'], ['fieldAddr2', 'address_line_2'], ['fieldCity', 'city'],
             ['fieldState', 'state'], ['fieldPostal', 'postal_code'], ['fieldPhone', 'phone']].forEach(function (p) {
                document.getElementById(p[0]).value = addr[p[1]] || '';
            });
            document.getElementById('fieldType').value = addr.type || 'home';
            document.getElementById('fieldDefault').checked = addr.is_default == 1;
        }
        modal.classList.add('active'); modal.setAttribute('aria-hidden', 'false');
        document.getElementById('fieldName').focus();
    }
    function close() { modal.classList.remove('active'); modal.setAttribute('aria-hidden', 'true'); }
    function post(url, data) {
        return fetch(url, { method: 'POST', body: data }).then(function (r) { return r.json(); });
    }
    function idForm(id) { var d = new FormData(); d.append('id', id); d.append(CSRF_NAME, CSRF); return d; }

    document.querySelectorAll('[data-open-address]').forEach(function (b) { b.addEventListener('click', function () { open(null); }); });
    document.querySelectorAll('[data-edit-address]').forEach(function (b) { b.addEventListener('click', function () { open(JSON.parse(b.dataset.editAddress)); }); });
    document.querySelector('[data-close-address]').addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var data = new FormData(form);
        var url = data.get('id') !== '' ? <?= json_encode(url('account/addresses/update')) ?> : <?= json_encode(url('account/addresses/store')) ?>;
        post(url, data).then(function (json) {
            if (json.success) { flash(T.saved, true); close(); setTimeout(function () { location.reload(); }, 900); }
            else { flash(serverMsg(json.message), false); }
        }).catch(function () { flash(T.network, false); });
    });
    document.querySelectorAll('[data-default-address]').forEach(function (b) {
        b.addEventListener('click', function () {
            post(<?= json_encode(url('account/addresses/set-default')) ?>, idForm(b.dataset.defaultAddress)).then(function (json) {
                if (json.success) { location.reload(); } else { flash(serverMsg(json.message), false); }
            }).catch(function () { flash(T.network, false); });
        });
    });
    document.querySelectorAll('[data-delete-address]').forEach(function (b) {
        b.addEventListener('click', function () {
            if (!confirm(T.confirmDelete)) return;
            post(<?= json_encode(url('account/addresses/delete')) ?>, idForm(b.dataset.deleteAddress)).then(function (json) {
                if (json.success) { location.reload(); } else { flash(serverMsg(json.message), false); }
            }).catch(function () { flash(T.network, false); });
        });
    });
})();
</script>
</body>
</html>
