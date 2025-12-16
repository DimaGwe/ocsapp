<?php
/**
 * OCS Admin Legal Content Edit
 * File: app/Views/admin/legal/edit.php
 */

$pageTitle = 'Edit Legal Content';
$currentPage = 'legal';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'edit_legal_content' => 'Edit Legal Content',
        'back_to_list' => 'Back to Legal Pages',
        'title' => 'Title',
        'content' => 'Content',
        'meta_description' => 'Meta Description',
        'meta_description_help' => 'SEO description for search engines (160 characters max)',
        'version_notes' => 'Version Notes',
        'version_notes_placeholder' => 'What changed in this version? (optional)',
        'save_changes' => 'Save Changes',
        'preview' => 'Preview',
        'current_version' => 'Current Version',
        'revision_history' => 'Revision History',
        'version' => 'Version',
        'updated_by' => 'Updated By',
        'updated_at' => 'Updated',
        'notes' => 'Notes',
        'restore' => 'Restore',
        'no_revisions' => 'No revision history yet',
        'confirm_restore' => 'Are you sure you want to restore this version?',
    ],
    'fr' => [
        'edit_legal_content' => 'Modifier le Contenu Juridique',
        'back_to_list' => 'Retour aux Pages Juridiques',
        'title' => 'Titre',
        'content' => 'Contenu',
        'meta_description' => 'Méta Description',
        'meta_description_help' => 'Description SEO pour les moteurs de recherche (max 160 caractères)',
        'version_notes' => 'Notes de Version',
        'version_notes_placeholder' => 'Qu\'est-ce qui a changé dans cette version? (optionnel)',
        'save_changes' => 'Enregistrer les Modifications',
        'preview' => 'Aperçu',
        'current_version' => 'Version Actuelle',
        'revision_history' => 'Historique des Révisions',
        'version' => 'Version',
        'updated_by' => 'Mis à Jour Par',
        'updated_at' => 'Mis à Jour',
        'notes' => 'Notes',
        'restore' => 'Restaurer',
        'no_revisions' => 'Aucun historique de révision pour le moment',
        'confirm_restore' => 'Êtes-vous sûr de vouloir restaurer cette version?',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/2q2m3kagr07784sbu50bx4vlmycy4cxsr249gstug6teosyw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<style>
  /* Page Header */
  .edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
  }

  .header-left {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .back-btn {
    padding: 8px 16px;
    background: var(--gray-200);
    color: var(--gray-700);
    border-radius: var(--radius-md);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all var(--transition-base);
  }

  .back-btn:hover {
    background: var(--gray-300);
  }

  .edit-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .version-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background: var(--gray-100);
    border-radius: var(--radius-md);
    font-size: 14px;
  }

  .version-label {
    color: var(--gray-600);
    font-weight: 600;
  }

  .version-number {
    color: var(--primary);
    font-weight: 700;
  }

  /* Two Column Layout */
  .edit-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    align-items: start;
  }

  /* Form Card */
  .form-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
  }

  .form-group {
    margin-bottom: 24px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-help {
    display: block;
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
  }

  .form-input,
  .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-textarea {
    resize: vertical;
    min-height: 100px;
  }

  /* TinyMCE wrapper */
  .editor-wrapper {
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: border-color var(--transition-base);
  }

  .editor-wrapper:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  /* Form Actions */
  .form-actions {
    display: flex;
    gap: 12px;
    padding-top: 8px;
  }

  .btn {
    padding: 12px 24px;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  /* Revision History Card */
  .revision-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    position: sticky;
    top: 24px;
  }

  .revision-card h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
  }

  .revision-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .revision-item {
    padding: 12px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    transition: all var(--transition-base);
  }

  .revision-item:hover {
    border-color: var(--primary);
    background: var(--gray-50);
  }

  .revision-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
  }

  .revision-version {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
  }

  .revision-date {
    font-size: 11px;
    color: var(--gray-500);
  }

  .revision-author {
    font-size: 12px;
    color: var(--gray-600);
    margin-bottom: 6px;
  }

  .revision-notes {
    font-size: 12px;
    color: var(--gray-600);
    font-style: italic;
    margin-bottom: 8px;
  }

  .revision-actions {
    display: flex;
    justify-content: flex-end;
  }

  .btn-restore {
    padding: 6px 12px;
    background: var(--gray-200);
    color: var(--gray-700);
    border: none;
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-restore:hover {
    background: var(--primary);
    color: white;
  }

  .empty-revisions {
    text-align: center;
    padding: 24px;
    color: var(--gray-500);
    font-size: 13px;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .edit-layout {
      grid-template-columns: 1fr;
    }

    .revision-card {
      position: static;
    }
  }
</style>

<!-- Page Header -->
<div class="edit-header">
  <div class="header-left">
    <a href="<?= url('admin/legal') ?>" class="back-btn">
      <i class="fas fa-arrow-left"></i> <?= $t['back_to_list'] ?>
    </a>
    <h1><?= $t['edit_legal_content'] ?></h1>
  </div>
  <div class="version-info">
    <span class="version-label"><?= $t['current_version'] ?>:</span>
    <span class="version-number">v<?= $page['version'] ?? 1 ?></span>
  </div>
</div>

<!-- Edit Layout -->
<div class="edit-layout">
  <!-- Main Form -->
  <div class="form-card">
    <form method="POST" action="<?= url('admin/legal/update') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="id" value="<?= $page['id'] ?>">

      <!-- Title -->
      <div class="form-group">
        <label for="title" class="form-label">
          <i class="fas fa-heading"></i> <?= $t['title'] ?>
        </label>
        <input
          type="text"
          id="title"
          name="title"
          value="<?= htmlspecialchars($page['title'] ?? '') ?>"
          class="form-input"
          required
        >
      </div>

      <!-- Meta Description -->
      <div class="form-group">
        <label for="meta_description" class="form-label">
          <i class="fas fa-search"></i> <?= $t['meta_description'] ?>
        </label>
        <textarea
          id="meta_description"
          name="meta_description"
          class="form-textarea"
          maxlength="160"
          rows="3"
        ><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
        <small class="form-help"><?= $t['meta_description_help'] ?></small>
      </div>

      <!-- Content Editor -->
      <div class="form-group">
        <label for="content" class="form-label">
          <i class="fas fa-file-alt"></i> <?= $t['content'] ?>
        </label>
        <div class="editor-wrapper">
          <textarea
            id="content"
            name="content"
            style="height: 500px;"
          ><?= $page['content'] ?? '' ?></textarea>
        </div>
      </div>

      <!-- Version Notes -->
      <div class="form-group">
        <label for="notes" class="form-label">
          <i class="fas fa-sticky-note"></i> <?= $t['version_notes'] ?>
        </label>
        <textarea
          id="notes"
          name="notes"
          class="form-textarea"
          rows="3"
          placeholder="<?= $t['version_notes_placeholder'] ?>"
        ></textarea>
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> <?= $t['save_changes'] ?>
        </button>
        <a href="<?= url('admin/legal/preview?id=' . $page['id']) ?>" target="_blank" class="btn btn-secondary">
          <i class="fas fa-eye"></i> <?= $t['preview'] ?>
        </a>
      </div>
    </form>
  </div>

  <!-- Revision History Sidebar -->
  <div class="revision-card">
    <h3>
      <i class="fas fa-history"></i> <?= $t['revision_history'] ?>
    </h3>

    <?php if (!empty($revisions)): ?>
      <div class="revision-list">
        <?php foreach ($revisions as $revision): ?>
          <div class="revision-item">
            <div class="revision-header">
              <span class="revision-version">v<?= $revision['version'] ?></span>
              <span class="revision-date">
                <?= formatDate($revision['created_at'], 'M d, Y') ?>
              </span>
            </div>

            <?php if (!empty($revision['creator_name'])): ?>
              <div class="revision-author">
                <?= htmlspecialchars($revision['creator_name'] . ' ' . $revision['creator_lastname']) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($revision['notes'])): ?>
              <div class="revision-notes">
                "<?= htmlspecialchars($revision['notes']) ?>"
              </div>
            <?php endif; ?>

            <div class="revision-actions">
              <form method="POST" action="<?= url('admin/legal/restore') ?>" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_restore'] ?>')">
                <?= csrfField() ?>
                <input type="hidden" name="revision_id" value="<?= $revision['id'] ?>">
                <button type="submit" class="btn-restore">
                  <i class="fas fa-undo"></i> <?= $t['restore'] ?>
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-revisions">
        <i class="fas fa-info-circle"></i>
        <?= $t['no_revisions'] ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Initialize TinyMCE -->
