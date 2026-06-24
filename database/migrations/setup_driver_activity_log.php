<?php
/**
 * Migration: setup_driver_activity_log
 * Creates the driver_activity_log table to record every accept/decline event
 * from the ODA Flutter driver app.
 *
 * Run: php database/migrations/setup_driver_activity_log.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

try {
    $db = Database::getConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS driver_activity_log (
        id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        driver_id               BIGINT UNSIGNED NOT NULL,
        action                  ENUM('accepted','declined') NOT NULL,
        order_type              ENUM('marketplace','distribution') NOT NULL,
        order_id                BIGINT UNSIGNED NULL DEFAULT NULL,
        po_id                   BIGINT UNSIGNED NULL DEFAULT NULL,
        distribution_request_id INT NULL DEFAULT NULL,
        reference_number        VARCHAR(60) NULL DEFAULT NULL,
        reason                  VARCHAR(255) NULL DEFAULT NULL,
        created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        INDEX idx_driver  (driver_id),
        INDEX idx_action  (action),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "driver_activity_log table created (or already exists).\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
