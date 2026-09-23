<?php
/**
 * Migration: working unsubscribe for waitlist emails.
 * - unsubscribe_token: random, unguessable token used in the email footer link
 *   (the referral code is shared publicly, so it cannot double as this token)
 * - unsubscribed_at: set when the person opts out; launch notifications skip them.
 *   Separate from marketing_consent, which is 0 for anyone who never ticked the box.
 *
 * Run: php database/migrations/add_unsubscribe_to_waitlist.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_unsubscribe_to_waitlist\n";

try {
    if (empty($db->query("SHOW COLUMNS FROM waitlist LIKE 'unsubscribe_token'")->fetchAll())) {
        $db->exec("ALTER TABLE waitlist ADD COLUMN unsubscribe_token CHAR(32) NULL DEFAULT NULL AFTER referred_by, ADD UNIQUE KEY uq_waitlist_unsubscribe_token (unsubscribe_token)");
        echo "  [OK] Added waitlist.unsubscribe_token\n";
    } else {
        echo "  [SKIP] waitlist.unsubscribe_token already exists\n";
    }

    if (empty($db->query("SHOW COLUMNS FROM waitlist LIKE 'unsubscribed_at'")->fetchAll())) {
        $db->exec("ALTER TABLE waitlist ADD COLUMN unsubscribed_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Opted out of waitlist emails' AFTER unsubscribe_token");
        echo "  [OK] Added waitlist.unsubscribed_at\n";
    } else {
        echo "  [SKIP] waitlist.unsubscribed_at already exists\n";
    }

    // Backfill tokens for existing rows
    $ids = $db->query("SELECT id FROM waitlist WHERE unsubscribe_token IS NULL")->fetchAll(PDO::FETCH_COLUMN);
    $upd = $db->prepare("UPDATE waitlist SET unsubscribe_token = ? WHERE id = ?");
    foreach ($ids as $id) {
        $upd->execute([bin2hex(random_bytes(16)), $id]);
    }
    echo "  [OK] Backfilled " . count($ids) . " unsubscribe token(s)\n";

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
