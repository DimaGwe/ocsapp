<?php
/**
 * Bulk Product Upload Page
 * File: app/Views/admin/products/bulk-upload.php
 */

$pageTitle = 'Bulk Product Upload';
$currentPage = 'products';

ob_start();
?>

<style>
  /* Page Layout */
  .bulk-upload-page {
    max-width: 1200px;
    margin: 0 auto;
  }

  /* Page Header */
  .page-header {
    margin-bottom: 32px;
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .page-subtitle {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Back Button */
  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 24px;
    transition: color var(--transition-base);
  }

  .back-btn:hover {
    color: var(--primary-600);
  }

  /* Alert */
  .alert {
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .alert-error {
    background: #fee2e2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
  }

  .alert-success {
    background: #dcfce7;
    border-left: 4px solid #22c55e;
    color: #166534;
  }

  .alert-info {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    color: #1e40af;
  }

  /* Card */
  .card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  .card-header {
    font-size: 20px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
  }

  /* Steps */
  .steps {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
  }

  .step {
    flex: 1;
    display: flex;
    align-items: flex-start;
    gap: 16px;
  }

  .step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 700;
    flex-shrink: 0;
  }

  .step-content h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
  }

  .step-content p {
    font-size: 14px;
    color: var(--gray-600);
  }

  /* Upload Zone */
  .upload-zone {
    border: 2px dashed var(--border);
    border-radius: var(--radius-md);
    padding: 48px 32px;
    text-align: center;
    transition: all var(--transition-base);
    cursor: pointer;
  }

  .upload-zone:hover {
    border-color: var(--primary);
    background: rgba(0, 178, 7, 0.02);
  }

  .upload-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 24px;
  }

  .upload-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .upload-text {
    font-size: 14px;
    color: var(--gray-600);
    margin-bottom: 24px;
  }

  .upload-btn {
    display: inline-block;
    padding: 12px 32px;
    background: var(--primary);
    color: white;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .upload-btn:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .download-template {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    margin-top: 16px;
  }

  .download-template:hover {
    color: var(--primary-600);
  }

  /* File Info */
  .file-info {
    background: var(--gray-50);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-top: 24px;
  }

  .file-info-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .file-details {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .file-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .file-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
  }

  .file-size {
    font-size: 12px;
    color: var(--gray-500);
  }

  .file-remove {
    padding: 8px 16px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .file-remove:hover {
    background: #dc2626;
  }

  /* Progress */
  .upload-progress {
    background: #dbeafe;
    border: 1px solid #93c5fd;
    border-radius: var(--radius-md);
    padding: 20px;
    margin-top: 24px;
  }

  .progress-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .progress-spinner {
    color: #3b82f6;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .progress-text {
    font-size: 14px;
    font-weight: 600;
    color: #1e40af;
  }

  .progress-bar {
    width: 100%;
    height: 8px;
    background: #bfdbfe;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    background: #3b82f6;
    transition: width 0.3s ease;
  }

  .progress-details {
    display: flex;
    justify-content: space-between;
    margin-top: 12px;
    font-size: 12px;
    color: #1e40af;
  }

  /* Results */
  .results {
    margin-top: 24px;
  }

  .results-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }

  .result-stat {
    background: var(--gray-50);
    padding: 20px;
    border-radius: var(--radius-md);
    text-align: center;
  }

  .result-stat.success {
    background: #dcfce7;
    border: 2px solid #22c55e;
  }

  .result-stat.error {
    background: #fee2e2;
    border: 2px solid #ef4444;
  }

  .result-stat.skip {
    background: #fef3c7;
    border: 2px solid #f59e0b;
  }

  .result-number {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .result-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .result-stat.success .result-number {
    color: #166534;
  }

  .result-stat.error .result-number {
    color: #991b1b;
  }

  .result-stat.skip .result-number {
    color: #92400e;
  }

  /* Error List */
  .error-list {
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    max-height: 400px;
    overflow-y: auto;
  }

  .error-item {
    padding: 16px;
    border-bottom: 1px solid var(--border);
  }

  .error-item:last-child {
    border-bottom: none;
  }

  .error-row {
    font-size: 12px;
    font-weight: 600;
    color: #ef4444;
    margin-bottom: 4px;
  }

  .error-message {
    font-size: 14px;
    color: var(--gray-700);
  }

  /* Buttons */
  .btn {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    border: none;
    text-decoration: none;
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
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  /* Hidden */
  .hidden {
    display: none;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .steps {
      flex-direction: column;
    }

    .results-summary {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="bulk-upload-page">
  <!-- Back Button -->
  <a href="<?= url('admin/products') ?>" class="back-btn">
    <i class="fas fa-arrow-left"></i> Back to Products
  </a>

  <!-- Page Header -->
  <div class="page-header">
    <h1>Bulk Product Upload</h1>
    <p class="page-subtitle">Upload multiple products at once using a CSV file</p>
  </div>

  <!-- Alerts -->
  <?php if (hasFlash('error')): ?>
    <div class="alert alert-error">
      <?= getFlash('error') ?>
    </div>
  <?php endif; ?>

  <?php if (hasFlash('success')): ?>
    <div class="alert alert-success">
      <?= getFlash('success') ?>
    </div>
  <?php endif; ?>

  <!-- Info Alert -->
  <div class="alert alert-info">
    <strong>Important:</strong> Make sure your CSV file follows the template format. Download the template below to get started.
  </div>

  <!-- How It Works -->
  <div class="card">
    <h2 class="card-header">How It Works</h2>
    <div class="steps">
      <div class="step">
        <div class="step-number">1</div>
        <div class="step-content">
          <h3>Download Template</h3>
          <p>Get the CSV template with all required columns</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <div class="step-content">
          <h3>Fill Your Data</h3>
          <p>Add your products following the template format</p>
        </div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-content">
          <h3>Upload & Import</h3>
          <p>Upload the file and we'll import all products</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload Card -->
  <div class="card">
    <h2 class="card-header">Upload CSV File</h2>

    <!-- Download Template -->
    <div style="margin-bottom: 32px;">
      <a href="<?= url('admin/products/bulk-upload/template') ?>" class="download-template">
        <i class="fas fa-download"></i>
        Download CSV Template
      </a>
    </div>

    <!-- Upload Zone -->
    <form id="uploadForm" enctype="multipart/form-data">
      <?= csrfField() ?>
      
      <div class="upload-zone" id="uploadZone">
        <div class="upload-icon">
          <i class="fas fa-file-csv"></i>
        </div>
        <h3 class="upload-title">Drop your CSV file here</h3>
        <p class="upload-text">or click to browse from your computer</p>
        
        <input 
          type="file" 
          id="csvFile" 
          name="csv_file" 
          accept=".csv,text/csv"
          style="display: none;"
          onchange="handleFileSelect(this.files[0])"
        >
        <label for="csvFile" class="upload-btn">
          <i class="fas fa-upload"></i> Choose CSV File
        </label>
      </div>

      <!-- File Info -->
      <div id="fileInfo" class="file-info hidden">
        <div class="file-info-content">
          <div class="file-details">
            <div class="file-icon">
              <i class="fas fa-file-csv"></i>
            </div>
            <div>
              <div class="file-name" id="fileName"></div>
              <div class="file-size" id="fileSize"></div>
            </div>
          </div>
          <button type="button" class="file-remove" onclick="removeFile()">
            <i class="fas fa-times"></i> Remove
          </button>
        </div>
      </div>

      <!-- Upload Progress -->
      <div id="uploadProgress" class="upload-progress hidden">
        <div class="progress-header">
          <i class="fas fa-spinner progress-spinner"></i>
          <span class="progress-text">Processing products...</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" id="progressFill" style="width: 0%"></div>
        </div>
        <div class="progress-details">
          <span id="progressText">0 of 0 processed</span>
          <span id="progressPercent">0%</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px;">
        <a href="<?= url('admin/products') ?>" class="btn btn-secondary">
          Cancel
        </a>
        <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
          <i class="fas fa-cloud-upload-alt"></i> Upload & Import
        </button>
      </div>
    </form>
  </div>

  <!-- Results Card -->
  <div id="resultsCard" class="card hidden">
    <h2 class="card-header">Import Results</h2>

    <!-- Results Summary -->
    <div class="results-summary">
      <div class="result-stat success">
        <div class="result-number" id="successCount">0</div>
        <div class="result-label">Success</div>
      </div>
      <div class="result-stat error">
        <div class="result-number" id="errorCount">0</div>
        <div class="result-label">Errors</div>
      </div>
      <div class="result-stat skip">
        <div class="result-number" id="skipCount">0</div>
        <div class="result-label">Skipped</div>
      </div>
    </div>

    <!-- Error List -->
    <div id="errorsList" class="hidden">
      <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--dark);">
        Errors & Skipped Rows
      </h3>
      <div class="error-list" id="errorsContainer"></div>
    </div>

    <!-- Actions -->
    <div style="margin-top: 24px; display: flex; gap: 12px;">
      <a href="<?= url('admin/products') ?>" class="btn btn-primary">
        <i class="fas fa-list"></i> View All Products
      </a>
      <button onclick="location.reload()" class="btn btn-secondary">
        <i class="fas fa-redo"></i> Upload Another File
      </button>
    </div>
  </div>
</div>

<script>
let selectedFile = null;

// Drag and drop
const uploadZone = document.getElementById('uploadZone');
uploadZone.addEventListener('dragover', (e) => {
  e.preventDefault();
  uploadZone.style.borderColor = 'var(--primary)';
  uploadZone.style.background = 'rgba(0, 178, 7, 0.05)';
});

uploadZone.addEventListener('dragleave', () => {
  uploadZone.style.borderColor = '';
  uploadZone.style.background = '';
});

uploadZone.addEventListener('drop', (e) => {
  e.preventDefault();
  uploadZone.style.borderColor = '';
  uploadZone.style.background = '';
  
  const file = e.dataTransfer.files[0];
  if (file && file.name.endsWith('.csv')) {
    handleFileSelect(file);
  } else {
    alert('Please select a valid CSV file');
  }
});

function handleFileSelect(file) {
  if (!file) return;
  
  selectedFile = file;
  
  // Show file info
  document.getElementById('fileName').textContent = file.name;
  document.getElementById('fileSize').textContent = formatFileSize(file.size);
  document.getElementById('fileInfo').classList.remove('hidden');
  
  // Enable upload button
  document.getElementById('uploadBtn').disabled = false;
}

function removeFile() {
  selectedFile = null;
  document.getElementById('csvFile').value = '';
  document.getElementById('fileInfo').classList.add('hidden');
  document.getElementById('uploadBtn').disabled = true;
}

function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

// Form submit
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  if (!selectedFile) return;
  
  const formData = new FormData();
  formData.append('csv_file', selectedFile);
  formData.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', document.querySelector('meta[name="csrf-token"]').content);
  
  // Show progress
  document.getElementById('uploadProgress').classList.remove('hidden');
  document.getElementById('uploadBtn').disabled = true;
  
  try {
    const response = await fetch('<?= url('admin/products/bulk-upload/process') ?>', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    // Hide progress
    document.getElementById('uploadProgress').classList.add('hidden');
    
    // Show results
    showResults(result);
    
  } catch (error) {
    console.error('Upload error:', error);
    alert('Upload failed. Please try again.');
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('uploadBtn').disabled = false;
  }
});

function showResults(result) {
  // Update counts
  document.getElementById('successCount').textContent = result.success || 0;
  document.getElementById('errorCount').textContent = result.errors ? result.errors.length : 0;
  document.getElementById('skipCount').textContent = result.skipped || 0;
  
  // Show errors if any
  if (result.errors && result.errors.length > 0) {
    const errorsContainer = document.getElementById('errorsContainer');
    errorsContainer.innerHTML = '';
    
    result.errors.forEach(error => {
      const errorItem = document.createElement('div');
      errorItem.className = 'error-item';
      errorItem.innerHTML = `
        <div class="error-row">Row ${error.row}</div>
        <div class="error-message">${error.message}</div>
      `;
      errorsContainer.appendChild(errorItem);
    });
    
    document.getElementById('errorsList').classList.remove('hidden');
  }
  
  // Show results card
  document.getElementById('resultsCard').classList.remove('hidden');
  
  // Scroll to results
  document.getElementById('resultsCard').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>