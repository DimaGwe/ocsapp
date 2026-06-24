<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class AdminWaitlistController
{
    private $db;

    public function __construct()
    {
        AuthMiddleware::handle('admin');
        $this->db = \Database::getConnection();
    }

    public function index(): void
    {
        $page    = max(1, (int) get('page', 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;
        $search  = sanitize(get('search', ''));
        $role    = sanitize(get('role', ''));
        $status  = sanitize(get('status', ''));

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[]  = "(email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $term     = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if ($role) {
            $where[]  = "role = ?";
            $params[] = $role;
        }
        if ($status) {
            $where[]  = "status = ?";
            $params[] = $status;
        }

        $clause = implode(' AND ', $where);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM waitlist WHERE {$clause}");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT w.*,
                   (SELECT COUNT(*) FROM waitlist w2 WHERE w2.referred_by = w.referral_code) AS referral_count
            FROM waitlist w
            WHERE {$clause}
            ORDER BY w.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $entries = $stmt->fetchAll();

        // Stats
        $stats = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(role='buyer')     AS buyers,
                SUM(role='seller')    AS sellers,
                SUM(role='supplier')  AS suppliers,
                SUM(role='driver')    AS drivers,
                SUM(role='business')  AS businesses,
                SUM(status='pending')   AS pending,
                SUM(status='notified')  AS notified,
                SUM(status='converted') AS converted
            FROM waitlist
        ")->fetch();

        view('admin.waitlist.index', compact('entries', 'total', 'page', 'perPage', 'search', 'role', 'status', 'stats'));
    }

    public function notify(): void
    {
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $ids = post('ids', []);
        if (!is_array($ids) || empty($ids)) {
            jsonResponse(['success' => false, 'message' => 'No entries selected.']);
            return;
        }

        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("SELECT * FROM waitlist WHERE id IN ({$placeholders}) AND status = 'pending'");
        $stmt->execute($ids);
        $entries = $stmt->fetchAll();

        $sent = 0;
        foreach ($entries as $entry) {
            if ($this->sendLaunchNotification($entry)) {
                $upd = $this->db->prepare("UPDATE waitlist SET status = 'notified' WHERE id = ?");
                $upd->execute([$entry['id']]);
                $sent++;
            }
        }

        jsonResponse(['success' => true, 'message' => "{$sent} notification(s) sent."]);
    }

    public function updateStatus(): void
    {
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id     = (int) post('id', 0);
        $status = sanitize(post('status', ''));
        $valid  = ['pending', 'notified', 'converted'];

        if (!$id || !in_array($status, $valid, true)) {
            jsonResponse(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $stmt = $this->db->prepare("UPDATE waitlist SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        jsonResponse(['success' => true]);
    }

    public function delete(): void
    {
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id = (int) post('id', 0);
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Invalid ID.']);
            return;
        }

        $this->db->prepare("DELETE FROM waitlist WHERE id = ?")->execute([$id]);
        jsonResponse(['success' => true]);
    }

    public function export(): void
    {
        $role   = sanitize(get('role', ''));
        $status = sanitize(get('status', ''));

        $where  = ['1=1'];
        $params = [];

        if ($role) {
            $where[]  = "role = ?";
            $params[] = $role;
        }
        if ($status) {
            $where[]  = "status = ?";
            $params[] = $status;
        }

        $clause = implode(' AND ', $where);
        $stmt   = $this->db->prepare("SELECT * FROM waitlist WHERE {$clause} ORDER BY created_at DESC");
        $stmt->execute($params);
        $rows   = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="waitlist-' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Email', 'First Name', 'Last Name', 'Role', 'Locale', 'Referral Code', 'Referred By', 'Status', 'IP', 'Joined At']);

        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id'],
                $row['email'],
                $row['first_name'],
                $row['last_name'],
                $row['role'],
                $row['locale'],
                $row['referral_code'],
                $row['referred_by'],
                $row['status'],
                $row['ip_address'],
                $row['created_at'],
            ]);
        }

        fclose($out);
        exit;
    }

    private function sendLaunchNotification(array $entry): bool
    {
        $fr        = ($entry['locale'] === 'fr');
        $roleLabels = [
            'buyer'    => $fr ? 'Acheteur'             : 'Buyer',
            'seller'   => $fr ? 'Vendeur'              : 'Seller',
            'supplier' => $fr ? 'Fournisseur'          : 'Supplier',
            'driver'   => $fr ? 'Livreur'              : 'Driver',
            'business' => $fr ? 'Client Distribution'  : 'Business Client',
        ];

        $firstName = $entry['first_name'];
        $role      = $entry['role'];
        $roleLabel = $roleLabels[$role] ?? $role;

        $subject = $fr
            ? 'OCSAPP est maintenant ouvert - Votre accès est prêt !'
            : 'OCSAPP is now live - Your access is ready!';

        ob_start();
        require __DIR__ . '/../Views/emails/waitlist-launch-notification.php';
        $body = ob_get_clean();

        \App\Helpers\EmailHelper::setNextMeta('waitlist_launch', 'waitlist', (int) $entry['id']);
        return \App\Helpers\EmailHelper::send($entry['email'], $subject, $body);
    }
}
