<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Shop - OCSAPP</title>
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        .shop-create-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        .form-group label.required::after {
            content: " *";
            color: #e74c3c;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group select {
            cursor: pointer;
            background-color: white;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
        }
        .btn-primary {
            background: #00b207;
            color: white;
        }
        .btn-primary:hover {
            background: #009206;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .image-upload-preview {
            margin-top: 10px;
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            padding: 10px;
            display: none;
        }
        .image-upload-preview img {
            max-width: 100%;
            max-height: 180px;
            border-radius: 5px;
        }
        .image-upload-box {
            border: 2px dashed #00b207;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8fdf8;
            cursor: pointer;
            transition: all 0.3s;
        }
        .image-upload-box:hover {
            background: #e8f5e9;
            border-color: #009206;
        }
        .image-upload-box i {
            font-size: 48px;
            color: #00b207;
            margin-bottom: 10px;
        }
        
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
            .form-row {
                grid-template-columns: 1fr;
            }
            .shop-type-card {
                padding: 12px;
            }
            .shop-type-icon {
                font-size: 2rem;
                min-width: 50px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="shop-create-container">
        <h1>Create Your Shop</h1>
        <p>Fill in the details below to start selling on OCS</p>

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

        <form method="POST" action="<?= url('seller/shop/store') ?>" enctype="multipart/form-data">
            <?= csrfField() ?>

            <!-- Basic Information -->
            <div class="section-title">Basic Information</div>

            <div class="form-group">
                <label for="name" class="required">Shop Name</label>
                <input type="text" id="name" name="name" value="<?= old('name') ?>" required>
                <small>This will be displayed to customers</small>
            </div>

            <div class="form-group">
                <label for="slug">Shop URL</label>
                <input type="text" id="slug" name="slug" value="<?= old('slug') ?>" placeholder="my-shop-name">
                <small>Leave empty to auto-generate from shop name. Example: https://ocsapp.ca/shops/your-slug</small>
            </div>

            <!-- Shop Type Selector -->
            <div class="form-group">
                <label class="required">Shop Type</label>
                <small style="margin-bottom: 10px;">Choose the category that best describes your business. This helps customers find you in OCS Virtual Malls.</small>

                <input type="hidden" id="shop_type_hidden" name="shop_type" value="<?= old('shop_type', 'grocery_store') ?>">

                <div class="shop-type-card" onclick="selectShopType('grocery_store', this)">
                    <div class="shop-type-icon">🛒</div>
                    <div class="shop-type-info">
                        <div class="shop-type-title">Grocery Store</div>
                        <div class="shop-type-description">Fresh produce, groceries & daily essentials</div>
                    </div>
                    <input type="radio" name="shop_type_radio" value="grocery_store" class="shop-type-radio" <?= old('shop_type', 'grocery_store') === 'grocery_store' ? 'checked' : '' ?>>
                </div>

                <div class="shop-type-card" onclick="selectShopType('food_court', this)">
                    <div class="shop-type-icon">🍽️</div>
                    <div class="shop-type-info">
                        <div class="shop-type-title">Food Court</div>
                        <div class="shop-type-description">Restaurants, fast food, cafes & bakeries</div>
                    </div>
                    <input type="radio" name="shop_type_radio" value="food_court" class="shop-type-radio" <?= old('shop_type') === 'food_court' ? 'checked' : '' ?>>
                </div>

                <div class="shop-type-card" onclick="selectShopType('store', this)">
                    <div class="shop-type-icon">🛍️</div>
                    <div class="shop-type-info">
                        <div class="shop-type-title">Store</div>
                        <div class="shop-type-description">Clothing, electronics, pharmacy, salon & services</div>
                    </div>
                    <input type="radio" name="shop_type_radio" value="store" class="shop-type-radio" <?= old('shop_type') === 'store' ? 'checked' : '' ?>>
                </div>

                <div class="shop-type-card" onclick="selectShopType('products', this)">
                    <div class="shop-type-icon">🎁</div>
                    <div class="shop-type-info">
                        <div class="shop-type-title">Products</div>
                        <div class="shop-type-description">Furniture, toys, sports, pets, auto & office supplies</div>
                    </div>
                    <input type="radio" name="shop_type_radio" value="products" class="shop-type-radio" <?= old('shop_type') === 'products' ? 'checked' : '' ?>>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?= old('description') ?></textarea>
                <small>Tell customers about your shop</small>
            </div>

            <!-- Shop Images -->
            <div class="section-title">Shop Images</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="logo">Shop Logo (Optional)</label>
                    <div class="image-upload-box" onclick="document.getElementById('logo').click()">
                        <i class="fas fa-image"></i>
                        <p>Click to upload logo</p>
                        <small>Displayed on homepage (500x500px recommended)</small>
                    </div>
                    <input type="file" id="logo" name="logo" accept="image/*" style="display: none;" onchange="previewImage(this, 'logo-preview')">
                    <div id="logo-preview" class="image-upload-preview"></div>
                </div>

                <div class="form-group">
                    <label for="cover_image">Shop Banner (Optional)</label>
                    <div class="image-upload-box" onclick="document.getElementById('cover_image').click()">
                        <i class="fas fa-panorama"></i>
                        <p>Click to upload banner</p>
                        <small>Displayed on shop page (1200x400px recommended)</small>
                    </div>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'cover-preview')">
                    <div id="cover-preview" class="image-upload-preview"></div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="section-title">Contact Information</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone" class="required">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= old('phone') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= old('email') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="required">Shop Address</label>
                <textarea id="address" name="address" rows="3" required><?= old('address') ?></textarea>
                <small>Full physical address of your shop</small>
            </div>

            <!-- Delivery Settings -->
            <div class="section-title">Delivery Settings</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="delivery_radius">Delivery Radius (km)</label>
                    <input type="number" id="delivery_radius" name="delivery_radius" value="<?= old('delivery_radius', '5') ?>" min="0">
                    <small>How far you deliver from your shop</small>
                </div>

                <div class="form-group">
                    <label for="packaging_time">Packaging Time (minutes)</label>
                    <input type="number" id="packaging_time" name="packaging_time" value="<?= old('packaging_time', '30') ?>" min="0">
                    <small>Time needed to prepare orders</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="base_delivery_fee">Base Delivery Fee (CAD$)</label>
                    <input type="number" id="base_delivery_fee" name="base_delivery_fee" value="<?= old('base_delivery_fee', '0') ?>" min="0" step="0.01">
                </div>

                <div class="form-group">
                    <label for="per_km_fee">Per KM Fee (CAD$)</label>
                    <input type="number" id="per_km_fee" name="per_km_fee" value="<?= old('per_km_fee', '0') ?>" min="0" step="0.01">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="free_delivery_threshold">Free Delivery Above (CAD$)</label>
                    <input type="number" id="free_delivery_threshold" name="free_delivery_threshold" value="<?= old('free_delivery_threshold', '0') ?>" min="0" step="0.01">
                    <small>Orders above this amount get free delivery</small>
                </div>

                <div class="form-group">
                    <label for="min_order_amount">Minimum Order (CAD$)</label>
                    <input type="number" id="min_order_amount" name="min_order_amount" value="<?= old('min_order_amount', '0') ?>" min="0" step="0.01">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="allow_delivery" name="allow_delivery" <?= old('allow_delivery', 'on') === 'on' ? 'checked' : '' ?>>
                        <label for="allow_delivery">Allow Delivery</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="allow_self_pickup" name="allow_self_pickup" <?= old('allow_self_pickup') === 'on' ? 'checked' : '' ?>>
                        <label for="allow_self_pickup">Allow Self Pickup</label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Create Shop</button>
                <a href="<?= url('seller/dashboard') ?>" class="btn btn-secondary">Cancel</a>
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
        
        // Auto-generate slug from shop name
        document.getElementById('name').addEventListener('input', function(e) {
            const slugInput = document.getElementById('slug');
            if (!slugInput.value || slugInput.dataset.autoGenerated) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });

        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.autoGenerated = '';
        });

        // Image preview function
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>