<?php
/**
 * Migration: sync_user_role_column
 * Backfills users.role column for any users where it is 'buyer' (default)
 * but their user_roles table says otherwise (e.g. they registered as seller).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

// Update users whose role column is 'buyer' (the old default) but user_roles says something else
$stmt = $db->query("
    UPDATE users u
    INNER JOIN user_roles ur ON u.id = ur.user_id
    INNER JOIN roles r ON ur.role_id = r.id
    SET u.role = r.name
    WHERE u.role = 'buyer' AND r.name != 'buyer'
");

$affected = $stmt->rowCount();
echo "OK: Synced role column for {$affected} user(s) where user_roles differed from users.role.\n";

// Also ensure sellers with verification_status='verified' have status='active'
$stmt2 = $db->query("
    UPDATE users
    SET status = 'active'
    WHERE role = 'seller' AND verification_status = 'verified' AND status != 'active'
");
$affected2 = $stmt2->rowCount();
echo "OK: Activated {$affected2} verified seller(s) whose status was not active.\n";

echo "\nDone.\n";
