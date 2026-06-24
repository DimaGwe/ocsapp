<?php
/**
 * UPDATED: Product Create Page with Image Validation Feedback
 * Replace: app/Views/admin/products/create.php
 */

$pageTitle = 'Add Product';
$currentPage = 'products';
ob_start();
?>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="<?= url('admin/products') ?>" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-gray-600 mt-2">Create a new product in your catalog</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (hasFlash('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <p class="font-medium"><?= getFlash('error') ?></p>
    </div>
<?php endif; ?>

<?php if (hasFlash('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p class="font-medium"><?= getFlash('success') ?></p>
    </div>
<?php endif; ?>

<!-- Image Upload Warnings -->
<div id="image-warnings" class="hidden bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle mt-1 mr-3"></i>
        <div>
            <p class="font-medium">Image Upload Issues:</p>
            <ul id="warning-list" class="list-disc list-inside mt-2 text-sm"></ul>
        </div>
    </div>
</div>

<!-- Product Form -->
<form method="POST" action="<?= url('admin/products/store') ?>" enctype="multipart/form-data" class="space-y-6" id="productForm">
    <?= csrfField() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content (Left - 2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                
                <!-- Product Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="<?= old('name') ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="e.g., Samsung Galaxy S23"
                        required
                    >
                </div>

                <!-- Slug -->
                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug (URL) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        value="<?= old('slug') ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="auto-generated from name if left empty"
                    >
                    <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from product name</p>
                </div>

                <!-- SKU -->
                <div class="mb-4">
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU (Stock Keeping Unit)
                    </label>
                    <input 
                        type="text" 
                        id="sku" 
                        name="sku" 
                        value="<?= old('sku') ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="e.g., SAM-S23-001"
                    >
                </div>

                <!-- Short Description -->
                <div class="mb-4">
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Short Description
                    </label>
                    <textarea 
                        id="short_description" 
                        name="short_description" 
                        rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Brief product description (max 255 characters)"
                    ><?= old('short_description') ?></textarea>
                </div>

                <!-- Full Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Detailed product description, features, specifications..."
                    ><?= old('description') ?></textarea>
                </div>
            </div>

            <!-- Product Images -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h3>
                
                <!-- Image Preview Area -->
                <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 hidden">
                    <!-- Previews will be added here dynamically -->
                </div>

                <!-- Upload Area -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-indigo-500 transition" id="upload-area">
                    <div class="text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-sm text-gray-600 mb-2">Drag and drop images here or click to browse</p>
                        <p class="text-xs text-gray-500 mb-2">
                            <strong>Accepted:</strong> JPG, JPEG, PNG, GIF, WebP<br>
                            <strong>Max size:</strong> 5MB per image
                        </p>
                        
                        <input 
                            type="file" 
                            id="images" 
                            name="images[]" 
                            accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                            multiple
                            class="hidden"
                            onchange="validateAndPreviewImages(this.files)"
                        >
                        <label 
                            for="images"
                            class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition cursor-pointer mt-2"
                        >
                            <i class="fas fa-plus mr-2"></i> Choose Images
                        </label>
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle"></i> The first image will be set as the primary product image
                </p>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing & Inventory</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Base Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Base Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500"><?= currencySymbol() ?></span>
                            <input
                                type="number"
                                id="base_price"
                                name="base_price"
                                value="<?= old('base_price', '0') ?>"
                                step="0.01"
                                min="0"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >
                        </div>
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Cost Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500"><?= currencySymbol() ?></span>
                            <input 
                                type="number" 
                                id="cost_price" 
                                name="cost_price" 
                                value="<?= old('cost_price', '0') ?>"
                                step="0.01"
                                min="0"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                            Unit
                        </label>
                        <select 
                            id="unit" 
                            name="unit"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="piece" <?= old('unit') === 'piece' ? 'selected' : '' ?>>Piece</option>
                            <option value="kg" <?= old('unit') === 'kg' ? 'selected' : '' ?>>Kilogram</option>
                            <option value="liter" <?= old('unit') === 'liter' ? 'selected' : '' ?>>Liter</option>
                            <option value="meter" <?= old('unit') === 'meter' ? 'selected' : '' ?>>Meter</option>
                            <option value="pair" <?= old('unit') === 'pair' ? 'selected' : '' ?>>Pair</option>
                            <option value="set" <?= old('unit') === 'set' ? 'selected' : '' ?>>Set</option>
                            <option value="box" <?= old('unit') === 'box' ? 'selected' : '' ?>>Box</option>
                        </select>
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Weight (kg)
                        </label>
                        <input 
                            type="number" 
                            id="weight" 
                            name="weight" 
                            value="<?= old('weight', '0') ?>"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right - 1 column) -->
        <div class="space-y-6">
            
            <!-- Status & Visibility -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status & Visibility</h3>
                
                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select 
                        id="status" 
                        name="status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="draft" <?= old('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="out_of_stock" <?= old('status') === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>

                <!-- Featured -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="is_featured" 
                        name="is_featured"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        <?= old('is_featured') ? 'checked' : '' ?>
                    >
                    <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                        Mark as Featured Product
                    </label>
                </div>

                <!-- Show on Home Page -->
                <div class="flex items-center mt-3">
                    <input 
                        type="checkbox" 
                        id="show_on_home" 
                        name="show_on_home"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        <?= old('show_on_home') ? 'checked' : '' ?>
                    >
                    <label for="show_on_home" class="ml-2 block text-sm text-gray-700">
                        Show in "Best Sellers" on Homepage
                    </label>
                </div>
            </div>

            <!-- Brand -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Brand</h3>
                
                <select 
                    id="brand_id" 
                    name="brand_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">No Brand</option>
                    <?php foreach ($brands ?? [] as $brand): ?>
                        <option value="<?= $brand['id'] ?>" <?= old('brand_id') == $brand['id'] ? 'selected' : '' ?>>
                            <?= sanitize($brand['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <?php foreach ($categories ?? [] as $category): ?>
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="category_<?= $category['id'] ?>" 
                                name="categories[]"
                                value="<?= $category['id'] ?>"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            >
                            <label for="category_<?= $category['id'] ?>" class="ml-2 block text-sm text-gray-700">
                                <?= sanitize($category['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="mt-2 text-xs text-gray-500">Select one or more categories</p>
            </div>

            <!-- Tags -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php foreach ($tags ?? [] as $tag): ?>
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="tag_<?= $tag['id'] ?>" 
                                name="tags[]"
                                value="<?= $tag['id'] ?>"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            >
                            <label for="tag_<?= $tag['id'] ?>" class="ml-2 block text-sm text-gray-700">
                                <?= sanitize($tag['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-search mr-2 text-indigo-600"></i>
                        Search Engine Optimization
                    </h3>
                    <button
                        type="button"
                        onclick="toggleSeoSection()"
                        class="text-sm text-indigo-600 hover:text-indigo-800"
                    >
                        <span id="seo-toggle-text">Show SEO Fields</span>
                        <i id="seo-toggle-icon" class="fas fa-chevron-down ml-1"></i>
                    </button>
                </div>

                <p class="text-sm text-gray-600 mb-4">Optimize how this product appears in search engines and social media</p>

                <div id="seo-fields" class="hidden space-y-4">
                    <!-- Meta Title -->
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Title
                        </label>
                        <input
                            type="text"
                            id="meta_title"
                            name="meta_title"
                            value="<?= old('meta_title') ?>"
                            maxlength="60"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Leave empty to use product name"
                            onkeyup="updateCharCount('meta_title', 60)"
                        >
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500">Title that appears in search results</p>
                            <p class="text-xs text-gray-500">
                                <span id="meta_title_count">0</span>/60
                            </p>
                        </div>
                    </div>

                    <!-- Meta Description -->
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Description
                        </label>
                        <textarea
                            id="meta_description"
                            name="meta_description"
                            rows="3"
                            maxlength="160"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Brief description for search results"
                            onkeyup="updateCharCount('meta_description', 160)"
                        ><?= old('meta_description') ?></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-500">Description that appears below title in search results</p>
                            <p class="text-xs text-gray-500">
                                <span id="meta_description_count">0</span>/160
                            </p>
                        </div>
                    </div>

                    <!-- Meta Keywords -->
                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Keywords
                        </label>
                        <input
                            type="text"
                            id="meta_keywords"
                            name="meta_keywords"
                            value="<?= old('meta_keywords') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="organic, fresh, local, groceries"
                        >
                        <p class="mt-1 text-xs text-gray-500">Comma-separated keywords related to this product</p>
                    </div>

                    <!-- Robots Meta -->
                    <div>
                        <label for="robots_meta" class="block text-sm font-medium text-gray-700 mb-2">
                            Robots Meta Tag
                        </label>
                        <select
                            id="robots_meta"
                            name="robots_meta"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="index,follow" <?= old('robots_meta') === 'index,follow' ? 'selected' : '' ?>>
                                Index, Follow (Default - Allow search engines)
                            </option>
                            <option value="noindex,follow" <?= old('robots_meta') === 'noindex,follow' ? 'selected' : '' ?>>
                                No Index, Follow (Hide from search, but follow links)
                            </option>
                            <option value="index,nofollow" <?= old('robots_meta') === 'index,nofollow' ? 'selected' : '' ?>>
                                Index, No Follow (Show in search, don't follow links)
                            </option>
                            <option value="noindex,nofollow" <?= old('robots_meta') === 'noindex,nofollow' ? 'selected' : '' ?>>
                                No Index, No Follow (Hide completely)
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Control how search engines index this product</p>
                    </div>

                    <!-- Canonical URL -->
                    <div>
                        <label for="canonical_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Canonical URL
                        </label>
                        <input
                            type="url"
                            id="canonical_url"
                            name="canonical_url"
                            value="<?= old('canonical_url') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="https://ocsapp.ca/product/slug"
                        >
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use default product URL. Used to prevent duplicate content issues.</p>
                    </div>

                    <!-- OG Image URL -->
                    <div>
                        <label for="og_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Social Share Image URL
                        </label>
                        <input
                            type="url"
                            id="og_image"
                            name="og_image"
                            value="<?= old('og_image') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="https://ocsapp.ca/uploads/products/image.jpg"
                        >
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use primary product image. Recommended size: 1200x630px</p>
                    </div>

                    <!-- SEO Preview -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-semibold text-gray-700 mb-3">Google Search Preview</p>
                        <div class="bg-white p-4 rounded">
                            <div class="text-sm text-blue-700 hover:underline cursor-pointer" id="preview_title">
                                Product Name | OCS Marketplace
                            </div>
                            <div class="text-xs text-green-700 mt-1" id="preview_url">
                                https://ocsapp.ca/product/product-slug
                            </div>
                            <div class="text-sm text-gray-600 mt-2" id="preview_description">
                                Product description will appear here...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 flex justify-between">
        <a href="<?= url('admin/products') ?>" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
            Cancel
        </a>
        <button 
            type="submit" 
            class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-medium"
        >
            <i class="fas fa-save mr-2"></i> Create Product
        </button>
    </div>
</form>

<script>
let selectedFiles = [];
let validFiles = [];
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
const ALLOWED_EXTENSIONS = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];

function validateAndPreviewImages(files) {
    const warnings = [];
    validFiles = [];
    
    Array.from(files).forEach((file, index) => {
        // Check file size
        if (file.size > MAX_FILE_SIZE) {
            warnings.push(`"${file.name}" is too large (${(file.size / 1024 / 1024).toFixed(2)}MB). Max 5MB allowed.`);
            return;
        }
        
        // Check file type
        const fileExt = '.' + file.name.split('.').pop().toLowerCase();
        if (!ALLOWED_TYPES.includes(file.type) && !ALLOWED_EXTENSIONS.includes(fileExt)) {
            warnings.push(`"${file.name}" has invalid format. Allowed: JPG, PNG, GIF, WebP`);
            return;
        }
        
        // Valid file
        validFiles.push(file);
    });
    
    // Show warnings
    if (warnings.length > 0) {
        showWarnings(warnings);
    } else {
        hideWarnings();
    }
    
    // Update file input with valid files only
    if (validFiles.length > 0) {
        const dataTransfer = new DataTransfer();
        validFiles.forEach(file => dataTransfer.items.add(file));
        document.getElementById('images').files = dataTransfer.files;
        
        // Preview valid files
        previewImages(validFiles);
    } else {
        document.getElementById('image-preview').classList.add('hidden');
    }
}

function showWarnings(warnings) {
    const warningDiv = document.getElementById('image-warnings');
    const warningList = document.getElementById('warning-list');
    
    warningList.innerHTML = '';
    warnings.forEach(warning => {
        const li = document.createElement('li');
        li.textContent = warning;
        warningList.appendChild(li);
    });
    
    warningDiv.classList.remove('hidden');
    
    // Scroll to warnings
    warningDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function hideWarnings() {
    document.getElementById('image-warnings').classList.add('hidden');
}

function previewImages(files) {
    const previewContainer = document.getElementById('image-preview');
    
    if (files.length === 0) {
        previewContainer.classList.add('hidden');
        return;
    }
    
    previewContainer.classList.remove('hidden');
    previewContainer.innerHTML = '';
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'relative group';
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover rounded-lg border-2 ${index === 0 ? 'border-yellow-400' : 'border-gray-200'}">
                <div class="absolute top-2 right-2">
                    <button 
                        type="button"
                        onclick="removeImage(${index})"
                        class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition opacity-0 group-hover:opacity-100"
                        title="Remove"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                ${index === 0 ? '<div class="absolute bottom-2 left-2"><span class="bg-yellow-400 text-gray-900 px-2 py-1 rounded text-xs font-medium"><i class="fas fa-star"></i> Primary</span></div>' : ''}
            `;
            previewContainer.appendChild(preview);
        };
        
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    validFiles.splice(index, 1);
    
    if (validFiles.length > 0) {
        const dataTransfer = new DataTransfer();
        validFiles.forEach(file => dataTransfer.items.add(file));
        document.getElementById('images').files = dataTransfer.files;
        previewImages(validFiles);
    } else {
        document.getElementById('images').value = '';
        document.getElementById('image-preview').classList.add('hidden');
        hideWarnings();
    }
}

// Drag and drop support
const uploadArea = document.getElementById('upload-area');
if (uploadArea) {
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
        
        const files = e.dataTransfer.files;
        validateAndPreviewImages(files);
    });
}

// Validate on form submit
document.getElementById('productForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('images');
    if (fileInput.files.length > 0 && validFiles.length === 0) {
        e.preventDefault();
        alert('Please remove invalid images before submitting or choose valid images.');
        return false;
    }
});

// ===========================
// SEO Functions
// ===========================

// Toggle SEO Section
function toggleSeoSection() {
    const seoFields = document.getElementById('seo-fields');
    const toggleText = document.getElementById('seo-toggle-text');
    const toggleIcon = document.getElementById('seo-toggle-icon');

    if (seoFields.classList.contains('hidden')) {
        seoFields.classList.remove('hidden');
        toggleText.textContent = 'Hide SEO Fields';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    } else {
        seoFields.classList.add('hidden');
        toggleText.textContent = 'Show SEO Fields';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    }
}

// Update Character Count
function updateCharCount(fieldId, maxLength) {
    const field = document.getElementById(fieldId);
    const counter = document.getElementById(fieldId + '_count');
    const currentLength = field.value.length;

    counter.textContent = currentLength;

    // Change color based on length
    if (currentLength > maxLength * 0.9) {
        counter.parentElement.classList.add('text-red-600');
        counter.parentElement.classList.remove('text-gray-500');
    } else if (currentLength > maxLength * 0.7) {
        counter.parentElement.classList.add('text-yellow-600');
        counter.parentElement.classList.remove('text-gray-500', 'text-red-600');
    } else {
        counter.parentElement.classList.remove('text-red-600', 'text-yellow-600');
        counter.parentElement.classList.add('text-gray-500');
    }

    // Update preview
    updateSeoPreview();
}

// Update SEO Preview
function updateSeoPreview() {
    const productName = document.getElementById('name')?.value || 'Product Name';
    const metaTitle = document.getElementById('meta_title')?.value || productName;
    const metaDescription = document.getElementById('meta_description')?.value || 'Product description will appear here...';
    const slug = document.getElementById('slug')?.value || 'product-slug';

    // Update preview elements
    document.getElementById('preview_title').textContent = metaTitle + ' | OCS Marketplace';
    document.getElementById('preview_url').textContent = 'https://ocsapp.ca/product/' + (slug || 'product-slug');
    document.getElementById('preview_description').textContent = metaDescription;
}

// Update preview when product name or slug changes
document.getElementById('name')?.addEventListener('input', updateSeoPreview);
document.getElementById('slug')?.addEventListener('input', updateSeoPreview);
document.getElementById('meta_title')?.addEventListener('input', updateSeoPreview);
document.getElementById('meta_description')?.addEventListener('input', updateSeoPreview);

// Auto-generate slug from product name
document.getElementById('name')?.addEventListener('blur', function() {
    const slugField = document.getElementById('slug');
    if (!slugField.value) {
        const slug = this.value
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugField.value = slug;
        updateSeoPreview();
    }
});

// Initialize character counters on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCharCount('meta_title', 60);
    updateCharCount('meta_description', 160);
    updateSeoPreview();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>