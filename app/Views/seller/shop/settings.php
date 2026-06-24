<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Settings - OCSAPP</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/shop-settings.css') ?>">
    <style>
        /* Shop Type Selector Styles */
        .shop-type-card {
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .shop-type-card:hover {
            border-color: #00b207;
            background: #f8fdf8;
        }
        .shop-type-card.selected {
            border-color: #00b207;
            background: #e8f5e9;
            box-shadow: 0 2px 8px rgba(0, 178, 7, 0.2);
        }
        .shop-type-icon {
            font-size: 2.5rem;
            min-width: 60px;
            text-align: center;
        }
        .shop-type-info {
            flex: 1;
        }
        .shop-type-title {
            font-weight: 600;
            font-size: 16px;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .shop-type-description {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.4;
        }
        .shop-type-radio {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        #shop_type_hidden {
            display: none;
        }
        
        @media (max-width: 768px) {
            .shop-type-card {
                padding: 12px;
            }
            .shop-type-icon {
                font-size: 2rem;
                min-width: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <!-- Header -->
        <div class="settings-header">
            <h1>
                <i class="fas fa-cog"></i>
                Shop Settings
            </h1>
            <span class="status-badge status-<?= $shop['status'] ?? 'pending' ?>">
                <i class="fas fa-circle" style="font-size: 8px;"></i>
                <?= ucfirst($shop['status'] ?? 'pending') ?>
            </span>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlash('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Error!</strong>
                    <p><?= getFlash('error') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (hasFlash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Success!</strong>
                    <p><?= getFlash('success') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pending Approval Alert -->
        <?php if (($shop['status'] ?? '') === 'pending'): ?>
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>Pending Approval</strong>
                    <p>Your shop is waiting for admin approval. You'll be notified once it's approved.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('basic')">
                <i class="fas fa-info-circle"></i>
                Basic Info
            </button>
            <button class="nav-tab" onclick="showTab('images')">
                <i class="fas fa-images"></i>
                Shop Images
            </button>
            <button class="nav-tab" onclick="showTab('delivery')">
                <i class="fas fa-truck"></i>
                Delivery
            </button>
            <button class="nav-tab" onclick="showTab('hours')">
                <i class="fas fa-clock"></i>
                Business Hours
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="<?= url('seller/shop/update') ?>" enctype="multipart/form-data">
            <?= csrfField() ?>

            <!-- Basic Information Tab -->
            <div id="basic" class="tab-content active">
                <h2>Basic Information</h2>
                <p>Update your shop's basic information and contact details</p>

                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-store"></i> Shop Name *
                    </label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($shop['name'] ?? '') ?>" required placeholder="Enter your shop name">
                </div>

                <div class="form-group">
                    <label for="slug">
                        <i class="fas fa-link"></i> Shop URL
                    </label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($shop['slug'] ?? '') ?>" readonly>
                    <small>
                        <i class="fas fa-info-circle"></i>
                        Your shop will be accessible at: ocs.com/shops/<?= htmlspecialchars($shop['slug'] ?? 'your-slug') ?>
                    </small>
                </div>

                <!-- Shop Type Selector -->
                <div class="form-group">
                    <label>
                        <i class="fas fa-store-alt"></i> Shop Type *
                    </label>
                    <small style="margin-bottom: 10px; display: block;">Choose the category that best describes your business. This determines where your shop appears in OCS Virtual Malls.</small>

                    <input type="hidden" id="shop_type_hidden" name="shop_type" value="<?= htmlspecialchars($shop['shop_type'] ?? 'grocery_store') ?>">

                    <div class="shop-type-card" onclick="selectShopType('grocery_store', this)">
                        <div class="shop-type-icon">🛒</div>
                        <div class="shop-type-info">
                            <div class="shop-type-title">Grocery Store</div>
                            <div class="shop-type-description">Fresh produce, groceries & daily essentials</div>
                        </div>
                        <input type="radio" name="shop_type_radio" value="grocery_store" class="shop-type-radio" <?= ($shop['shop_type'] ?? 'grocery_store') === 'grocery_store' ? 'checked' : '' ?>>
                    </div>

                    <div class="shop-type-card" onclick="selectShopType('food_court', this)">
                        <div class="shop-type-icon">🍽️</div>
                        <div class="shop-type-info">
                            <div class="shop-type-title">Food Court</div>
                            <div class="shop-type-description">Restaurants, fast food, cafes & bakeries</div>
                        </div>
                        <input type="radio" name="shop_type_radio" value="food_court" class="shop-type-radio" <?= ($shop['shop_type'] ?? '') === 'food_court' ? 'checked' : '' ?>>
                    </div>

                    <div class="shop-type-card" onclick="selectShopType('store', this)">
                        <div class="shop-type-icon">🛍️</div>
                        <div class="shop-type-info">
                            <div class="shop-type-title">Store</div>
                            <div class="shop-type-description">Clothing, electronics, pharmacy, salon & services</div>
                        </div>
                        <input type="radio" name="shop_type_radio" value="store" class="shop-type-radio" <?= ($shop['shop_type'] ?? '') === 'store' ? 'checked' : '' ?>>
                    </div>

                    <div class="shop-type-card" onclick="selectShopType('products', this)">
                        <div class="shop-type-icon">🎁</div>
                        <div class="shop-type-info">
                            <div class="shop-type-title">Products</div>
                            <div class="shop-type-description">Furniture, toys, sports, pets, auto & office supplies</div>
                        </div>
                        <input type="radio" name="shop_type_radio" value="products" class="shop-type-radio" <?= ($shop['shop_type'] ?? '') === 'products' ? 'checked' : '' ?>>
                    </div>
                    
                    <div class="help-text" style="margin-top: 10px;">
                        <i class="fas fa-lightbulb"></i>
                        <span>Your shop type determines which Virtual Mall section displays your shop on the OCS homepage.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-align-left"></i> Description
                    </label>
                    <textarea id="description" name="description" placeholder="Tell customers about your shop..."><?= htmlspecialchars($shop['description'] ?? '') ?></textarea>
                    <small>Describe what makes your shop unique</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i> Phone Number *
                        </label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($shop['phone'] ?? '') ?>" required placeholder="(809) 123-4567">
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($shop['email'] ?? '') ?>" placeholder="shop@example.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">
                        <i class="fas fa-map-marker-alt"></i> Shop Address *
                    </label>
                    <textarea id="address" name="address" rows="3" required placeholder="Enter your complete shop address..."><?= htmlspecialchars($shop['address'] ?? '') ?></textarea>
                    <small>Include street, sector, city, and any landmarks</small>
                </div>
            </div>

            <!-- Shop Images Tab -->
            <div id="images" class="tab-content">
                <h2>Shop Images</h2>
                <p>Upload high-quality images to make your shop stand out</p>

                <!-- Logo Upload -->
                <div class="form-group">
                    <label for="logo">
                        <i class="fas fa-store"></i> Shop Logo
                    </label>
                    
                    <?php if (!empty($shop['logo'])): ?>
                        <div class="current-image logo-container">
                            <p><i class="fas fa-image"></i> Current Logo:</p>
                            <img src="<?= asset($shop['logo']) ?>" alt="Current Logo">
                        </div>
                    <?php endif; ?>
                    
                    <div class="image-upload-box" onclick="document.getElementById('logo').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p><?= !empty($shop['logo']) ? 'Change Shop Logo' : 'Upload Shop Logo' ?></p>
                        <small>Square format • 500x500px recommended • Max 5MB</small>
                    </div>
                    <input type="file" id="logo" name="logo" accept="image/*" style="display: none;" onchange="previewImage(this, 'logo-preview')">
                    <div id="logo-preview" class="image-preview">
                        <span class="image-preview-label">
                            <i class="fas fa-eye"></i> Preview:
                        </span>
                    </div>
                    
                    <div class="help-text">
                        <i class="fas fa-lightbulb"></i>
                        <span>Your logo will be displayed on the homepage and in search results. Use a clear, recognizable image.</span>
                    </div>
                </div>

                <!-- Banner Upload -->
                <div class="form-group" style="margin-top: 40px;">
                    <label for="cover_image">
                        <i class="fas fa-panorama"></i> Shop Banner
                    </label>
                    
                    <?php if (!empty($shop['cover_image'])): ?>
                        <div class="current-image banner-container">
                            <p><i class="fas fa-image"></i> Current Banner:</p>
                            <img src="<?= asset($shop['cover_image']) ?>" alt="Current Banner">
                        </div>
                    <?php endif; ?>
                    
                    <div class="image-upload-box" onclick="document.getElementById('cover_image').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p><?= !empty($shop['cover_image']) ? 'Change Shop Banner' : 'Upload Shop Banner' ?></p>
                        <small>Wide format • 1200x400px recommended • Max 5MB</small>
                    </div>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'cover-preview')">
                    <div id="cover-preview" class="image-preview">
                        <span class="image-preview-label">
                            <i class="fas fa-eye"></i> Preview:
                        </span>
                    </div>
                    
                    <div class="help-text">
                        <i class="fas fa-lightbulb"></i>
                        <span>Your banner appears at the top of your shop page. Use a high-quality image that represents your brand.</span>
                    </div>
                </div>
            </div>

            <!-- Delivery Settings Tab -->
            <div id="delivery" class="tab-content">
                <h2>Delivery Settings</h2>
                <p>Configure your delivery options and pricing</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="delivery_radius">
                            <i class="fas fa-map-marked-alt"></i> Delivery Radius (km)
                        </label>
                        <input type="number" id="delivery_radius" name="delivery_radius" value="<?= $shop['delivery_radius'] ?? 5 ?>" min="0" max="50" placeholder="5">
                        <small>Maximum distance you deliver from your shop</small>
                    </div>

                    <div class="form-group">
                        <label for="packaging_time">
                            <i class="fas fa-box"></i> Packaging Time (minutes)
                        </label>
                        <input type="number" id="packaging_time" name="packaging_time" value="<?= $shop['packaging_time'] ?? 30 ?>" min="0" max="180" placeholder="30">
                        <small>Average time to prepare an order</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="base_delivery_fee">
                            <i class="fas fa-dollar-sign"></i> Base Delivery Fee (CAD$)
                        </label>
                        <input type="number" id="base_delivery_fee" name="base_delivery_fee" value="<?= $shop['base_delivery_fee'] ?? 0 ?>" min="0" step="0.01" placeholder="0.00">
                        <small>Fixed delivery fee for all orders</small>
                    </div>

                    <div class="form-group">
                        <label for="per_km_fee">
                            <i class="fas fa-route"></i> Per KM Fee (CAD$)
                        </label>
                        <input type="number" id="per_km_fee" name="per_km_fee" value="<?= $shop['per_km_fee'] ?? 0 ?>" min="0" step="0.01" placeholder="0.00">
                        <small>Additional fee per kilometer</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="free_delivery_threshold">
                            <i class="fas fa-gift"></i> Free Delivery Above (CAD$)
                        </label>
                        <input type="number" id="free_delivery_threshold" name="free_delivery_threshold" value="<?= $shop['free_delivery_threshold'] ?? 0 ?>" min="0" step="0.01" placeholder="0.00">
                        <small>Minimum order amount for free delivery</small>
                    </div>

                    <div class="form-group">
                        <label for="min_order_amount">
                            <i class="fas fa-shopping-cart"></i> Minimum Order (CAD$)
                        </label>
                        <input type="number" id="min_order_amount" name="min_order_amount" value="<?= $shop['min_order_amount'] ?? 0 ?>" min="0" step="0.01" placeholder="0.00">
                        <small>Minimum order value to accept</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="allow_delivery" name="allow_delivery" value="1" <?= ($shop['allow_delivery'] ?? false) ? 'checked' : '' ?>>
                            <label for="allow_delivery">
                                <i class="fas fa-truck"></i> Allow Delivery
                            </label>
                        </div>
                        <small>Enable delivery service for your shop</small>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="allow_self_pickup" name="allow_self_pickup" value="1" <?= ($shop['allow_self_pickup'] ?? false) ? 'checked' : '' ?>>
                            <label for="allow_self_pickup">
                                <i class="fas fa-walking"></i> Allow Self Pickup
                            </label>
                        </div>
                        <small>Let customers pick up orders at your shop</small>
                    </div>
                </div>
            </div>

            <!-- Business Hours Tab -->
            <div id="hours" class="tab-content">
                <h2>Business Hours</h2>
                <p>Set your shop's operating hours for each day of the week</p>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Coming Soon</strong>
                        <p>Business hours management will be available in the next update. Your shop is currently set to open 24/7.</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
                <a href="<?= url('seller/dashboard') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>

    <script>
        // Shop Type Selection
        function selectShopType(type, card) {
            // Remove selected class from all cards
            document.querySelectorAll('.shop-type-card').forEach(c => {
                c.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            card.classList.add('selected');
            
            // Update hidden input
            document.getElementById('shop_type_hidden').value = type;
            
            // Update radio button
            card.querySelector('input[type="radio"]').checked = true;
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedType = document.getElementById('shop_type_hidden').value;
            const selectedCard = document.querySelector(`input[value="${selectedType}"]`)?.closest('.shop-type-card');
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
        });
        
        // Tab switching function
        function showTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Deactivate all nav tabs
            const navTabs = document.querySelectorAll('.nav-tab');
            navTabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabName).classList.add('active');

            // Activate clicked nav tab
            event.target.classList.add('active');
        }

        // Image preview function
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    
                    // Clear previous preview
                    const existingImg = preview.querySelector('img');
                    if (existingImg) {
                        existingImg.remove();
                    }
                    
                    preview.appendChild(img);
                    preview.classList.add('active');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            
            if (!name || !phone || !address) {
                e.preventDefault();
                alert('Please fill in all required fields (marked with *)');
                return false;
            }
        });
    </script>
</body>
</html>