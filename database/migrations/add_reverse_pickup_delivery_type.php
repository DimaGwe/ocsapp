<?php
/**
 * Migration: add 'reverse_pickup' to delivery_assignments.delivery_type
 * (Ecosystem Backend Requirements Sec. 4.4 Instant Reverse Routing)
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $row = $db->query("SHOW COLUMNS FROM delivery_assignments LIKE 'delivery_type'")->fetch(PDO::FETCH_ASSOC);
    if ($row && strpos($row['Type'], "'reverse_pickup'") !== false) {
        echo "delivery_assignments.delivery_type already includes 'reverse_pickup'. Skipping.\n";
    } else {
        $db->exec("ALTER TABLE delivery_assignments MODIFY COLUMN delivery_type ENUM('order','distribution','reverse_pickup') NOT NULL DEFAULT 'order'");
        echo "delivery_assignments.delivery_type extended with 'reverse_pickup'.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
