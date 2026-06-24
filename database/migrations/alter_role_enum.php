<?php
/**
 * Alter users.role ENUM to include new admin tier roles
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

echo "=== Altering users.role ENUM ===\n\n";

try {
    $db = Database::getConnection();

    // Alter the ENUM to include new admin tier roles
    echo "Altering ENUM to add super_admin and admin_staff...\n";
    $db->exec("
        ALTER TABLE users
        MODIFY COLUMN role ENUM('super_admin', 'admin', 'admin_staff', 'seller', 'buyer', 'delivery', 'supplier', 'advertiser', 'affiliate')
        NOT NULL DEFAULT 'buyer'
    ");
    echo "ENUM altered successfully!\n\n";

    // Now update the super admin users
    echo "Updating super admin users...\n";

    $stmt = $db->prepare("UPDATE users SET role = 'super_admin' WHERE email = ?");

    $stmt->execute(['dima@ocsapp.ca']);
    echo "  dima@ocsapp.ca: {$stmt->rowCount()} rows updated\n";

    $stmt->execute(['jack@ocsapp.ca']);
    echo "  jack@ocsapp.ca: {$stmt->rowCount()} rows updated\n";

    // Verify
    echo "\nVerification:\n";
    $stmt = $db->query("SELECT email, role FROM users WHERE email IN ('dima@ocsapp.ca', 'jack@ocsapp.ca')");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['email']}: role = '{$row['role']}'\n";
    }

    echo "\n=== Complete! Please log out and log back in. ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
