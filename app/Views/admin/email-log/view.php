<?php
$pageTitle = 'Email Details';
$currentPage = 'email-log';
ob_start();
?>

<style>
  .back-link { color: #6b7280; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 16px; }
  .back-link:hover { color: var(--primary); }

  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
  .page-title { font-size: 22px; font-weight: 700; color: var(--dark); }

  .email-grid { display: grid; grid-template-columns: 1fr 320px; gap: 24px; }

  .card { background: white; border-radius: var(--radius-xl); padding: 24px; box-shadow: var(--shadow-sm); margin-bottom: 20px; }
  .card-title { font-size: 15px; font-weight: 700; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .info-item label { font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 3px; }
  .info-item .value { font-size: 14px; color: #1f2937; font-weight: 500; }

  .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
  .badge-sent { background: #d1fae5; color: #065f46; }
  .badge-failed { background: #fee2e2; color: #991b1b; }
  .badge-test_mode { background: #fef3c7; color: #92400e; }

  .type-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #f3f4f6; color: #374151; text-transform: uppercase; }

  .email-preview {
    border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-top: 12px;
  }
  .email-preview iframe {
    width: 100%; min-height: 500px; border: none;
  }

  .error-box {
    background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 14px 16px;
    font-size: 13px; color: #991b1b; margin-top: 12px;
  }

  @media (max-width: 900px) { .email-grid { grid-template-columns: 1fr; } }
</style>

<a href="<?= url('admin/email-log') ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Email Log</a>

<div class="page-header">
  <h1 class="page-title"><i class="fas fa-envelope" style="color:var(--primary);margin-right:6px;"></i> <?= htmlspecialchars($email['subject']) ?></h1>
  <span class="badge badge-<?= $email['status'] ?>" style="font-size:13px;padding:5px 14px;"><?= ucfirst(str_replace('_', ' ', $email['status'])) ?></span>
</div>

<div class="email-grid">
  <!-- Left: Email Content -->
  <div>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-file-alt" style="color:var(--primary);"></i> Email Content</h3>
      <div class="email-preview">
        <iframe id="emailFrame" srcdoc="<?= htmlspecialchars($email['body'] ?? '<p style=&quot;padding:20px;color:#999;&quot;>No content stored</p>') ?>"></iframe>
      </div>
    </div>
  </div>

  <!-- Right: Metadata -->
  <div>
    <div class="card">
      <h3 class="card-title"><i class="fas fa-info-circle" style="color:#6b7280;"></i> Details</h3>
      <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="info-item">
          <label>To</label>
          <div class="value"><?= htmlspecialchars($email['recipient_email']) ?></div>
          <?php if ($email['recipient_name']): ?>
            <div style="font-size:12px;color:#9ca3af;"><?= htmlspecialchars($email['recipient_name']) ?></div>
          <?php endif; ?>
        </div>
        <div class="info-item">
          <label>From</label>
          <div class="value"><?= htmlspecialchars($email['sender_email'] ?? 'info@ocsapp.ca') ?></div>
          <?php if ($email['sender_name']): ?>
            <div style="font-size:12px;color:#9ca3af;"><?= htmlspecialchars($email['sender_name']) ?></div>
          <?php endif; ?>
        </div>
        <div class="info-item">
          <label>Subject</label>
          <div class="value"><?= htmlspecialchars($email['subject']) ?></div>
        </div>
        <div class="info-item">
          <label>Type</label>
          <div class="value">
            <?php if ($email['email_type']): ?>
              <span class="type-badge"><?= htmlspecialchars(str_replace('_', ' ', $email['email_type'])) ?></span>
            <?php else: ?>
              <span style="color:#9ca3af;">Not categorized</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="info-item">
          <label>Status</label>
          <div class="value"><span class="badge badge-<?= $email['status'] ?>"><?= ucfirst(str_replace('_', ' ', $email['status'])) ?></span></div>
        </div>
        <div class="info-item">
          <label>Sent At</label>
          <div class="value"><?= date('M j, Y \a\t g:i:s A', strtotime($email['created_at'])) ?></div>
        </div>
        <?php if ($email['related_type']): ?>
        <div class="info-item">
          <label>Related Entity</label>
          <div class="value"><?= ucfirst($email['related_type']) ?> #<?= $email['related_id'] ?></div>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($email['status'] === 'failed' && $email['error_message']): ?>
        <div class="error-box">
          <strong><i class="fas fa-exclamation-triangle"></i> Error:</strong><br>
          <?= htmlspecialchars($email['error_message']) ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="card">
      <h3 class="card-title"><i class="fas fa-search" style="color:#6b7280;"></i> Related Emails</h3>
      <a href="<?= url('admin/email-log?search=' . urlencode($email['recipient_email'])) ?>" style="color:var(--primary);font-size:13px;font-weight:600;text-decoration:none;">
        <i class="fas fa-filter"></i> View all emails to <?= htmlspecialchars($email['recipient_email']) ?>
      </a>
    </div>
  </div>
</div>

<script>
// Auto-resize iframe to content height
document.getElementById('emailFrame').addEventListener('load', function() {
  try {
    this.style.height = this.contentWindow.document.body.scrollHeight + 40 + 'px';
  } catch(e) {}
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
