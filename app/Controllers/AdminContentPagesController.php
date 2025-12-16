<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use Exception;
use PDOException;

/**
 * Admin Content Pages Controller
 * Manages About Us, Contact Us, and other CMS pages
 */

class AdminContentPagesController {
    private $db;

    public function __construct() {
        $this->db = \Database::getConnection();
    }

    /**
     * List all content pages
     */
    public function index() {
        AuthMiddleware::handle('admin');

        try {
            $stmt = $this->db->query("
                SELECT * FROM content_pages
                ORDER BY page_type, language
            ");
            $pages = $stmt->fetchAll();

            require __DIR__ . '/../Views/admin/content-pages/index.php';
        } catch (Exception $e) {
            error_log("Error fetching content pages: " . $e->getMessage());
            $_SESSION['error'] = "Failed to load content pages";
            redirect('/admin/cms');
        }
    }

    /**
     * Show edit form for a content page
     */
    public function edit() {
        AuthMiddleware::handle('admin');

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = "Page ID is required";
            redirect('/admin/content-pages');
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM content_pages WHERE id = ?");
            $stmt->execute([$id]);
            $page = $stmt->fetch();

            if (!$page) {
                $_SESSION['error'] = "Page not found";
                redirect('/admin/content-pages');
                return;
            }

            require __DIR__ . '/../Views/admin/content-pages/edit.php';
        } catch (Exception $e) {
            error_log("Error fetching content page: " . $e->getMessage());
            $_SESSION['error'] = "Failed to load page";
            redirect('/admin/content-pages');
        }
    }

    /**
     * Update a content page
     */
    public function update() {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            return;
        }

        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if (!$id || !$title || !$content) {
            jsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE content_pages
                SET title = ?, content = ?, meta_description = ?,
                    is_published = ?, updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([$title, $content, $metaDescription, $isPublished, $id]);

            jsonResponse([
                'success' => true,
                'message' => 'Page updated successfully'
            ]);
        } catch (Exception $e) {
            error_log("Error updating content page: " . $e->getMessage());
            jsonResponse([
                'success' => false,
                'message' => 'Failed to update page'
            ], 500);
        }
    }

    /**
     * Create a new content page
     */
    public function create() {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        require __DIR__ . '/../Views/admin/content-pages/create.php';
    }

    /**
     * Store a new content page
     */
    private function store() {
        $pageType = $_POST['page_type'] ?? '';
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $content = $_POST['content'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $language = $_POST['language'] ?? 'fr';
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if (!$pageType || !$title || !$slug || !$content) {
            $_SESSION['error'] = "All fields are required";
            redirect('/admin/content-pages/create');
            return;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO content_pages
                (page_type, title, slug, content, meta_description, language, is_published)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([$pageType, $title, $slug, $content, $metaDescription, $language, $isPublished]);

            $_SESSION['success'] = "Page created successfully";
            redirect('/admin/content-pages');
        } catch (Exception $e) {
            error_log("Error creating content page: " . $e->getMessage());
            $_SESSION['error'] = "Failed to create page";
            redirect('/admin/content-pages/create');
        }
    }

    /**
     * Delete a content page
     */
    public function delete() {
        AuthMiddleware::handle('admin');

        $id = $_POST['id'] ?? null;
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Page ID is required'], 400);
            return;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM content_pages WHERE id = ?");
            $stmt->execute([$id]);

            jsonResponse([
                'success' => true,
                'message' => 'Page deleted successfully'
            ]);
        } catch (Exception $e) {
            error_log("Error deleting content page: " . $e->getMessage());
            jsonResponse([
                'success' => false,
                'message' => 'Failed to delete page'
            ], 500);
        }
    }
}
