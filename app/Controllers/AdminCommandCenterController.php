<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

class AdminCommandCenterController
{
    private \PDO $db;
    private array $user;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            header('Location: /admin/login');
            exit;
        }

        $this->db   = \Database::getConnection();
        $this->user = $_SESSION['user'];
    }

    public function index(): void
    {
        $stats        = $this->getStats();
        $backlog      = $this->parseBacklog();
        $journalEntry = $this->getLatestJournalEntry();
        $recentOrders = $this->getRecentOrders();
        $systemFlags  = $this->getSystemFlags();

        $pageTitle   = 'Command Center';
        $currentPage = 'command-center';
        $content     = $this->renderView('command-center', compact(
            'stats', 'backlog', 'journalEntry', 'recentOrders', 'systemFlags', 'pageTitle'
        ));
        require __DIR__ . '/../Views/admin/layout.php';
    }

    // -------------------------------------------------------------------------

    private function getStats(): array
    {
        $stats = [
            'total_users'    => 0,
            'orders_today'   => 0,
            'revenue_month'  => 0.0,
            'pending_orders' => 0,
            'active_sellers' => 0,
        ];

        try {
            $stats['total_users'] = (int)$this->db
                ->query("SELECT COUNT(*) FROM users WHERE status = 'active'")
                ->fetchColumn();

            $stats['orders_today'] = (int)$this->db
                ->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")
                ->fetchColumn();

            $stats['revenue_month'] = (float)$this->db
                ->query("SELECT COALESCE(SUM(total_amount),0) FROM orders
                         WHERE MONTH(created_at) = MONTH(NOW())
                           AND YEAR(created_at)  = YEAR(NOW())
                           AND status NOT IN ('cancelled','refunded')")
                ->fetchColumn();

            $stats['pending_orders'] = (int)$this->db
                ->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','processing')")
                ->fetchColumn();

            $stats['active_sellers'] = (int)$this->db
                ->query("SELECT COUNT(*) FROM sellers WHERE verification_status = 'approved'")
                ->fetchColumn();

        } catch (\Throwable $e) {}

        return $stats;
    }

    private function parseBacklog(): array
    {
        $path = dirname(__DIR__, 2) . '/journal/backlog.md';
        if (!file_exists($path)) return [];

        $raw   = file_get_contents($path);
        $items = [];

        preg_match_all(
            '/^[-*]\s*(🔴|🟡|🟢)\s*\*\*(.+?)\*\*\s*[—\-]?\s*(.*)$/mu',
            $raw,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $items[] = [
                'priority' => match($m[1]) { '🔴' => 'high', '🟡' => 'medium', default => 'low' },
                'emoji'    => $m[1],
                'title'    => trim($m[2]),
                'detail'   => trim($m[3]),
            ];
        }

        return $items;
    }

    private function getLatestJournalEntry(): array
    {
        $path = dirname(__DIR__, 2) . '/journal/' . date('Y-m') . '.md';
        if (!file_exists($path)) return ['date' => '', 'body' => ''];

        $raw     = file_get_contents($path);
        $parts   = preg_split('/^## (\d{4}-\d{2}-\d{2})/m', $raw, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // $parts alternates: date, body, date, body …
        if (count($parts) < 2) return ['date' => '', 'body' => ''];

        $lastDate = '';
        $lastBody = '';
        for ($i = 0; $i < count($parts) - 1; $i += 2) {
            $lastDate = trim($parts[$i]);
            $lastBody = trim($parts[$i + 1] ?? '');
        }

        return ['date' => $lastDate, 'body' => $lastBody];
    }

    private function getRecentOrders(): array
    {
        try {
            return $this->db->query("
                SELECT o.id, o.order_number, o.status, o.total_amount, o.created_at,
                       u.first_name, u.last_name
                FROM orders o
                LEFT JOIN users u ON u.id = o.user_id
                ORDER BY o.created_at DESC
                LIMIT 6
            ")->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getSystemFlags(): array
    {
        $flags = [];

        // Hardcoded known risk — remove when resolved
        $flags[] = [
            'level'   => 'danger',
            'title'   => 'No Staging Environment',
            'detail'  => 'All deploys go straight to production. One bad push can take down the live site.',
            'action'  => 'See backlog',
        ];

        // Dynamic: pending seller verifications
        try {
            $pending = (int)$this->db->query("SELECT COUNT(*) FROM sellers WHERE verification_status = 'pending'")->fetchColumn();
            if ($pending > 0) {
                $flags[] = [
                    'level'   => 'warning',
                    'title'   => "{$pending} Seller(s) Awaiting Verification",
                    'detail'  => 'Seller applications are pending review.',
                    'action'  => '/admin/sellers',
                ];
            }
        } catch (\Throwable $e) {}

        // Dynamic: urgent support tickets
        try {
            $urgent = (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE priority = 'urgent' AND status NOT IN ('resolved','closed')")->fetchColumn();
            if ($urgent > 0) {
                $flags[] = [
                    'level'   => 'warning',
                    'title'   => "{$urgent} Urgent Support Ticket(s) Open",
                    'detail'  => 'Unresolved urgent tickets require attention.',
                    'action'  => '/admin/support',
                ];
            }
        } catch (\Throwable $e) {}

        return $flags;
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/admin/' . $view . '.php';
        return ob_get_clean();
    }
}
