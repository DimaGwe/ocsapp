<?php

namespace App\Controllers;

class AdminEmailLogController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Email log dashboard — list all sent emails with filters
     */
    public function index(): void
    {
        $search = trim(get('search', ''));
        $statusFilter = get('status', '');
        $typeFilter = get('type', '');
        $dateFrom = get('date_from', '');
        $dateTo = get('date_to', '');
        $page = max(1, (int)get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        // Build query
        $where = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(el.recipient_email LIKE ? OR el.subject LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($statusFilter) {
            $where[] = 'el.status = ?';
            $params[] = $statusFilter;
        }

        if ($typeFilter) {
            $where[] = 'el.email_type = ?';
            $params[] = $typeFilter;
        }

        if ($dateFrom) {
            $where[] = 'el.created_at >= ?';
            $params[] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo) {
            $where[] = 'el.created_at <= ?';
            $params[] = $dateTo . ' 23:59:59';
        }

        $whereStr = implode(' AND ', $where);

        // Count total
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM email_log el WHERE {$whereStr}");
        $countStmt->execute($params);
        $totalEmails = (int)$countStmt->fetchColumn();
        $totalPages = max(1, ceil($totalEmails / $perPage));

        // Fetch emails
        $stmt = $this->db->prepare("
            SELECT el.*
            FROM email_log el
            WHERE {$whereStr}
            ORDER BY el.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $emails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Stats
        $stats = $this->db->query("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                COUNT(DISTINCT recipient_email) as unique_recipients,
                COUNT(DISTINCT email_type) as email_types
            FROM email_log
        ")->fetch(\PDO::FETCH_ASSOC);

        // Get distinct email types for filter dropdown
        $emailTypes = $this->db->query("
            SELECT DISTINCT email_type FROM email_log
            WHERE email_type IS NOT NULL
            ORDER BY email_type
        ")->fetchAll(\PDO::FETCH_COLUMN);

        view('admin.email-log.index', [
            'pageTitle' => 'Email Log',
            'emails' => $emails,
            'stats' => $stats,
            'emailTypes' => $emailTypes,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalEmails' => $totalEmails,
        ]);
    }

    /**
     * View a single email's full content
     */
    public function view(): void
    {
        $id = (int)get('id');

        $stmt = $this->db->prepare("SELECT * FROM email_log WHERE id = ?");
        $stmt->execute([$id]);
        $email = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$email) {
            setFlash('error', 'Email record not found.');
            redirect('admin/email-log');
            return;
        }

        view('admin.email-log.view', [
            'pageTitle' => 'Email Details',
            'email' => $email,
        ]);
    }

    /**
     * Get email history for a specific recipient (used by supplier/user views)
     */
    public static function getEmailHistory(string $recipientEmail, int $limit = 50): array
    {
        $db = \Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, recipient_email, subject, email_type, status, error_message, created_at
            FROM email_log
            WHERE recipient_email = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $recipientEmail, \PDO::PARAM_STR);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
