<?php
$pageTitle = (($_SESSION['language'] ?? 'fr') === 'fr') ? 'Ajouter un produit' : 'Add New Product';
require dirname(__DIR__) . '/layout-header.php';
?>

<style>
  .form-card {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    max-width: 800px;
  }

  .form-header {
    margin-bottom: 32px;
  }

  .form-header h2 {
    font-size: 24px;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .form-header p {
    color: var(--gray-600);
    font-size: 14px;
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

  .form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.2s;
  }

  .form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .form-hint {
    font-size: 13px;
    color: var(--gray-600);
    margin-top: 6px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }

  .checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 8px;
  }

  .checkbox-wrapper input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .checkbox-wrapper label {
    font-size: 14px;
    color: var(--gray-700);
    cursor: pointer;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--gray-200);
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
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-dark);
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  .image-preview-container {
    margin-top: 12px;
    display: none;
  }

  .image-preview-container.active {
    display: block;
  }

  .image-preview {
    width: 100%;
    max-width: 300px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid var(--gray-200);
  }

  .remove-image-btn {
    margin-top: 8px;
    padding: 6px 12px;
    background: var(--danger);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .remove-image-btn:hover {
    background: #dc2626;
  }

  .img-source-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }

  .btn-img-source {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: 2px solid var(--gray-200);
    background: white;
    color: var(--gray-700);
    transition: all 0.2s;
    font-family: 'Poppins', sans-serif;
  }

  .btn-img-source:hover { border-color: var(--primary); color: var(--primary); }
  .btn-img-source.camera { border-color: #3b82f6; color: #3b82f6; }
  .btn-img-source.camera:hover { background: #eff6ff; }

  .camera-wrap {
    display: none;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 10px;
  }

  .camera-wrap.active { display: flex; }

  .camera-wrap video {
    width: 100%;
    max-width: 420px;
    border-radius: 8px;
    border: 2px solid #3b82f6;
    background: #000;
  }

  .camera-controls { display: flex; gap: 10px; }

  .btn-capture {
    padding: 9px 18px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-family: 'Poppins', sans-serif;
  }

  .btn-capture:hover { background: #2563eb; }

  .btn-cancel-cam {
    padding: 9px 18px;
    background: var(--gray-200);
    color: var(--gray-700);
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
  }

  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="form-card">
  <div class="form-header">
    <h2><?= $fr ? 'Ajouter un produit' : 'Add New Product' ?></h2>
    <p><?= $fr ? 'Ajoutez un produit à votre catalogue fournisseur' : 'Add a product to your supplier catalog' ?></p>
  </div>

  <form method="POST" action="<?= url('supplier/products/store') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="form-group">
      <label for="product_name" class="form-label">
        <?= $fr ? 'Nom du produit' : 'Product Name' ?> <span class="required">*</span>
      </label>
      <input
        type="text"
        id="product_name"
        name="product_name"
        class="form-input"
        required
        placeholder="<?= $fr ? 'ex. Bananes biologiques' : 'e.g., Organic Bananas' ?>"
      >
    </div>

    <div class="form-group">
      <label class="form-label"><?= $fr ? 'Image du produit' : 'Product Image' ?></label>

      <!-- Hidden file input — populated either by file picker or camera capture -->
      <input type="file" id="image" name="image" accept="image/*" style="display:none" onchange="previewImage(this)">

      <!-- Source buttons -->
      <div class="img-source-row">
        <button type="button" class="btn-img-source" onclick="document.getElementById('image').click()">
          <i class="fas fa-folder-open"></i> <?= $fr ? 'Choisir un fichier' : 'Choose File' ?>
        </button>
        <button type="button" class="btn-img-source camera" id="btnTakePhoto" onclick="openCamera()">
          <i class="fas fa-camera"></i> <?= $fr ? 'Prendre une photo' : 'Take Photo' ?>
        </button>
      </div>

      <!-- Live camera view -->
      <div class="camera-wrap" id="cameraWrap">
        <video id="cameraVideo" autoplay playsinline muted></video>
        <div class="camera-controls">
          <button type="button" class="btn-capture" onclick="capturePhoto()">
            <i class="fas fa-circle"></i> <?= $fr ? 'Capturer' : 'Capture' ?>
          </button>
          <button type="button" class="btn-cancel-cam" onclick="closeCamera()"><?= $fr ? 'Annuler' : 'Cancel' ?></button>
        </div>
      </div>
      <canvas id="cameraCanvas" style="display:none;"></canvas>

      <p class="form-hint"><?= $fr ? 'Choisissez un fichier ou prenez une photo (JPG, PNG, GIF, WebP — Max 5 Mo)' : 'Choose a file or take a photo with your camera (JPG, PNG, GIF, WebP — Max 5MB)' ?></p>

      <div id="imagePreviewContainer" class="image-preview-container">
        <img id="imagePreview" src="" alt="Image preview" class="image-preview">
        <button type="button" class="remove-image-btn" onclick="removeImage()">
          <i class="fas fa-times"></i> <?= $fr ? 'Supprimer l\'image' : 'Remove Image' ?>
        </button>
      </div>
    </div>

    <div class="form-group">
      <label for="marketplace_product_id" class="form-label">
        <?= $fr ? 'Lier à un produit de la marketplace (optionnel)' : 'Link to Marketplace Product (Optional)' ?>
      </label>
      <select id="marketplace_product_id" name="marketplace_product_id" class="form-select">
        <option value=""><?= $fr ? '-- Non lié --' : '-- Not Linked --' ?></option>
        <?php if (!empty($marketplaceProducts)): ?>
          <?php foreach ($marketplaceProducts as $mp): ?>
            <option value="<?= $mp['id'] ?>">
              <?= htmlspecialchars($mp['name']) ?>
              <?= $mp['sku'] ? ' (' . htmlspecialchars($mp['sku']) . ')' : '' ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
      <p class="form-hint"><?= $fr ? 'Liez ce produit à un article de la marketplace pour activer les mises à jour automatiques du stock lors de la réception des bons de commande' : 'Link this to a product in the marketplace to enable automatic stock updates when purchase orders are received' ?></p>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="sku" class="form-label">SKU</label>
        <input
          type="text"
          id="sku"
          name="sku"
          class="form-input"
          placeholder="e.g., BAN-ORG-001"
        >
        <p class="form-hint"><?= $fr ? 'Unité de gestion des stocks (optionnel)' : 'Stock Keeping Unit (optional)' ?></p>
      </div>

      <div class="form-group">
        <label for="unit" class="form-label"><?= $fr ? 'Unité' : 'Unit' ?></label>
        <select id="unit" name="unit" class="form-select">
          <option value="unit"><?= $fr ? 'Unité' : 'Unit' ?></option>
          <option value="box"><?= $fr ? 'Boîte' : 'Box' ?></option>
          <option value="case"><?= $fr ? 'Caisse' : 'Case' ?></option>
          <option value="kg"><?= $fr ? 'Kilogramme (kg)' : 'Kilogram (kg)' ?></option>
          <option value="lb"><?= $fr ? 'Livre (lb)' : 'Pound (lb)' ?></option>
          <option value="liter"><?= $fr ? 'Litre' : 'Liter' ?></option>
          <option value="gallon">Gallon</option>
          <option value="dozen"><?= $fr ? 'Douzaine' : 'Dozen' ?></option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="description" class="form-label"><?= $fr ? 'Description' : 'Description' ?></label>
      <textarea
        id="description"
        name="description"
        rows="4"
        class="form-textarea"
        placeholder="<?= $fr ? 'Décrivez ce produit en détail...' : 'Provide details about this product...' ?>"
      ></textarea>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="unit_price" class="form-label">
          <?= $fr ? 'Prix unitaire' : 'Unit Price' ?> <span class="required">*</span>
        </label>
        <input
          type="number"
          id="unit_price"
          name="unit_price"
          class="form-input"
          step="0.01"
          min="0"
          required
          placeholder="0.00"
        >
        <p class="form-hint"><?= $fr ? 'Prix par unité en CAD' : 'Price per unit in CAD' ?></p>
      </div>

      <div class="form-group">
        <label for="weight_kg" class="form-label">
          <?= $fr ? 'Poids par unité (kg)' : 'Weight per Unit (kg)' ?> <span class="required">*</span>
        </label>
        <input
          type="number"
          id="weight_kg"
          name="weight_kg"
          class="form-input"
          step="0.01"
          min="0"
          required
          placeholder="0.00"
        >
        <p class="form-hint"><?= $fr ? 'Poids en kilogrammes — utilisé pour calculer les frais de manutention (0,20 $/kg)' : 'Weight in kilograms - used to calculate handling fees ($0.20/kg)' ?></p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="minimum_order_quantity" class="form-label">
          <?= $fr ? 'Quantité minimale de commande' : 'Minimum Order Quantity' ?>
        </label>
        <input
          type="number"
          id="minimum_order_quantity"
          name="minimum_order_quantity"
          class="form-input"
          min="1"
          value="1"
        >
        <p class="form-hint"><?= $fr ? 'Nombre minimum d\'unités par commande' : 'Minimum units per order' ?></p>
      </div>

      <div class="form-group">
        <label for="stock_quantity" class="form-label">
          <?= $fr ? 'Quantité en stock disponible' : 'Available Stock Quantity' ?>
        </label>
        <input
          type="number"
          id="stock_quantity"
          name="stock_quantity"
          class="form-input"
          min="0"
          placeholder="e.g., 100"
        >
        <p class="form-hint"><?= $fr ? 'Nombre d\'unités disponibles à l\'achat (laisser vide pour illimité)' : 'Number of units available for purchase (leave empty for unlimited)' ?></p>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label for="lead_time_days" class="form-label"><?= $fr ? 'Délai de livraison (jours)' : 'Lead Time (Days)' ?></label>
        <input
          type="number"
          id="lead_time_days"
          name="lead_time_days"
          class="form-input"
          min="0"
          value="7"
        >
        <p class="form-hint"><?= $fr ? 'Nombre de jours pour exécuter les commandes' : 'Number of days to fulfill orders' ?></p>
      </div>

      <div class="form-group"></div>
    </div>

    <div class="form-group">
      <label for="notes" class="form-label"><?= $fr ? 'Notes' : 'Notes' ?></label>
      <textarea
        id="notes"
        name="notes"
        rows="3"
        class="form-textarea"
        placeholder="<?= $fr ? 'Notes supplémentaires ou instructions de manutention...' : 'Any additional notes or special handling instructions...' ?>"
      ></textarea>
    </div>

    <div class="checkbox-wrapper">
      <input type="checkbox" id="is_available" name="is_available" value="1" checked>
      <label for="is_available"><?= $fr ? 'Le produit est disponible pour commande' : 'Product is available for ordering' ?></label>
    </div>

    <div class="form-actions">
      <a href="<?= url('supplier/products') ?>" class="btn btn-secondary">
        <?= $fr ? 'Annuler' : 'Cancel' ?>
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> <?= $fr ? 'Enregistrer le produit' : 'Save Product' ?>
      </button>
    </div>
  </form>
</div>

<script>
function previewImage(input) {
  const preview = document.getElementById('imagePreview');
  const previewContainer = document.getElementById('imagePreviewContainer');

  if (input.files && input.files[0]) {
    const fileSize = input.files[0].size / 1024 / 1024;
    if (fileSize > 5) {
      alert('<?= $fr ? 'Le fichier image est trop volumineux. La taille maximale est de 5 Mo.' : 'Image file is too large. Maximum size is 5MB.' ?>');
      input.value = '';
      previewContainer.classList.remove('active');
      return;
    }

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(input.files[0].type)) {
      alert('<?= $fr ? 'Type de fichier invalide. Veuillez télécharger uniquement des images JPG, PNG, GIF ou WebP.' : 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.' ?>');
      input.value = '';
      previewContainer.classList.remove('active');
      return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      previewContainer.classList.add('active');
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    previewContainer.classList.remove('active');
  }
}

function removeImage() {
  const input = document.getElementById('image');
  document.getElementById('imagePreview').src = '';
  document.getElementById('imagePreviewContainer').classList.remove('active');
  input.value = '';
}

// ── Camera ────────────────────────────────────────────────
let cameraStream = null;

function showCameraError(msg) {
  let el = document.getElementById('cameraPermissionMsg');
  if (!el) {
    el = document.createElement('div');
    el.id = 'cameraPermissionMsg';
    el.style.cssText = 'margin:12px 0;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:13px;color:#991b1b;line-height:1.6;';
    document.getElementById('cameraWrap').insertAdjacentElement('beforebegin', el);
  }
  el.innerHTML = msg;
  el.style.display = 'block';
}

function hideCameraError() {
  const el = document.getElementById('cameraPermissionMsg');
  if (el) el.style.display = 'none';
}

function openCamera() {
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    showCameraError('<?= $fr ? '⚠️ L\'accès à la caméra nécessite une connexion sécurisée (HTTPS) ou n\'est pas pris en charge par ce navigateur. Veuillez télécharger une image à la place.' : '⚠️ Camera access requires a secure (HTTPS) connection or is not supported in this browser. Please upload an image file instead.' ?>');
    return;
  }
  hideCameraError();
  if (navigator.permissions) {
    navigator.permissions.query({ name: 'camera' }).then(function(result) {
      if (result.state === 'denied') {
        showCameraError(
          '<?= $fr ? '🚫 <strong>Accès à la caméra bloqué.</strong> Pour l\'activer :<br>1. Cliquez sur l\'icône <strong>verrou 🔒</strong> dans la barre d\'adresse.<br>2. Réglez <strong>Caméra</strong> sur <em>Autoriser</em>.<br>3. Rafraîchissez la page.' : '🚫 <strong>Camera access is blocked.</strong> To enable it:<br>1. Click the <strong>lock 🔒</strong> or camera icon in your browser\'s address bar.<br>2. Set <strong>Camera</strong> to <em>Allow</em>.<br>3. Refresh the page and try again.' ?>'
        );
      } else {
        startCamera();
      }
    }).catch(startCamera);
  } else {
    startCamera();
  }
}

function startCamera() {
  // Use 'ideal' so it prefers rear camera but falls back to front camera on desktop
  navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } })
    .then(function(stream) {
      cameraStream = stream;
      const video = document.getElementById('cameraVideo');
      video.srcObject = stream;
      document.getElementById('cameraWrap').classList.add('active');
      hideCameraError();
    })
    .catch(function(err) {
      if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
        showCameraError(
          '<?= $fr ? '🚫 <strong>Accès à la caméra refusé.</strong> Pour corriger :<br>1. Cliquez sur l\'icône <strong>verrou 🔒</strong> dans la barre d\'adresse.<br>2. Réglez <strong>Caméra</strong> sur <em>Autoriser</em>.<br>3. Rafraîchissez la page.' : '🚫 <strong>Camera access was denied.</strong> To fix this:<br>1. Click the <strong>lock 🔒</strong> or camera icon in your browser\'s address bar.<br>2. Set <strong>Camera</strong> to <em>Allow</em>.<br>3. Refresh the page and try again.' ?>'
        );
      } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
        showCameraError('<?= $fr ? '⚠️ Aucune caméra trouvée sur cet appareil. Veuillez télécharger une image à la place.' : '⚠️ No camera was found on this device. Please upload an image file instead.' ?>');
      } else {
        showCameraError('<?= $fr ? '⚠️ Impossible de démarrer la caméra : ' : '⚠️ Could not start camera: ' ?>' + err.message);
      }
    });
}

function capturePhoto() {
  const video  = document.getElementById('cameraVideo');
  const canvas = document.getElementById('cameraCanvas');
  canvas.width  = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0);

  canvas.toBlob(function(blob) {
    const file = new File([blob], 'photo_' + Date.now() + '.jpg', { type: 'image/jpeg' });
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('image');
    input.files = dt.files;

    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('imagePreview').src = e.target.result;
      document.getElementById('imagePreviewContainer').classList.add('active');
    };
    reader.readAsDataURL(blob);

    closeCamera();
  }, 'image/jpeg', 0.92);
}

function closeCamera() {
  if (cameraStream) {
    cameraStream.getTracks().forEach(t => t.stop());
    cameraStream = null;
  }
  document.getElementById('cameraWrap').classList.remove('active');
  document.getElementById('cameraVideo').srcObject = null;
}

// Hide Take Photo button if camera API is unavailable
if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
  const btn = document.getElementById('btnTakePhoto');
  if (btn) btn.style.display = 'none';
}
</script>

<?php require dirname(__DIR__) . '/layout-footer.php'; ?>
