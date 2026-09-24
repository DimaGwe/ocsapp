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
        // sort=position: by role, then position within the role; default newest first
        $sort    = get('sort', '') === 'position' ? 'position' : 'newest';

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[]  = "(email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR referral_code = ?)";
            $term     = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = strtoupper(trim($search));
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
                   (SELECT COUNT(*) FROM waitlist w2 WHERE w2.referred_by = w.referral_code) AS referral_count,
                   EXISTS(SELECT 1 FROM users u WHERE u.email = w.email) AS has_account
            FROM waitlist w
            WHERE {$clause}
            ORDER BY " . ($sort === 'position' ? "w.role, w.signup_position, w.id" : "w.created_at DESC") . "
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
                SUM(role='partner')   AS partners,
                SUM(status='pending')   AS pending,
                SUM(status='notified')  AS notified,
                SUM(status='converted') AS converted
            FROM waitlist
        ")->fetch();

        view('admin.waitlist.index', compact('entries', 'total', 'page', 'perPage', 'search', 'role', 'status', 'stats', 'sort'));
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

        // Beta invites: any not-yet-converted entry can be (re)invited; re-sends reuse the same link.
        $stmt = $this->db->prepare("
            SELECT w.*, EXISTS(SELECT 1 FROM users u WHERE u.email = w.email) AS has_account
            FROM waitlist w
            WHERE w.id IN ({$placeholders}) AND w.status IN ('pending', 'notified')
        ");
        $stmt->execute($ids);
        $entries = $stmt->fetchAll();

        $sent = 0;
        $skipped = [];
        foreach ($entries as $entry) {
            if (!empty($entry['unsubscribed_at'])) { $skipped[] = $entry['email'] . ' (unsubscribed)'; continue; }
            if (!empty($entry['has_account']))     { $skipped[] = $entry['email'] . ' (already has an account)'; continue; }
            if (!isset(\App\Helpers\BetaAccessHelper::SIGNUP_PATHS[$entry['role']])) {
                $skipped[] = $entry['email'] . ' (' . $entry['role'] . ': no account type)';
                continue;
            }

            $inviteToken = $entry['invite_token'] ?: bin2hex(random_bytes(16));
            if (empty($entry['invite_token'])) {
                $this->db->prepare("UPDATE waitlist SET invite_token = ? WHERE id = ?")->execute([$inviteToken, $entry['id']]);
                $entry['invite_token'] = $inviteToken;
            }

            if ($this->sendInvite($entry)) {
                $this->db->prepare("UPDATE waitlist SET status = 'notified', invite_sent_at = NOW() WHERE id = ?")
                         ->execute([$entry['id']]);
                $sent++;
            } else {
                $skipped[] = $entry['email'] . ' (email failed)';
            }
        }

        $notFound = count($ids) - count($entries);
        $msg = "{$sent} invite(s) sent.";
        if ($skipped)      { $msg .= "\nSkipped: " . implode(', ', $skipped); }
        if ($notFound > 0) { $msg .= "\n{$notFound} already converted, skipped."; }
        jsonResponse(['success' => true, 'message' => $msg]);
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

    /**
     * Beta invite email (bilingual, FR first): account-creation link for the entry's
     * role plus the role's onboarding guide page.
     */
    private function sendInvite(array $entry): bool
    {
        $role      = $entry['role'];
        $firstName = $entry['first_name'];
        $email     = $entry['email'];
        $inviteUrl = \App\Helpers\BetaAccessHelper::inviteUrl($role, $entry['invite_token']);
        $guideUrl  = url('onboarding/' . $role);
        $unsubUrl  = url('/waitlist/unsubscribe') . '?t=' . ($entry['unsubscribe_token'] ?? '');

        $roleLabels = [
            'buyer'    => ['Acheteur', 'Buyer'],
            'seller'   => ['Vendeur', 'Seller'],
            'supplier' => ['Fournisseur', 'Supplier'],
            'driver'   => ['Livreur', 'Driver'],
            'business' => ['Client Distribution', 'Business Client'],
        ];
        [$roleLabelFr, $roleLabelEn] = $roleLabels[$role] ?? [$role, $role];

        $subject = 'Votre accès OCSAPP est prêt / Your OCSAPP access is ready';

        ob_start();
        require __DIR__ . '/../Views/emails/waitlist-invite.php';
        $body = ob_get_clean();

        \App\Helpers\EmailHelper::setNextMeta('waitlist_invite', 'waitlist', (int) $entry['id']);
        return \App\Helpers\EmailHelper::send($email, $subject, $body);
    }
}
