<?php
/**
 * Migration: business_activity_log + business_email_log
 * Idempotent — safe to re-run.
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

$db = Database::getConnection();

$db->exec("
    CREATE TABLE IF NOT EXISTS business_activity_log (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT NOT NULL,
        actor       ENUM('admin','system','business') NOT NULL DEFAULT 'system',
        actor_name  VARCHAR(100) DEFAULT NULL,
        action_type VARCHAR(60)  NOT NULL,
        description TEXT         NOT NULL,
        created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_bal_business (business_id),
        INDEX idx_bal_created  (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "business_activity_log: OK\n";

$db->exec("
    CREATE TABLE IF NOT EXISTS business_email_log (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        business_id INT NOT NULL,
        subject     VARCHAR(255) NOT NULL,
        preview     TEXT,
        sent_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_bel_business (business_id),
        INDEX idx_bel_sent     (sent_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "business_email_log: OK\n";

echo "Migration complete.\n";
