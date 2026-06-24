<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Helpers\ImageUploadHelper;

class CategoryController {
    
    private ImageUploadHelper $imageUploader;

    public function __construct() {
        AuthMiddleware::handle('admin');
        $this->imageUploader = new ImageUploadHelper('uploads/categories');
    }

    // Categories List
    public function index(): void {
        try {
            $db = \Database::getConnection();
            
            // Get all categories with parent info
            $stmt = $db->query("
                SELECT c.*, 
                       p.name as parent_name,
                       (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as children_count,
                       (SELECT COUNT(*) FROM product_categories WHERE category_id = c.id) as products_count
                FROM categories c
                LEFT JOIN categories p ON c.parent_id = p.id
                ORDER BY c.parent_id, c.sort_order, c.name
            ");
            $categories = $stmt->fetchAll();

            view('admin.categories.index', [
                'categories' => $categories,
            ]);

        } catch (\PDOException $e) {
            logger("Categories index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading categories');
            back();
        }
    }

    // Create Category Form
    public function create(): void {
        try {
            $db = \Database::getConnection();
            
            // Get parent categories only
            $parentCategories = $db->query("
                SELECT * FROM categories 
                WHERE parent_id IS NULL 
                ORDER BY name
            ")->fetchAll();

            view('admin.categories.create', [
                'parentCategories' => $parentCategories,
            ]);

        } catch (\PDOException $e) {
            logger("Category create error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading form');
            back();
        }
    }

    // Store Category
    public function store(): void {
        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        $data = [
            'parent_id' => post('parent_id', null),
            'name' => sanitize(post('name', '')),
            'slug' => sanitize(post('slug', '')),
            'description' => sanitize(post('description', '')),
            'icon' => sanitize(post('icon', '')),
            'sort_order' => (int) post('sort_order', 0),
            'is_active' => post('is_active') === 'on' ? 1 : 0,
            'meta_title' => sanitize(post('meta_title', '')),
            'meta_description' => sanitize(post('meta_description', '')),
        ];

        // Auto-generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // Validate required fields
        $errors = validateRequired(['name', 'slug'], $data);
        
        if (!empty($errors)) {
            setFlash('error', 'Please fill in all required fields');
            setOldInput($data);
            back();
        }

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->imageUploader->upload($_FILES['image']);
            
            if (!$uploadResult['success']) {
                setFlash('error', $uploadResult['message']);
                setOldInput($data);
                back();
            }
            
            $imagePath = $uploadResult['path'];
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                INSERT INTO categories (
                    parent_id, name, slug, description, icon, image, sort_order, 
                    is_active, meta_title, meta_description
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['parent_id'] ?: null,
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['icon'],
                $imagePath,
                $data['sort_order'],
                $data['is_active'],
                $data['meta_title'],
                $data['meta_description']
            ]);

            clearOldInput();
            setFlash('success', 'Category created successfully');
            redirect(url('admin/categories'));

        } catch (\PDOException $e) {
            // Delete uploaded image if database insert fails
            if ($imagePath) {
                $this->imageUploader->delete($imagePath);
            }
            
            logger("Category store error: " . $e->getMessage(), 'error');
            
            // Friendly error messages
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), "'slug'") !== false) {
                    setFlash('error', 'A category with this URL slug already exists. Please use a different slug.');
                } else {
                    setFlash('error', 'This category already exists. Please check your input.');
                }
            } else {
                setFlash('error', 'Error creating category. Please try again.');
            }
            
            setOldInput($data);
            back();
        }
    }

    // Edit Category Form
    public function edit(): void {
        $id = get('id');
        
        if (!$id) {
            setFlash('error', 'Category not found');
            redirect(url('admin/categories'));
        }

        try {
            $db = \Database::getConnection();
            
            $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch();

            if (!$category) {
                setFlash('error', 'Category not found');
                redirect(url('admin/categories'));
            }

            // Get parent categories (exclude current category and its children)
            $parentCategories = $db->prepare("
                SELECT * FROM categories 
                WHERE parent_id IS NULL AND id != ?
                ORDER BY name
            ");
            $parentCategories->execute([$id]);
            $parentCategories = $parentCategories->fetchAll();

            view('admin.categories.edit', [
                'category' => $category,
                'parentCategories' => $parentCategories,
            ]);

        } catch (\PDOException $e) {
            logger("Category edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading category');
            redirect(url('admin/categories'));
        }
    }

    // Update Category
    public function update(): void {
        $id = post('id');
        
        if (!$id) {
            setFlash('error', 'Category not found');
            redirect(url('admin/categories'));
        }

        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        try {
            $db = \Database::getConnection();
            
            // Get existing category
            $stmt = $db->prepare("SELECT image FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $existingCategory = $stmt->fetch();
            
            if (!$existingCategory) {
                setFlash('error', 'Category not found');
                redirect(url('admin/categories'));
            }

            $data = [
                'parent_id' => post('parent_id', null),
                'name' => sanitize(post('name', '')),
                'slug' => sanitize(post('slug', '')),
                'description' => sanitize(post('description', '')),
                'icon' => sanitize(post('icon', '')),
                'sort_order' => (int) post('sort_order', 0),
                'is_active' => post('is_active') === 'on' ? 1 : 0,
                'meta_title' => sanitize(post('meta_title', '')),
                'meta_description' => sanitize(post('meta_description', '')),
            ];

            // Handle image upload
            $imagePath = $existingCategory['image'];
            $removeImage = post('remove_image') === 'on';
            
            if ($removeImage && $imagePath) {
                // Delete existing image
                $this->imageUploader->delete($imagePath);
                $imagePath = null;
            } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Upload new image
                $uploadResult = $this->imageUploader->upload($_FILES['image']);
                
                if (!$uploadResult['success']) {
                    setFlash('error', $uploadResult['message']);
                    back();
                }
                
                // Delete old image
                if ($existingCategory['image']) {
                    $this->imageUploader->delete($existingCategory['image']);
                }
                
                $imagePath = $uploadResult['path'];
            }

            $stmt = $db->prepare("
                UPDATE categories SET
                    parent_id = ?, name = ?, slug = ?, description = ?, 
                    icon = ?, image = ?, sort_order = ?, is_active = ?,
                    meta_title = ?, meta_description = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['parent_id'] ?: null,
                $data['name'],
                $data['slug'],
                $data['description'],
                $data['icon'],
                $imagePath,
                $data['sort_order'],
                $data['is_active'],
                $data['meta_title'],
                $data['meta_description'],
                $id
            ]);

            setFlash('success', 'Category updated successfully');
            redirect(url('admin/categories'));

        } catch (\PDOException $e) {
            logger("Category update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating category');
            back();
        }
    }

    // Delete Category
    public function delete(): void {
        $id = post('id');
        
        if (!$id) {
            setFlash('error', 'Category not found');
            back();
        }

        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();
            
            // Check if category has children
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM categories WHERE parent_id = ?");
            $stmt->execute([$id]);
            $children = $stmt->fetch()['count'];

            if ($children > 0) {
                setFlash('error', 'Cannot delete category with subcategories');
                back();
            }

            // Check if category has products
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM product_categories WHERE category_id = ?");
            $stmt->execute([$id]);
            $products = $stmt->fetch()['count'];

            if ($products > 0) {
                setFlash('error', 'Cannot delete category with products. Remove products first.');
                back();
            }

            // Get category image
            $stmt = $db->prepare("SELECT image FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch();

            // Delete category
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);

            // Delete image file
            if ($category && $category['image']) {
                $this->imageUploader->delete($category['image']);
            }

            setFlash('success', 'Category deleted successfully');
            redirect(url('admin/categories'));

        } catch (\PDOException $e) {
            logger("Category delete error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error deleting category');
            back();
        }
    }

    private function generateSlug(string $text): string {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}