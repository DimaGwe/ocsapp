<?php

namespace App\Controllers;

/**
 * AdminTrainingController — Driver Training Module Management
 */
class AdminTrainingController
{
    private $db;

    public function __construct()
    {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect(url('login'));
            exit;
        }
        $this->db = \Database::getConnection();
    }

    /**
     * Training overview: Tab 1 = Modules, Tab 2 = Driver Progress
     */
    public function index(): void
    {
        $tab = sanitize($_GET['tab'] ?? 'modules');

        // Module list with question count and driver pass count
        $modules = $this->db->query("
            SELECT m.*,
                (SELECT COUNT(*) FROM training_questions q WHERE q.module_id = m.id) AS question_count,
                (SELECT COUNT(DISTINCT p.driver_id) FROM driver_training_progress p WHERE p.module_id = m.id AND p.status = 'passed') AS drivers_passed
            FROM training_modules m
            ORDER BY m.order_num ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        // Driver progress
        $totalModules = count($modules);
        $drivers = $this->db->query("
            SELECT u.id, u.first_name, u.last_name, u.email, u.status,
                (SELECT COUNT(*) FROM driver_training_progress p WHERE p.driver_id = u.id AND p.status = 'passed') AS modules_passed,
                (SELECT issued_at FROM driver_certificates c WHERE c.driver_id = u.id LIMIT 1) AS certified_at,
                (SELECT cert_number FROM driver_certificates c WHERE c.driver_id = u.id LIMIT 1) AS cert_number,
                (SELECT MAX(updated_at) FROM driver_training_progress p WHERE p.driver_id = u.id) AS last_activity
            FROM users u
            WHERE u.role = 'delivery'
            ORDER BY u.first_name, u.last_name
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $pageTitle = 'Driver Training';
        $currentPage = 'training';
        view('admin.training.index', compact('modules', 'drivers', 'totalModules', 'pageTitle', 'currentPage'));
    }

    /**
     * Edit a training module's content and questions
     */
    public function editModule(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            setFlash('error', 'Module not found.');
            redirect(url('admin/training'));
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM training_modules WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $module = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$module) {
            setFlash('error', 'Module not found.');
            redirect(url('admin/training'));
            return;
        }

        $questions = $this->db->prepare("SELECT * FROM training_questions WHERE module_id = ? ORDER BY order_num, id ASC");
        $questions->execute([$id]);
        $questions = $questions->fetchAll(\PDO::FETCH_ASSOC);

        $pageTitle = 'Edit Module: ' . htmlspecialchars($module['title']);
        $currentPage = 'training';
        view('admin.training.edit-module', compact('module', 'questions', 'pageTitle', 'currentPage'));
    }

    /**
     * Save module content (title, description, content_html, pass_score, max_attempts)
     */
    public function saveModule(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $id          = (int) post('module_id', 0);
        $title       = sanitize(post('title', ''));
        $description = sanitize(post('description', ''));
        $contentHtml = post('content_html', '');
        $passScore   = max(1, min(100, (int) post('pass_score', 80)));
        $maxAttempts = max(1, (int) post('max_attempts', 3));

        if (!$id || !$title) {
            setFlash('error', 'Title is required.');
            back();
            return;
        }

        $this->db->prepare("
            UPDATE training_modules
            SET title = ?, description = ?, content_html = ?, pass_score = ?, max_attempts = ?, updated_at = NOW()
            WHERE id = ?
        ")->execute([$title, $description, $contentHtml, $passScore, $maxAttempts, $id]);

        setFlash('success', 'Module saved successfully.');
        redirect(url('admin/training/module/edit?id=' . $id));
    }

    /**
     * Add or update a quiz question
     */
    public function saveQuestion(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['error' => 'Invalid token'], 403);
            return;
        }

        $questionId    = (int) post('question_id', 0);
        $moduleId      = (int) post('module_id', 0);
        $questionText  = sanitize(post('question_text', ''));
        $optionA       = sanitize(post('option_a', ''));
        $optionB       = sanitize(post('option_b', ''));
        $optionC       = sanitize(post('option_c', ''));
        $optionD       = sanitize(post('option_d', ''));
        $correctOption = post('correct_option', 'a');
        $explanation   = sanitize(post('explanation', ''));
        $orderNum      = (int) post('order_num', 1);

        if (!$moduleId || !$questionText || !$optionA || !$optionB) {
            setFlash('error', 'Question text and at least 2 options are required.');
            back();
            return;
        }

        if (!in_array($correctOption, ['a', 'b', 'c', 'd'])) {
            $correctOption = 'a';
        }

        if ($questionId > 0) {
            $this->db->prepare("
                UPDATE training_questions
                SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?,
                    correct_option = ?, explanation = ?, order_num = ?
                WHERE id = ? AND module_id = ?
            ")->execute([$questionText, $optionA, $optionB, $optionC ?: null, $optionD ?: null,
                         $correctOption, $explanation ?: null, $orderNum, $questionId, $moduleId]);
            setFlash('success', 'Question updated.');
        } else {
            $this->db->prepare("
                INSERT INTO training_questions (module_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, order_num)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([$moduleId, $questionText, $optionA, $optionB, $optionC ?: null, $optionD ?: null,
                         $correctOption, $explanation ?: null, $orderNum]);
            setFlash('success', 'Question added.');
        }

        redirect(url('admin/training/module/edit?id=' . $moduleId));
    }

    /**
     * Delete a quiz question
     */
    public function deleteQuestion(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['error' => 'Invalid token'], 403);
            return;
        }

        $questionId = (int) post('question_id', 0);
        $moduleId   = (int) post('module_id', 0);

        if ($questionId && $moduleId) {
            $this->db->prepare("DELETE FROM training_questions WHERE id = ? AND module_id = ?")->execute([$questionId, $moduleId]);
            setFlash('success', 'Question deleted.');
        }

        redirect(url('admin/training/module/edit?id=' . $moduleId));
    }

    /**
     * Reset a driver's progress on a specific module
     */
    public function resetDriverModule(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid token.');
            back();
            return;
        }

        $driverId = (int) post('driver_id', 0);
        $moduleId = (int) post('module_id', 0);

        if (!$driverId || !$moduleId) {
            setFlash('error', 'Invalid parameters.');
            back();
            return;
        }

        $this->db->prepare("
            UPDATE driver_training_progress
            SET status = 'available', attempts = 0, best_score = 0, completed_at = NULL, updated_at = NOW()
            WHERE driver_id = ? AND module_id = ?
        ")->execute([$driverId, $moduleId]);

        setFlash('success', 'Module reset for driver. They can retry now.');
        $redirectTo = post('redirect', '');
        if ($redirectTo && (strpos($redirectTo, '/') !== 0 || strpos($redirectTo, '//') === 0)) {
            $redirectTo = '';
        }
        redirect($redirectTo ?: url('admin/training?tab=drivers'));
    }

    /**
     * Manually certify a driver (bypass training requirement)
     */
    public function manualCertify(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid token.');
            back();
            return;
        }

        $driverId = (int) post('driver_id', 0);
        if (!$driverId) {
            setFlash('error', 'Invalid driver.');
            back();
            return;
        }

        // Generate cert number
        $certNum = 'OCS-DRV-' . str_pad($driverId, 5, '0', STR_PAD_LEFT);

        $this->db->prepare("
            INSERT IGNORE INTO driver_certificates (driver_id, cert_number, issued_at)
            VALUES (?, ?, NOW())
        ")->execute([$driverId, $certNum]);

        // Mark all active modules as passed for this driver
        $modules = $this->db->query("SELECT id FROM training_modules WHERE is_active = 1")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($modules as $moduleId) {
            $this->db->prepare("
                INSERT INTO driver_training_progress (driver_id, module_id, status, attempts, best_score, unlocked_at, completed_at)
                VALUES (?, ?, 'passed', 1, 100, NOW(), NOW())
                ON DUPLICATE KEY UPDATE status = 'passed', best_score = GREATEST(best_score, 100), completed_at = IFNULL(completed_at, NOW())
            ")->execute([$driverId, $moduleId]);
        }

        setFlash('success', 'Driver manually certified. They can now accept deliveries.');
        $redirectTo = post('redirect', '');
        if ($redirectTo && (strpos($redirectTo, '/') !== 0 || strpos($redirectTo, '//') === 0)) {
            $redirectTo = '';
        }
        redirect($redirectTo ?: url('admin/training?tab=drivers'));
    }
}
