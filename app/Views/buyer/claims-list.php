<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = $currentLang === 'fr';
?>
<?php include __DIR__ . '/../components/header.php'; ?>

<div style="max-width:800px; margin:40px auto; padding:0 20px;">
    <h1><?= $fr ? 'Mes reclamations' : 'My Claims' ?></h1>

    <?php if (empty($claims)): ?>
        <p style="color:#6b7280;"><?= $fr ? 'Aucune reclamation.' : 'No claims filed yet.' ?></p>
    <?php else: ?>
        <?php foreach ($claims as $c): ?>
        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between;">
                <strong><?= htmlspecialchars($c['order_number'] ?? '-') ?></strong>
                <span style="padding:2px 10px; border-radius:12px; background:#f3f4f6; font-size:12px;"><?= htmlspecialchars(str_replace('_', ' ', $c['status'])) ?></span>
            </div>
            <p style="color:#6b7280; margin:6px 0;"><?= htmlspecialchars(str_replace('_', ' ', $c['claim_type'])) ?> - $<?= number_format($c['claimed_value'], 2) ?></p>
            <p style="font-size:13px; color:#9ca3af;"><?= $fr ? 'Depose le' : 'Filed' ?> <?= date('M j, Y', strtotime($c['created_at'])) ?></p>
            <?php if ($c['status'] === 'resolved'): ?>
                <p style="font-size:13px;"><?= $fr ? 'Resolution' : 'Resolution' ?>: <strong><?= htmlspecialchars(str_replace('_', ' ', $c['resolution'] ?? '')) ?></strong></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
