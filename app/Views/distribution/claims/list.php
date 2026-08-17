<?php
/**
 * Track B claim list (Returns Policy Sec. B1-B5) - business's own claims
 * against Approvisionnement requests and Distribution shipments.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'   => 'My Claims',
        'col_ref'      => 'Reference',
        'col_type'     => 'Type',
        'col_value'    => 'Claimed',
        'col_status'   => 'Status',
        'col_resolution' => 'Resolution',
        'col_filed'    => 'Filed',
        'empty'        => 'No claims filed yet.',
        'status_submitted' => 'Submitted',
        'status_under_review' => 'Under Review',
        'status_resolved' => 'Resolved',
        'status_window_expired' => 'Window Expired',
    ],
    'fr' => [
        'page_title'   => 'Mes réclamations',
        'col_ref'      => 'Référence',
        'col_type'     => 'Type',
        'col_value'    => 'Réclamé',
        'col_status'   => 'Statut',
        'col_resolution' => 'Résolution',
        'col_filed'    => 'Déposée',
        'empty'        => 'Aucune réclamation déposée pour le moment.',
        'status_submitted' => 'Soumise',
        'status_under_review' => 'En révision',
        'status_resolved' => 'Résolue',
        'status_window_expired' => 'Délai expiré',
    ],
];
$currentPage = 'claims';
$pageTitle = $translations[$currentLang]['page_title'] ?? $translations['en']['page_title'];
$_pageT = $translations[$currentLang] ?? $translations['en'];
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT);
?>

<div style="max-width:900px; margin:0 auto; padding:24px 20px;">
    <h1 style="margin-bottom:16px;"><?= $t['page_title'] ?></h1>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb; text-align:left;">
                <th style="padding:10px;"><?= $t['col_ref'] ?></th>
                <th style="padding:10px;"><?= $t['col_type'] ?></th>
                <th style="padding:10px;"><?= $t['col_value'] ?></th>
                <th style="padding:10px;"><?= $t['col_status'] ?></th>
                <th style="padding:10px;"><?= $t['col_resolution'] ?></th>
                <th style="padding:10px;"><?= $t['col_filed'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($claims as $c): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px;"><?= htmlspecialchars($c['request_number'] ?? $c['shipment_number'] ?? ('#' . $c['id'])) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars(str_replace('_', ' ', $c['claim_type'])) ?></td>
                <td style="padding:10px;">$<?= number_format($c['claimed_value'], 2) ?></td>
                <td style="padding:10px;"><span class="badge"><?= htmlspecialchars($t['status_' . $c['status']] ?? str_replace('_', ' ', $c['status'])) ?></span></td>
                <td style="padding:10px;"><?= htmlspecialchars(str_replace('_', ' ', $c['resolution'] ?? '-')) ?></td>
                <td style="padding:10px;"><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($claims)): ?>
            <tr><td colspan="6" style="padding:20px; text-align:center; color:#6b7280;"><?= $t['empty'] ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
