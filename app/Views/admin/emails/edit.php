<?php
/**
 * Admin Email Templates - Edit View
 * File: app/Views/admin/emails/edit.php
 */

$pageTitle = 'Edit Template: ' . $name;
$currentPage = 'emails';

// Detect if this is a TinyMCE template (placeholder-based)
// All email templates now use TinyMCE with placeholder system
$useTinyMCE = true;

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'edit_template' => 'Edit Template',
        'file' => 'File',
        'location' => 'Location',
        'available_variables' => 'Available Variables',
        'template_content' => 'Template Content (HTML/PHP)',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel',
        'preview' => 'Preview',
        'back_to_list' => 'Back to List',
        'help_text' => 'You can use HTML and PHP code. Changes are saved with automatic backup.',
        'template_info' => 'Template Information',
        'file_size' => 'File Size',
        'last_modified' => 'Last Modified',
        'template_file' => 'Template File',
        'file_path' => 'File Path',
        'code_editor' => 'Code Editor',
        'live_preview' => 'Live Preview',
        'refresh_preview' => 'Refresh Preview',
    ],
    'fr' => [
        'edit_template' => 'Modifier le modèle',
        'file' => 'Fichier',
        'location' => 'Emplacement',
        'available_variables' => 'Variables disponibles',
        'template_content' => 'Contenu du modèle (HTML/PHP)',
        'save_changes' => 'Sauvegarder',
        'cancel' => 'Annuler',
        'preview' => 'Aperçu',
        'back_to_list' => 'Retour à la liste',
        'help_text' => 'Vous pouvez utiliser du code HTML et PHP. Les modifications sont sauvegardées avec sauvegarde automatique.',
        'template_info' => 'Informations du modèle',
        'file_size' => 'Taille du fichier',
        'last_modified' => 'Dernière modification',
        'template_file' => 'Fichier du modèle',
        'file_path' => 'Chemin du fichier',
        'code_editor' => 'Éditeur de code',
        'live_preview' => 'Aperçu en direct',
        'refresh_preview' => 'Actualiser l\'aperçu',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<?php if (!$useTinyMCE): ?>
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<?php endif; ?>

<style>
  .page-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    background: #dbeafe;
    color: #1e40af;
  }

  .card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  .card-header {
    margin-bottom: 24px;
  }

  .card-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
    color: var(--gray-700);
  }

  .form-text {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: var(--gray-500);
  }

  .info-box {
    background: #e7f3ff;
    border-left: 4px solid #2196F3;
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .info-box h3 {
    font-size: 14px;
    color: #1976D2;
    margin-bottom: 12px;
    font-weight: 600;
  }

  .info-box ul {
    margin-left: 20px;
    font-size: 13px;
    color: #666;
    line-height: 1.8;
  }

  .info-box ul li {
    margin-bottom: 6px;
  }

  .info-box code {
    background: rgba(33, 150, 243, 0.1);
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    color: #1976D2;
    font-size: 12px;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
  }

  .btn {
    padding: 12px 24px;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover:not(:disabled) {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }

  .info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .info-item strong {
    font-size: 13px;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .info-item span,
  .info-item code {
    font-size: 14px;
    color: var(--dark);
  }

  .info-item code {
    font-family: 'Courier New', monospace;
    background: var(--gray-100);
    padding: 4px 8px;
    border-radius: 4px;
  }

  .alert {
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .alert-success {
    background: #dcfce7;
    color: #166534;
    border-left: 4px solid #16a34a;
  }

  .alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #dc2626;
  }

  /* Split View Layout */
  .editor-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
  }

  .editor-panel {
    display: flex;
    flex-direction: column;
  }

  .panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--border);
  }

  .panel-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .preview-frame {
    width: 100%;
    height: 600px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background: white;
  }

  .CodeMirror {
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    height: 600px;
  }

  .CodeMirror-focused {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  @media (max-width: 1200px) {
    .editor-container {
      grid-template-columns: 1fr;
    }
  }
</style>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1>
      <i class="fas fa-edit"></i> <?= htmlspecialchars($name) ?>
    </h1>
    <span class="badge">Email Template</span>
  </div>
  <div style="display: flex; gap: 12px;">
    <a href="<?= url('admin/emails/preview?template=' . urlencode($filename)) ?>"
       class="btn btn-secondary" target="_blank">
      <i class="fas fa-external-link-alt"></i> <?= $t['preview'] ?>
    </a>
    <a href="<?= url('admin/emails') ?>" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> <?= $t['back_to_list'] ?>
    </a>
  </div>
</div>

<div id="alert-container"></div>

<!-- Main Edit Card -->
<div class="card">
  <form id="editTemplateForm">
    <?= csrfField() ?>
    <input type="hidden" name="filename" value="<?= htmlspecialchars($filename) ?>">

    <div class="info-box">
      <h3><i class="fas fa-info-circle"></i> <?= $t['available_variables'] ?></h3>
      <p style="font-size: 13px; color: #666; margin-bottom: 12px;">Use these placeholders in your email content. They will be automatically replaced with actual values when the email is sent:</p>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px;">
        <div>
          <strong style="color: #00b207;">User/Buyer:</strong>
          <ul style="margin: 4px 0; padding-left: 20px;">
            <li><code>{{user_first_name}}</code></li>
            <li><code>{{user_last_name}}</code></li>
            <li><code>{{user_email}}</code></li>
          </ul>
        </div>
        <div>
          <strong style="color: #f59e0b;">Seller:</strong>
          <ul style="margin: 4px 0; padding-left: 20px;">
            <li><code>{{seller_first_name}}</code></li>
            <li><code>{{seller_email}}</code></li>
          </ul>
        </div>
        <div>
          <strong style="color: #3b82f6;">Orders:</strong>
          <ul style="margin: 4px 0; padding-left: 20px;">
            <li><code>{{order_number}}</code></li>
            <li><code>{{order_total}}</code></li>
            <li><code>{{order_date}}</code></li>
            <li><code>{{old_status}}</code></li>
            <li><code>{{new_status}}</code></li>
            <li><code>{{cancellation_reason}}</code></li>
          </ul>
        </div>
        <div>
          <strong style="color: #8b5cf6;">Products & Dates:</strong>
          <ul style="margin: 4px 0; padding-left: 20px;">
            <li><code>{{product_name}}</code></li>
            <li><code>{{product_sku}}</code></li>
            <li><code>{{current_stock}}</code></li>
            <li><code>{{current_year}}</code></li>
            <li><code>{{submitted_date}}</code></li>
          </ul>
        </div>
      </div>
    </div>

    <?php if ($useTinyMCE): ?>
      <!-- TinyMCE Editor (No Preview) -->
      <div style="margin-bottom: 24px;">
        <div class="panel-header">
          <div class="panel-title">
            <i class="fas fa-edit"></i> Email Content
          </div>
        </div>
        <textarea id="content" name="content"><?= htmlspecialchars($content) ?></textarea>
      </div>
    <?php else: ?>
      <!-- Split View: CodeMirror + Live Preview -->
      <div class="editor-container">
        <!-- Code Editor Panel -->
        <div class="editor-panel">
          <div class="panel-header">
            <div class="panel-title">
              <i class="fas fa-code"></i> <?= $t['code_editor'] ?>
            </div>
          </div>
          <textarea id="content" name="content" class="form-control"><?= htmlspecialchars($content) ?></textarea>
        </div>

        <!-- Live Preview Panel -->
        <div class="editor-panel">
          <div class="panel-header">
            <div class="panel-title">
              <i class="fas fa-eye"></i> <?= $t['live_preview'] ?>
            </div>
            <button type="button" class="btn btn-secondary" id="refreshPreview" style="padding: 8px 16px; font-size: 13px;">
              <i class="fas fa-sync-alt"></i> <?= $t['refresh_preview'] ?>
            </button>
          </div>
          <iframe id="previewFrame" class="preview-frame" sandbox="allow-same-origin"></iframe>
        </div>
      </div>
    <?php endif; ?>

    <small class="form-text">
      <i class="fas fa-lightbulb"></i> <?= $t['help_text'] ?>
    </small>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="saveBtn">
        <i class="fas fa-save"></i> <?= $t['save_changes'] ?>
      </button>
      <a href="<?= url('admin/emails') ?>" class="btn btn-secondary">
        <i class="fas fa-times"></i> <?= $t['cancel'] ?>
      </a>
    </div>
  </form>
</div>

<!-- Template Information Card -->
<div class="card">
  <div class="card-header">
    <h3><?= $t['template_info'] ?></h3>
  </div>
  <div class="info-grid">
    <div class="info-item">
      <strong><?= $t['template_file'] ?>:</strong>
      <code><?= htmlspecialchars($filename) ?></code>
    </div>
    <div class="info-item">
      <strong><?= $t['file_path'] ?>:</strong>
      <code><?= htmlspecialchars($filePath) ?></code>
    </div>
    <div class="info-item">
      <strong><?= $t['file_size'] ?>:</strong>
      <span><?= number_format(filesize($filePath) / 1024, 2) ?> KB</span>
    </div>
    <div class="info-item">
      <strong><?= $t['last_modified'] ?>:</strong>
      <span><?= date('F d, Y g:i A', filemtime($filePath)) ?></span>
    </div>
  </div>
</div>

<?php if ($useTinyMCE): ?>
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/2q2m3kagr07784sbu50bx4vlmycy4cxsr249gstug6teosyw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
  // Initialize TinyMCE
  tinymce.init({
    selector: '#content',
    height: 600,
    menubar: true,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
      'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | help',
    content_style: 'body { font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; font-size: 14px; }',
    setup: function(editor) {
      editor.on('init', function() {
        console.log('TinyMCE initialized');
      });
    }
  });
</script>

<?php else: ?>
<!-- CodeMirror JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>

<script>
  // Initialize CodeMirror
  const editor = CodeMirror.fromTextArea(document.getElementById('content'), {
    mode: 'application/x-httpd-php',
    theme: 'monokai',
    lineNumbers: true,
    indentUnit: 2,
    tabSize: 2,
    indentWithTabs: false,
    lineWrapping: true,
    matchBrackets: true,
    autoCloseBrackets: true,
    autoCloseTags: true,
    extraKeys: {
      "Ctrl-Space": "autocomplete",
      "Ctrl-S": function(cm) {
        document.getElementById('editTemplateForm').dispatchEvent(new Event('submit'));
      }
    }
  });

  // Live Preview Function
  function updatePreview() {
    const iframe = document.getElementById('previewFrame');
    const previewUrl = '<?= url('admin/emails/preview?template=' . urlencode($filename)) ?>';

    // Create a temporary form to POST the content
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = '<?= url('admin/emails/preview-content') ?>';
    tempForm.target = 'previewFrame';

    const contentInput = document.createElement('input');
    contentInput.type = 'hidden';
    contentInput.name = 'content';
    contentInput.value = editor.getValue();

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';

    tempForm.appendChild(contentInput);
    tempForm.appendChild(csrfInput);
    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
  }

  // Refresh preview on button click
  document.getElementById('refreshPreview').addEventListener('click', function() {
    const btn = this;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;

    updatePreview();

    setTimeout(() => {
      btn.innerHTML = originalHTML;
      btn.disabled = false;
    }, 1000);
  });

  // Auto-refresh preview on load
  window.addEventListener('load', function() {
    updatePreview();
  });

  // Form submission
  document.getElementById('editTemplateForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const formData = new FormData(e.target);
    <?php if ($useTinyMCE): ?>
    // For TinyMCE, get content from TinyMCE editor
    formData.set('content', tinymce.get('content').getContent());
    <?php else: ?>
    // For CodeMirror, get content from CodeMirror editor
    formData.set('content', editor.getValue());
    <?php endif; ?>

    try {
      const response = await fetch('<?= url('admin/emails/update') ?>', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: formData
      });

      // Check if response is JSON or redirect
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        const data = await response.json();
        if (data.success) {
          showAlert('success', data.message || 'Template saved successfully!');
          <?php if (!$useTinyMCE): ?>
          updatePreview(); // Refresh preview after save (CodeMirror only)
          <?php endif; ?>
        } else {
          showAlert('error', data.message || 'Failed to save template');
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i class="fas fa-save"></i> <?= $t['save_changes'] ?>';
        }
      } else {
        // Handle redirect or HTML response
        showAlert('success', 'Template saved successfully! Refreshing...');
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      }
    } catch (error) {
      console.error('Error:', error);
      showAlert('error', 'An error occurred. Please try again.');
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fas fa-save"></i> <?= $t['save_changes'] ?>';
    }
  });

  function showAlert(type, message) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    alertContainer.innerHTML = `<div class="alert ${alertClass}"><i class="fas ${icon}"></i> ${message}</div>`;
    setTimeout(() => {
      alertContainer.innerHTML = '';
    }, 5000);
  }

  // Save shortcut hint
  console.log('💡 Tip: Press Ctrl+S to save the template');
