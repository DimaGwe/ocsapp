<?php
/**
 * Upgrade dima@ocsapp.ca to super_admin
 *
 * Run: php database/migrations/upgrade_dima_to_super_admin.php
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

echo "=== Upgrading dima@ocsapp.ca to super_admin ===\n\n";

try {
    $pdo = Database::getConnection();

    // Find the user by email
    $stmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE email = ?");
    $stmt->execute(['dima@ocsapp.ca']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "ERROR: User dima@ocsapp.ca not found!\n";
        exit(1);
    }

    echo "Found user: {$user['first_name']} {$user['last_name']} (ID: {$user['id']})\n";

    // Get super_admin role id
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'super_admin'");
    $stmt->execute();
    $superAdminRole = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$superAdminRole) {
        echo "ERROR: super_admin role not found! Run setup_admin_tiers.php first.\n";
        exit(1);
    }

    echo "Found super_admin role (ID: {$superAdminRole['id']})\n";

    // Check if user already has super_admin role
    $stmt = $pdo->prepare("SELECT id FROM user_roles WHERE user_id = ? AND role_id = ?");
    $stmt->execute([$user['id'], $superAdminRole['id']]);

    if ($stmt->fetch()) {
        echo "\nUser already has super_admin role. Nothing to do.\n";
        exit(0);
    }

    // Add super_admin role to user
    $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user['id'], $superAdminRole['id']]);

    echo "\nSUCCESS: Upgraded {$user['email']} to super_admin!\n";

    // Also update the users table role column if it exists
    $stmt = $pdo->prepare("UPDATE users SET role = 'super_admin' WHERE id = ?");
    $stmt->execute([$user['id']]);
    echo "Updated users.role column to super_admin\n";

    echo "\n=== Upgrade Complete ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
