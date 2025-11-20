<?php

namespace App\Controllers;

use Database;

class SliderController
{
    /**
     * Display all hero sliders
     */
    public function index(): void
    {
        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT *
            FROM hero_sliders
            ORDER BY sort_order ASC, id ASC
        ");

        $sliders = $stmt->fetchAll();

        view('admin/sliders/index', [
            'sliders' => $sliders,
            'pageTitle' => 'Hero Sliders Management'
        ]);
    }

    /**
     * Show edit form for a slider
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            setFlash('error', 'Invalid slider ID');
            redirect('/admin/sliders');
            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM hero_sliders WHERE id = ?");
        $stmt->execute([$id]);
        $slider = $stmt->fetch();

        if (!$slider) {
            setFlash('error', 'Slider not found');
            redirect('/admin/sliders');
            return;
        }

        view('admin/sliders/edit', [
            'slider' => $slider,
            'pageTitle' => 'Edit Slider'
        ]);
    }

    /**
     * Update a slider
     */
    public function update(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $id = post('id');
        $title = post('title');
        $description = post('description');
        $buttonText = post('button_text');
        $buttonUrl = post('button_url');
        $sortOrder = post('sort_order', 0);
        $status = post('status', 'active');

        // Validate required fields
        if (empty($title)) {
            setFlash('error', 'Title is required');
            back();
            return;
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $db = Database::getConnection();

        // Check if slider exists
        $stmt = $db->prepare("SELECT id, image_path FROM hero_sliders WHERE id = ?");
        $stmt->execute([$id]);
        $slider = $stmt->fetch();

        if (!$slider) {
            setFlash('error', 'Slider not found');
            redirect('/admin/sliders');
            return;
        }

        // Handle image upload
        $imagePath = $slider['image_path'] ?? '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['image']);

            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];

                // Delete old image if it exists and is different
                if (!empty($slider['image_path']) && $slider['image_path'] !== $imagePath) {
                    $this->deleteImage($slider['image_path']);
                }
            } else {
                setFlash('error', 'Image upload failed: ' . $uploadResult['error']);
                back();
                return;
            }
        }

        // Update slider
        $stmt = $db->prepare("
            UPDATE hero_sliders
            SET title = ?,
                description = ?,
                button_text = ?,
                button_url = ?,
                image_path = ?,
                sort_order = ?,
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        $result = $stmt->execute([
            $title,
            $description,
            $buttonText,
            $buttonUrl,
            $imagePath,
            $sortOrder,
            $status,
            $id
        ]);

        if ($result) {
            setFlash('success', 'Slider updated successfully');
        } else {
            setFlash('error', 'Failed to update slider');
        }

        redirect('/admin/sliders');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            view('admin/sliders/create', [
                'pageTitle' => 'Create New Slider'
            ]);
            return;
        }

        // Handle POST request
        if (!verifyCsrfToken(post('_csrf_token'))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $title = post('title');
        $description = post('description');
        $buttonText = post('button_text');
        $buttonUrl = post('button_url');
        $sortOrder = post('sort_order', 999);
        $status = post('status', 'active');

        // Validate required fields
        if (empty($title)) {
            setFlash('error', 'Title is required');
            back();
            return;
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        // Handle image upload
        $imagePath = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['image']);

            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
                setFlash('error', 'Image upload failed: ' . $uploadResult['error']);
                back();
                return;
            }
        }

        $db = Database::getConnection();

        // Insert new slider
        $stmt = $db->prepare("
            INSERT INTO hero_sliders (title, description, button_text, button_url, image_path, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $result = $stmt->execute([
            $title,
            $description,
            $buttonText,
            $buttonUrl,
            $imagePath,
            $sortOrder,
            $status
        ]);

        if ($result) {
            setFlash('success', 'Slider created successfully');
        } else {
            setFlash('error', 'Failed to create slider');
        }

        redirect('/admin/sliders');
    }

    /**
     * Delete a slider
     */
    public function delete(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        $id = post('id');

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Invalid slider ID']);
            exit;
        }

        $db = Database::getConnection();

        // Get slider to delete image
        $stmt = $db->prepare("SELECT image_path FROM hero_sliders WHERE id = ?");
        $stmt->execute([$id]);
        $slider = $stmt->fetch();

        if (!$slider) {
            echo json_encode(['success' => false, 'error' => 'Slider not found']);
            exit;
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM hero_sliders WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            // Delete image file
            if (!empty($slider['image_path'])) {
                $this->deleteImage($slider['image_path']);
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete slider']);
        }

        exit;
    }

    /**
     * Update slider order
     */
    public function updateOrder(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        $order = post('order'); // Array of IDs in new order

        if (!is_array($order)) {
            echo json_encode(['success' => false, 'error' => 'Invalid order data']);
            exit;
        }

        $db = Database::getConnection();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE hero_sliders SET sort_order = ? WHERE id = ?");

            foreach ($order as $index => $id) {
                $stmt->execute([$index + 1, $id]);
            }

            $db->commit();

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Failed to update order']);
        }

        exit;
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(array $file): array
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed'];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size is 5MB'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'hero_' . time() . '_' . uniqid() . '.' . $extension;

        // Upload directory
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/images/hero/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'path' => 'images/hero/' . $filename
            ];
        }

        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }

    /**
     * Delete an image file
     */
    private function deleteImage(string $imagePath): void
    {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/public/' . $imagePath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
