<?php
/**
 * Admin Tiers Migration
 * Adds super_admin and admin_staff roles to the roles table
 *
 * Run: php database/migrations/setup_admin_tiers.php
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

echo "=== Admin Tiers Migration ===\n\n";

try {
    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    // Check if roles table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'roles'");
    if ($tableCheck->rowCount() === 0) {
        throw new Exception("Roles table does not exist. Please run the main database migration first.");
    }

    // Check if super_admin role already exists
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
    $stmt->execute(['super_admin']);

    if ($stmt->fetch()) {
        echo "super_admin role already exists. Skipping...\n";
    } else {
        // Insert super_admin role
        $stmt = $pdo->prepare("
            INSERT INTO roles (name, display_name, description, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            'super_admin',
            'Super Administrator',
            'Full system access including settings, content management, and user administration'
        ]);
        echo "Created role: super_admin\n";
    }

    // Check if admin_staff role already exists
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
    $stmt->execute(['admin_staff']);

    if ($stmt->fetch()) {
        echo "admin_staff role already exists. Skipping...\n";
    } else {
        // Insert admin_staff role
        $stmt = $pdo->prepare("
            INSERT INTO roles (name, display_name, description, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            'admin_staff',
            'Admin Staff',
            'Limited admin access for day-to-day operations like orders, products, and delivery'
        ]);
        echo "Created role: admin_staff\n";
    }

    // Update existing admin role description if needed
    $stmt = $pdo->prepare("
        UPDATE roles
        SET description = 'Standard admin access - most features except system settings and content management',
            updated_at = NOW()
        WHERE name = 'admin'
    ");
    $stmt->execute();
    echo "Updated admin role description\n";

    // Upgrade existing admin users to super_admin (optional - for first setup)
    // This ensures the primary admin doesn't lose access
    $stmt = $pdo->query("
        SELECT ur.user_id, u.email
        FROM user_roles ur
        JOIN roles r ON ur.role_id = r.id
        JOIN users u ON ur.user_id = u.id
        WHERE r.name = 'admin'
        LIMIT 1
    ");
    $firstAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($firstAdmin) {
        // Get super_admin role id
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'super_admin'");
        $stmt->execute();
        $superAdminRole = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($superAdminRole) {
            // Check if user already has super_admin role
            $stmt = $pdo->prepare("
                SELECT id FROM user_roles
                WHERE user_id = ? AND role_id = ?
            ");
            $stmt->execute([$firstAdmin['user_id'], $superAdminRole['id']]);

            if (!$stmt->fetch()) {
                // Add super_admin role to first admin user
                $stmt = $pdo->prepare("
                    INSERT INTO user_roles (user_id, role_id, created_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$firstAdmin['user_id'], $superAdminRole['id']]);
                echo "Upgraded user '{$firstAdmin['email']}' to super_admin\n";
            }
        }
    }

    $pdo->commit();

    echo "\n=== Migration Complete ===\n";
    echo "\nAdmin Tiers:\n";
    echo "  1. super_admin - Full access (system, content, users)\n";
    echo "  2. admin - Standard access (no system/content)\n";
    echo "  3. admin_staff - Limited access (orders, products, delivery)\n";

    // Display current roles
    echo "\nCurrent roles in database:\n";
    $stmt = $pdo->query("SELECT id, name, display_name FROM roles ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  [{$row['id']}] {$row['name']} - {$row['display_name']}\n";
    }

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
