<?php
$pageTitle = 'Review Seller Verification - ' . htmlspecialchars($seller['name']);
$currentPage = 'sellers';
ob_start();

$documents = !empty($seller['verification_documents'])
    ? json_decode($seller['verification_documents'], true)
    : [];
?>

<style>
  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .breadcrumb {
    display: flex;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-600);
  }

  .breadcrumb a {
    color: var(--primary);
    text-decoration: none;
  }

  .verification-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
  }

  .card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .card-icon {
    width: 36px;
    height: 36px;
    background: var(--primary);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
  }

  .info-label {
    font-weight: 600;
    color: var(--gray-600);
  }

  .info-value {
    color: var(--dark);
    text-align: right;
  }

  .badge {
    padding: 6px 16px;
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 600;
  }

  .badge.unverified { background: #fef3c7; color: #92400e; }
  .badge.pending { background: #dbeafe; color: #1e40af; }
  .badge.verified { background: #dcfce7; color: #166534; }
  .badge.rejected { background: #fee2e2; color: #991b1b; }

  .documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 16px;
  }

  .document-card {
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    transition: all 0.2s;
  }

  .document-card:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-sm);
  }

  .document-icon {
    font-size: 48px;
    color: var(--primary);
    margin-bottom: 12px;
  }

  .document-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
    word-break: break-word;
  }

  .document-date {
    font-size: 12px;
    color: var(--gray-600);
  }

  .document-link {
    display: inline-block;
    margin-top: 12px;
    padding: 6px 12px;
    background: var(--primary);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
  }

  .document-link:hover {
    background: var(--primary-dark);
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    resize: vertical;
  }

  .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
  }

  .btn {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-success {
    background: #10b981;
    color: white;
    flex: 1;
  }

  .btn-success:hover {
    background: #059669;
  }

  .btn-danger {
    background: #ef4444;
    color: white;
    flex: 1;
  }

  .btn-danger:hover {
    background: #dc2626;
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  .alert {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
  }

  .alert-warning {
    background: #fef3c7;
    border: 2px solid #f59e0b;
    color: #92400e;
  }

  @media (max-width: 968px) {
    .verification-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="page-header">
  <h1 class="page-title">Review Seller Verification</h1>
  <div class="breadcrumb">
    <a href="<?= url('admin/sellers') ?>">Sellers</a>
    <span>/</span>
    <span><?= htmlspecialchars($seller['name']) ?></span>
    <span>/</span>
    <span>Verification</span>
  </div>
</div>

<?php if ($seller['verification_status'] === 'verified'): ?>
  <div class="alert alert-warning">
    <strong>Note:</strong> This seller is already verified.
    Verified on <?= date('F d, Y', strtotime($seller['verified_at'])) ?>
    <?php if (!empty($seller['verified_by_name'])): ?>
      by <?= htmlspecialchars($seller['verified_by_name']) ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="verification-grid">
  <!-- Main Content -->
  <div>
    <!-- Seller Information -->
    <div class="card">
      <h3 class="card-title">
        <div class="card-icon"><i class="fas fa-user"></i></div>
        Seller Information
      </h3>

      <div class="info-row">
        <span class="info-label">Name:</span>
        <span class="info-value"><strong><?= htmlspecialchars($seller['name']) ?></strong></span>
      </div>

      <div class="info-row">
        <span class="info-label">Email:</span>
        <span class="info-value">
          <a href="mailto:<?= htmlspecialchars($seller['email']) ?>">
            <?= htmlspecialchars($seller['email']) ?>
          </a>
        </span>
      </div>

      <div class="info-row">
        <span class="info-label">Phone:</span>
        <span class="info-value"><?= htmlspecialchars($seller['phone'] ?? 'N/A') ?></span>
      </div>

      <div class="info-row">
        <span class="info-label">Registration Date:</span>
        <span class="info-value"><?= date('F d, Y', strtotime($seller['created_at'])) ?></span>
      </div>

      <div class="info-row">
        <span class="info-label">Verification Status:</span>
        <span class="info-value">
          <span class="badge <?= $seller['verification_status'] ?>">
            <?= ucfirst($seller['verification_status']) ?>
          </span>
        </span>
      </div>

      <?php if (!empty($seller['verification_submitted_at'])): ?>
        <div class="info-row">
          <span class="info-label">Submitted:</span>
          <span class="info-value"><?= date('F d, Y g:i A', strtotime($seller['verification_submitted_at'])) ?></span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Business Information -->
    <div class="card">
      <h3 class="card-title">
        <div class="card-icon"><i class="fas fa-building"></i></div>
        Business Information
      </h3>

      <div class="info-row">
        <span class="info-label">Business Name:</span>
        <span class="info-value"><strong><?= htmlspecialchars($seller['business_name'] ?? 'Not provided') ?></strong></span>
      </div>

      <?php if (!empty($seller['business_number'])): ?>
        <div class="info-row">
          <span class="info-label">Registration Number:</span>
          <span class="info-value"><?= htmlspecialchars($seller['business_number']) ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($seller['tax_id'])): ?>
        <div class="info-row">
          <span class="info-label">Tax ID / GST/HST:</span>
          <span class="info-value"><?= htmlspecialchars($seller['tax_id']) ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($seller['business_address'])): ?>
        <div class="info-row" style="border: none; flex-direction: column; gap: 8px;">
          <span class="info-label">Business Address:</span>
          <span class="info-value" style="text-align: left;">
            <?= nl2br(htmlspecialchars($seller['business_address'])) ?>
          </span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Verification Documents -->
    <div class="card">
      <h3 class="card-title">
        <div class="card-icon"><i class="fas fa-file-alt"></i></div>
        Verification Documents
      </h3>

      <?php if (!empty($documents)): ?>
        <div class="documents-grid">
          <?php foreach ($documents as $doc): ?>
            <div class="document-card">
              <div class="document-icon">
                <?php
                $ext = pathinfo($doc['path'] ?? '', PATHINFO_EXTENSION);
                $icon = $ext === 'pdf' ? 'fa-file-pdf' : 'fa-file-image';
                ?>
                <i class="fas <?= $icon ?>"></i>
              </div>
              <div class="document-name"><?= htmlspecialchars($doc['name'] ?? 'Document') ?></div>
              <?php if (!empty($doc['uploaded_at'])): ?>
                <div class="document-date"><?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></div>
              <?php endif; ?>
              <a href="<?= url('admin/sellers/document?file=' . urlencode($doc['path'])) ?>" target="_blank" class="document-link">
                <i class="fas fa-external-link-alt"></i> View
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="color: var(--gray-600);">No documents uploaded.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Sidebar -->
  <div>
    <!-- Verification Actions -->
    <div class="card">
      <h3 class="card-title">
        <div class="card-icon"><i class="fas fa-tasks"></i></div>
        Verification Actions
      </h3>

      <form method="POST" action="<?= url('admin/sellers/verification/approve') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">

        <div class="form-group">
          <label for="notes" class="form-label">Admin Notes (Optional)</label>
          <textarea
            id="notes"
            name="notes"
            class="form-textarea"
            rows="4"
            placeholder="Add any notes about this verification..."
          ><?= htmlspecialchars($seller['verification_notes'] ?? '') ?></textarea>
        </div>

        <div class="action-buttons">
          <button type="submit" name="action" value="approve" class="btn btn-success">
            <i class="fas fa-check"></i> Approve
          </button>
          <button type="submit" name="action" value="reject" class="btn btn-danger">
            <i class="fas fa-times"></i> Reject
          </button>
        </div>
      </form>

      <a href="<?= url('admin/sellers') ?>" class="btn btn-secondary" style="width: 100%; margin-top: 12px; justify-content: center;">
        <i class="fas fa-arrow-left"></i> Back to Sellers
      </a>
    </div>

    <!-- Shop Information -->
    <?php if (!empty($seller['shop_name'])): ?>
      <div class="card">
        <h3 class="card-title">
          <div class="card-icon"><i class="fas fa-store"></i></div>
          Shop Information
        </h3>

        <div class="info-row">
          <span class="info-label">Shop Name:</span>
          <span class="info-value"><?= htmlspecialchars($seller['shop_name']) ?></span>
        </div>

        <div class="info-row">
          <span class="info-label">Shop Visible:</span>
          <span class="info-value">
            <?= !empty($seller['shop_is_visible']) ? 'Yes' : 'No' ?>
          </span>
        </div>

        <a href="<?= url('admin/shops/edit?id=' . $seller['shop_id']) ?>" class="btn btn-secondary" style="width: 100%; margin-top: 12px; justify-content: center;">
          <i class="fas fa-edit"></i> Edit Shop
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
