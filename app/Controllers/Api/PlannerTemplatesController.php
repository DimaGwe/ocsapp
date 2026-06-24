<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Planner Templates API Controller
 * Handles CRUD operations for editable templates with revision history
 */
class PlannerTemplatesController
{
    private $db;

    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        // Check authentication - must be logged in as any admin tier
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized. Please log in as admin.']);
            exit;
        }

        // Verify CSRF token for state-changing requests
        verifyCsrfForApi();

        $this->db = \Database::getConnection();
    }

    /**
     * Get all templates with creator info
     */
    public function index(): void
    {
        try {
            // Check if table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'planner_templates'");
            if ($stmt->rowCount() === 0) {
                http_response_code(500);
                echo json_encode([
                    'error' => 'Database table not found. Please run: php database/migrations/setup_planner_templates.php'
                ]);
                return;
            }

            $category = $_GET['category'] ?? null;
            $search = $_GET['search'] ?? null;

            $sql = "
                SELECT
                    t.*,
                    CONCAT(uc.first_name, ' ', uc.last_name) as created_by_name,
                    CONCAT(uu.first_name, ' ', uu.last_name) as updated_by_name,
                    (SELECT COUNT(*) FROM planner_template_revisions WHERE template_id = t.id) as revision_count
                FROM planner_templates t
                LEFT JOIN users uc ON t.created_by = uc.id
                LEFT JOIN users uu ON t.updated_by = uu.id
                WHERE t.is_active = 1
            ";

            $params = [];

            if ($category && $category !== 'all') {
                $sql .= " AND t.category = ?";
                $params[] = $category;
            }

            if ($search) {
                $sql .= " AND (t.name LIKE ? OR t.slug LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            $sql .= " ORDER BY t.updated_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $templates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($templates);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch templates: ' . $e->getMessage()]);
        }
    }

    /**
     * Get a single template by ID
     */
    public function show(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Template ID is required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT
                    t.*,
                    CONCAT(uc.first_name, ' ', uc.last_name) as created_by_name,
                    CONCAT(uu.first_name, ' ', uu.last_name) as updated_by_name
                FROM planner_templates t
                LEFT JOIN users uc ON t.created_by = uc.id
                LEFT JOIN users uu ON t.updated_by = uu.id
                WHERE t.id = ?
            ");
            $stmt->execute([$id]);
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$template) {
                http_response_code(404);
                echo json_encode(['error' => 'Template not found']);
                return;
            }

            echo json_encode($template);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch template']);
        }
    }

    /**
     * Create a new template
     */
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['name']) || empty($input['content']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Name, content, and user_id are required']);
                return;
            }

            // Generate slug from name
            $slug = $this->generateSlug($input['name']);

            // Check if slug already exists
            $stmt = $this->db->prepare("SELECT id FROM planner_templates WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                // Make slug unique by appending timestamp
                $slug .= '-' . time();
            }

            $category = $input['category'] ?? 'general';

            // Start transaction
            $this->db->beginTransaction();

            // Insert template
            $stmt = $this->db->prepare("
                INSERT INTO planner_templates (name, slug, category, content, created_by, updated_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['name'],
                $slug,
                $category,
                $input['content'],
                $input['user_id'],
                $input['user_id']
            ]);

            $templateId = $this->db->lastInsertId();

            // Create initial revision
            $stmt = $this->db->prepare("
                INSERT INTO planner_template_revisions (template_id, revision_number, content, change_summary, changed_by)
                VALUES (?, 1, ?, 'Initial version', ?)
            ");
            $stmt->execute([
                $templateId,
                $input['content'],
                $input['user_id']
            ]);

            $this->db->commit();

            // Log activity
            $this->logActivity($input['user_id'], 'template', 'created template: ' . $input['name']);

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $templateId, 'slug' => $slug]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create template: ' . $e->getMessage()]);
        }
    }

    /**
     * Update a template (creates new revision)
     */
    public function update(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Template ID and user_id are required']);
                return;
            }

            // Get current template
            $stmt = $this->db->prepare("SELECT * FROM planner_templates WHERE id = ?");
            $stmt->execute([$input['id']]);
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$template) {
                http_response_code(404);
                echo json_encode(['error' => 'Template not found']);
                return;
            }

            // Start transaction
            $this->db->beginTransaction();

            // Build update fields
            $updates = [];
            $params = [];

            if (isset($input['name'])) {
                $updates[] = 'name = ?';
                $params[] = $input['name'];

                // Update slug if name changed
                if ($input['name'] !== $template['name']) {
                    $newSlug = $this->generateSlug($input['name']);
                    $stmt = $this->db->prepare("SELECT id FROM planner_templates WHERE slug = ? AND id != ?");
                    $stmt->execute([$newSlug, $input['id']]);
                    if ($stmt->fetch()) {
                        $newSlug .= '-' . time();
                    }
                    $updates[] = 'slug = ?';
                    $params[] = $newSlug;
                }
            }

            if (isset($input['category'])) {
                $updates[] = 'category = ?';
                $params[] = $input['category'];
            }

            if (isset($input['content'])) {
                $updates[] = 'content = ?';
                $params[] = $input['content'];

                // Create new revision only if content changed
                if ($input['content'] !== $template['content']) {
                    // Get next revision number
                    $stmt = $this->db->prepare("
                        SELECT COALESCE(MAX(revision_number), 0) + 1 as next_rev
                        FROM planner_template_revisions
                        WHERE template_id = ?
                    ");
                    $stmt->execute([$input['id']]);
                    $nextRev = $stmt->fetch(\PDO::FETCH_ASSOC)['next_rev'];

                    // Create revision
                    $changeSummary = $input['change_summary'] ?? 'Updated content';
                    $stmt = $this->db->prepare("
                        INSERT INTO planner_template_revisions (template_id, revision_number, content, change_summary, changed_by)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $input['id'],
                        $nextRev,
                        $input['content'],
                        $changeSummary,
                        $input['user_id']
                    ]);
                }
            }

            // Always update updated_by
            $updates[] = 'updated_by = ?';
            $params[] = $input['user_id'];

            // Add ID at the end for WHERE clause
            $params[] = $input['id'];

            // Execute update
            if (!empty($updates)) {
                $sql = "UPDATE planner_templates SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            $this->db->commit();

            // Log activity
            $this->logActivity($input['user_id'], 'template', 'updated template: ' . ($input['name'] ?? $template['name']));

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update template: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a template
     */
    public function destroy(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Template ID is required']);
                return;
            }

            // Get template name for activity log
            $stmt = $this->db->prepare("SELECT name FROM planner_templates WHERE id = ?");
            $stmt->execute([$input['id']]);
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$template) {
                http_response_code(404);
                echo json_encode(['error' => 'Template not found']);
                return;
            }

            // Delete template (revisions will cascade)
            $stmt = $this->db->prepare("DELETE FROM planner_templates WHERE id = ?");
            $stmt->execute([$input['id']]);

            // Log activity
            if (!empty($input['user_id'])) {
                $this->logActivity($input['user_id'], 'template', 'deleted template: ' . $template['name']);
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete template']);
        }
    }

    /**
     * Get revision history for a template
     */
    public function getRevisions(): void
    {
        try {
            $templateId = $_GET['template_id'] ?? null;

            if (!$templateId) {
                http_response_code(400);
                echo json_encode(['error' => 'Template ID is required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT
                    r.*,
                    CONCAT(u.first_name, ' ', u.last_name) as changed_by_name
                FROM planner_template_revisions r
                LEFT JOIN users u ON r.changed_by = u.id
                WHERE r.template_id = ?
                ORDER BY r.revision_number DESC
            ");
            $stmt->execute([$templateId]);
            $revisions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode($revisions);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch revisions']);
        }
    }

    /**
     * Get a specific revision
     */
    public function getRevision(): void
    {
        try {
            $revisionId = $_GET['revision_id'] ?? null;

            if (!$revisionId) {
                http_response_code(400);
                echo json_encode(['error' => 'Revision ID is required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT
                    r.*,
                    CONCAT(u.first_name, ' ', u.last_name) as changed_by_name,
                    t.name as template_name
                FROM planner_template_revisions r
                LEFT JOIN users u ON r.changed_by = u.id
                LEFT JOIN planner_templates t ON r.template_id = t.id
                WHERE r.id = ?
            ");
            $stmt->execute([$revisionId]);
            $revision = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$revision) {
                http_response_code(404);
                echo json_encode(['error' => 'Revision not found']);
                return;
            }

            echo json_encode($revision);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch revision']);
        }
    }

    /**
     * Restore a template to a previous revision
     */
    public function restoreRevision(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['template_id']) || empty($input['revision_id']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Template ID, revision ID, and user_id are required']);
                return;
            }

            // Get the revision content
            $stmt = $this->db->prepare("
                SELECT content, revision_number FROM planner_template_revisions
                WHERE id = ? AND template_id = ?
            ");
            $stmt->execute([$input['revision_id'], $input['template_id']]);
            $revision = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$revision) {
                http_response_code(404);
                echo json_encode(['error' => 'Revision not found']);
                return;
            }

            // Start transaction
            $this->db->beginTransaction();

            // Update template with revision content
            $stmt = $this->db->prepare("
                UPDATE planner_templates
                SET content = ?, updated_by = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $revision['content'],
                $input['user_id'],
                $input['template_id']
            ]);

            // Create new revision for the restore
            $stmt = $this->db->prepare("
                SELECT COALESCE(MAX(revision_number), 0) + 1 as next_rev
                FROM planner_template_revisions
                WHERE template_id = ?
            ");
            $stmt->execute([$input['template_id']]);
            $nextRev = $stmt->fetch(\PDO::FETCH_ASSOC)['next_rev'];

            $stmt = $this->db->prepare("
                INSERT INTO planner_template_revisions (template_id, revision_number, content, change_summary, changed_by)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['template_id'],
                $nextRev,
                $revision['content'],
                'Restored from version ' . $revision['revision_number'],
                $input['user_id']
            ]);

            $this->db->commit();

            // Log activity
            $this->logActivity($input['user_id'], 'template', 'restored template to version ' . $revision['revision_number']);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to restore revision']);
        }
    }

    /**
     * Get available categories from DB (falls back to hard-coded list if migration hasn't run)
     */
    public function getCategories(): void
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'planner_template_categories'");
            if ($stmt->rowCount() === 0) {
                echo json_encode([
                    ['id' => null, 'slug' => 'onboarding',       'name' => 'Onboarding',      'sort_order' => 1, 'template_count' => 0],
                    ['id' => null, 'slug' => 'job-descriptions',  'name' => 'Job Descriptions', 'sort_order' => 2, 'template_count' => 0],
                    ['id' => null, 'slug' => 'policies',          'name' => 'Policies',         'sort_order' => 3, 'template_count' => 0],
                    ['id' => null, 'slug' => 'legal',             'name' => 'Legal Documents',  'sort_order' => 4, 'template_count' => 0],
                    ['id' => null, 'slug' => 'email-templates',   'name' => 'Email Templates',  'sort_order' => 5, 'template_count' => 0],
                    ['id' => null, 'slug' => 'contracts',         'name' => 'Contracts',        'sort_order' => 6, 'template_count' => 0],
                    ['id' => null, 'slug' => 'general',           'name' => 'Other',            'sort_order' => 7, 'template_count' => 0],
                ]);
                return;
            }

            $stmt = $this->db->query("
                SELECT c.id, c.slug, c.name, c.sort_order,
                       COUNT(t.id) as template_count
                FROM planner_template_categories c
                LEFT JOIN planner_templates t ON t.category = c.slug AND t.is_active = 1
                GROUP BY c.id, c.slug, c.name, c.sort_order
                ORDER BY c.sort_order ASC, c.name ASC
            ");
            echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch categories']);
        }
    }

    /**
     * Create a new category
     */
    public function storeCategory(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Category name is required']);
                return;
            }

            $name = trim($input['name']);
            $slug = $this->generateSlug($name);

            if (empty($slug)) {
                http_response_code(400);
                echo json_encode(['error' => 'Category name produces an invalid slug']);
                return;
            }

            $stmt = $this->db->prepare("SELECT id FROM planner_template_categories WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(['error' => 'A category with that name already exists']);
                return;
            }

            $stmt = $this->db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 as next_order FROM planner_template_categories");
            $nextOrder = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['next_order'];

            $stmt = $this->db->prepare("INSERT INTO planner_template_categories (name, slug, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $nextOrder]);

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => (int) $this->db->lastInsertId(), 'slug' => $slug, 'name' => $name]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create category']);
        }
    }

    /**
     * Rename an existing category (slug stays the same to keep existing templates intact)
     */
    public function updateCategory(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id']) || empty($input['name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Category ID and name are required']);
                return;
            }

            $name = trim($input['name']);

            $stmt = $this->db->prepare("UPDATE planner_template_categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $input['id']]);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update category']);
        }
    }

    /**
     * Delete a category - reassigns its templates to 'general' first
     */
    public function deleteCategory(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Category ID is required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM planner_template_categories WHERE id = ?");
            $stmt->execute([$input['id']]);
            $category = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$category) {
                http_response_code(404);
                echo json_encode(['error' => 'Category not found']);
                return;
            }

            if ($category['slug'] === 'general') {
                http_response_code(400);
                echo json_encode(['error' => 'Cannot delete the default category']);
                return;
            }

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("UPDATE planner_templates SET category = 'general' WHERE category = ?");
            $stmt->execute([$category['slug']]);

            $stmt = $this->db->prepare("DELETE FROM planner_template_categories WHERE id = ?");
            $stmt->execute([$input['id']]);

            $this->db->commit();

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete category']);
        }
    }

    /**
     * Generate URL-friendly slug from name
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }

    /**
     * Generate PDF from HTML content
     */
    public function generatePdf(): void
    {
        try {
            // Don't set JSON header - we're returning PDF
            header_remove('Content-Type');

            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['html']) || empty($input['title'])) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'HTML content and title are required']);
                return;
            }

            $html = $input['html'];
            $title = $input['title'];
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title) . '.pdf';

            // Extract style tags from HTML content
            $documentStyles = '';
            if (preg_match_all('/<style[^>]*>([\s\S]*?)<\/style>/i', $html, $matches)) {
                foreach ($matches[1] as $style) {
                    $documentStyles .= $style . "\n";
                }
            }
            // Remove style tags from body content
            $bodyContent = preg_replace('/<style[^>]*>[\s\S]*?<\/style>/i', '', $html);

            // Replace emoji/unicode icons with text equivalents for Dompdf
            $bodyContent = $this->convertEmojisForPdf($bodyContent);

            // Convert CSS that Dompdf doesn't support well
            $documentStyles = $this->convertCssForPdf($documentStyles);

            // Create full HTML document for PDF
            // Document styles come AFTER base styles so they take priority
            $fullHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        /* Minimal base - only fallbacks, document styles override these */
        * { box-sizing: border-box; }
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 30px 40px;
        }
        strong, b { font-weight: bold; }
        em, i { font-style: italic; }
        a { color: #00b207; text-decoration: underline; }
        hr { border: none; border-top: 1px solid #ddd; margin: 15px 0; }
        img { max-width: 100%; }

        /* Meeting-specific base styles (low priority fallbacks) */
        .meeting-details, .detail-box {
            border: 2px solid #00b207;
            padding: 15px;
            margin: 15px 0;
            background-color: #f0fff0;
        }
        .detail-row { margin-bottom: 8px; }
        .detail-label { font-weight: bold; color: #555; display: inline-block; width: 100px; }
        .detail-value { color: #1a1a1a; }
        .agenda-list { list-style: none; padding: 0; }
        .agenda-item { padding: 10px 0; border-bottom: 1px solid #eee; }
        .agenda-number {
            display: inline-block;
            width: 24px;
            height: 24px;
            background-color: #00b207;
            color: white;
            text-align: center;
            margin-right: 10px;
            font-size: 10pt;
            line-height: 24px;
        }
        .status-complete { color: #059669; font-weight: bold; }
        .status-progress { color: #d97706; font-weight: bold; }
        .status-pending { color: #6b7280; font-weight: bold; }
        .action-items, .previous-meeting {
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #f59e0b;
            background-color: #fffbeb;
        }
        .cta-button {
            display: inline-block;
            background-color: #00b207;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            font-weight: bold;
        }
        .meeting-badge, .agenda-owner {
            display: inline-block;
            padding: 3px 10px;
            font-size: 9pt;
            background-color: #e0f2fe;
            color: #0369a1;
        }
    </style>
    <style>
        /* Document-specific styles (highest priority) */
        ' . $documentStyles . '
    </style>
    <style>
        /* Dompdf layout workarounds - override grid/flex with float/table */
        .tier-grid, .subscription-grid, .value-grid {
            width: 100%;
            overflow: hidden;
            margin: 20px 0;
        }
        .tier-grid > div, .subscription-grid > div, .value-grid > div {
            float: left;
            width: 48%;
            margin-right: 4%;
            margin-bottom: 16px;
        }
        .tier-grid > div:nth-child(2n), .subscription-grid > div:nth-child(2n), .value-grid > div:nth-child(2n) {
            margin-right: 0;
        }

        /* Flex space-between rows → table layout */
        .example-box .line, .tier-details li {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }
        .example-box .line > span, .tier-details li > span {
            display: table-cell;
        }
        .example-box .line > span:last-child, .tier-details li > span:last-child {
            text-align: right;
        }

        /* Tier card absolute positioned badge */
        .tier-badge {
            position: static;
            display: inline-block;
            margin-bottom: 8px;
        }

        /* Section card header with icon */
        .section-card-header {
            margin-bottom: 12px;
            overflow: hidden;
        }
        .section-icon {
            float: left;
            margin-right: 12px;
        }

        /* Page header */
        .page-header {
            text-align: center;
            padding: 30px 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
' . $bodyContent . '
</body>
</html>';

            // Initialize Dompdf
            require_once __DIR__ . '/../../../vendor/autoload.php';

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', false);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($fullHtml);

            // First render at letter size to measure actual content height
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();

            // Get page count - if more than 1 page, re-render as one long page
            $pageCount = $dompdf->getCanvas()->get_page_count();
            if ($pageCount > 1) {
                // Binary search for the tightest single-page height
                // Letter page = 792pt. Content is between 792 and pageCount*792
                $low = 792;
                $high = $pageCount * 792;

                while ($high - $low > 20) {
                    $mid = (int)(($high + $low) / 2);
                    $test = new \Dompdf\Dompdf($options);
                    $test->loadHtml($fullHtml);
                    $test->setPaper(array(0, 0, 612, $mid), 'portrait');
                    $test->render();

                    if ($test->getCanvas()->get_page_count() > 1) {
                        $low = $mid;
                    } else {
                        $high = $mid;
                    }
                }

                // Final render at the tight height + small breathing room
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($fullHtml);
                $dompdf->setPaper(array(0, 0, 612, $high + 30), 'portrait');
                $dompdf->render();
            }

            // Output PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');

            echo $dompdf->output();
            exit;

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Convert CSS properties that Dompdf doesn't support well
     */
    private function convertCssForPdf(string $css): string
    {
        // Convert linear-gradient to solid color (extract first hex color)
        $css = preg_replace_callback(
            '/background:\s*linear-gradient\([^)]*?(#[0-9a-fA-F]{3,8})[^)]*\)/i',
            function($matches) {
                return 'background-color: ' . $matches[1];
            },
            $css
        );
        $css = preg_replace_callback(
            '/background-image:\s*linear-gradient\([^)]*?(#[0-9a-fA-F]{3,8})[^)]*\)/i',
            function($matches) {
                return 'background-color: ' . $matches[1];
            },
            $css
        );

        // Remove box-shadow, transitions, animations (not supported by Dompdf)
        $css = preg_replace('/box-shadow:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/transition:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/animation:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/transform:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/cursor:\s*[^;]+;?/i', '', $css);

        // Convert rgba to rgb
        $css = preg_replace_callback(
            '/rgba\((\d+),\s*(\d+),\s*(\d+),\s*[\d.]+\)/',
            function($matches) {
                return "rgb({$matches[1]}, {$matches[2]}, {$matches[3]})";
            },
            $css
        );

        // Convert CSS Grid 2-column to float layout
        // display: grid with grid-template-columns: repeat(2, 1fr) → children float at 48%
        $css = preg_replace('/display:\s*grid\s*;/i', '', $css);
        $css = preg_replace('/grid-template-columns:\s*[^;]+;?/i', '', $css);

        // Convert flex with justify-content: space-between to table layout
        // This handles rows like ".line { display: flex; justify-content: space-between; }"
        $css = preg_replace('/display:\s*flex\s*;/i', '', $css);

        // Remove unsupported flex/grid properties but keep gap as margin
        $css = preg_replace('/flex-direction:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/flex-wrap:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/flex-shrink:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/flex:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/align-items:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/align-self:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/justify-content:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/gap:\s*[^;]+;?/i', '', $css);
        $css = preg_replace('/contain:\s*[^;]+;?/i', '', $css);

        return $css;
    }

    /**
     * Replace emoji and unicode icons with text symbols that DejaVu Sans can render
     */
    private function convertEmojisForPdf(string $html): string
    {
        // Common emoji → safe unicode/text replacements
        $emojiMap = [
            // HTML entities (&#x format)
            '&#x1F6D2;' => '&#x25CF;', // shopping cart → bullet
            '&#x1F69A;' => '&#x25CF;', // truck → bullet
            '&#x1F3E2;' => '&#x25CF;', // office → bullet
            '&#x1F475;' => '&#x25CF;', // elderly → bullet
            '&#x2713;'  => '&#x2713;', // checkmark (supported)
            '&#x2714;'  => '&#x2713;', // heavy checkmark → checkmark
        ];

        foreach ($emojiMap as $emoji => $replacement) {
            $html = str_ireplace($emoji, $replacement, $html);
        }

        // Strip any remaining emoji (Unicode ranges for emoji)
        // Supplementary Multilingual Plane emoji: U+1F000 - U+1FFFF
        $html = preg_replace('/[\x{1F000}-\x{1FFFF}]/u', '', $html);
        // Miscellaneous Symbols and Pictographs: U+2600 - U+27BF (keep basic symbols like checkmarks)
        $html = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $html);
        // Emoticons block
        $html = preg_replace('/[\x{FE00}-\x{FE0F}]/u', '', $html); // variation selectors

        return $html;
    }

    /**
     * Log activity
     */
    private function logActivity(int $userId, string $type, string $description): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO planner_activity (user_id, activity_type, description)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $type, $description]);
        } catch (\Exception $e) {
            // Silent fail - activity logging shouldn't break the main flow
        }
    }
}
