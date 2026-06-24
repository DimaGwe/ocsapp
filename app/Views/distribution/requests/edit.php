<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$currentPage = 'request-create';
$pageTitle = $currentLang === 'fr' ? 'Modifier la demande' : 'Edit Request';
require __DIR__ . '/../layout-header.php';
?>
        <div class="breadcrumb">
            <a href="<?= url('distribution/requests') ?>">Requests</a>
            <span> / </span>
            <a href="<?= url('distribution/requests/show?id=' . $request['id']) ?>"><?= htmlspecialchars($request['request_number']) ?></a>
            <span> / Edit</span>
        </div>

        <div class="page-header">
            <h1 class="page-title">Edit Request</h1>
            <p class="page-subtitle"><?= htmlspecialchars($request['request_number']) ?></p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors['items'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errors['items']) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('distribution/requests/update') ?>" id="requestForm">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="distribution_request_id" value="<?= $request['id'] ?>">

            <div class="form-grid">
                <div class="main-column">
                    <!-- Request Details -->
                    <div class="card">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Request Details</h3>
                        <div class="form-group">
                            <label class="form-label">Request Name *</label>
                            <input type="text" name="request_name" class="form-input <?= !empty($errors['request_name']) ? 'error' : '' ?>"
                                   value="<?= htmlspecialchars($request['request_name'] ?? '') ?>"
                                   placeholder="e.g., Weekly Office Supplies">
                            <?php if (!empty($errors['request_name'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['request_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-input" rows="3"
                                      placeholder="Any special instructions or notes..."><?= htmlspecialchars($request['notes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="card">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                        <div class="form-group">
                            <label class="form-label">Street Address *</label>
                            <input type="text" name="delivery_street" class="form-input <?= !empty($errors['delivery_street']) ? 'error' : '' ?>"
                                   value="<?= htmlspecialchars($request['delivery_street'] ?? '') ?>">
                            <?php if (!empty($errors['delivery_street'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['delivery_street']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">City *</label>
                                <input type="text" name="delivery_city" class="form-input <?= !empty($errors['delivery_city']) ? 'error' : '' ?>"
                                       value="<?= htmlspecialchars($request['delivery_city'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Province *</label>
                                <select name="delivery_province" class="form-input">
                                    <option value="">Select Province</option>
                                    <?php
                                    $provinces = ['AB' => 'Alberta', 'BC' => 'British Columbia', 'MB' => 'Manitoba', 'NB' => 'New Brunswick',
                                                  'NL' => 'Newfoundland and Labrador', 'NS' => 'Nova Scotia', 'NT' => 'Northwest Territories',
                                                  'NU' => 'Nunavut', 'ON' => 'Ontario', 'PE' => 'Prince Edward Island', 'QC' => 'Quebec',
                                                  'SK' => 'Saskatchewan', 'YT' => 'Yukon'];
                                    foreach ($provinces as $code => $name):
                                    ?>
                                        <option value="<?= $code ?>" <?= ($request['delivery_province'] ?? '') === $code ? 'selected' : '' ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Postal Code *</label>
                                <input type="text" name="delivery_postal_code" class="form-input <?= !empty($errors['delivery_postal_code']) ? 'error' : '' ?>"
                                       value="<?= htmlspecialchars($request['delivery_postal_code'] ?? '') ?>" placeholder="A1A 1A1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Preferred Delivery Date</label>
                                <input type="date" name="preferred_delivery_date" class="form-input"
                                       value="<?= htmlspecialchars($request['preferred_delivery_date'] ?? '') ?>"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Items Selection -->
                    <div class="card">
                        <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Items</h3>

                        <div class="tabs">
                            <button type="button" class="tab-btn active" data-tab="catalog">
                                <i class="fas fa-store"></i> Catalog
                            </button>
                            <button type="button" class="tab-btn" data-tab="shopping">
                                <i class="fas fa-list"></i> Shopping List
                            </button>
                        </div>

                        <!-- Catalog Tab -->
                        <div class="tab-content active" id="tab-catalog">
                            <div id="suppliersView">
                                <p style="font-size: 13px; color: #666; margin-bottom: 16px;">
                                    Select a supplier to browse their products.
                                </p>
                                <?php if (empty($suppliers)): ?>
                                    <p style="text-align: center; color: #666; padding: 24px;">No suppliers available.</p>
                                <?php else: ?>
                                    <div class="suppliers-grid">
                                        <?php foreach ($suppliers as $supplier):
                                            $supplierDisplayName = $supplier['company_name'] ?? $supplier['name'] ?? 'Unknown Supplier';
                                        ?>
                                            <div class="supplier-card" data-supplier-id="<?= $supplier['id'] ?>"
                                                 data-supplier-name="<?= htmlspecialchars($supplierDisplayName) ?>">
                                                <div class="supplier-logo">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div class="supplier-name"><?= htmlspecialchars($supplierDisplayName) ?></div>
                                                <div class="supplier-meta"><?= $supplier['product_count'] ?> products</div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div id="supplierProducts">
                                <div class="supplier-products-header">
                                    <button type="button" class="btn-back" id="backToSuppliers">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </button>
                                    <span class="current-supplier-name" id="currentSupplierName"></span>
                                </div>

                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="catalogSearch" placeholder="Search products...">
                                </div>

                                <div class="catalog-scroll" id="productsContainer"></div>
                            </div>

                            <?php foreach ($productsBySupplier as $supplierId => $products): ?>
                                <template id="supplier-products-<?= $supplierId ?>">
                                    <?php foreach ($products as $product):
                                        $existingQty = $catalogItemsKeyed[$product['id']] ?? 0;
                                    ?>
                                        <div class="product-item <?= $existingQty > 0 ? 'selected' : '' ?>" data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
                                            <img src="<?= $product['image'] ? asset('uploads/supplier-products/' . $product['image']) : asset('images/placeholder.png') ?>"
                                                 alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                                            <div class="product-info">
                                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                                <div class="product-sku">SKU: <?= htmlspecialchars($product['sku'] ?? 'N/A') ?> <?= $product['unit'] ? '• ' . htmlspecialchars($product['unit']) : '' ?></div>
                                            </div>
                                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                            <input type="number" name="catalog_items[<?= $product['id'] ?>]"
                                                   class="product-qty" min="0" value="<?= $existingQty ?>" placeholder="0"
                                                   data-price="<?= $product['price'] ?>"
                                                   data-weight="<?= $product['weight_kg'] ?? 0 ?>"
                                                   data-product-id="<?= $product['id'] ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </template>
                            <?php endforeach; ?>
                        </div>

                        <!-- Shopping List Tab -->
                        <div class="tab-content" id="tab-shopping">
                            <p style="font-size: 13px; color: #666; margin-bottom: 16px;">
                                Add custom items that aren't in our catalog. We'll source them for you!
                            </p>

                            <div class="shopping-items-list" id="shoppingItemsList">
                                <?php foreach ($shoppingItems as $index => $item): ?>
                                    <div class="shopping-item" data-index="<?= $index ?>">
                                        <input type="text" name="shopping_items[<?= $index ?>][description]"
                                               placeholder="Item description" value="<?= htmlspecialchars($item['item_description']) ?>" required>
                                        <input type="number" name="shopping_items[<?= $index ?>][quantity]"
                                               placeholder="Qty" min="1" value="<?= $item['quantity'] ?>">
                                        <select name="shopping_items[<?= $index ?>][unit]">
                                            <option value="each" <?= ($item['unit'] ?? 'each') === 'each' ? 'selected' : '' ?>>Each</option>
                                            <option value="box" <?= ($item['unit'] ?? '') === 'box' ? 'selected' : '' ?>>Box</option>
                                            <option value="case" <?= ($item['unit'] ?? '') === 'case' ? 'selected' : '' ?>>Case</option>
                                            <option value="pack" <?= ($item['unit'] ?? '') === 'pack' ? 'selected' : '' ?>>Pack</option>
                                            <option value="kg" <?= ($item['unit'] ?? '') === 'kg' ? 'selected' : '' ?>>Kg</option>
                                            <option value="lb" <?= ($item['unit'] ?? '') === 'lb' ? 'selected' : '' ?>>Lb</option>
                                        </select>
                                        <button type="button" class="btn-remove" onclick="removeShoppingItem(<?= $index ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="button" class="btn-add-item" id="addShoppingItem">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="summary-column">
                    <div class="card summary-section">
                        <h3 class="card-title"><i class="fas fa-receipt"></i> Order Summary</h3>

                        <div id="tierBadge" style="display: none;"></div>

                        <div id="summaryItems">
                            <p style="color: #666; font-size: 14px; text-align: center; padding: 24px 0;">
                                No items added yet
                            </p>
                        </div>

                        <div class="delivery-input-group" id="deliveryInputGroup" style="display: none;">
                            <label><i class="fas fa-truck"></i> Distance:</label>
                            <input type="number" id="deliveryDistance" name="delivery_distance" min="0" max="500"
                                   value="<?= htmlspecialchars($request['delivery_distance'] ?? 0) ?>" step="1">
                            <span>km</span>
                        </div>

                        <div class="fee-breakdown" id="feeBreakdown" style="display: none;">
                            <div class="fee-row">
                                <span>Items Total</span>
                                <span id="itemsTotal">$0.00</span>
                            </div>
                            <div class="fee-row">
                                <span>Service Fee (<span id="serviceFeePercent">0</span>%)</span>
                                <span id="serviceFeeAmount">$0.00</span>
                            </div>
                            <div class="fee-row">
                                <span>Handling (<span id="handlingWeightInfo">0 kg</span> × $0.20/kg)</span>
                                <span id="handlingFeeAmount">$0.00</span>
                            </div>
                            <div class="fee-row" id="deliveryFeeRow">
                                <span>Delivery (<span id="deliveryInfo">Free</span>)</span>
                                <span id="deliveryFeeAmount">$0.00</span>
                            </div>
                        </div>

                        <div class="summary-subtotal" id="summarySubtotal" style="display: none;">
                            <span>Subtotal</span>
                            <span id="subtotalAmount">$0.00</span>
                        </div>

                        <div class="tax-section" id="taxSection" style="display: none;">
                            <div class="tax-row">
                                <span>GST (5%)</span>
                                <span id="gstAmount">$0.00</span>
                            </div>
                            <div class="tax-row">
                                <span>QST (9.975%)</span>
                                <span id="qstAmount">$0.00</span>
                            </div>
                        </div>

                        <!-- Tip Section -->
                        <div class="tip-section" id="tipSection" style="display: none;">
                            <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 8px;">
                                <i class="fas fa-heart" style="color: #00b207;"></i> Add a Tip (Optional)
                            </div>
                            <p style="font-size: 11px; color: #888; margin-bottom: 10px;">
                                Calculated on pre-tax amount per Canadian regulations. We never take a cut of your tip.
                            </p>
                            <div class="tip-options">
                                <button type="button" class="tip-btn" data-tip="0" onclick="selectTip(0)">Skip</button>
                                <button type="button" class="tip-btn" data-tip="15" onclick="selectTip(15)">15%</button>
                                <button type="button" class="tip-btn" data-tip="18" onclick="selectTip(18)">18%</button>
                                <button type="button" class="tip-btn" data-tip="20" onclick="selectTip(20)">20%</button>
                                <button type="button" class="tip-btn" data-tip="custom" onclick="selectTip('custom')">Custom</button>
                            </div>
                            <div class="tip-custom-input" id="tipCustomInput" style="display: none;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                                    <span style="font-size: 14px; font-weight: 500;">$</span>
                                    <input type="number" id="tipCustomAmount" min="0" max="9999" step="0.01" placeholder="0.00"
                                           style="flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; font-family: inherit;"
                                           oninput="updateCustomTip()">
                                </div>
                            </div>
                            <div class="tip-display" id="tipDisplay" style="display: none;">
                                <span id="tipLabel">Tip</span>
                                <span id="tipAmount">$0.00</span>
                            </div>
                            <input type="hidden" name="tip_percentage" id="tipPercentage" value="<?= $request['tip_percentage'] ?? 0 ?>">
                            <input type="hidden" name="tip_custom_amount" id="tipCustomAmountHidden" value="<?= ($request['tip_percentage'] == 0 && ($request['tip_amount'] ?? 0) > 0) ? $request['tip_amount'] : 0 ?>">
                        </div>

                        <div class="summary-total" id="summaryTotal" style="display: none;">
                            <span>Estimated Total</span>
                            <span id="totalAmount">$0.00</span>
                        </div>

                        <div class="summary-note">
                            <i class="fas fa-info-circle"></i>
                            Shopping list items will be quoted after review. Final total may vary.
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="<?= url('distribution/requests/show?id=' . $request['id']) ?>" class="btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>

    <script>
        // Pre-populate selected items from existing data
        let selectedItems = <?= json_encode($catalogItemsKeyed) ?>;

        // Prevent Enter key from submitting the form
        document.getElementById('requestForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                if (target.tagName.toLowerCase() !== 'textarea') {
                    e.preventDefault();
                    if (target.classList.contains('product-qty')) {
                        target.dispatchEvent(new Event('change'));
                        target.blur();
                    }
                }
            }
        });

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });

        // Supplier card click
        document.querySelectorAll('.supplier-card').forEach(card => {
            card.addEventListener('click', function() {
                showSupplierProducts(this.dataset.supplierId, this.dataset.supplierName);
            });
        });

        document.getElementById('backToSuppliers').addEventListener('click', function() {
            saveCurrentQuantities();
            showSuppliersView();
        });

        function showSupplierProducts(supplierId, supplierName) {
            const template = document.getElementById('supplier-products-' + supplierId);
            const container = document.getElementById('productsContainer');

            if (template) {
                container.innerHTML = '';
                container.appendChild(template.content.cloneNode(true));

                // Restore quantities
                container.querySelectorAll('.product-qty').forEach(input => {
                    const productId = input.dataset.productId;
                    if (selectedItems[productId]) {
                        input.value = selectedItems[productId];
                        if (selectedItems[productId] > 0) {
                            input.closest('.product-item').classList.add('selected');
                        }
                    }
                });

                // Add event listeners
                container.querySelectorAll('.product-qty').forEach(input => {
                    input.addEventListener('change', function() {
                        const item = this.closest('.product-item');
                        const productId = this.dataset.productId;
                        const qty = parseInt(this.value) || 0;

                        if (qty > 0) {
                            selectedItems[productId] = qty;
                            item.classList.add('selected');
                        } else {
                            delete selectedItems[productId];
                            item.classList.remove('selected');
                        }
                        updateSummary();
                    });
                });
            }

            document.getElementById('currentSupplierName').textContent = supplierName;
            document.getElementById('suppliersView').style.display = 'none';
            document.getElementById('supplierProducts').style.display = 'block';
            document.getElementById('catalogSearch').value = '';
        }

        function showSuppliersView() {
            document.getElementById('suppliersView').style.display = 'block';
            document.getElementById('supplierProducts').style.display = 'none';
        }

        function saveCurrentQuantities() {
            document.querySelectorAll('#productsContainer .product-qty').forEach(input => {
                const productId = input.dataset.productId;
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    selectedItems[productId] = qty;
                } else {
                    delete selectedItems[productId];
                }
            });
        }

        // Search
        document.getElementById('catalogSearch').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('#productsContainer .product-item').forEach(item => {
                item.style.display = item.dataset.name.includes(search) ? 'flex' : 'none';
            });
        });

        // Shopping list items
        let shoppingItemIndex = <?= count($shoppingItems) ?>;
        document.getElementById('addShoppingItem').addEventListener('click', function() {
            const html = `
                <div class="shopping-item" data-index="${shoppingItemIndex}">
                    <input type="text" name="shopping_items[${shoppingItemIndex}][description]" placeholder="Item description" required>
                    <input type="number" name="shopping_items[${shoppingItemIndex}][quantity]" placeholder="Qty" min="1" value="1">
                    <select name="shopping_items[${shoppingItemIndex}][unit]">
                        <option value="each">Each</option>
                        <option value="box">Box</option>
                        <option value="case">Case</option>
                        <option value="pack">Pack</option>
                        <option value="kg">Kg</option>
                        <option value="lb">Lb</option>
                    </select>
                    <button type="button" class="btn-remove" onclick="removeShoppingItem(${shoppingItemIndex})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.getElementById('shoppingItemsList').insertAdjacentHTML('beforeend', html);
            shoppingItemIndex++;
            updateSummary();
        });

        function removeShoppingItem(index) {
            document.querySelector(`.shopping-item[data-index="${index}"]`).remove();
            updateSummary();
        }

        // Form submit - inject hidden inputs
        document.getElementById('requestForm').addEventListener('submit', function(e) {
            saveCurrentQuantities();
            this.querySelectorAll('input[name^="catalog_items["]').forEach(input => {
                if (input.type === 'hidden') input.remove();
            });
            for (const [productId, qty] of Object.entries(selectedItems)) {
                if (qty > 0) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = `catalog_items[${productId}]`;
                    hidden.value = qty;
                    this.appendChild(hidden);
                }
            }
        });

        // Pricing Tiers
        const PRICING_TIERS = {
            1: { maxAmount: 500, serviceFee: 0.25, freeDeliveryKm: 5, perKmRate: 1.00, vehicle: 'Small Car/Van' },
            2: { maxAmount: 1500, serviceFee: 0.20, freeDeliveryKm: 5, perKmRate: 1.30, vehicle: 'Medium Truck/Van' },
            3: { maxAmount: 3000, serviceFee: 0.15, freeDeliveryKm: 5, perKmRate: 2.00, vehicle: 'Large Truck/Forklift' },
            4: { maxAmount: Infinity, serviceFee: 0.12, freeDeliveryKm: 5, perKmRate: 2.20, vehicle: 'Large Truck/Forklift' }
        };

        const HANDLING_RATE_PER_KG = 0.20;
        const GST_RATE = 0.05;
        const QST_RATE = 0.09975;

        // Initialize tip from saved value
        let selectedTipPercent = parseInt(document.getElementById('tipPercentage').value) || 0;

        function getTier(itemsTotal) {
            if (itemsTotal <= 500) return 1;
            if (itemsTotal <= 1500) return 2;
            if (itemsTotal <= 3000) return 3;
            return 4;
        }

        function calculateDeliveryFee(distance, tier) {
            const tierConfig = PRICING_TIERS[tier];
            if (distance <= tierConfig.freeDeliveryKm) return 0;
            return (distance - tierConfig.freeDeliveryKm) * tierConfig.perKmRate;
        }

        document.getElementById('deliveryDistance').addEventListener('input', updateSummary);

        function updateSummary() {
            let catalogTotal = 0;
            let totalWeightKg = 0;
            let catalogItems = [];

            const productInfo = {};
            document.querySelectorAll('template[id^="supplier-products-"]').forEach(template => {
                const clone = template.content.cloneNode(true);
                clone.querySelectorAll('.product-item').forEach(item => {
                    const input = item.querySelector('.product-qty');
                    if (input) {
                        productInfo[input.dataset.productId] = {
                            name: item.querySelector('.product-name').textContent,
                            price: parseFloat(input.dataset.price),
                            weight: parseFloat(input.dataset.weight) || 0
                        };
                    }
                });
            });

            for (const [productId, qty] of Object.entries(selectedItems)) {
                if (qty > 0 && productInfo[productId]) {
                    const info = productInfo[productId];
                    catalogTotal += qty * info.price;
                    totalWeightKg += qty * info.weight;
                    catalogItems.push({ name: info.name, qty, price: info.price, total: qty * info.price });
                }
            }

            const shoppingCount = document.querySelectorAll('.shopping-item').length;
            const summaryDiv = document.getElementById('summaryItems');
            const tierBadgeDiv = document.getElementById('tierBadge');
            const deliveryInputGroup = document.getElementById('deliveryInputGroup');
            const feeBreakdown = document.getElementById('feeBreakdown');
            const summarySubtotal = document.getElementById('summarySubtotal');
            const taxSection = document.getElementById('taxSection');
            const tipSection = document.getElementById('tipSection');
            const totalDiv = document.getElementById('summaryTotal');

            if (catalogItems.length === 0 && shoppingCount === 0) {
                summaryDiv.innerHTML = '<p style="color: #666; font-size: 14px; text-align: center; padding: 24px 0;">No items added yet</p>';
                tierBadgeDiv.style.display = 'none';
                deliveryInputGroup.style.display = 'none';
                feeBreakdown.style.display = 'none';
                summarySubtotal.style.display = 'none';
                taxSection.style.display = 'none';
                tipSection.style.display = 'none';
                totalDiv.style.display = 'none';
            } else {
                const tier = getTier(catalogTotal);
                const tierConfig = PRICING_TIERS[tier];

                tierBadgeDiv.style.display = 'block';
                tierBadgeDiv.innerHTML = `<span class="tier-badge tier-${tier}"><i class="fas fa-layer-group"></i> Tier ${tier} - ${tierConfig.vehicle}</span>`;

                let html = '';
                catalogItems.forEach(item => {
                    html += `<div class="summary-item item-row"><span>${item.qty}x ${item.name.substring(0, 20)}${item.name.length > 20 ? '...' : ''}</span><span>$${item.total.toFixed(2)}</span></div>`;
                });
                if (shoppingCount > 0) {
                    html += `<div class="summary-item item-row"><span><i class="fas fa-list"></i> ${shoppingCount} shopping list item(s)</span><span>TBD</span></div>`;
                }
                summaryDiv.innerHTML = html;

                deliveryInputGroup.style.display = 'flex';
                const distance = parseFloat(document.getElementById('deliveryDistance').value) || 0;

                const serviceFee = catalogTotal * tierConfig.serviceFee;
                const handlingFee = totalWeightKg * HANDLING_RATE_PER_KG;
                const deliveryFee = calculateDeliveryFee(distance, tier);

                feeBreakdown.style.display = 'block';
                document.getElementById('itemsTotal').textContent = '$' + catalogTotal.toFixed(2) + (shoppingCount > 0 ? '+' : '');
                document.getElementById('serviceFeePercent').textContent = (tierConfig.serviceFee * 100).toFixed(0);
                document.getElementById('serviceFeeAmount').textContent = '$' + serviceFee.toFixed(2);
                document.getElementById('handlingWeightInfo').textContent = totalWeightKg.toFixed(1) + ' kg';
                document.getElementById('handlingFeeAmount').textContent = '$' + handlingFee.toFixed(2);

                if (distance <= tierConfig.freeDeliveryKm) {
                    document.getElementById('deliveryInfo').textContent = `Free ≤${tierConfig.freeDeliveryKm}km`;
                    document.getElementById('deliveryFeeAmount').textContent = '$0.00';
                } else {
                    document.getElementById('deliveryInfo').textContent = `${distance - tierConfig.freeDeliveryKm}km × $${tierConfig.perKmRate.toFixed(2)}`;
                    document.getElementById('deliveryFeeAmount').textContent = '$' + deliveryFee.toFixed(2);
                }

                const subtotal = catalogTotal + serviceFee + handlingFee + deliveryFee;
                summarySubtotal.style.display = 'flex';
                document.getElementById('subtotalAmount').textContent = '$' + subtotal.toFixed(2) + (shoppingCount > 0 ? '+' : '');

                const gst = subtotal * GST_RATE;
                const qst = subtotal * QST_RATE;
                taxSection.style.display = 'block';
                document.getElementById('gstAmount').textContent = '$' + gst.toFixed(2);
                document.getElementById('qstAmount').textContent = '$' + qst.toFixed(2);

                // Show tip section and calculate tip
                tipSection.style.display = 'block';
                let tipAmount = 0;
                if (selectedTipPercent === 'custom') {
                    tipAmount = parseFloat(document.getElementById('tipCustomAmount').value) || 0;
                } else if (selectedTipPercent > 0) {
                    tipAmount = catalogTotal * (selectedTipPercent / 100);
                }
                if (tipAmount > 0) {
                    document.getElementById('tipDisplay').style.display = 'flex';
                    if (selectedTipPercent === 'custom') {
                        document.getElementById('tipLabel').textContent = 'Tip (Custom)';
                    } else {
                        document.getElementById('tipLabel').textContent = 'Tip (' + selectedTipPercent + '%)';
                    }
                    document.getElementById('tipAmount').textContent = '$' + tipAmount.toFixed(2);
                } else {
                    document.getElementById('tipDisplay').style.display = 'none';
                }
                document.getElementById('tipCustomAmountHidden').value = tipAmount.toFixed(2);

                const total = subtotal + gst + qst + tipAmount;
                totalDiv.style.display = 'flex';
                document.getElementById('totalAmount').textContent = '$' + total.toFixed(2) + (shoppingCount > 0 ? '+' : '');
            }
        }

        function selectTip(percent) {
            selectedTipPercent = percent;
            if (percent === 'custom') {
                document.getElementById('tipPercentage').value = 0;
                document.getElementById('tipCustomInput').style.display = 'block';
                document.getElementById('tipCustomAmount').focus();
            } else {
                document.getElementById('tipPercentage').value = percent;
                document.getElementById('tipCustomInput').style.display = 'none';
                document.getElementById('tipCustomAmount').value = '';
                document.getElementById('tipCustomAmountHidden').value = 0;
            }
            document.querySelectorAll('.tip-btn').forEach(btn => {
                const btnTip = btn.dataset.tip;
                btn.classList.toggle('active', btnTip === String(percent));
            });
            updateSummary();
        }

        function updateCustomTip() {
            updateSummary();
        }

        // Initialize tip button active state
        if (selectedTipPercent > 0) {
            document.querySelector(`.tip-btn[data-tip="${selectedTipPercent}"]`)?.classList.add('active');
        } else {
            // Check if there's a custom tip amount saved
            const savedCustom = parseFloat(document.getElementById('tipCustomAmountHidden').value) || 0;
            if (savedCustom > 0) {
                selectedTipPercent = 'custom';
                document.querySelector('.tip-btn[data-tip="custom"]')?.classList.add('active');
                document.getElementById('tipCustomInput').style.display = 'block';
                document.getElementById('tipCustomAmount').value = savedCustom.toFixed(2);
            }
        }

        // Initial update
        updateSummary();
    </script>
<?php require __DIR__ . '/../layout-footer.php'; ?>
