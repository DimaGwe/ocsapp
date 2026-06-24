<?php
// Get current user verification data
$db = \Database::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$verificationStatus = $user['verification_status'] ?? 'unverified';
$documents = !empty($user['verification_documents']) ? json_decode($user['verification_documents'], true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Verification - OCSAPP</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  :root {
    --primary: #667eea;
    --primary-dark: #5568d3;
    --dark: #1a1a2e;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --border: #e5e7eb;
    --danger: #ef4444;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-full: 9999px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  }

  body {
    font-family: 'Poppins', sans-serif;
    background: var(--gray-50);
    padding-top: 70px;
  }

  .nav-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: var(--shadow-sm);
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
  }

  .nav-brand {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
  }

  .nav-links {
    display: flex;
    gap: 24px;
  }

  .nav-links a {
    color: var(--gray-700);
    text-decoration: none;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: var(--radius-md);
    transition: all 0.2s;
  }

  .nav-links a:hover {
    background: var(--gray-100);
  }

  .nav-links a.active {
    background: var(--primary);
    color: white;
  }

  .verification-container {
    max-width: 900px;
    margin: 32px auto;
    padding: 0 16px;
  }

  .status-banner {
    padding: 24px;
    border-radius: 12px;
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .status-banner.unverified {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #f59e0b;
  }

  .status-banner.pending {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 2px solid #3b82f6;
  }

  .status-banner.verified {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border: 2px solid #10b981;
  }

  .status-banner.rejected {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border: 2px solid #ef4444;
  }

  .status-icon {
    font-size: 48px;
  }

  .status-banner.unverified .status-icon { color: #f59e0b; }
  .status-banner.pending .status-icon { color: #3b82f6; }
  .status-banner.verified .status-icon { color: #10b981; }
  .status-banner.rejected .status-icon { color: #ef4444; }

  .status-content h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .status-content p {
    font-size: 14px;
    color: var(--gray-700);
  }

  .card {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
  }

  .card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gray-200);
  }

  .card-header-icon {
    width: 40px;
    height: 40px;
    background: var(--primary);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .card-header h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
  }

  .checklist-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    margin-bottom: 12px;
    transition: all 0.2s;
  }

  .checklist-item.completed {
    border-color: #10b981;
    background: #f0fdf4;
  }

  .checklist-checkbox {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid var(--gray-300);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .checklist-item.completed .checklist-checkbox {
    background: #10b981;
    border-color: #10b981;
    color: white;
  }

  .checklist-content {
    flex: 1;
  }

  .checklist-content h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .checklist-content p {
    font-size: 14px;
    color: var(--gray-600);
  }

  .form-group {
    margin-bottom: 24px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .required {
    color: var(--danger);
  }

  .form-input, .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.2s;
  }

  .form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-hint {
    font-size: 13px;
    color: var(--gray-600);
    margin-top: 6px;
  }

  .file-upload-area {
    border: 2px dashed var(--gray-300);
    border-radius: 8px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
  }

  .file-upload-area:hover {
    border-color: var(--primary);
    background: var(--gray-50);
  }

  .file-upload-area input[type="file"] {
    display: none;
  }

  .file-icon {
    font-size: 48px;
    color: var(--gray-400);
    margin-bottom: 12px;
  }

  .uploaded-files {
    margin-top: 16px;
  }

  .uploaded-file {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: var(--gray-50);
    border-radius: 6px;
    margin-bottom: 8px;
  }

  .uploaded-file-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .uploaded-file-icon {
    color: var(--primary);
  }

  .btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-dark);
  }

  .alert {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
  }

  .alert-warning {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    color: #92400e;
  }

  .alert-info {
    background: #dbeafe;
    border: 1px solid #3b82f6;
    color: #1e40af;
  }

  @media (max-width: 768px) {
    .nav-links {
      display: none;
    }
  }
</style>
</head>
<body>
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div style="position: fixed; top: 80px; right: 20px; background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999;">
            <strong>❌ Error:</strong> <?= $_SESSION['flash']['error'] ?>
        </div>
        <?php unset($_SESSION['flash']['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div style="position: fixed; top: 80px; right: 20px; background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999;">
            <strong>✅ Success:</strong> <?= $_SESSION['flash']['success'] ?>
        </div>
        <?php unset($_SESSION['flash']['success']); ?>
    <?php endif; ?>

    <!-- Navigation -->
    <div class="nav-bar">
        <a href="<?= url('/') ?>" class="nav-brand">
            <i class="fas fa-store"></i> OCS Seller
        </a>
        <div class="nav-links">
            <a href="<?= url('seller/dashboard') ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= url('seller/inventory') ?>">
                <i class="fas fa-boxes"></i> Products
            </a>
            <a href="<?= url('seller/orders') ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="<?= url('seller/shop/settings') ?>">
                <i class="fas fa-cog"></i> Shop Settings
            </a>
            <a href="<?= url('seller/verification') ?>" class="active">
                <i class="fas fa-check-circle"></i> Verification
            </a>
            <form method="POST" action="<?= url('logout') ?>" style="margin:0;display:inline;">
                <?= csrfField() ?>
                <button type="submit" style="background:none;border:none;cursor:pointer;font-size:inherit;color:inherit;padding:0;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

<div class="verification-container">
  <!-- Status Banner -->
  <div class="status-banner <?= $verificationStatus ?>">
    <div class="status-icon">
      <?php if ($verificationStatus === 'unverified'): ?>
        <i class="fas fa-exclamation-triangle"></i>
      <?php elseif ($verificationStatus === 'pending'): ?>
        <i class="fas fa-clock"></i>
      <?php elseif ($verificationStatus === 'verified'): ?>
        <i class="fas fa-check-circle"></i>
      <?php else: ?>
        <i class="fas fa-times-circle"></i>
      <?php endif; ?>
    </div>
    <div class="status-content">
      <?php if ($verificationStatus === 'unverified'): ?>
        <h2>Verification Required</h2>
        <p>Complete your business verification to unlock full marketplace features and start selling.</p>
      <?php elseif ($verificationStatus === 'pending'): ?>
        <h2>Verification Pending</h2>
        <p>Your verification is under review. We'll notify you once it's processed (usually within 1-2 business days).</p>
      <?php elseif ($verificationStatus === 'verified'): ?>
        <h2>Verified Seller</h2>
        <p>Your account is verified! You have full access to marketplace features.</p>
      <?php else: ?>
        <h2>Verification Rejected</h2>
        <p>Your verification was rejected. Please review the notes below and resubmit.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($verificationStatus === 'rejected' && !empty($user['verification_notes'])): ?>
    <div class="alert alert-warning">
      <strong>Rejection Reason:</strong><br>
      <?= nl2br(htmlspecialchars($user['verification_notes'])) ?>
    </div>
  <?php endif; ?>

  <?php if ($verificationStatus === 'verified'): ?>
    <div class="alert alert-info">
      <strong>Verification Date:</strong> <?= date('F d, Y', strtotime($user['verified_at'])) ?><br>
      Your shop is now visible on the marketplace and you can start adding products.
    </div>
  <?php endif; ?>

  <?php if ($verificationStatus !== 'verified'): ?>
    <!-- Verification Checklist -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon">
          <i class="fas fa-clipboard-check"></i>
        </div>
        <h3>Verification Checklist</h3>
      </div>

      <div class="checklist-item <?= !empty($user['business_name']) ? 'completed' : '' ?>">
        <div class="checklist-checkbox">
          <?php if (!empty($user['business_name'])): ?>
            <i class="fas fa-check"></i>
          <?php endif; ?>
        </div>
        <div class="checklist-content">
          <h4>Business Information</h4>
          <p>Provide your business name, registration number, and tax ID</p>
        </div>
      </div>

      <div class="checklist-item <?= !empty($user['business_address']) ? 'completed' : '' ?>">
        <div class="checklist-checkbox">
          <?php if (!empty($user['business_address'])): ?>
            <i class="fas fa-check"></i>
          <?php endif; ?>
        </div>
        <div class="checklist-content">
          <h4>Business Address</h4>
          <p>Enter your registered business address</p>
        </div>
      </div>

      <div class="checklist-item <?= !empty($documents) ? 'completed' : '' ?>">
        <div class="checklist-checkbox">
          <?php if (!empty($documents)): ?>
            <i class="fas fa-check"></i>
          <?php endif; ?>
        </div>
        <div class="checklist-content">
          <h4>Verification Documents</h4>
          <p>Upload business license, tax certificate, or incorporation documents</p>
        </div>
      </div>
    </div>

    <!-- Verification Form -->
    <form method="POST" action="<?= url('seller/verification/submit') ?>" enctype="multipart/form-data">
      <?= csrfField() ?>

      <div class="card">
        <div class="card-header">
          <div class="card-header-icon">
            <i class="fas fa-building"></i>
          </div>
          <h3>Business Information</h3>
        </div>

        <div class="form-group">
          <label for="business_name" class="form-label">
            Business Name <span class="required">*</span>
          </label>
          <input
            type="text"
            id="business_name"
            name="business_name"
            class="form-input"
            value="<?= htmlspecialchars($user['business_name'] ?? '') ?>"
            required
            placeholder="e.g., ABC Grocery Store Inc."
          >
        </div>

        <div class="form-group">
          <label for="business_number" class="form-label">
            Business Registration Number
          </label>
          <input
            type="text"
            id="business_number"
            name="business_number"
            class="form-input"
            value="<?= htmlspecialchars($user['business_number'] ?? '') ?>"
            placeholder="e.g., BN 123456789"
          >
          <p class="form-hint">Your CRA Business Number or provincial registration number</p>
        </div>

        <div class="form-group">
          <label for="tax_id" class="form-label">
            Tax ID / GST/HST Number
          </label>
          <input
            type="text"
            id="tax_id"
            name="tax_id"
            class="form-input"
            value="<?= htmlspecialchars($user['tax_id'] ?? '') ?>"
            placeholder="e.g., 123456789RT0001"
          >
          <p class="form-hint">Your GST/HST registration number if applicable</p>
        </div>

        <div class="form-group">
          <label for="business_address" class="form-label">
            Business Address <span class="required">*</span>
          </label>
          <textarea
            id="business_address"
            name="business_address"
            class="form-textarea"
            rows="3"
            required
            placeholder="Enter your complete business address"
          ><?= htmlspecialchars($user['business_address'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-header-icon">
            <i class="fas fa-file-upload"></i>
          </div>
          <h3>Verification Documents</h3>
        </div>

        <div class="form-group">
          <label class="form-label">
            Upload Documents <span class="required">*</span>
          </label>
          <p class="form-hint" style="margin-bottom: 12px;">
            Upload at least one of the following: Business License, Tax Certificate, Articles of Incorporation, or other official business documents (PDF, JPG, PNG - Max 5MB each)
          </p>

          <div class="file-upload-area" onclick="document.getElementById('documents').click()">
            <div class="file-icon">
              <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <p><strong>Click to upload</strong> or drag and drop</p>
            <p class="form-hint">PDF, JPG, PNG up to 5MB</p>
            <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(this)">
          </div>

          <div id="uploadedFiles" class="uploaded-files"></div>

          <?php if (!empty($documents)): ?>
            <div class="uploaded-files">
              <p class="form-hint" style="margin-bottom: 12px;"><strong>Previously uploaded documents:</strong></p>
              <?php foreach ($documents as $doc): ?>
                <div class="uploaded-file">
                  <div class="uploaded-file-info">
                    <i class="fas fa-file-pdf uploaded-file-icon"></i>
                    <span><?= htmlspecialchars($doc['name'] ?? 'Document') ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">
        <i class="fas fa-paper-plane"></i> Submit for Verification
      </button>
    </form>
  <?php endif; ?>
</div>

<script>
function handleFileSelect(input) {
  const filesContainer = document.getElementById('uploadedFiles');
  filesContainer.innerHTML = '';

  if (input.files.length > 0) {
    Array.from(input.files).forEach((file, index) => {
      // Validate file size
      if (file.size > 5 * 1024 * 1024) {
        alert(`${file.name} is too large. Maximum size is 5MB.`);
        return;
      }

      // Validate file type
      const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
      if (!allowedTypes.includes(file.type)) {
        alert(`${file.name} is not a valid file type. Please upload PDF, JPG, or PNG files.`);
        return;
      }

      const fileDiv = document.createElement('div');
      fileDiv.className = 'uploaded-file';
      fileDiv.innerHTML = `
        <div class="uploaded-file-info">
          <i class="fas fa-file-${file.type === 'application/pdf' ? 'pdf' : 'image'} uploaded-file-icon"></i>
          <span>${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
        </div>
      `;
      filesContainer.appendChild(fileDiv);
    });
  }
}

// Auto-hide flash messages after 5 seconds
setTimeout(function() {
    const flashMessages = document.querySelectorAll('[style*="position: fixed"]');
    flashMessages.forEach(msg => {
        if (msg.textContent.includes('Error') || msg.textContent.includes('Success')) {
            msg.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => msg.remove(), 300);
        }
    });
}, 5000);
</script>

</body>
</html>
