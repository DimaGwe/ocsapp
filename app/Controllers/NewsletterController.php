<?php

namespace App\Controllers;

use PDO;

class NewsletterController {

    /** Canonical CASL consent record stored with every subscription */
    private const CONSENT_TEXT    = 'I agree to receive promotional emails from OCSAPP. I understand I can unsubscribe at any time.';
    private const CONSENT_VERSION = 'v2';

    /**
     * Subscribe to one or more newsletter lists (CASL compliant — explicit consent).
     * Expects JSON: { email, consent, lists: ["general","buyer",...] }
     */
    public function subscribe() {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
            $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);

            if (!$email) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Please provide a valid email address']);
                return;
            }

            // CASL: require explicit consent
            if (empty($input['consent'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Please check the consent box to subscribe']);
                return;
            }

            // Requested list slugs — default to General if none provided
            $slugs = $input['lists'] ?? [];
            if (!is_array($slugs)) {
                $slugs = [];
            }
            $slugs = array_values(array_unique(array_filter(array_map('strval', $slugs))));
            if (empty($slugs)) {
                $slugs = ['general'];
            }

            $pdo = db();

            // Resolve requested slugs to active list IDs
            $placeholders = implode(',', array_fill(0, count($slugs), '?'));
            $stmt = $pdo->prepare("SELECT id, slug FROM newsletter_lists WHERE is_active = 1 AND slug IN ({$placeholders})");
            $stmt->execute($slugs);
            $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($lists)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid newsletter selected.']);
                return;
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

            // Upsert the subscriber record
            $stmt = $pdo->prepare("SELECT id, unsubscribe_token FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($subscriber) {
                $subscriberId = (int) $subscriber['id'];
                $token = $subscriber['unsubscribe_token'] ?: bin2hex(random_bytes(24));
                $pdo->prepare("
                    UPDATE newsletter_subscribers
                    SET status = 'active', subscribed_at = COALESCE(subscribed_at, NOW()), unsubscribed_at = NULL,
                        unsubscribe_token = ?, ip_address = ?, user_agent = ?,
                        consent_text = ?, consent_version = ?
                    WHERE id = ?
                ")->execute([$token, $ip, $ua, self::CONSENT_TEXT, self::CONSENT_VERSION, $subscriberId]);
            } else {
                $token = bin2hex(random_bytes(24));
                $pdo->prepare("
                    INSERT INTO newsletter_subscribers
                        (email, unsubscribe_token, status, ip_address, user_agent, subscribed_at, consent_text, consent_version)
                    VALUES (?, ?, 'active', ?, ?, NOW(), ?, ?)
                ")->execute([$email, $token, $ip, $ua, self::CONSENT_TEXT, self::CONSENT_VERSION]);
                $subscriberId = (int) $pdo->lastInsertId();
            }

            // Upsert each list subscription with its own consent record
            $sub = $pdo->prepare("
                INSERT INTO newsletter_subscriptions
                    (subscriber_id, list_id, status, consent_text, consent_version, subscribed_at)
                VALUES (?, ?, 'active', ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    status = 'active', unsubscribed_at = NULL,
                    consent_text = VALUES(consent_text), consent_version = VALUES(consent_version),
                    subscribed_at = COALESCE(subscribed_at, NOW())
            ");
            foreach ($lists as $list) {
                $sub->execute([$subscriberId, (int) $list['id'], self::CONSENT_TEXT, self::CONSENT_VERSION]);
            }

            $this->syncMasterStatus($pdo, $subscriberId);

            $count = count($lists);
            echo json_encode([
                'success' => true,
                'message' => $count > 1
                    ? "Thank you! You are subscribed to {$count} newsletters."
                    : 'Thank you for subscribing!',
            ]);

        } catch (\PDOException $e) {
            error_log("Newsletter subscription error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        }
    }

    /**
     * Preference centre — manage individual list subscriptions via a secure token.
     * GET /newsletter/preferences?token=...   (also accepts ?email= for legacy links)
     */
    public function preferences() {
        $currentLang = $_SESSION['language'] ?? 'fr';
        $fr = ($currentLang === 'fr');
        $token = preg_replace('/[^a-f0-9]/', '', (string) ($_GET['token'] ?? ''));

        $pdo = db();
        $subscriber = null;

        if ($token) {
            $stmt = $pdo->prepare("SELECT * FROM newsletter_subscribers WHERE unsubscribe_token = ?");
            $stmt->execute([$token]);
            $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Build the list of all active newsletters with this subscriber's current state
        $lists = $pdo->query("SELECT id, slug, name_en, name_fr, description_en, description_fr FROM newsletter_lists WHERE is_active = 1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

        $subscribed = [];
        if ($subscriber) {
            $stmt = $pdo->prepare("SELECT list_id FROM newsletter_subscriptions WHERE subscriber_id = ? AND status = 'active'");
            $stmt->execute([$subscriber['id']]);
            $subscribed = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        $this->renderPreferences($subscriber, $lists, $subscribed, $token, $fr);
    }

    /**
     * Save preference changes. POST /newsletter/preferences
     * Body: token + lists[] (slugs to keep active); anything not listed is unsubscribed.
     */
    public function updatePreferences() {
        header('Content-Type: application/json');

        $token = preg_replace('/[^a-f0-9]/', '', (string) ($_POST['token'] ?? ''));
        if (!$token) {
            jsonResponse(['success' => false, 'message' => 'Invalid or missing token.'], 400);
            return;
        }

        $pdo = db();
        $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE unsubscribe_token = ?");
        $stmt->execute([$token]);
        $subscriberId = $stmt->fetchColumn();
        if (!$subscriberId) {
            jsonResponse(['success' => false, 'message' => 'Subscriber not found.'], 404);
            return;
        }
        $subscriberId = (int) $subscriberId;

        $keep = $_POST['lists'] ?? [];
        if (!is_array($keep)) {
            $keep = [];
        }
        $keep = array_values(array_unique(array_filter(array_map('strval', $keep))));

        // Map kept slugs to active list IDs
        $keepIds = [];
        if ($keep) {
            $placeholders = implode(',', array_fill(0, count($keep), '?'));
            $stmt = $pdo->prepare("SELECT id FROM newsletter_lists WHERE is_active = 1 AND slug IN ({$placeholders})");
            $stmt->execute($keep);
            $keepIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        // Reactivate kept lists, unsubscribe the rest
        $allLists = $pdo->query("SELECT id FROM newsletter_lists WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($allLists as $listId) {
            $listId = (int) $listId;
            if (in_array($listId, $keepIds, true)) {
                $pdo->prepare("
                    INSERT INTO newsletter_subscriptions (subscriber_id, list_id, status, consent_text, consent_version, subscribed_at)
                    VALUES (?, ?, 'active', ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE status = 'active', unsubscribed_at = NULL
                ")->execute([$subscriberId, $listId, self::CONSENT_TEXT, self::CONSENT_VERSION]);
            } else {
                $pdo->prepare("
                    UPDATE newsletter_subscriptions
                    SET status = 'unsubscribed', unsubscribed_at = NOW()
                    WHERE subscriber_id = ? AND list_id = ? AND status = 'active'
                ")->execute([$subscriberId, $listId]);
            }
        }

        $this->syncMasterStatus($pdo, $subscriberId);

        jsonResponse(['success' => true, 'message' => 'Your preferences have been updated.']);
    }

    /**
     * Unsubscribe from everything via token. POST /newsletter/unsubscribe-all
     */
    public function unsubscribeAll() {
        header('Content-Type: application/json');

        $token = preg_replace('/[^a-f0-9]/', '', (string) ($_POST['token'] ?? ''));
        if (!$token) {
            jsonResponse(['success' => false, 'message' => 'Invalid or missing token.'], 400);
            return;
        }

        $pdo = db();
        $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE unsubscribe_token = ?");
        $stmt->execute([$token]);
        $subscriberId = $stmt->fetchColumn();
        if (!$subscriberId) {
            jsonResponse(['success' => false, 'message' => 'Subscriber not found.'], 404);
            return;
        }
        $subscriberId = (int) $subscriberId;

        $pdo->prepare("UPDATE newsletter_subscriptions SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE subscriber_id = ? AND status = 'active'")->execute([$subscriberId]);
        $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE id = ?")->execute([$subscriberId]);

        jsonResponse(['success' => true, 'message' => 'You have been unsubscribed from all OCSAPP newsletters.']);
    }

    /**
     * Legacy unsubscribe page (GET /unsubscribe). Redirects token links to the
     * preference centre; falls back to the simple email form for old links.
     */
    public function unsubscribePage() {
        $token = preg_replace('/[^a-f0-9]/', '', (string) ($_GET['token'] ?? ''));
        if ($token) {
            redirect(url('newsletter/preferences') . '?token=' . $token);
            return;
        }
        // No token: send to the preference centre, which shows a "request a link" notice.
        $this->preferences();
    }

    /**
     * Legacy email-based unsubscribe endpoint (kept for old email footers).
     * Unsubscribes the email from every list.
     */
    public function unsubscribe() {
        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $email = $method === 'POST'
            ? filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)
            : filter_var($_GET['email'] ?? '', FILTER_VALIDATE_EMAIL);

        if (!$email) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            return;
        }

        try {
            $pdo = db();
            $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $subscriberId = $stmt->fetchColumn();

            if (!$subscriberId) {
                echo json_encode(['success' => false, 'message' => 'Email not found in our newsletter list.']);
                return;
            }
            $subscriberId = (int) $subscriberId;

            $pdo->prepare("UPDATE newsletter_subscriptions SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE subscriber_id = ? AND status = 'active'")->execute([$subscriberId]);
            $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE id = ?")->execute([$subscriberId]);

            echo json_encode(['success' => true, 'message' => 'You have been unsubscribed successfully.']);

        } catch (\PDOException $e) {
            error_log("Newsletter unsubscribe error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        }
    }

    /**
     * Keep the master subscriber.status in sync: active if any list subscription
     * is active, otherwise unsubscribed.
     */
    private function syncMasterStatus(\PDO $pdo, int $subscriberId): void {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM newsletter_subscriptions WHERE subscriber_id = ? AND status = 'active'");
        $stmt->execute([$subscriberId]);
        $hasActive = (int) $stmt->fetchColumn() > 0;

        if ($hasActive) {
            $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active', unsubscribed_at = NULL WHERE id = ?")->execute([$subscriberId]);
        } else {
            $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE id = ?")->execute([$subscriberId]);
        }
    }

    /**
     * Render the preference-centre page (self-contained, bilingual).
     */
    private function renderPreferences(?array $subscriber, array $lists, array $subscribed, string $token, bool $fr): void {
        $title = $fr ? 'Préférences de courriel' : 'Email Preferences';
        ?>
<!DOCTYPE html>
<html lang="<?= $fr ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - OCSAPP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <style>
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: #f3f4f6; margin: 0; color: #1f2937; }
        .wrap { max-width: 560px; margin: 48px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 16px; padding: 36px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .card h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .card .sub { font-size: 14px; color: #6b7280; margin: 0 0 24px; line-height: 1.6; }
        .list-row { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
        .list-row:last-of-type { border-bottom: none; }
        .list-row input { margin-top: 3px; width: 18px; height: 18px; accent-color: #00b207; flex-shrink: 0; }
        .list-row .meta strong { display: block; font-size: 14px; font-weight: 600; }
        .list-row .meta span { font-size: 12.5px; color: #6b7280; line-height: 1.5; }
        .actions { margin-top: 26px; display: flex; flex-direction: column; gap: 10px; }
        .btn { padding: 12px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .btn-primary { background: #00b207; color: #fff; }
        .btn-primary:hover { background: #009906; }
        .btn-link { background: none; color: #c62828; font-size: 13px; padding: 6px; }
        .btn-link:hover { text-decoration: underline; }
        .flash { padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; text-align: center; display: none; font-size: 14px; }
        .notice { background: #fff; border-radius: 16px; padding: 36px; box-shadow: 0 2px 12px rgba(0,0,0,.07); text-align: center; }
        .notice i { font-size: 44px; color: #00b207; margin-bottom: 14px; display: block; }
    </style>
</head>
<body>
<?php
    $t = []; $currentLang = $fr ? 'fr' : 'en';
    $headerPath = dirname(__DIR__) . '/Views/components/header.php';
    if (file_exists($headerPath)) { include $headerPath; }
?>
<div class="wrap">
<?php if (!$subscriber): ?>
    <div class="notice">
        <i class="fas fa-envelope-circle-check"></i>
        <h1><?= $fr ? 'Lien introuvable' : 'Link not found' ?></h1>
        <p class="sub"><?= $fr
            ? 'Ce lien de préférences est invalide ou a expiré. Vous pouvez vous désabonner en utilisant le lien au bas de tout courriel que nous vous envoyons.'
            : 'This preferences link is invalid or has expired. You can unsubscribe using the link at the bottom of any email we send you.' ?></p>
    </div>
<?php else: ?>
    <div class="card">
        <h1><?= $title ?></h1>
        <p class="sub"><?= $fr
            ? 'Gérez les infolettres OCSAPP que vous recevez à <strong>' . htmlspecialchars($subscriber['email']) . '</strong>. Décochez celles que vous ne voulez plus recevoir.'
            : 'Manage the OCSAPP newsletters sent to <strong>' . htmlspecialchars($subscriber['email']) . '</strong>. Uncheck any you no longer want.' ?></p>

        <div id="flash" class="flash"></div>

        <form id="prefForm">
            <?php foreach ($lists as $list): ?>
            <label class="list-row">
                <input type="checkbox" name="lists[]" value="<?= htmlspecialchars($list['slug']) ?>"
                    <?= in_array((int) $list['id'], $subscribed, true) ? 'checked' : '' ?>>
                <span class="meta">
                    <strong><?= htmlspecialchars($fr ? $list['name_fr'] : $list['name_en']) ?></strong>
                    <span><?= htmlspecialchars($fr ? ($list['description_fr'] ?? '') : ($list['description_en'] ?? '')) ?></span>
                </span>
            </label>
            <?php endforeach; ?>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><?= $fr ? 'Enregistrer mes préférences' : 'Save my preferences' ?></button>
                <button type="button" class="btn btn-link" onclick="unsubAll()"><?= $fr ? 'Me désabonner de tout' : 'Unsubscribe from everything' ?></button>
            </div>
        </form>
    </div>

    <script>
    const TOKEN = <?= json_encode($token) ?>;
    const URLS = {
        update: <?= json_encode(url('newsletter/preferences')) ?>,
        unsubAll: <?= json_encode(url('newsletter/unsubscribe-all')) ?>
    };
    const flash = document.getElementById('flash');
    function showFlash(msg, ok) {
        flash.style.display = 'block';
        flash.style.background = ok ? '#e8f5e9' : '#fce4ec';
        flash.style.color = ok ? '#2e7d32' : '#c62828';
        flash.textContent = msg;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    document.getElementById('prefForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('token', TOKEN);
        const res = await fetch(URLS.update, { method: 'POST', body: fd });
        const json = await res.json();
        showFlash(json.message, !!json.success);
    });
    async function unsubAll() {
        if (!confirm(<?= json_encode($fr ? 'Vous désabonner de toutes les infolettres OCSAPP ?' : 'Unsubscribe from all OCSAPP newsletters?') ?>)) return;
        const fd = new FormData();
        fd.append('token', TOKEN);
        const res = await fetch(URLS.unsubAll, { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            document.querySelectorAll('#prefForm input[type=checkbox]').forEach(cb => cb.checked = false);
        }
        showFlash(json.message, !!json.success);
    }
    </script>
<?php endif; ?>
</div>
<?php
    $footerPath = dirname(__DIR__) . '/Views/components/footer.php';
    if (file_exists($footerPath)) { include $footerPath; }
?>
</body>
</html>
        <?php
    }
}
