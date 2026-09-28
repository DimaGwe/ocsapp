<?php
/**
 * Migration: Founders' Wall consent (Law 25 + Civil Code art. 36).
 * A founder's name is shown on the public Founders' Wall ONLY with an express, opt-in consent
 * given on the waitlist form (or later from the preferences link). Default is NO (Law 25 s. 9.1).
 *
 * waitlist:
 *   wall_consent          1 = agreed to be shown once founding status is granted (default 0)
 *   wall_consent_at       when the current choice was made
 *   wall_consent_version  version of the consent wording the person saw (FoundersWallHelper::CONSENT_VERSION)
 *   wall_token            random token for the no-login preferences page (/founders-wall/preferences?t=...)
 *   wall_notified_at      when the "you're a founder, here is your wall choice" email went out (sent once)
 *
 * founders_wall_consent_log: proof of every consent given or withdrawn (CAI guidelines 2023-1, section C).
 *
 * Existing waitlist rows keep wall_consent = 0: they never saw the question, so they are not shown.
 *
 * Run: php database/migrations/add_founders_wall_consent.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_founders_wall_consent\n";

try {
    $columns = [
        'wall_consent'         => "TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Founders Wall opt-in (express, default no)' AFTER marketing_consent",
        'wall_consent_at'      => "TIMESTAMP NULL DEFAULT NULL AFTER wall_consent",
        'wall_consent_version' => "VARCHAR(20) NULL DEFAULT NULL AFTER wall_consent_at",
        'wall_token'           => "CHAR(32) NULL DEFAULT NULL AFTER wall_consent_version",
        'wall_notified_at'     => "TIMESTAMP NULL DEFAULT NULL AFTER wall_token",
    ];
    foreach ($columns as $name => $definition) {
        if (empty($db->query("SHOW COLUMNS FROM waitlist LIKE '{$name}'")->fetchAll())) {
            $db->exec("ALTER TABLE waitlist ADD COLUMN {$name} {$definition}");
            echo "  [OK] Added waitlist.{$name}\n";
        } else {
            echo "  [SKIP] waitlist.{$name} already exists\n";
        }
    }
    if (empty($db->query("SHOW INDEX FROM waitlist WHERE Key_name = 'uniq_waitlist_wall_token'")->fetchAll())) {
        $db->exec("ALTER TABLE waitlist ADD UNIQUE INDEX uniq_waitlist_wall_token (wall_token)");
        echo "  [OK] Added unique index on waitlist.wall_token\n";
    }

    $db->exec("
        CREATE TABLE IF NOT EXISTS founders_wall_consent_log (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            waitlist_id INT UNSIGNED NOT NULL,
            consent TINYINT(1) NOT NULL COMMENT '1 = given, 0 = refused or withdrawn',
            consent_version VARCHAR(20) NOT NULL,
            source ENUM('waitlist_form','preferences') NOT NULL,
            locale ENUM('fr','en') NULL,
            ip_address VARCHAR(45) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_fwcl_waitlist (waitlist_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  [OK] founders_wall_consent_log ready\n";

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
