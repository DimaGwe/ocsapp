<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = $currentLang === 'fr';
?>
<?php include __DIR__ . '/../components/header.php'; ?>

<div style="max-width:700px; margin:40px auto; padding:0 20px;">
    <a href="<?= url('account/orders/detail?id=' . $order['id']) ?>">&laquo; <?= $fr ? 'Retour a la commande' : 'Back to order' ?></a>
    <h1 style="margin:16px 0;"><?= $fr ? 'Faire une reclamation' : 'File a Claim' ?></h1>
    <p style="color:#6b7280;"><?= $fr ? 'Commande' : 'Order' ?> #<?= htmlspecialchars($order['order_number']) ?> - <?= htmlspecialchars($order['shop_name']) ?></p>

    <div id="formMessage" style="display:none; padding:12px; border-radius:8px; margin-bottom:16px;"></div>

    <form id="claimForm" enctype="multipart/form-data">
        <?= csrfMeta() ?>
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $fr ? 'Type de reclamation' : 'Claim Type' ?></label>
            <select name="claim_type" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="damaged"><?= $fr ? 'Endommage' : 'Damaged' ?></option>
                <option value="missing_item"><?= $fr ? 'Article manquant' : 'Missing Item' ?></option>
                <option value="wrong_item"><?= $fr ? 'Mauvais article' : 'Wrong Item' ?></option>
                <option value="not_as_described"><?= $fr ? 'Non conforme a la description' : 'Not as Described' ?></option>
                <option value="buyer_change_of_mind"><?= $fr ? "Changement d'avis" : 'Change of Mind' ?></option>
                <option value="other"><?= $fr ? 'Autre' : 'Other' ?></option>
            </select>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $fr ? 'Valeur reclamee ($)' : 'Claimed Value ($)' ?></label>
            <input type="number" name="claimed_value" step="0.01" min="0" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $fr ? 'Description' : 'Description' ?></label>
            <textarea name="description" rows="4" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;"></textarea>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600;"><?= $fr ? 'Photos (optionnel)' : 'Photos (optional)' ?></label>
            <input type="file" name="evidence[]" multiple accept="image/*">
        </div>

        <button type="submit" id="submitBtn" style="background:#16a34a; color:white; padding:12px 24px; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
            <?= $fr ? 'Soumettre la reclamation' : 'Submit Claim' ?>
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
        const res = await fetch(<?= json_encode(url('account/orders/claim')) ?>, { method: 'POST', body: formData });
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
        msg.textContent = <?= json_encode($fr ? 'Une erreur est survenue.' : 'An error occurred.') ?>;
    }
    btn.disabled = false;
});
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>
