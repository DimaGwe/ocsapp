<?php
/**
 * Migration: beta-mode invites for the waitlist.
 * While signup is closed (settings.allow_registration = false), an admin sends a
 * waitlist entry an invite; the link carries invite_token and lets that person
 * register for their waitlist role with their waitlist email only.
 *
 * Run: php database/migrations/add_invite_to_waitlist.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_invite_to_waitlist\n";

try {
    if (empty($db->query("SHOW COLUMNS FROM waitlist LIKE 'invite_token'")->fetchAll())) {
        $db->exec("ALTER TABLE waitlist ADD COLUMN invite_token CHAR(32) NULL DEFAULT NULL AFTER unsubscribed_at, ADD UNIQUE KEY uq_waitlist_invite_token (invite_token)");
        echo "  [OK] Added waitlist.invite_token\n";
    } else {
        echo "  [SKIP] waitlist.invite_token already exists\n";
    }

    if (empty($db->query("SHOW COLUMNS FROM waitlist LIKE 'invite_sent_at'")->fetchAll())) {
        $db->exec("ALTER TABLE waitlist ADD COLUMN invite_sent_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Last time the account invite was emailed' AFTER invite_token");
        echo "  [OK] Added waitlist.invite_sent_at\n";
    } else {
        echo "  [SKIP] waitlist.invite_sent_at already exists\n";
    }

    // Beta switch: reuse the existing (previously unused) allow_registration setting.
    // Only update the description here; the value is flipped by an admin in Settings.
    $db->prepare("UPDATE settings SET label = ?, description = ? WHERE `key` = 'allow_registration'")
       ->execute([
           'Allow Public Registration',
           'Unticked = beta mode: all signup pages (buyer, seller, supplier, driver, business) show a "join the waitlist" message, and only people invited from Admin > Waitlist can create an account.',
       ]);
    echo "  [OK] Updated allow_registration description\n";

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
