<?php
/**
 * Migration: distribution_credit_events audit log (Ecosystem Backend
 * Requirements Sec. 5)
 *
 * Every credit-affecting state change (qualification met, bureau/manual
 * review outcome, net-30 approval, plan change, suspension, reinstatement,
 * admin override) gets a row here - mirrors the existing
 * distribution_status_history pattern (changed_by_type system/admin/business)
 * already used for shipment status. This is a money-adjacent system; every
 * automated decision needs a durable trail, not just a mutated column.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $stmt = $db->query("SHOW TABLES LIKE 'distribution_credit_events'");
    if ($stmt->fetch()) {
        echo "distribution_credit_events already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE distribution_credit_events (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                business_profile_id INT NOT NULL,
                event_type VARCHAR(50) NOT NULL,
                changed_by_type ENUM('system','admin','business') NOT NULL,
                changed_by_id BIGINT UNSIGNED NULL,
                details TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_business_profile (business_profile_id),
                INDEX idx_event_type (event_type),
                CONSTRAINT fk_credit_events_business_profile FOREIGN KEY (business_profile_id)
                    REFERENCES business_profiles(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "distribution_credit_events table created.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
