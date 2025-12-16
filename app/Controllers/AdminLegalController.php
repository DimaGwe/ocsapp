<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * AdminLegalController - Manage Legal Content (Terms, Privacy, etc.)
 * CMS for editable legal pages with version control
 */
class AdminLegalController
{
    private $db;

    public function __construct()
    {
        AuthMiddleware::handle('admin');
        $this->db = \Database::getConnection();
    }

    /**
     * List all legal pages
     */
    public function index(): void
    {
        try {
            // Get all legal content pages with latest version info
            $stmt = $this->db->query("
                SELECT
                    lc.*,
                    u1.first_name as creator_name,
                    u2.first_name as updater_name,
                    (SELECT COUNT(*) FROM legal_content_revisions WHERE legal_content_id = lc.id) as revision_count
                FROM legal_content lc
                LEFT JOIN users u1 ON lc.created_by = u1.id
                LEFT JOIN users u2 ON lc.updated_by = u2.id
                WHERE lc.is_published = 1
                ORDER BY lc.page_type, lc.language
            ");
            $pages = $stmt->fetchAll();

            // Group by page type
            $groupedPages = [];
            foreach ($pages as $page) {
                $groupedPages[$page['page_type']][] = $page;
            }

            view('admin.legal.index', [
                'pages' => $pages,
                'groupedPages' => $groupedPages,
                'currentPage' => 'legal'
            ]);

        } catch (\PDOException $e) {
            logger("Legal content index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading legal content');
            back();
        }
    }

    /**
     * Edit legal content page
     */
    public function edit(): void
    {
        try {
            $id = (int) get('id');

            if (!$id) {
                setFlash('error', 'Invalid page ID');
                redirect(url('admin/legal'));
            }

            // Get page content
            $stmt = $this->db->prepare("
                SELECT * FROM legal_content WHERE id = ? LIMIT 1
            ");
            $stmt->execute([$id]);
            $page = $stmt->fetch();

            if (!$page) {
                setFlash('error', 'Page not found');
                redirect(url('admin/legal'));
            }

            // Get revision history
            $stmt = $this->db->prepare("
                SELECT
                    lcr.*,
                    u.first_name as creator_name,
                    u.last_name as creator_lastname
                FROM legal_content_revisions lcr
                LEFT JOIN users u ON lcr.created_by = u.id
                WHERE lcr.legal_content_id = ?
                ORDER BY lcr.version DESC
                LIMIT 10
            ");
            $stmt->execute([$id]);
            $revisions = $stmt->fetchAll();

            view('admin.legal.edit', [
                'page' => $page,
                'revisions' => $revisions,
                'currentPage' => 'legal'
            ]);

        } catch (\PDOException $e) {
            logger("Legal content edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading page');
            redirect(url('admin/legal'));
        }
    }

    /**
     * Update legal content
     */
    public function update(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $id = (int) post('id');
            $title = sanitize(post('title', ''));
            $content = post('content', ''); // Don't sanitize HTML content from TinyMCE
            $metaDescription = sanitize(post('meta_description', ''));
            $notes = sanitize(post('notes', ''));

            if (!$id || !$title || !$content) {
                setFlash('error', 'Title and content are required');
                back();
            }

            // Get current page data
            $stmt = $this->db->prepare("SELECT * FROM legal_content WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $currentPage = $stmt->fetch();

            if (!$currentPage) {
                setFlash('error', 'Page not found');
                redirect(url('admin/legal'));
            }

            // Create revision of current version before updating
            $stmt = $this->db->prepare("
                INSERT INTO legal_content_revisions
                (legal_content_id, title, content, version, created_by, notes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $id,
                $currentPage['title'],
                $currentPage['content'],
                $currentPage['version'],
                userId(),
                'Auto-saved before update'
            ]);

            // Update content and increment version
            $newVersion = $currentPage['version'] + 1;
            $stmt = $this->db->prepare("
                UPDATE legal_content
                SET title = ?,
                    content = ?,
                    meta_description = ?,
                    version = ?,
                    updated_by = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $title,
                $content,
                $metaDescription,
                $newVersion,
                userId(),
                $id
            ]);

            // Create revision of new version
            if ($notes) {
                $stmt = $this->db->prepare("
                    INSERT INTO legal_content_revisions
                    (legal_content_id, title, content, version, created_by, notes)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $id,
                    $title,
                    $content,
                    $newVersion,
                    userId(),
                    $notes
                ]);
            }

            logger("Legal content updated: {$currentPage['page_type']} (v{$newVersion})", 'info');
            setFlash('success', "Page updated successfully! Now at version {$newVersion}");
            redirect(url('admin/legal'));

        } catch (\PDOException $e) {
            logger("Legal content update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating page');
            back();
        }
    }

    /**
     * Preview legal content
     */
    public function preview(): void
    {
        try {
            $id = (int) get('id');

            if (!$id) {
                setFlash('error', 'Invalid page ID');
                redirect(url('admin/legal'));
            }

            $stmt = $this->db->prepare("SELECT * FROM legal_content WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $page = $stmt->fetch();

            if (!$page) {
                setFlash('error', 'Page not found');
                redirect(url('admin/legal'));
            }

            view('admin.legal.preview', [
                'page' => $page
            ]);

        } catch (\PDOException $e) {
            logger("Legal content preview error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading preview');
            redirect(url('admin/legal'));
        }
    }

    /**
     * Restore from revision
     */
    public function restore(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $revisionId = (int) post('revision_id');

            if (!$revisionId) {
                setFlash('error', 'Invalid revision ID');
                back();
            }

            // Get revision data
            $stmt = $this->db->prepare("
                SELECT lcr.*, lc.id as page_id, lc.version as current_version
                FROM legal_content_revisions lcr
                INNER JOIN legal_content lc ON lcr.legal_content_id = lc.id
                WHERE lcr.id = ?
                LIMIT 1
            ");
            $stmt->execute([$revisionId]);
            $revision = $stmt->fetch();

            if (!$revision) {
                setFlash('error', 'Revision not found');
                back();
            }

            // Create backup of current version
            $stmt = $this->db->prepare("
                INSERT INTO legal_content_revisions
                (legal_content_id, title, content, version, created_by, notes)
                SELECT id, title, content, version, ?, 'Backup before restore'
                FROM legal_content WHERE id = ?
            ");
            $stmt->execute([userId(), $revision['page_id']]);

            // Restore from revision
            $newVersion = $revision['current_version'] + 1;
            $stmt = $this->db->prepare("
                UPDATE legal_content
                SET title = ?,
                    content = ?,
                    version = ?,
                    updated_by = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $revision['title'],
                $revision['content'],
                $newVersion,
                userId(),
                $revision['page_id']
            ]);

            logger("Legal content restored from v{$revision['version']} to v{$newVersion}", 'info');
            setFlash('success', "Page restored to version {$revision['version']} (now v{$newVersion})");
            redirect(url('admin/legal/edit?id=' . $revision['page_id']));

        } catch (\PDOException $e) {
            logger("Legal content restore error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error restoring page');
            back();
        }
    }
}
