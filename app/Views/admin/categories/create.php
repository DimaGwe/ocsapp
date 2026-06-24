<?php
$pageTitle = 'Add Category';
$currentPage = 'categories';
ob_start();
?>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font Awesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="<?= url('admin/categories') ?>" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Category</h1>
            <p class="text-gray-600 mt-2">Create a new product category</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (hasFlash('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <p class="font-medium"><?= getFlash('error') ?></p>
    </div>
<?php endif; ?>

<!-- Category Form -->
<form method="POST" action="<?= url('admin/categories/store') ?>" enctype="multipart/form-data" class="max-w-3xl">
    <?= csrfField() ?>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
        
        <!-- Category Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Category Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= old('name') ?>"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., Electronics"
                required
            >
        </div>

        <!-- Slug -->
        <div>
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
            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from category name</p>
        </div>

        <!-- Parent Category -->
        <div>
            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                Parent Category
            </label>
            <select 
                id="parent_id" 
                name="parent_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">None (Top Level Category)</option>
                <?php foreach ($parentCategories ?? [] as $parent): ?>
                    <option value="<?= $parent['id'] ?>" <?= old('parent_id') == $parent['id'] ? 'selected' : '' ?>>
                        <?= sanitize($parent['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea 
                id="description" 
                name="description" 
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Category description..."
            ><?= old('description') ?></textarea>
        </div>

        <!-- Image Upload -->
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                Category Image
            </label>
            <div class="mt-2 flex items-center gap-4">
                <div class="flex-1">
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        onchange="previewImage(event)"
                    >
                    <p class="mt-1 text-xs text-gray-500">
                        Allowed: JPG, PNG, GIF, WebP. Max size: 5MB. Recommended: 800x600px
                    </p>
                </div>
                <div id="imagePreview" class="hidden">
                    <img src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                </div>
            </div>
        </div>

        <!-- Icon -->
        <div>
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                Icon (Font Awesome class)
            </label>
            <input 
                type="text" 
                id="icon" 
                name="icon" 
                value="<?= old('icon') ?>"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="e.g., fa-laptop"
            >
            <p class="mt-1 text-xs text-gray-500">
                Browse icons at <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-600 hover:underline">fontawesome.com/icons</a>
            </p>
        </div>

        <!-- Sort Order -->
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                Sort Order
            </label>
            <input 
                type="number" 
                id="sort_order" 
                name="sort_order" 
                value="<?= old('sort_order', '0') ?>"
                min="0"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
        </div>

        <!-- Active Status -->
        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="is_active" 
                name="is_active"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                <?= old('is_active', true) ? 'checked' : '' ?>
            >
            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                Active (visible to customers)
            </label>
        </div>

        <!-- SEO Section -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h3>
            
            <!-- Meta Title -->
            <div class="mb-4">
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Title
                </label>
                <input 
                    type="text" 
                    id="meta_title" 
                    name="meta_title" 
                    value="<?= old('meta_title') ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="SEO title for search engines"
                >
            </div>

            <!-- Meta Description -->
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Description
                </label>
                <textarea 
                    id="meta_description" 
                    name="meta_description" 
                    rows="2"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="SEO description for search engines (max 160 characters)"
                ><?= old('meta_description') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6 flex justify-between">
        <a href="<?= url('admin/categories') ?>" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
            Cancel
        </a>
        <button 
            type="submit" 
            class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition font-medium"
        >
            <i class="fas fa-save mr-2"></i> Create Category
        </button>
    </div>
</form>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>