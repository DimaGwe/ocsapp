<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * AdminTranslationsController
 * Manage multilingual translations from admin panel
 */
class AdminTranslationsController
{
    private $db;

    public function __construct()
    {
        AuthMiddleware::handle('admin');
        $this->db = \Database::getConnection();
    }

    /**
     * List all translations grouped by category
     */
    public function index(): void
    {
        try {
            $category = get('category', '');
            $search = get('search', '');

            // Get all categories for filter
            $stmt = $this->db->query("SELECT DISTINCT category FROM translations ORDER BY category");
            $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Build query
            $sql = "SELECT * FROM translations WHERE 1=1";
            $params = [];

            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }

            if ($search) {
                $sql .= " AND (`key` LIKE ? OR en LIKE ? OR fr LIKE ? OR description LIKE ?)";
                $searchTerm = "%{$search}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            $sql .= " ORDER BY category, `key`";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $translations = $stmt->fetchAll();

            // Group by category for display
            $translationsByCategory = [];
            foreach ($translations as $t) {
                $translationsByCategory[$t['category']][] = $t;
            }

            // Get counts
            $stmt = $this->db->query("SELECT COUNT(*) FROM translations");
            $totalCount = $stmt->fetchColumn();

            view('admin/translations/index', [
                'translationsByCategory' => $translationsByCategory,
                'categories' => $categories,
                'currentCategory' => $category,
                'search' => $search,
                'totalCount' => $totalCount,
                'pageTitle' => 'Translations',
                'currentPage' => 'translations',
            ]);

        } catch (\PDOException $e) {
            logger("Translations index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading translations');
            redirect(url('admin/dashboard'));
        }
    }

    /**
     * Edit a single translation
     */
    public function edit(): void
    {
        $id = get('id');

        if (!$id) {
            setFlash('error', 'Translation ID required');
            redirect(url('admin/translations'));
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM translations WHERE id = ?");
            $stmt->execute([$id]);
            $translation = $stmt->fetch();

            if (!$translation) {
                setFlash('error', 'Translation not found');
                redirect(url('admin/translations'));
            }

            view('admin/translations/edit', [
                'translation' => $translation,
                'pageTitle' => 'Edit Translation',
                'currentPage' => 'translations',
            ]);

        } catch (\PDOException $e) {
            logger("Translation edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading translation');
            redirect(url('admin/translations'));
        }
    }

    /**
     * Update translation
     */
    public function update(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        $id = post('id');
        $en = post('en', '');
        $fr = post('fr', '');
        $description = post('description', '');
        $is_html = post('is_html') ? 1 : 0;

        if (!$id) {
            setFlash('error', 'Translation ID required');
            redirect(url('admin/translations'));
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE translations
                SET en = ?, fr = ?, description = ?, is_html = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$en, $fr, $description, $is_html, $id]);

            // Clear translation cache if implemented
            $this->clearTranslationCache();

            setFlash('success', 'Translation updated successfully');
            redirect(url('admin/translations'));

        } catch (\PDOException $e) {
            logger("Translation update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating translation');
            back();
        }
    }

    /**
     * Bulk update translations (for inline editing)
     */
    public function bulkUpdate(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
        }

        $translations = post('translations', []);

        if (empty($translations)) {
            jsonResponse(['success' => false, 'message' => 'No translations provided'], 400);
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE translations SET en = ?, fr = ?, updated_at = NOW() WHERE id = ?
            ");

            foreach ($translations as $id => $values) {
                $stmt->execute([
                    $values['en'] ?? '',
                    $values['fr'] ?? '',
                    $id
                ]);
            }

            $this->db->commit();
            $this->clearTranslationCache();

            jsonResponse(['success' => true, 'message' => 'Translations updated']);

        } catch (\PDOException $e) {
            $this->db->rollBack();
            logger("Bulk translation update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating translations'], 500);
        }
    }

    /**
     * Create new translation
     */
    public function create(): void
    {
        if (isPost()) {
            if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
                setFlash('error', 'Invalid request');
                back();
            }

            $key = trim(post('key', ''));
            $category = trim(post('category', 'general'));
            $en = post('en', '');
            $fr = post('fr', '');
            $description = post('description', '');
            $is_html = post('is_html') ? 1 : 0;

            if (empty($key)) {
                setFlash('error', 'Translation key is required');
                back();
            }

            // Validate key format (lowercase, underscores only)
            if (!preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
                setFlash('error', 'Key must be lowercase letters, numbers, and underscores only');
                back();
            }

            try {
                // Check if key exists
                $stmt = $this->db->prepare("SELECT id FROM translations WHERE `key` = ?");
                $stmt->execute([$key]);
                if ($stmt->fetch()) {
                    setFlash('error', 'Translation key already exists');
                    back();
                }

                $stmt = $this->db->prepare("
                    INSERT INTO translations (`key`, category, en, fr, description, is_html)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$key, $category, $en, $fr, $description, $is_html]);

                $this->clearTranslationCache();

                setFlash('success', 'Translation created successfully');
                redirect(url('admin/translations'));

            } catch (\PDOException $e) {
                logger("Translation create error: " . $e->getMessage(), 'error');
                setFlash('error', 'Error creating translation');
                back();
            }
        } else {
            // Get categories for dropdown
            $stmt = $this->db->query("SELECT DISTINCT category FROM translations ORDER BY category");
            $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            view('admin/translations/create', [
                'categories' => $categories,
                'pageTitle' => 'Add Translation',
                'currentPage' => 'translations',
            ]);
        }
    }

    /**
     * Delete translation
     */
    public function delete(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        $id = post('id');

        if (!$id) {
            setFlash('error', 'Translation ID required');
            redirect(url('admin/translations'));
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM translations WHERE id = ?");
            $stmt->execute([$id]);

            $this->clearTranslationCache();

            setFlash('success', 'Translation deleted');
            redirect(url('admin/translations'));

        } catch (\PDOException $e) {
            logger("Translation delete error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error deleting translation');
            redirect(url('admin/translations'));
        }
    }

    /**
     * Export translations as JSON
     */
    public function export(): void
    {
        try {
            $stmt = $this->db->query("SELECT `key`, category, en, fr FROM translations ORDER BY category, `key`");
            $translations = $stmt->fetchAll();

            $export = [
                'exported_at' => date('Y-m-d H:i:s'),
                'total' => count($translations),
                'translations' => $translations
            ];

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="translations_' . date('Y-m-d') . '.json"');
            echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        } catch (\PDOException $e) {
            logger("Translation export error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error exporting translations');
            redirect(url('admin/translations'));
        }
    }

    /**
     * Clear translation cache (if caching is implemented)
     */
    private function clearTranslationCache(): void
    {
        // Clear any cached translations
        if (isset($_SESSION['translations_cache'])) {
            unset($_SESSION['translations_cache']);
        }

        // Could also clear file cache if implemented
        $cacheFile = dirname(__DIR__, 2) . '/storage/cache/translations.php';
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }
    }
}
