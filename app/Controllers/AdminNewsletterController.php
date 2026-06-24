<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Helpers\EmailHelper;
use PDO;

class AdminNewsletterController
{
    private $db;

    public function __construct()
    {
        AuthMiddleware::handle('admin');
        $this->db = \Database::getConnection();
    }

    /**
     * Newsletter dashboard: per-list subscriber stats, compose form, campaign history.
     */
    public function index(): void
    {
        $lists = $this->db->query("
            SELECT l.*,
                   (SELECT COUNT(*) FROM newsletter_subscriptions ns
                     WHERE ns.list_id = l.id AND ns.status = 'active') AS active_count
            FROM newsletter_lists l
            ORDER BY l.sort_order
        ")->fetchAll(PDO::FETCH_ASSOC);

        $totalSubscribers = (int) $this->db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'")->fetchColumn();

        $campaigns = $this->db->query("
            SELECT c.*, l.name_en AS list_name_en, l.name_fr AS list_name_fr
            FROM newsletter_campaigns c
            JOIN newsletter_lists l ON l.id = c.list_id
            ORDER BY c.created_at DESC
            LIMIT 50
        ")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.newsletter.index', compact('lists', 'totalSubscribers', 'campaigns'));
    }

    /**
     * Create and send a campaign to every active subscriber of a list.
     */
    public function send(): void
    {
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Invalid session token. Please try again.');
            redirect(url('admin/newsletter'));
            return;
        }

        $listId    = (int) post('list_id', 0);
        $subjectEn = trim((string) post('subject_en', ''));
        $subjectFr = trim((string) post('subject_fr', ''));
        $bodyEn    = (string) post('body_en', '');
        $bodyFr    = (string) post('body_fr', '');
        $testOnly  = (post('action', '') === 'test');

        if (!$listId || $subjectEn === '' || $subjectFr === '' || trim($bodyEn) === '' || trim($bodyFr) === '') {
            setFlash('error', 'Please fill in the list, both subjects and both message bodies.');
            redirect(url('admin/newsletter'));
            return;
        }

        $list = $this->db->prepare("SELECT * FROM newsletter_lists WHERE id = ?");
        $list->execute([$listId]);
        $list = $list->fetch(PDO::FETCH_ASSOC);
        if (!$list) {
            setFlash('error', 'Selected newsletter list was not found.');
            redirect(url('admin/newsletter'));
            return;
        }

        $subject = $subjectEn . ' / ' . $subjectFr;

        // --- Test send: deliver only to the logged-in admin, do not log a campaign ---
        if ($testOnly) {
            $adminEmail = $_SESSION['user']['email'] ?? null;
            if (!$adminEmail) {
                setFlash('error', 'Could not determine your email for the test send.');
                redirect(url('admin/newsletter'));
                return;
            }
            $body = $this->buildEmailBody($subjectEn, $subjectFr, $bodyEn, $bodyFr, 'PREVIEW-TOKEN');
            EmailHelper::setNextMeta('newsletter_test', 'newsletter_list', $listId);
            EmailHelper::send($adminEmail, '[TEST] ' . $subject, $body);
            setFlash('success', 'Test email sent to ' . $adminEmail . '.');
            redirect(url('admin/newsletter'));
            return;
        }

        // --- Real send ---
        $recipients = $this->db->prepare("
            SELECT s.id, s.email, s.unsubscribe_token
            FROM newsletter_subscriptions ns
            JOIN newsletter_subscribers s ON s.id = ns.subscriber_id
            WHERE ns.list_id = ? AND ns.status = 'active' AND s.status = 'active'
              AND s.email IS NOT NULL AND s.email <> ''
        ");
        $recipients->execute([$listId]);
        $recipients = $recipients->fetchAll(PDO::FETCH_ASSOC);

        if (empty($recipients)) {
            setFlash('error', 'That list has no active subscribers to send to.');
            redirect(url('admin/newsletter'));
            return;
        }

        // Record the campaign
        $stmt = $this->db->prepare("
            INSERT INTO newsletter_campaigns
                (list_id, subject_en, subject_fr, body_en, body_fr, status, recipient_count, created_by)
            VALUES (?, ?, ?, ?, ?, 'sending', ?, ?)
        ");
        $stmt->execute([
            $listId, $subjectEn, $subjectFr, $bodyEn, $bodyFr,
            count($recipients), $_SESSION['user']['id'] ?? null,
        ]);
        $campaignId = (int) $this->db->lastInsertId();

        $sent = 0;
        foreach ($recipients as $r) {
            $body = $this->buildEmailBody($subjectEn, $subjectFr, $bodyEn, $bodyFr, $r['unsubscribe_token']);
            EmailHelper::setNextMeta('newsletter', 'newsletter_campaign', $campaignId);
            // no_admin_bcc: don't BCC the admin inbox on every single recipient
            if (EmailHelper::send($r['email'], $subject, $body, ['no_admin_bcc' => true])) {
                $sent++;
            }
        }

        $this->db->prepare("UPDATE newsletter_campaigns SET status = 'sent', sent_count = ?, sent_at = NOW() WHERE id = ?")
                 ->execute([$sent, $campaignId]);

        setFlash('success', "Campaign sent to {$sent} of " . count($recipients) . " subscriber(s).");
        redirect(url('admin/newsletter'));
    }

    /**
     * Subscriber browser with list/status filters.
     */
    public function subscribers(): void
    {
        $page    = max(1, (int) get('page', 1));
        $perPage = 30;
        $offset  = ($page - 1) * $perPage;
        $listSlug = sanitize(get('list', ''));
        $status   = sanitize(get('status', ''));
        $search   = sanitize(get('search', ''));

        [$where, $params, $joinList] = $this->subscriberFilter($listSlug, $status, $search);
        $clause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(DISTINCT s.id) FROM newsletter_subscribers s {$joinList} WHERE {$clause}";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $sql = "
            SELECT DISTINCT s.id, s.email, s.status, s.subscribed_at, s.unsubscribe_token,
                   (SELECT GROUP_CONCAT(l2.name_en ORDER BY l2.sort_order SEPARATOR ', ')
                      FROM newsletter_subscriptions ns2
                      JOIN newsletter_lists l2 ON l2.id = ns2.list_id
                     WHERE ns2.subscriber_id = s.id AND ns2.status = 'active') AS lists
            FROM newsletter_subscribers s
            {$joinList}
            WHERE {$clause}
            ORDER BY s.subscribed_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $allLists = $this->db->query("SELECT slug, name_en FROM newsletter_lists ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

        view('admin.newsletter.subscribers', compact('subscribers', 'total', 'page', 'perPage', 'allLists', 'listSlug', 'status', 'search'));
    }

    /**
     * Export subscribers (respects the same filters) to CSV.
     */
    public function exportSubscribers(): void
    {
        $listSlug = sanitize(get('list', ''));
        $status   = sanitize(get('status', ''));
        $search   = sanitize(get('search', ''));

        [$where, $params, $joinList] = $this->subscriberFilter($listSlug, $status, $search);
        $clause = implode(' AND ', $where);

        $sql = "
            SELECT DISTINCT s.email, s.status, s.subscribed_at,
                   (SELECT GROUP_CONCAT(l2.slug ORDER BY l2.sort_order SEPARATOR '|')
                      FROM newsletter_subscriptions ns2
                      JOIN newsletter_lists l2 ON l2.id = ns2.list_id
                     WHERE ns2.subscriber_id = s.id AND ns2.status = 'active') AS lists
            FROM newsletter_subscribers s
            {$joinList}
            WHERE {$clause}
            ORDER BY s.subscribed_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Email', 'Status', 'Subscribed At', 'Active Lists']);
        foreach ($rows as $row) {
            fputcsv($out, [$row['email'], $row['status'], $row['subscribed_at'], $row['lists']]);
        }
        fclose($out);
        exit;
    }

    /**
     * Build the WHERE clause + params shared by the browser and CSV export.
     * @return array{0:array,1:array,2:string}
     */
    private function subscriberFilter(string $listSlug, string $status, string $search): array
    {
        $where  = ['1=1'];
        $params = [];
        $joinList = '';

        if ($listSlug !== '') {
            $joinList = "JOIN newsletter_subscriptions nsf ON nsf.subscriber_id = s.id AND nsf.status = 'active'
                         JOIN newsletter_lists lf ON lf.id = nsf.list_id";
            $where[]  = "lf.slug = ?";
            $params[] = $listSlug;
        }
        if ($status !== '' && in_array($status, ['active', 'unsubscribed'], true)) {
            $where[]  = "s.status = ?";
            $params[] = $status;
        }
        if ($search !== '') {
            $where[]  = "s.email LIKE ?";
            $params[] = "%{$search}%";
        }

        return [$where, $params, $joinList];
    }

    /**
     * Wrap a campaign in the branded bilingual HTML shell with a CASL-compliant
     * footer (preference link via token + physical mailing address).
     */
    private function buildEmailBody(string $subjectEn, string $subjectFr, string $bodyEn, string $bodyFr, string $token): string
    {
        $prefUrl = url('newsletter/preferences') . '?token=' . urlencode($token);
        $address = setting('company_address', 'OCSAPP Inc., West Island, Montréal, QC, Canada');
        $year    = date('Y');

        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #1f2937;">
            <div style="background: linear-gradient(135deg, #00b207 0%, #0b3d2e 100%); color: #fff; padding: 26px 30px; text-align: center;">
                <h1 style="margin: 0; font-size: 22px;">OCSAPP</h1>
            </div>
            <div style="background: #fff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
                <h2 style="font-size: 18px; margin: 0 0 14px;">' . htmlspecialchars($subjectEn) . '</h2>
                <div style="font-size: 14px; line-height: 1.7; color: #374151;">' . $bodyEn . '</div>

                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 28px 0;">

                <h2 style="font-size: 18px; margin: 0 0 14px;">' . htmlspecialchars($subjectFr) . '</h2>
                <div style="font-size: 14px; line-height: 1.7; color: #374151;">' . $bodyFr . '</div>
            </div>
            <div style="background: #f9fafb; padding: 20px 30px; text-align: center; color: #6b7280; font-size: 12px; border: 1px solid #e5e7eb; border-top: none; line-height: 1.7;">
                <p style="margin: 0 0 6px;">&copy; ' . $year . ' ' . htmlspecialchars($address) . '</p>
                <p style="margin: 0;">
                    <a href="' . htmlspecialchars($prefUrl) . '" style="color: #00b207;">Manage preferences / G&eacute;rer mes pr&eacute;f&eacute;rences</a>
                    &nbsp;|&nbsp;
                    <a href="' . htmlspecialchars($prefUrl) . '" style="color: #6b7280;">Unsubscribe / Se d&eacute;sabonner</a>
                </p>
            </div>
        </div>';
    }
}
