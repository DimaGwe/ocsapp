<?php
/**
 * Add registered_email to supplier_invites
 * Stores the email the supplier actually signed up with (may differ from the invited email).
 */

require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$db->exec("
    ALTER TABLE supplier_invites
    ADD COLUMN registered_email VARCHAR(255) NULL DEFAULT NULL
        COMMENT 'Email used during registration (may differ from invited email)'
        AFTER email
");

echo "Migration complete: registered_email added to supplier_invites\n";
