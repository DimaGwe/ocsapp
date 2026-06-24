<?php
$pageTitle = 'Supplier Catalog';
$currentPage = 'supplier-catalog';
ob_start();
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }

  .btn-draft {
    position: relative;
    background: #10b981;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
  }

  .btn-draft:hover {
    background: #059669;
    transform: translateY(-1px);
  }

  .draft-badge {
    background: white;
    color: #10b981;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
  }

  .filters-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 16px;
    align-items: end;
  }

  .form-group {
    margin-bottom: 0;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .form-input, .form-select {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
  }

  .btn-filter {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
  }

  .product-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
  }

  .product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
  }

  .product-image-container {
    width: 100%;
    height: 200px;
    background: var(--gray-100);
    border-radius: 8px;
    margin-bottom: 16px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    color: var(--gray-400);
    font-size: 48px;
  }

  .product-supplier {
    font-size: 12px;
    color: var(--gray-600);
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .product-name {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .product-sku {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 12px;
  }

  .product-description {
    font-size: 14px;
    color: var(--gray-700);
    line-height: 1.5;
    margin-bottom: 12px;
    max-height: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .product-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-bottom: 16px;
    padding: 12px;
    background: var(--gray-50);
    border-radius: 8px;
  }

  .info-item {
    font-size: 13px;
  }

  .info-label {
    color: var(--gray-600);
    font-weight: 500;
  }

  .info-value {
    color: var(--dark);
    font-weight: 600;
  }

  .product-price {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 16px;
  }

  .product-stock-link {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
    margin-bottom: 12px;
    display: inline-block;
  }

  .stock-linked {
    background: #dcfce7;
    color: #166534;
  }

  .stock-not-linked {
    background: #fee2e2;
    color: #991b1b;
  }

  .add-to-draft-form {
    display: flex;
    gap: 8px;
  }

  .qty-input {
    width: 70px;
    padding: 8px;
    border: 2px solid var(--border);
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
  }

  .btn-add {
    flex: 1;
    padding: 8px 16px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .btn-add:hover {
    background: var(--primary-600);
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 64px;
    margin-bottom: 16px;
    display: block;
  }

  .btn-backups {
    width: 100%;
    margin-top: 8px;
    padding: 7px 16px;
    background: white;
    color: #4f46e5;
    border: 2px solid #4f46e5;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s;
  }
  .btn-backups:hover { background: #4f46e5; color: white; }
  .btn-backups .backup-count {
    background: #4f46e5;
    color: white;
    border-radius: 10px;
    padding: 1px 7px;
    font-size: 11px;
  }
  .btn-backups:hover .backup-count { background: white; color: #4f46e5; }

  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }
  .modal-overlay.open { display: flex; }
  .modal-box {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 640px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  }
  .modal-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }
  .modal-title { font-size: 18px; font-weight: 700; color: #111; }
  .modal-subtitle { font-size: 13px; color: #666; margin-top: 2px; }
  .modal-close { background: none; border: none; font-size: 20px; color: #666; cursor: pointer; line-height: 1; flex-shrink: 0; }
  .modal-body { padding: 20px 24px; overflow-y: auto; flex: 1; }
  .modal-footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; }

  .backup-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: grab;
  }
  .backup-priority {
    width: 24px; height: 24px;
    background: #4f46e5; color: white;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
  }
  .backup-info { flex: 1; min-width: 0; }
  .backup-name { font-weight: 600; font-size: 14px; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .backup-meta { font-size: 12px; color: #666; }
  .backup-price { font-weight: 700; color: #059669; font-size: 14px; flex-shrink: 0; }
  .btn-remove-backup { background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; font-size: 16px; flex-shrink: 0; }
  .auto-tag { display: inline-block; background: #e0e7ff; color: #4f46e5; font-size: 11px; font-weight: 600; padding: 2px 7px; border-radius: 10px; margin-left: 6px; }
  .section-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin: 16px 0 8px; }
  .add-backup-row { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; }
  .add-backup-row select { flex: 1; min-width: 180px; padding: 9px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 13px; }
  .add-backup-row input { width: 150px; padding: 9px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 13px; }
  .btn-add-backup { padding: 9px 16px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px; white-space: nowrap; }
  .btn-add-backup:hover { background: #4338ca; }
  .modal-loader { text-align: center; padding: 32px; color: #6b7280; }
  .empty-backups { text-align: center; padding: 24px; color: #9ca3af; font-size: 14px; }
</style>

<div class="page-header">
  <h1 class="page-title">Supplier Catalog</h1>
  <div class="header-actions">
    <a href="<?= url('admin/supplier-catalog/draft') ?>" class="btn-draft">
      <i class="fas fa-shopping-cart"></i>
      Draft List
      <?php if ($draftCount > 0): ?>
        <span class="draft-badge" id="draftBadge"><?= $draftCount ?></span>
      <?php endif; ?>
    </a>
  </div>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/supplier-catalog') ?>">
    <div class="filters-grid">
      <div class="form-group">
        <label for="search" class="form-label">Search</label>
        <input
          type="text"
          id="search"
          name="search"
          placeholder="Search products, suppliers, SKUs..."
          value="<?= htmlspecialchars($search) ?>"
          class="form-input"
        >
      </div>

      <div class="form-group">
        <label for="supplier" class="form-label">Supplier</label>
        <select id="supplier" name="supplier" class="form-select">
          <option value="">All Suppliers</option>
          <?php foreach ($suppliers as $supplier): ?>
            <option value="<?= $supplier['id'] ?>" <?= $supplierId == $supplier['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($supplier['name'] ?? $supplier['company_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="sort" class="form-label">Sort By</label>
        <select id="sort" name="sort" class="form-select">
          <option value="product_name" <?= $sortBy === 'product_name' ? 'selected' : '' ?>>Product Name</option>
          <option value="supplier" <?= $sortBy === 'supplier' ? 'selected' : '' ?>>Supplier</option>
          <option value="price_low" <?= $sortBy === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_high" <?= $sortBy === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="lead_time" <?= $sortBy === 'lead_time' ? 'selected' : '' ?>>Lead Time</option>
        </select>
      </div>

      <button type="submit" class="btn-filter">
        <i class="fas fa-filter"></i> Filter
      </button>
    </div>
  </form>
</div>

<!-- Products Grid -->
<?php if (empty($products)): ?>
  <div class="empty-state">
    <i class="fas fa-box-open"></i>
    <h3>No products found</h3>
    <p>Try adjusting your filters or search terms</p>
  </div>
<?php else: ?>
  <div class="products-grid">
    <?php foreach ($products as $product): ?>
      <div class="product-card">
        <div class="product-image-container">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= asset($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
          <?php else: ?>
            <div class="product-image-placeholder">
              <i class="fas fa-box"></i>
            </div>
          <?php endif; ?>
        </div>

        <div class="product-supplier">
          <i class="fas fa-building"></i>
          <?= htmlspecialchars($product['supplier_name'] ?? $product['supplier_company_name']) ?>
        </div>

        <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>

        <?php if ($product['sku']): ?>
          <div class="product-sku">SKU: <?= htmlspecialchars($product['sku']) ?></div>
        <?php endif; ?>

        <?php if ($product['description']): ?>
          <div class="product-description"><?= htmlspecialchars($product['description']) ?></div>
        <?php endif; ?>

        <div class="product-info">
          <?php
            $stockQty = (int)($product['stock_quantity'] ?? 0);
            $stockColor = $stockQty > 20 ? '#059669' : ($stockQty > 0 ? '#d97706' : '#dc2626');
          ?>
          <div class="info-item">
            <div class="info-label">Stock</div>
            <div class="info-value" style="color:<?= $stockColor ?>;font-weight:700;"><?= number_format($stockQty) ?> units</div>
          </div>
          <div class="info-item">
            <div class="info-label">Unit</div>
            <div class="info-value"><?= htmlspecialchars($product['unit']) ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Min Order</div>
            <div class="info-value"><?= $product['minimum_order_quantity'] ?> <?= htmlspecialchars($product['unit']) ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Lead Time</div>
            <div class="info-value"><?= $product['lead_time_days'] ?> days</div>
          </div>
        </div>

        <?php if ($product['marketplace_product_id']): ?>
          <div class="product-stock-link stock-linked">
            <i class="fas fa-link"></i> Linked: <?= htmlspecialchars($product['marketplace_product_name']) ?>
          </div>
        <?php else: ?>
          <div class="product-stock-link stock-not-linked">
            <i class="fas fa-unlink"></i> Not linked to inventory
          </div>
        <?php endif; ?>

        <div class="product-price">
          <?= currencySymbol() ?><?= number_format($product['unit_price'], 2) ?>
          <span style="font-size: 14px; font-weight: 500; color: var(--gray-600);">/ <?= htmlspecialchars($product['unit']) ?></span>
        </div>

        <button type="button" class="btn-backups" onclick="openBackupsModal(<?= $product['id'] ?>, <?= htmlspecialchars(json_encode($product['product_name'])) ?>)"
                id="backups-btn-<?= $product['id'] ?>">
          <i class="fas fa-layer-group"></i>
          Manage Backups
        </button>

        <form class="add-to-draft-form" onsubmit="addToDraft(event, <?= $product['id'] ?>, <?= $stockQty ?>)">
          <input
            type="number"
            class="qty-input"
            id="qty-<?= $product['id'] ?>"
            value="<?= min($product['minimum_order_quantity'], $stockQty ?: $product['minimum_order_quantity']) ?>"
            min="<?= $product['minimum_order_quantity'] ?>"
            <?php if ($stockQty > 0): ?>max="<?= $stockQty ?>"<?php endif; ?>
            step="1"
          >
          <?php if ($stockQty > 0): ?>
          <button type="submit" class="btn-add">
            <i class="fas fa-plus"></i>
            Add to Draft
          </button>
          <?php else: ?>
          <button type="button" class="btn-add" style="background:#d1d5db;cursor:not-allowed;" disabled>
            <i class="fas fa-ban"></i>
            Out of Stock
          </button>
          <?php endif; ?>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Backup Suppliers Modal -->
<div class="modal-overlay" id="backupsModal" onclick="if(event.target===this) closeBackupsModal()">
  <div class="modal-box">
    <div class="modal-header">
      <div>
        <div class="modal-title"><i class="fas fa-layer-group" style="color:#4f46e5;margin-right:8px;"></i>Backup Suppliers</div>
        <div class="modal-subtitle" id="modalSubtitle">Loading...</div>
      </div>
      <button class="modal-close" onclick="closeBackupsModal()">&times;</button>
    </div>
    <div class="modal-body" id="modalBody">
      <div class="modal-loader"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
    </div>
    <div class="modal-footer">
      <div class="section-label">Add New Backup</div>
      <div class="add-backup-row">
        <select id="newBackupSelect">
          <option value="">— Select a supplier product —</option>
        </select>
        <input type="text" id="newBackupNotes" placeholder="Notes (optional)" maxlength="255">
        <button class="btn-add-backup" onclick="addBackup()">
          <i class="fas fa-plus"></i> Add
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const csrfName  = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

let currentProductId = null;
let currentExplicit  = [];

// ── Open modal ──────────────────────────────────────────────────────────────
function openBackupsModal(productId, productName) {
  currentProductId = productId;
  document.getElementById('modalSubtitle').textContent = productName;
  document.getElementById('modalBody').innerHTML = '<div class="modal-loader"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
  document.getElementById('newBackupSelect').innerHTML = '<option value="">— Select a supplier product —</option>';
  document.getElementById('newBackupNotes').value = '';
  document.getElementById('backupsModal').classList.add('open');
  loadAlternatives(productId);
}

function closeBackupsModal() {
  document.getElementById('backupsModal').classList.remove('open');
  currentProductId = null;
}

// ── Load alternatives ────────────────────────────────────────────────────────
function loadAlternatives(productId) {
  fetch('<?= url('admin/supplier-catalog/alternatives') ?>?product_id=' + productId)
    .then(r => r.json())
    .then(data => {
      if (!data.success) { document.getElementById('modalBody').innerHTML = '<p style="color:#ef4444;">Error loading data.</p>'; return; }
      currentExplicit = data.explicit || [];
      renderBackups(data);
      populateAddDropdown(data.available || []);
    })
    .catch(() => { document.getElementById('modalBody').innerHTML = '<p style="color:#ef4444;">Network error.</p>'; });
}

// ── Render backup list ───────────────────────────────────────────────────────
function renderBackups(data) {
  const body = document.getElementById('modalBody');
  let html = '';

  if (data.explicit.length > 0) {
    html += '<div class="section-label">Configured Backups <span style="color:#6b7280;font-weight:400;">(drag to reorder)</span></div>';
    html += '<div id="backupsList">';
    data.explicit.forEach((b, i) => {
      const supplier = b.supplier_name || b.supplier_company || 'Unknown Supplier';
      html += `
        <div class="backup-item" data-mapping-id="${b.mapping_id}" draggable="true">
          <div class="backup-priority">${i + 1}</div>
          <div class="backup-info">
            <div class="backup-name">${escHtml(b.product_name)}</div>
            <div class="backup-meta">${escHtml(supplier)}${b.sku ? ' · SKU: ' + escHtml(b.sku) : ''}${b.notes ? ' · ' + escHtml(b.notes) : ''}</div>
          </div>
          <div class="backup-price">$${parseFloat(b.unit_price).toFixed(2)}/${escHtml(b.unit)}</div>
          <button class="btn-remove-backup" onclick="removeBackup(${b.mapping_id})" title="Remove backup">
            <i class="fas fa-times-circle"></i>
          </button>
        </div>`;
    });
    html += '</div>';
  } else {
    html += '<div class="empty-backups"><i class="fas fa-layer-group" style="font-size:32px;display:block;margin-bottom:8px;"></i>No backups configured yet.</div>';
  }

  if (data.auto_discovered.length > 0) {
    html += '<div class="section-label">Auto-Discovered <span class="auto-tag">via marketplace link</span></div>';
    data.auto_discovered.forEach(b => {
      const supplier = b.supplier_name || b.supplier_company || 'Unknown Supplier';
      html += `
        <div class="backup-item" style="opacity:0.8;cursor:default;">
          <div class="backup-priority" style="background:#9ca3af;">~</div>
          <div class="backup-info">
            <div class="backup-name">${escHtml(b.product_name)}</div>
            <div class="backup-meta">${escHtml(supplier)}${b.sku ? ' · SKU: ' + escHtml(b.sku) : ''} · Auto fallback</div>
          </div>
          <div class="backup-price">$${parseFloat(b.unit_price).toFixed(2)}/${escHtml(b.unit)}</div>
        </div>`;
    });
  }

  body.innerHTML = html || '<div class="empty-backups">No backups found.</div>';
  initDragSort();
  updateBtnBadge();
}

// ── Add backup ───────────────────────────────────────────────────────────────
function addBackup() {
  const altId = document.getElementById('newBackupSelect').value;
  const notes = document.getElementById('newBackupNotes').value.trim();
  if (!altId) { alert('Please select a supplier product'); return; }

  fetch('<?= url('admin/supplier-catalog/alternatives/add') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      [csrfName]: csrfToken,
      supplier_product_id: currentProductId,
      alternative_supplier_product_id: altId,
      notes: notes
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) loadAlternatives(currentProductId);
    else alert(data.message);
  })
  .catch(() => alert('Network error'));
}

// ── Remove backup ────────────────────────────────────────────────────────────
function removeBackup(mappingId) {
  if (!confirm('Remove this backup supplier?')) return;

  fetch('<?= url('admin/supplier-catalog/alternatives/remove') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, mapping_id: mappingId })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) loadAlternatives(currentProductId);
    else alert(data.message);
  })
  .catch(() => alert('Network error'));
}

// ── Populate add dropdown ────────────────────────────────────────────────────
function populateAddDropdown(available) {
  const sel = document.getElementById('newBackupSelect');
  sel.innerHTML = '<option value="">— Select a supplier product —</option>';
  available.forEach(p => {
    const supplier = p.supplier_name || p.supplier_company || 'Unknown';
    sel.innerHTML += `<option value="${p.id}">${escHtml(p.product_name)} — ${escHtml(supplier)} ($${parseFloat(p.unit_price).toFixed(2)})</option>`;
  });
}

// ── Update backup count badge on card button ─────────────────────────────────
function updateBtnBadge() {
  if (!currentProductId) return;
  const btn = document.getElementById('backups-btn-' + currentProductId);
  if (!btn) return;
  const count = currentExplicit.length;
  const existing = btn.querySelector('.backup-count');
  if (count > 0) {
    if (existing) { existing.textContent = count; }
    else { btn.innerHTML = '<i class="fas fa-layer-group"></i> Manage Backups <span class="backup-count">' + count + '</span>'; }
  } else {
    if (existing) existing.remove();
  }
}

// ── Drag-to-reorder ──────────────────────────────────────────────────────────
function initDragSort() {
  const list = document.getElementById('backupsList');
  if (!list) return;
  let dragged = null;

  list.querySelectorAll('.backup-item[draggable]').forEach(item => {
    item.addEventListener('dragstart', () => { dragged = item; item.style.opacity = '0.4'; });
    item.addEventListener('dragend',   () => { dragged.style.opacity = ''; saveNewOrder(); });
    item.addEventListener('dragover',  e => { e.preventDefault(); const after = getDragAfter(list, e.clientY); after ? list.insertBefore(dragged, after) : list.appendChild(dragged); });
  });

  function getDragAfter(container, y) {
    return [...container.querySelectorAll('.backup-item:not(.dragging)')].reduce((closest, child) => {
      const box = child.getBoundingClientRect();
      const offset = y - box.top - box.height / 2;
      return (offset < 0 && offset > closest.offset) ? { offset, element: child } : closest;
    }, { offset: Number.NEGATIVE_INFINITY }).element;
  }
}

function saveNewOrder() {
  const list = document.getElementById('backupsList');
  if (!list) return;
  const order = [...list.querySelectorAll('.backup-item[data-mapping-id]')].map(el => el.dataset.mappingId);

  fetch('<?= url('admin/supplier-catalog/alternatives/reorder') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ [csrfName]: csrfToken, order })
  })
  .then(r => r.json())
  .then(data => { if (data.success) loadAlternatives(currentProductId); })
  .catch(() => {});
}

function escHtml(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function addToDraft(event, productId, stockQty) {
  event.preventDefault();

  const qtyInput = document.getElementById('qty-' + productId);
  const quantity = parseInt(qtyInput.value);

  if (quantity < 1) {
    alert('Please enter a valid quantity');
    return;
  }

  if (stockQty > 0 && quantity > stockQty) {
    alert('Cannot order more than available stock (' + stockQty + ' units)');
    qtyInput.value = stockQty;
    return;
  }

  fetch('<?= url('admin/supplier-catalog/add-to-draft') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, product_id: productId, quantity: quantity })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const badge = document.getElementById('draftBadge');
      if (badge) {
        badge.textContent = data.draft_count;
      } else if (data.draft_count > 0) {
        const draftBtn = document.querySelector('.btn-draft');
        const newBadge = document.createElement('span');
        newBadge.className = 'draft-badge';
        newBadge.id = 'draftBadge';
        newBadge.textContent = data.draft_count;
        draftBtn.appendChild(newBadge);
      }

      const btn = event.target.querySelector('.btn-add');
      const originalHTML = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-check"></i> Added!';
      btn.style.background = '#10b981';

      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.style.background = '';
      }, 1500);
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error adding to draft'));
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