<script>
  tinymce.init({
    selector: '#content',
    height: 500,
    menubar: true,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
      'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
      'bold italic underline strikethrough | forecolor backcolor | ' +
      'alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent | link image | ' +
      'removeformat code preview fullscreen',
    content_style: `
      body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333;
        padding: 20px;
      }
      h1, h2, h3, h4, h5, h6 {
        margin-top: 24px;
        margin-bottom: 12px;
        font-weight: 600;
        color: #1a1a1a;
      }
      p {
        margin-bottom: 12px;
      }
      ul, ol {
        margin-bottom: 12px;
        padding-left: 24px;
      }
      a {
        color: #00b207;
      }
    `,
    setup: function(editor) {
      // Auto-save draft to localStorage every 30 seconds
      setInterval(function() {
        const content = editor.getContent();
        localStorage.setItem('legal_draft_<?= $page['id'] ?>', content);
      }, 30000);

      // Restore draft on init
      editor.on('init', function() {
        const draft = localStorage.getItem('legal_draft_<?= $page['id'] ?>');
        if (draft && draft !== editor.getContent()) {
          if (confirm('A draft was found. Do you want to restore it?')) {
            editor.setContent(draft);
          }
        }
      });

      // Clear draft on save
      editor.on('submit', function() {
        localStorage.removeItem('legal_draft_<?= $page['id'] ?>');
      });
    }
  });

  // Character counter for meta description
  const metaDesc = document.getElementById('meta_description');
  if (metaDesc) {
    const counter = document.createElement('small');
    counter.className = 'form-help';
    counter.style.float = 'right';
    metaDesc.parentNode.appendChild(counter);

    function updateCounter() {
      const length = metaDesc.value.length;
      counter.textContent = `${length}/160`;
      counter.style.color = length > 160 ? '#ef4444' : '#6b7280';
    }

    metaDesc.addEventListener('input', updateCounter);
    updateCounter();
  }
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
