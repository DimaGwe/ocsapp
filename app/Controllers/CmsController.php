<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class CmsController {

    public function __construct() {
        AuthMiddleware::handle('admin');
    }

    /**
     * Display all CMS content grouped by page
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();

            $stmt = $db->query("SELECT * FROM cms_contents ORDER BY page, sort_order, id");
            $allContents = $stmt->fetchAll();

            // Group by page
            $contentsByPage = [];
            foreach ($allContents as $content) {
                $contentsByPage[$content['page']][] = $content;
            }

            view('admin/cms/index', [
                'contentsByPage' => $contentsByPage,
                'pageTitle' => 'Content Management',
                'currentPage' => 'cms',
            ]);

        } catch (\PDOException $e) {
            logger("CMS index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading content');
            redirect(url('admin/dashboard'));
        }
    }

    /**
     * Show edit form for specific content
     */
    public function edit(): void {
        $id = get('id');

        if (!$id) {
            setFlash('error', 'Content ID is required');
            redirect(url('admin/cms'));
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("SELECT * FROM cms_contents WHERE id = ?");
            $stmt->execute([$id]);
            $content = $stmt->fetch();

            if (!$content) {
                setFlash('error', 'Content not found');
                redirect(url('admin/cms'));
            }

            view('admin/cms/edit', [
                'content' => $content,
                'pageTitle' => 'Edit Content',
                'currentPage' => 'cms',
            ]);

        } catch (\PDOException $e) {
            logger("CMS edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading content');
            redirect(url('admin/cms'));
        }
    }

    /**
     * Update content
     */
    public function update(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        $id = post('id');
        $content = post('content');
        $status = post('status', 'active');

        if (!$id) {
            setFlash('error', 'Content ID is required');
            redirect(url('admin/cms'));
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                UPDATE cms_contents
                SET content = ?,
                    status = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            $stmt->execute([$content, $status, $id]);

            setFlash('success', 'Content updated successfully');
            redirect(url('admin/cms'));

        } catch (\PDOException $e) {
            logger("CMS update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating content: ' . $e->getMessage());
            back();
        }
    }

    /**
     * Quick update via AJAX for inline editing
     */
    public function quickUpdate(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $id = post('id');
        $content = post('content');

        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'ID required'], 400);
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                UPDATE cms_contents
                SET content = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            $stmt->execute([$content, $id]);

            jsonResponse([
                'success' => true,
                'message' => 'Content updated'
            ]);

        } catch (\PDOException $e) {
            logger("CMS quick update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create new content section
     */
    public function create(): void {
        if (isPost()) {
            if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
                setFlash('error', 'Invalid request. Please try again.');
                back();
            }

            $page = post('page');
            $section = post('section');
            $label = post('label');
            $content = post('content');
            $content_type = post('content_type', 'text');
            $description = post('description');
            $status = post('status', 'active');

            if (!$page || !$section || !$label) {
                setFlash('error', 'Page, section, and label are required');
                back();
            }

            try {
                $db = \Database::getConnection();

                $stmt = $db->prepare("
                    INSERT INTO cms_contents (page, section, label, content, content_type, description, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([$page, $section, $label, $content, $content_type, $description, $status]);

                setFlash('success', 'Content created successfully');
                redirect(url('admin/cms'));

            } catch (\PDOException $e) {
                logger("CMS create error: " . $e->getMessage(), 'error');
                setFlash('error', 'Error creating content: ' . $e->getMessage());
                back();
            }
        } else {
            view('admin/cms/create', [
                'pageTitle' => 'Create Content',
                'currentPage' => 'cms',
            ]);
        }
    }

    /**
     * Delete content
     */
    public function delete(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        $id = post('id');

        if (!$id) {
            setFlash('error', 'Content ID is required');
            redirect(url('admin/cms'));
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("DELETE FROM cms_contents WHERE id = ?");
            $stmt->execute([$id]);

            setFlash('success', 'Content deleted successfully');
            redirect(url('admin/cms'));

        } catch (\PDOException $e) {
            logger("CMS delete error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error deleting content');
            redirect(url('admin/cms'));
        }
    }
}