</script>
<?php endif; ?>

<!-- Common script (loads for all templates) -->
<script>
  <?php if ($useTinyMCE): ?>
  // For TinyMCE templates, we don't have the form submission in the CodeMirror block
  // so we need to define it here
  document.getElementById('editTemplateForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const formData = new FormData(e.target);
    formData.set('content', tinymce.get('content').getContent());

    try {
      const response = await fetch('<?= url('admin/emails/update') ?>', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: formData
      });

      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        const data = await response.json();
        if (data.success) {
          showAlert('success', data.message || 'Template saved successfully!');
        } else {
          showAlert('error', data.message || 'Failed to save template');
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i class="fas fa-save"></i> <?= $t['save_changes'] ?>';
        }
      } else {
        showAlert('success', 'Template saved successfully! Refreshing...');
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      }
    } catch (error) {
      console.error('Error:', error);
      showAlert('error', 'An error occurred. Please try again.');
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fas fa-save"></i> <?= $t['save_changes'] ?>';
    }
  });

  function showAlert(type, message) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    alertContainer.innerHTML = `<div class="alert ${alertClass}"><i class="fas ${icon}"></i> ${message}</div>`;
    setTimeout(() => {
      alertContainer.innerHTML = '';
    }, 5000);
  }
  <?php endif; ?>
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
