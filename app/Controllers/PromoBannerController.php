<?php

namespace App\Controllers;

use Database;

class PromoBannerController
{
    /**
     * Display all promo banners
     */
    public function index(): void
    {
        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT *
            FROM promo_banners
            ORDER BY sort_order ASC, id ASC
        ");

        $banners = $stmt->fetchAll();

        view('admin/promo-banners/index', [
            'banners' => $banners,
            'pageTitle' => 'Promo Banners Management'
        ]);
    }

    /**
     * Show edit form for a promo banner
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            setFlash('error', 'Invalid promo banner ID');
            redirect('/admin/promo-banners');
            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM promo_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch();

        if (!$banner) {
            setFlash('error', 'Promo banner not found');
            redirect('/admin/promo-banners');
            return;
        }

        view('admin/promo-banners/edit', [
            'banner' => $banner,
            'pageTitle' => 'Edit Promo Banner'
        ]);
    }

    /**
     * Update a promo banner
     */
    public function update(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $id = post('id');
        $titleEn = post('title_en');
        $titleFr = post('title_fr');
        $subtitleEn = post('subtitle_en');
        $subtitleFr = post('subtitle_fr');
        $discountBadgeEn = post('discount_badge_en', '20% OFF');
        $discountBadgeFr = post('discount_badge_fr', '20% DE RABAIS');
        $buttonTextEn = post('button_text_en', 'Shop Now');
        $buttonTextFr = post('button_text_fr', 'Magasiner maintenant');
        $buttonUrl = post('button_url', '/deals');
        $sortOrder = post('sort_order', 0);
        $status = post('status', 'active');

        // Validate required fields
        if (empty($titleEn) || empty($titleFr)) {
            setFlash('error', 'Title is required in both languages');
            back();
            return;
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $db = Database::getConnection();

        // Check if banner exists
        $stmt = $db->prepare("SELECT id FROM promo_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch();

        if (!$banner) {
            setFlash('error', 'Promo banner not found');
            redirect('/admin/promo-banners');
            return;
        }

        // Update banner
        $stmt = $db->prepare("
            UPDATE promo_banners
            SET title_en = ?,
                title_fr = ?,
                subtitle_en = ?,
                subtitle_fr = ?,
                discount_badge_en = ?,
                discount_badge_fr = ?,
                button_text_en = ?,
                button_text_fr = ?,
                button_url = ?,
                sort_order = ?,
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        $result = $stmt->execute([
            $titleEn,
            $titleFr,
            $subtitleEn,
            $subtitleFr,
            $discountBadgeEn,
            $discountBadgeFr,
            $buttonTextEn,
            $buttonTextFr,
            $buttonUrl,
            $sortOrder,
            $status,
            $id
        ]);

        if ($result) {
            setFlash('success', 'Promo banner updated successfully');
        } else {
            setFlash('error', 'Failed to update promo banner');
        }

        redirect('/admin/promo-banners');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            view('admin/promo-banners/create', [
                'pageTitle' => 'Create New Promo Banner'
            ]);
            return;
        }

        // Handle POST request
        if (!verifyCsrfToken(post('_csrf_token'))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $titleEn = post('title_en');
        $titleFr = post('title_fr');
        $subtitleEn = post('subtitle_en');
        $subtitleFr = post('subtitle_fr');
        $discountBadgeEn = post('discount_badge_en', '20% OFF');
        $discountBadgeFr = post('discount_badge_fr', '20% DE RABAIS');
        $buttonTextEn = post('button_text_en', 'Shop Now');
        $buttonTextFr = post('button_text_fr', 'Magasiner maintenant');
        $buttonUrl = post('button_url', '/deals');
        $sortOrder = post('sort_order', 999);
        $status = post('status', 'active');

        // Validate required fields
        if (empty($titleEn) || empty($titleFr)) {
            setFlash('error', 'Title is required in both languages');
            back();
            return;
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $db = Database::getConnection();

        // Insert new banner
        $stmt = $db->prepare("
            INSERT INTO promo_banners (title_en, title_fr, subtitle_en, subtitle_fr, discount_badge_en, discount_badge_fr, button_text_en, button_text_fr, button_url, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $result = $stmt->execute([
            $titleEn,
            $titleFr,
            $subtitleEn,
            $subtitleFr,
            $discountBadgeEn,
            $discountBadgeFr,
            $buttonTextEn,
            $buttonTextFr,
            $buttonUrl,
            $sortOrder,
            $status
        ]);

        if ($result) {
            setFlash('success', 'Promo banner created successfully');
        } else {
            setFlash('error', 'Failed to create promo banner');
        }

        redirect('/admin/promo-banners');
    }

    /**
     * Delete a promo banner
     */
    public function delete(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        $id = post('id');

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Invalid promo banner ID']);
            exit;
        }

        $db = Database::getConnection();

        // Check if banner exists
        $stmt = $db->prepare("SELECT id FROM promo_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch();

        if (!$banner) {
            echo json_encode(['success' => false, 'error' => 'Promo banner not found']);
            exit;
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM promo_banners WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete promo banner']);
        }

        exit;
    }

    /**
     * Update promo banner order
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

            $stmt = $db->prepare("UPDATE promo_banners SET sort_order = ? WHERE id = ?");

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

}
