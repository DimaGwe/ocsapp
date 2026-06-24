<?php
$pageTitle = 'Create Purchase Order';
$currentPage = 'purchase-orders';
ob_start();
?>

<style>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  flex-wrap: wrap;
  gap: 12px;
}
.page-header h1 {
  font-size: 26px;
  font-weight: 700;
  color: #1a202c;
}
.breadcrumb {
  display: flex;
  gap: 8px;
  font-size: 13px;
  color: #6b7280;
  margin-top: 4px;
}
.breadcrumb a { color: #00b207; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }

/* Cards */
.form-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  margin-bottom: 20px;
}
.form-card h3 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #1a202c;
}
.form-card h3 i { color: #00b207; }

/* Layout */
.po-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 20px;
  align-items: start;
}
@media (max-width: 1100px) {
  .po-layout { grid-template-columns: 1fr; }
}

/* Forms */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}
@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
}
.form-group { margin-bottom: 0; }
.form-group.full-width { grid-column: 1 / -1; }
.form-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}
.required { color: #ef4444; }
.form-input, .form-select, .form-textarea {
  width: 100%;
  padding: 9px 14px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  color: #1a202c;
  background: white;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: inherit;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
  outline: none;
  border-color: #00b207;
  box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
}
.form-input::placeholder { color: #9ca3af; }

/* Supplier Info Card */
.supplier-info-card {
  background: #f0fdf4;
  border: 1.5px solid #bbf7d0;
  border-radius: 10px;
  padding: 16px;
  margin-top: 16px;
  display: none;
}
.supplier-info-card.active { display: block; }
.supplier-info-card h4 {
  font-size: 14px;
  font-weight: 600;
  color: #166534;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.supplier-info-row {
  display: flex;
  justify-content: space-between;
  padding: 4px 0;
  font-size: 13px;
}
.supplier-info-row .label { color: #6b7280; }
.supplier-info-row .value { color: #1a202c; font-weight: 500; }

/* Items Table */
.items-table {
  width: 100%;
  border-collapse: collapse;
}
.items-table thead th {
  background: #f9fafb;
  padding: 10px 12px;
  text-align: left;
  font-size: 11px;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e5e7eb;
}
.items-table tbody td {
  padding: 10px 12px;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: top;
}
.items-table tbody tr:hover { background: #fafafa; }
.items-table select, .items-table input {
  width: 100%;
  padding: 7px 10px;
  border: 1.5px solid #e5e7eb;
  border-radius: 6px;
  font-size: 13px;
  font-family: inherit;
}
.items-table select:focus, .items-table input:focus {
  outline: none;
  border-color: #00b207;
}
.product-meta {
  font-size: 11px;
  color: #9ca3af;
  margin-top: 4px;
  display: none;
}
.product-meta.active { display: block; }
.product-meta span { margin-right: 10px; }
.min-qty-warning {
  background: #fef3c7;
  color: #92400e;
  font-size: 11px;
  padding: 2px 8px;
  border-radius: 4px;
  display: none;
  margin-top: 4px;
}
.min-qty-warning.active { display: inline-block; }
.line-total-display {
  font-weight: 600;
  color: #1a202c;
  font-size: 14px;
}

/* Buttons */
.btn-add-item {
  background: white;
  color: #00b207;
  padding: 8px 16px;
  border-radius: 8px;
  border: 1.5px dashed #00b207;
  cursor: pointer;
  font-weight: 600;
  font-size: 13px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
  margin-top: 12px;
}
.btn-add-item:hover { background: #f0fdf4; }
.btn-remove {
  background: none;
  color: #ef4444;
  border: none;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 16px;
  transition: background 0.2s;
}
.btn-remove:hover { background: #fef2f2; }

/* Sidebar */
.sidebar-sticky { position: sticky; top: 80px; }

/* Totals */
.totals-card {
  background: #f9fafb;
  border: 1.5px solid #e5e7eb;
  padding: 16px;
  border-radius: 10px;
}
.total-row {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  font-size: 14px;
  color: #4b5563;
}
.total-row.final {
  border-top: 2px solid #1a202c;
  margin-top: 8px;
  padding-top: 10px;
  font-size: 18px;
  font-weight: 700;
  color: #1a202c;
}

/* Tax selector */
.tax-row {
  display: flex;
  gap: 10px;
  align-items: end;
}
.tax-row .form-group { flex: 1; }
.tax-auto-btn {
  padding: 9px 14px;
  background: #f3f4f6;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  white-space: nowrap;
  transition: all 0.2s;
}
.tax-auto-btn:hover { background: #e5e7eb; }
.tax-auto-btn.applied { background: #dcfce7; border-color: #86efac; color: #166534; }

/* Actions */
.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 20px;
}
.btn {
  padding: 10px 22px;
  border-radius: 8px;
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
.btn-primary { background: #00b207; color: white; }
.btn-primary:hover { background: #009906; }
.btn-secondary { background: #f3f4f6; color: #4b5563; }
.btn-secondary:hover { background: #e5e7eb; }

/* Item notes */
.item-notes-input {
  width: 100%;
  padding: 5px 8px;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  font-size: 12px;
  color: #6b7280;
  margin-top: 4px;
  font-family: inherit;
}
.item-notes-input::placeholder { color: #d1d5db; }
</style>

<div class="page-header">
  <div>
    <h1><i class="fas fa-file-invoice" style="color: #00b207;"></i> Create Purchase Order</h1>
    <div class="breadcrumb">
      <a href="<?= url('admin/purchase-orders') ?>">Purchase Orders</a>
      <span>/</span>
      <span>Create New</span>
    </div>
  </div>
  <a href="<?= url('admin/purchase-orders') ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<form method="POST" action="<?= url('admin/purchase-orders/store') ?>" id="poForm">
  <?= csrfField() ?>

  <div class="po-layout">
    <!-- Main Content -->
    <div>
      <!-- PO Header -->
      <div class="form-card">
        <h3><i class="fas fa-clipboard-list"></i> Order Details</h3>
        <div class="form-grid">
          <div class="form-group">
            <label for="po_number" class="form-label">PO Number <span class="required">*</span></label>
            <input type="text" id="po_number" name="po_number" value="<?= htmlspecialchars($nextPONumber) ?>" class="form-input" required readonly>
          </div>
          <div class="form-group">
            <label for="supplier_id" class="form-label">Supplier <span class="required">*</span></label>
            <select id="supplier_id" name="supplier_id" class="form-select" required>
              <option value="">Select Supplier...</option>
              <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['company_name'] ?: $s['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="order_date" class="form-label">Order Date <span class="required">*</span></label>
            <input type="date" id="order_date" name="order_date" value="<?= date('Y-m-d') ?>" class="form-input" required>
          </div>
          <div class="form-group">
            <label for="expected_delivery_date" class="form-label">Expected Delivery</label>
            <input type="date" id="expected_delivery_date" name="expected_delivery_date" class="form-input">
          </div>
          <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
              <option value="draft">Draft</option>
              <option value="sent">Sent to Supplier</option>
            </select>
          </div>
        </div>

        <!-- Supplier Info Card (shown dynamically) -->
        <div class="supplier-info-card" id="supplierInfoCard">
          <h4><i class="fas fa-building"></i> <span id="supplierInfoName">—</span></h4>
          <div class="supplier-info-row">
            <span class="label">Contact</span>
            <span class="value" id="supplierInfoContact">—</span>
          </div>
          <div class="supplier-info-row">
            <span class="label">Email</span>
            <span class="value" id="supplierInfoEmail">—</span>
          </div>
          <div class="supplier-info-row">
            <span class="label">Phone</span>
            <span class="value" id="supplierInfoPhone">—</span>
          </div>
          <div class="supplier-info-row">
            <span class="label">Location</span>
            <span class="value" id="supplierInfoLocation">—</span>
          </div>
          <div class="supplier-info-row">
            <span class="label">Payment Terms</span>
            <span class="value" id="supplierInfoTerms">—</span>
          </div>
        </div>
      </div>

      <!-- Line Items -->
      <div class="form-card">
        <h3><i class="fas fa-boxes"></i> Order Items</h3>
        <div style="overflow-x: auto;">
          <table class="items-table" id="itemsTable">
            <thead>
              <tr>
                <th style="width: 35%;">Product</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">Unit Cost</th>
                <th style="width: 12%;">Total</th>
                <th style="width: 23%;">Notes</th>
                <th style="width: 5%;"></th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <tr class="item-row">
                <td>
                  <select name="supplier_product_ids[]" class="product-select" required disabled>
                    <option value="">Select a supplier first</option>
                  </select>
                  <div class="product-meta" id="meta_0">
                    <span class="meta-sku"></span>
                    <span class="meta-unit"></span>
                    <span class="meta-stock"></span>
                  </div>
                  <div class="min-qty-warning" id="minqty_0">
                    <i class="fas fa-exclamation-triangle"></i> <span></span>
                  </div>
                </td>
                <td>
                  <input type="number" name="quantities[]" class="quantity-input" min="1" value="1" required>
                </td>
                <td>
                  <input type="number" name="unit_costs[]" class="cost-input" step="0.01" min="0" value="0.00" required>
                </td>
                <td>
                  <div class="line-total-display">$0.00</div>
                </td>
                <td>
                  <input type="text" name="item_notes[]" class="item-notes-input" placeholder="Optional note...">
                </td>
                <td>
                  <button type="button" class="btn-remove" onclick="removeItem(this)" title="Remove">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button type="button" class="btn-add-item" onclick="addItem()">
          <i class="fas fa-plus"></i> Add Item
        </button>
      </div>

      <!-- Notes -->
      <div class="form-card">
        <h3><i class="fas fa-sticky-note"></i> Notes</h3>
        <div class="form-group">
          <textarea id="notes" name="notes" rows="3" class="form-textarea" placeholder="Special instructions, delivery notes, etc."></textarea>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-sticky">
      <!-- Tax & Totals -->
      <div class="form-card">
        <h3><i class="fas fa-calculator"></i> Totals</h3>

        <input type="hidden" id="tax_amount" name="tax_amount" value="0.00">

        <div class="totals-card">
          <div class="total-row">
            <span>Subtotal</span>
            <span id="subtotalDisplay">$0.00</span>
          </div>
          <div class="total-row">
            <span>GST (5%)</span>
            <span id="gstDisplay">$0.00</span>
          </div>
          <div class="total-row">
            <span>QST (9.975%)</span>
            <span id="qstDisplay">$0.00</span>
          </div>
          <div class="total-row final">
            <span>Total</span>
            <span id="totalDisplay">$0.00</span>
          </div>
        </div>

        <div class="form-actions" style="padding-top: 16px;">
          <a href="<?= url('admin/purchase-orders') ?>" class="btn btn-secondary" style="flex: 1; justify-content: center;">Cancel</a>
          <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
            <i class="fas fa-save"></i> Create PO
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
const CURRENCY = '$';

// Supplier data for info card
const suppliersData = {};
<?php foreach ($suppliers as $s): ?>
suppliersData[<?= $s['id'] ?>] = {
  name: <?= json_encode($s['company_name'] ?: $s['name']) ?>,
  contact: <?= json_encode($s['contact_person'] ?? '') ?>,
  email: <?= json_encode($s['email'] ?? '') ?>,
  phone: <?= json_encode($s['phone'] ?? '') ?>,
  city: <?= json_encode($s['city'] ?? '') ?>,
  province: <?= json_encode($s['province'] ?? '') ?>,
  terms: <?= json_encode($s['payment_terms'] ?? 'N/A') ?>
};
<?php endforeach; ?>

// Supplier products data
const supplierProductsData = <?= json_encode($supplierProducts) ?>;
const productsBySupplier = {};
supplierProductsData.forEach(p => {
  if (!productsBySupplier[p.supplier_id]) productsBySupplier[p.supplier_id] = [];
  productsBySupplier[p.supplier_id].push(p);
});

// Canadian tax rates

let rowCounter = 1;

// Supplier selection
document.getElementById('supplier_id').addEventListener('change', function() {
  const id = this.value;
  const card = document.getElementById('supplierInfoCard');

  if (id && suppliersData[id]) {
    const s = suppliersData[id];
    document.getElementById('supplierInfoName').textContent = s.name;
    document.getElementById('supplierInfoContact').textContent = s.contact || '—';
    document.getElementById('supplierInfoEmail').textContent = s.email || '—';
    document.getElementById('supplierInfoPhone').textContent = s.phone || '—';
    document.getElementById('supplierInfoLocation').textContent = [s.city, s.province].filter(Boolean).join(', ') || '—';
    document.getElementById('supplierInfoTerms').textContent = s.terms || 'N/A';
    card.classList.add('active');

    calculateTotals();
  } else {
    card.classList.remove('active');
  }

  // Update product selects
  document.querySelectorAll('.product-select').forEach(select => {
    select.innerHTML = getProductOptions(id);
    select.disabled = !id;
    // Reset meta
    const row = select.closest('.item-row');
    if (row) {
      const meta = row.querySelector('.product-meta');
      if (meta) meta.classList.remove('active');
      const warn = row.querySelector('.min-qty-warning');
      if (warn) warn.classList.remove('active');
    }
  });

  calculateTotals();
});

function getProductOptions(supplierId) {
  if (!supplierId || !productsBySupplier[supplierId]) {
    return '<option value="">Select a supplier first</option>';
  }
  const products = productsBySupplier[supplierId];
  if (!products.length) return '<option value="">No products from this supplier</option>';

  let opts = '<option value="">Select product...</option>';
  products.forEach(p => {
    const sku = p.sku ? ` [${p.sku}]` : '';
    const effectiveCost = parseFloat(p.unit_price) || 0;
    const price = effectiveCost > 0 ? ` - ${CURRENCY}${effectiveCost.toFixed(2)}` : '';
    const stock = p.stock_quantity !== null && p.stock_quantity !== undefined ? p.stock_quantity : '';
    opts += `<option value="${p.id}" data-cost="${effectiveCost}" data-sku="${p.sku || ''}" data-unit="${p.unit || 'unit'}" data-minqty="${p.minimum_order_quantity || 1}" data-stock="${stock}" data-lead="${p.lead_time_days || ''}">${p.product_name}${sku}${price}</option>`;
  });
  return opts;
}

function attachProductListener(select) {
  select.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const row = this.closest('.item-row');
    const meta = row.querySelector('.product-meta');
    const warn = row.querySelector('.min-qty-warning');

    if (opt.value) {
      // Auto-fill cost
      const cost = parseFloat(opt.dataset.cost) || 0;
      if (cost > 0) row.querySelector('.cost-input').value = cost.toFixed(2);

      // Show meta
      const skuEl = meta.querySelector('.meta-sku');
      const unitEl = meta.querySelector('.meta-unit');
      const stockEl = meta.querySelector('.meta-stock');
      skuEl.textContent = opt.dataset.sku ? `SKU: ${opt.dataset.sku}` : '';
      unitEl.textContent = opt.dataset.unit ? `Unit: ${opt.dataset.unit}` : '';
      const stock = parseInt(opt.dataset.stock) || 0;
      if (stockEl) {
        if (stock > 0) {
          const stockColor = stock > 20 ? '#059669' : '#d97706';
          stockEl.innerHTML = `Stock: <strong style="color:${stockColor}">${stock}</strong>`;
        } else {
          stockEl.innerHTML = '<strong style="color:#dc2626">Out of Stock</strong>';
        }
      }
      // Set max on quantity input
      const qtyInput = row.querySelector('.quantity-input');
      if (stock > 0) {
        qtyInput.setAttribute('max', stock);
      } else {
        qtyInput.removeAttribute('max');
      }
      meta.classList.add('active');

      // Check min qty
      checkMinQty(row, opt);
    } else {
      meta.classList.remove('active');
      warn.classList.remove('active');
    }

    calculateLineTotal(row);
  });
}

function checkMinQty(row, opt) {
  const minQty = parseInt(opt.dataset.minqty) || 1;
  const stock = parseInt(opt.dataset.stock) || 0;
  const qtyInput = row.querySelector('.quantity-input');
  const qty = parseInt(qtyInput.value) || 0;
  const warn = row.querySelector('.min-qty-warning');

  if (stock > 0 && qty > stock) {
    warn.querySelector('span').textContent = `Exceeds available stock (${stock})`;
    warn.classList.add('active');
    qtyInput.value = stock;
    calculateLineTotal(row);
  } else if (minQty > 1 && qty < minQty) {
    warn.querySelector('span').textContent = `Min order: ${minQty}`;
    warn.classList.add('active');
  } else {
    warn.classList.remove('active');
  }
}

function attachQtyListener(row) {
  row.querySelector('.quantity-input').addEventListener('input', function() {
    const select = row.querySelector('.product-select');
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) checkMinQty(row, opt);
    calculateLineTotal(row);
  });
  row.querySelector('.cost-input').addEventListener('input', () => calculateLineTotal(row));
}

function addItem() {
  const supplierId = document.getElementById('supplier_id').value;
  if (!supplierId) { alert('Please select a supplier first'); return; }

  const tbody = document.getElementById('itemsBody');
  const row = document.createElement('tr');
  row.className = 'item-row';
  row.innerHTML = `
    <td>
      <select name="supplier_product_ids[]" class="product-select" required>
        ${getProductOptions(supplierId)}
      </select>
      <div class="product-meta" id="meta_${rowCounter}">
        <span class="meta-sku"></span>
        <span class="meta-unit"></span>
        <span class="meta-stock"></span>
      </div>
      <div class="min-qty-warning" id="minqty_${rowCounter}">
        <i class="fas fa-exclamation-triangle"></i> <span></span>
      </div>
    </td>
    <td><input type="number" name="quantities[]" class="quantity-input" min="1" value="1" required></td>
    <td><input type="number" name="unit_costs[]" class="cost-input" step="0.01" min="0" value="0.00" required></td>
    <td><div class="line-total-display">${CURRENCY}0.00</div></td>
    <td><input type="text" name="item_notes[]" class="item-notes-input" placeholder="Optional note..."></td>
    <td><button type="button" class="btn-remove" onclick="removeItem(this)" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
  `;
  tbody.appendChild(row);
  attachProductListener(row.querySelector('.product-select'));
  attachQtyListener(row);
  rowCounter++;

  // Focus the new product select
  row.querySelector('.product-select').focus();
}

function removeItem(btn) {
  const rows = document.querySelectorAll('.item-row');
  if (rows.length > 1) {
    btn.closest('tr').remove();
    calculateTotals();
  } else {
    alert('At least one item is required');
  }
}

function calculateLineTotal(row) {
  const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
  const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
  const total = qty * cost;
  row.querySelector('.line-total-display').textContent = CURRENCY + total.toFixed(2);
  calculateTotals();
}

function getSubtotal() {
  let subtotal = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    subtotal += qty * cost;
  });
  return subtotal;
}

function calculateTotals() {
  const subtotal = getSubtotal();
  const gst = Math.round(subtotal * 0.05 * 100) / 100;
  const qst = Math.round(subtotal * 0.09975 * 100) / 100;
  const tax = gst + qst;
  const total = subtotal + tax;

  document.getElementById('tax_amount').value = tax.toFixed(2);
  document.getElementById('subtotalDisplay').textContent = CURRENCY + subtotal.toFixed(2);
  document.getElementById('gstDisplay').textContent = CURRENCY + gst.toFixed(2);
  document.getElementById('qstDisplay').textContent = CURRENCY + qst.toFixed(2);
  document.getElementById('totalDisplay').textContent = CURRENCY + total.toFixed(2);
}

// Init listeners on first row
document.querySelectorAll('.item-row').forEach(row => {
  attachProductListener(row.querySelector('.product-select'));
  attachQtyListener(row);
});

// Validate stock on form submit
document.getElementById('poForm').addEventListener('submit', function(e) {
  let hasError = false;
  document.querySelectorAll('.item-row').forEach(row => {
    const select = row.querySelector('.product-select');
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    const stock = parseInt(opt.dataset.stock) || 0;
    const qty = parseInt(row.querySelector('.quantity-input').value) || 0;

    if (stock > 0 && qty > stock) {
      hasError = true;
      const name = opt.textContent.trim().split(' - ')[0];
      alert(`Cannot order ${qty} of "${name}" — only ${stock} in stock.`);
    }
  });

  if (hasError) {
    e.preventDefault();
  }
});

calculateTotals();
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
