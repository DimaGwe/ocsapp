<?php
/**
 * OCS Admin Legal Content Management
 * File: app/Views/admin/legal/index.php
 */

$pageTitle = 'Legal Content Management';
$currentPage = 'legal';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'legal_content' => 'Legal Content Management',
        'manage_legal_pages' => 'Manage Terms, Privacy Policy, and other legal pages',
        'page_type' => 'Page Type',
        'language' => 'Language',
        'title' => 'Title',
        'version' => 'Version',
        'last_updated' => 'Last Updated',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'preview' => 'Preview',
        'no_pages_found' => 'No legal pages found',
        'total_pages' => 'Total Pages',
        'total_revisions' => 'Total Revisions',
        'last_update' => 'Last Update',
        'page_types' => [
            'terms' => 'Terms of Service',
            'privacy' => 'Privacy Policy',
            'cookies' => 'Cookie Policy',
            'refund' => 'Refund Policy',
            'shipping' => 'Shipping Policy',
        ],
    ],
    'fr' => [
        'legal_content' => 'Gestion du Contenu Juridique',
        'manage_legal_pages' => 'Gérer les Conditions, Politique de Confidentialité et autres pages juridiques',
        'page_type' => 'Type de Page',
        'language' => 'Langue',
        'title' => 'Titre',
        'version' => 'Version',
        'last_updated' => 'Dernière Mise à Jour',
        'actions' => 'Actions',
        'edit' => 'Modifier',
        'preview' => 'Aperçu',
        'no_pages_found' => 'Aucune page juridique trouvée',
        'total_pages' => 'Total Pages',
        'total_revisions' => 'Total Révisions',
        'last_update' => 'Dernière Mise à Jour',
        'page_types' => [
            'terms' => 'Conditions de Service',
            'privacy' => 'Politique de Confidentialité',
            'cookies' => 'Politique de Cookies',
            'refund' => 'Politique de Remboursement',
            'shipping' => 'Politique d\'Expédition',
        ],
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .legal-header {
    margin-bottom: 32px;
  }

  .legal-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .legal-header p {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
  }

  .stat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .stat-icon.blue { background: #dbeafe; color: #3b82f6; }
  .stat-icon.purple { background: #f3e8ff; color: #a855f7; }
  .stat-icon.orange { background: #fed7aa; color: #ea580c; }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    margin-top: 12px;
  }

  .stat-value.blue { color: #3b82f6; }
  .stat-value.purple { color: #a855f7; }
  .stat-value.orange { color: #ea580c; }

  /* Table Card */
  .table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .table-wrapper {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 24px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  th.text-right {
    text-align: right;
  }

  td {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  td.text-right {
    text-align: right;
  }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .page-type-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .page-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }

  .page-icon.terms { background: #3b82f6; }
  .page-icon.privacy { background: #8b5cf6; }
  .page-icon.cookies { background: #f59e0b; }
  .page-icon.refund { background: #10b981; }
  .page-icon.shipping { background: #06b6d4; }

  .page-info {
    min-width: 0;
  }

  .page-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .page-type-label {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .title-cell {
    color: var(--dark);
    font-size: 14px;
    max-width: 300px;
  }

  /* Language Badge */
  .lang-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .lang-badge.en {
    background: #dbeafe;
    color: #1e40af;
  }

  .lang-badge.fr {
    background: #fce7f3;
    color: #9f1239;
  }

  /* Version Badge */
  .version-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    background: var(--gray-100);
    color: var(--gray-700);
  }

  .version-badge i {
    font-size: 10px;
  }

  .date-cell {
    font-size: 13px;
    color: var(--dark);
  }

  .date-author {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
  }

  .action-btn {
    padding: 8px 16px;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .action-btn.edit {
    background: var(--primary);
    color: white;
  }

  .action-btn.edit:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
  }

  .action-btn.preview {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .action-btn.preview:hover {
    background: var(--gray-300);
  }

  /* Empty State */
  .empty-state {
    padding: 64px 24px;
    text-align: center;
  }

  .empty-state-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 16px;
  }

  .empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-500);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    th, td {
      padding: 12px 16px;
    }

    .action-buttons {
      flex-direction: column;
      gap: 8px;
    }

    .action-btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<!-- Page Header -->
<div class="legal-header">
  <h1><?= $t['legal_content'] ?></h1>
  <p><?= $t['manage_legal_pages'] ?></p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_pages'] ?></span>
      <div class="stat-icon blue">
        <i class="fas fa-file-contract"></i>
      </div>
    </div>
    <div class="stat-value blue"><?= count($pages ?? []) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_revisions'] ?></span>
      <div class="stat-icon purple">
        <i class="fas fa-history"></i>
      </div>
    </div>
    <div class="stat-value purple">
      <?= array_sum(array_column($pages ?? [], 'revision_count')) ?>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['last_update'] ?></span>
      <div class="stat-icon orange">
        <i class="fas fa-clock"></i>
      </div>
    </div>
    <div class="stat-value orange" style="font-size: 16px; margin-top: 8px;">
      <?php
      if (!empty($pages)) {
        $latestUpdate = max(array_column($pages, 'updated_at'));
        echo formatDate($latestUpdate, 'M d, Y');
      } else {
        echo '—';
      }
      ?>
    </div>
  </div>
</div>

<!-- Legal Pages Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $t['page_type'] ?></th>
          <th><?= $t['language'] ?></th>
          <th><?= $t['title'] ?></th>
          <th><?= $t['version'] ?></th>
          <th><?= $t['last_updated'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pages)): ?>
          <?php foreach ($pages as $page): ?>
            <tr>
              <!-- Page Type -->
              <td>
                <div class="page-type-cell">
                  <div class="page-icon <?= $page['page_type'] ?>">
                    <?php
                    $icons = [
                      'terms' => 'fa-file-contract',
                      'privacy' => 'fa-shield-alt',
                      'cookies' => 'fa-cookie-bite',
                      'refund' => 'fa-undo',
                      'shipping' => 'fa-shipping-fast',
                    ];
                    $icon = $icons[$page['page_type']] ?? 'fa-file-alt';
                    ?>
                    <i class="fas <?= $icon ?>"></i>
                  </div>
                  <div class="page-info">
                    <div class="page-name">
                      <?= $t['page_types'][$page['page_type']] ?? ucfirst($page['page_type']) ?>
                    </div>
                    <div class="page-type-label"><?= strtoupper($page['page_type']) ?></div>
                  </div>
                </div>
              </td>

              <!-- Language -->
              <td>
                <span class="lang-badge <?= $page['language'] ?>">
                  <?= strtoupper($page['language']) ?>
                </span>
              </td>

              <!-- Title -->
              <td>
                <div class="title-cell">
                  <?= htmlspecialchars($page['title']) ?>
                </div>
              </td>

              <!-- Version -->
              <td>
                <span class="version-badge">
                  <i class="fas fa-code-branch"></i>
                  v<?= $page['version'] ?>
                </span>
              </td>

              <!-- Last Updated -->
              <td>
                <div class="date-cell">
                  <?= formatDate($page['updated_at'], 'M d, Y') ?>
                </div>
                <?php if (!empty($page['updater_name'])): ?>
                  <div class="date-author">
                    by <?= htmlspecialchars($page['updater_name']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <!-- Actions -->
              <td class="text-right">
                <div class="action-buttons">
                  <a
                    href="<?= url('admin/legal/edit?id=' . $page['id']) ?>"
                    class="action-btn edit"
                  >
                    <i class="fas fa-edit"></i> <?= $t['edit'] ?>
                  </a>
                  <a
                    href="<?= url('admin/legal/preview?id=' . $page['id']) ?>"
                    class="action-btn preview"
                    target="_blank"
                  >
                    <i class="fas fa-eye"></i> <?= $t['preview'] ?>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-file-contract"></i>
                </div>
                <div class="empty-state-title"><?= $t['no_pages_found'] ?></div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
