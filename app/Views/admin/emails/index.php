<?php
/**
 * Admin Email Templates - List View
 * File: app/Views/admin/emails/index.php
 */

$pageTitle = 'Email Templates';
$currentPage = 'emails';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'email_templates' => 'Email Templates',
        'manage_customize' => 'Manage and customize email templates sent to customers',
        'edit' => 'Edit',
        'preview' => 'Preview',
        'modified' => 'Modified',
    ],
    'fr' => [
        'email_templates' => 'Modèles d\'e-mail',
        'manage_customize' => 'Gérer et personnaliser les modèles d\'e-mail envoyés aux clients',
        'edit' => 'Modifier',
        'preview' => 'Aperçu',
        'modified' => 'Modifié',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .page-subtitle {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Templates Grid */
  .templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
  }

  .template-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 28px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
  }

  .template-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .template-icon {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
  }

  .template-icon i {
    color: white;
    font-size: 26px;
  }

  .template-name {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .template-meta {
    font-size: 13px;
    color: var(--gray-500);
    margin-bottom: 16px;
  }

  .template-meta i {
    margin-right: 6px;
  }

  .template-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
  }

  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transition: all var(--transition-base);
    flex: 1;
    justify-content: center;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-700);
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }
</style>

<!-- Page Header -->
<div class="page-header">
  <h1 class="page-title">
    <i class="fas fa-envelope"></i> <?= htmlspecialchars($t['email_templates']) ?>
  </h1>
  <p class="page-subtitle"><?= htmlspecialchars($t['manage_customize']) ?></p>
</div>

<!-- Templates Grid -->
<div class="templates-grid">
  <?php foreach ($templates as $template): ?>
    <div class="template-card">
      <div class="template-icon">
        <i class="fas fa-file-code"></i>
      </div>
      <div class="template-name"><?= htmlspecialchars($template['name']) ?></div>
      <div class="template-meta">
        <i class="fas fa-hdd"></i> <?= number_format($template['size'] / 1024, 1) ?> KB
        <br>
        <i class="fas fa-clock"></i> <?= $t['modified'] ?>: <?= date('M j, Y g:i A', $template['modified']) ?>
      </div>
      <div class="template-actions">
        <a href="<?= url('admin/emails/edit?template=' . urlencode($template['filename'])) ?>" class="btn btn-primary">
          <i class="fas fa-edit"></i> <?= $t['edit'] ?>
        </a>
        <a href="<?= url('admin/emails/preview?template=' . urlencode($template['filename'])) ?>"
           class="btn btn-secondary" target="_blank">
          <i class="fas fa-eye"></i> <?= $t['preview'] ?>
        </a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
