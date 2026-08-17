<?php
/**
 * Track B claim filing form (Returns Policy Sec. B1-B5) - Distribution
 * portal, covers both Approvisionnement requests and Distribution shipments.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'        => 'File a Claim',
        'back'              => 'Back',
        'request_label'     => 'Procurement Request',
        'shipment_label'    => 'Shipment',
        'window_notice'     => 'Businesses have 48 hours from delivery to report shortages, damage, or incorrect items (Sec B1).',
        'claim_type'        => 'Claim Type',
        'type_damaged'      => 'Damaged',
        'type_missing_item' => 'Missing Item / Shortage',
        'type_wrong_item'   => 'Wrong Item',
        'type_not_as_described' => 'Not as Described',
        'type_other'        => 'Other',
        'claimed_value'     => 'Claimed Value ($)',
        'preferred_resolution' => 'Preferred Resolution (if a replacement isn\'t available)',
        'resolution_note'   => 'Sec B5: a no-charge replacement is attempted first, on the next available run. If the original record can no longer be replaced, we fall back to your preference below.',
        'opt_cash'          => 'Cash refund / payout adjustment',
        'opt_credit_note'   => 'Credit note (+5% bonus to account balance)',
        'description'       => 'Description',
        'photos'            => 'Photos (optional)',
        'submit'            => 'Submit Claim',
        'error_generic'     => 'An error occurred.',
    ],
    'fr' => [
        'page_title'        => 'Déposer une réclamation',
        'back'              => 'Retour',
        'request_label'     => 'Demande d\'approvisionnement',
        'shipment_label'    => 'Envoi',
        'window_notice'     => 'Les entreprises disposent de 48 heures suivant la livraison pour signaler une pénurie, un dommage ou une erreur d\'article (Sec B1).',
        'claim_type'        => 'Type de réclamation',
        'type_damaged'      => 'Endommagé',
        'type_missing_item' => 'Article manquant / pénurie',
        'type_wrong_item'   => 'Mauvais article',
        'type_not_as_described' => 'Non conforme à la description',
        'type_other'        => 'Autre',
        'claimed_value'     => 'Valeur réclamée ($)',
        'preferred_resolution' => 'Résolution préférée (si un remplacement n\'est pas possible)',
        'resolution_note'   => 'Sec B5 : un remplacement sans frais est tenté en premier, dès la prochaine tournée disponible. Si l\'original ne peut plus être remplacé, votre préférence ci-dessous s\'applique.',
        'opt_cash'          => 'Remboursement en argent / ajustement de paiement',
        'opt_credit_note'   => 'Note de crédit (+5 % de bonus au solde du compte)',
        'description'       => 'Description',
        'photos'            => 'Photos (facultatif)',
        'submit'            => 'Soumettre la réclamation',
        'error_generic'     => 'Une erreur est survenue.',
    ],
];
$currentPage = 'claims';
$pageTitle = $translations[$currentLang]['page_title'] ?? $translations['en']['page_title'];
$_pageT = $translations[$currentLang] ?? $translations['en'];
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT);

$recordLabel = $type === 'shipment' ? $t['shipment_label'] : $t['request_label'];
$recordNumber = $type === 'shipment' ? ($record['shipment_number'] ?? '') : ($record['request_number'] ?? '');
$backUrl = $type === 'shipment'
    ? url('distribution/shipments/show?id=' . $record['id'])
    : url('distribution/requests/show?id=' . $record['id']);
?>

<div style="max-width:700px; margin:0 auto; padding:24px 20px;">
    <a href="<?= $backUrl ?>">&laquo; <?= $t['back'] ?></a>
    <h1 style="margin:16px 0;"><?= $t['page_title'] ?></h1>
    <p style="color:#6b7280;"><?= htmlspecialchars($recordLabel) ?> #<?= htmlspecialchars($recordNumber) ?></p>
    <p style="color:#6b7280; font-size:13px;"><?= $t['window_notice'] ?></p>

    <div id="formMessage" style="display:none; padding:12px; border-radius:8px; margin-bottom:16px;"></div>

    <form id="claimForm" enctype="multipart/form-data">
        <?= csrfMeta() ?>
        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
        <input type="hidden" name="record_id" value="<?= (int)$record['id'] ?>">

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $t['claim_type'] ?></label>
            <select name="claim_type" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="damaged"><?= $t['type_damaged'] ?></option>
                <option value="missing_item"><?= $t['type_missing_item'] ?></option>
                <option value="wrong_item"><?= $t['type_wrong_item'] ?></option>
                <option value="not_as_described"><?= $t['type_not_as_described'] ?></option>
                <option value="other"><?= $t['type_other'] ?></option>
            </select>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $t['claimed_value'] ?></label>
            <input type="number" name="claimed_value" step="0.01" min="0" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $t['preferred_resolution'] ?></label>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="display:flex; align-items:center; gap:8px; padding:12px; border:1px solid #d1d5db; border-radius:6px; cursor:pointer;">
                    <input type="radio" name="preferred_refund_method" value="cash" checked>
                    <span><?= $t['opt_cash'] ?></span>
                </label>
                <label style="display:flex; align-items:center; gap:8px; padding:12px; border:1px solid #16a34a; border-radius:6px; cursor:pointer; background:#f0fdf4;">
                    <input type="radio" name="preferred_refund_method" value="credit_note">
                    <span><?= $t['opt_credit_note'] ?></span>
                </label>
            </div>
            <p style="color:#6b7280; font-size:13px; margin-top:6px;"><?= $t['resolution_note'] ?></p>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $t['description'] ?></label>
            <textarea name="description" rows="4" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;"></textarea>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $t['photos'] ?></label>
            <input type="file" name="evidence[]" multiple accept="image/*">
        </div>

        <button type="submit" id="submitBtn" style="background:#16a34a; color:white; padding:12px 24px; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
            <?= $t['submit'] ?>
        </button>
    </form>
</div>

<script>
document.getElementById('claimForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const msg = document.getElementById('formMessage');
    btn.disabled = true;

    const formData = new FormData(this);
    formData.set('_csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

    try {
        const res = await fetch(<?= json_encode(url('distribution/claims/store')) ?>, { method: 'POST', body: formData });
        const data = await res.json();
        msg.style.display = 'block';
        msg.style.background = data.success ? '#f0fdf4' : '#fef2f2';
        msg.style.color = data.success ? '#166534' : '#991b1b';
        msg.textContent = data.message;
        if (data.success && data.redirect) {
            setTimeout(() => location.href = data.redirect, 1500);
        }
    } catch (err) {
        msg.style.display = 'block';
        msg.style.background = '#fef2f2';
        msg.textContent = <?= json_encode($t['error_generic']) ?>;
    }
    btn.disabled = false;
});
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
