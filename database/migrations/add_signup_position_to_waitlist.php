<?php
/**
 * Migration: store each waitlist entry's position, numbered per role.
 * Before this, the position was recalculated on every read (global COUNT of ids <= mine),
 * so deleting an earlier row silently changed the number people had been told.
 * Now it is fixed at signup: "Seller #1", "Buyer #1", ...
 *
 * Backfill numbers existing rows per role in signup order (id order).
 *
 * Run: php database/migrations/add_signup_position_to_waitlist.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_signup_position_to_waitlist\n";

try {
    if (empty($db->query("SHOW COLUMNS FROM waitlist LIKE 'signup_position'")->fetchAll())) {
        $db->exec("ALTER TABLE waitlist ADD COLUMN signup_position INT UNSIGNED NULL DEFAULT NULL COMMENT 'Position within its role at signup, never recalculated' AFTER role, ADD INDEX idx_waitlist_role_position (role, signup_position)");
        echo "  [OK] Added waitlist.signup_position\n";
    } else {
        echo "  [SKIP] waitlist.signup_position already exists\n";
    }

    $rows = $db->query("SELECT id, role FROM waitlist WHERE signup_position IS NULL ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $upd  = $db->prepare("
        UPDATE waitlist w
        SET w.signup_position = (SELECT c FROM (SELECT COUNT(*) c FROM waitlist w2 WHERE w2.role = ? AND w2.id <= ?) x)
        WHERE w.id = ?
    ");
    foreach ($rows as $r) {
        $upd->execute([$r['role'], $r['id'], $r['id']]);
    }
    echo "  [OK] Backfilled " . count($rows) . " position(s)\n";

    foreach ($db->query("SELECT role, COUNT(*) n, MAX(signup_position) maxpos FROM waitlist GROUP BY role")->fetchAll(PDO::FETCH_ASSOC) as $r) {
        echo "       {$r['role']}: {$r['n']} entries, highest #{$r['maxpos']}\n";
    }

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
