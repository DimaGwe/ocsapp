<?php
/**
 * Driver Hiring Pipeline Migration
 *
 * - Expands driver_applications: adds user_id, lead_id, pipeline_stage,
 *   interview_proposed_times (JSON), interview_selected_time, rejection_reason,
 *   reviewed_by, and extends status enum to 6 values.
 * - Creates driver_application_messages table (two-way messaging).
 * - Adds 'driver' to leads.interest_type enum (if leads table exists).
 * - Adds 'pending'/'rejected' to users.status enum (if not already there).
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Starting driver hiring pipeline migration...\n\n";

// ─── 1. Alter driver_applications ────────────────────────────────────────────

$columns = $db->query("SHOW COLUMNS FROM driver_applications")->fetchAll(PDO::FETCH_COLUMN);

$alterClauses = [];

if (!in_array('user_id', $columns)) {
    $alterClauses[] = "ADD COLUMN user_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER id";
}
if (!in_array('lead_id', $columns)) {
    $alterClauses[] = "ADD COLUMN lead_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER user_id";
}
if (!in_array('pipeline_stage', $columns)) {
    $alterClauses[] = "ADD COLUMN pipeline_stage ENUM('submitted','under_review','interview_requested','interview_scheduled','approved','rejected') NOT NULL DEFAULT 'submitted' AFTER status";
}
if (!in_array('interview_proposed_times', $columns)) {
    $alterClauses[] = "ADD COLUMN interview_proposed_times JSON NULL AFTER pipeline_stage";
}
if (!in_array('interview_selected_time', $columns)) {
    $alterClauses[] = "ADD COLUMN interview_selected_time DATETIME NULL AFTER interview_proposed_times";
}
if (!in_array('interview_notes', $columns)) {
    $alterClauses[] = "ADD COLUMN interview_notes TEXT NULL AFTER interview_selected_time";
}
if (!in_array('rejection_reason', $columns)) {
    $alterClauses[] = "ADD COLUMN rejection_reason TEXT NULL AFTER interview_notes";
}
if (!in_array('reviewed_by', $columns)) {
    $alterClauses[] = "ADD COLUMN reviewed_by BIGINT UNSIGNED NULL DEFAULT NULL AFTER rejection_reason";
}
if (!in_array('reviewed_at', $columns)) {
    $alterClauses[] = "ADD COLUMN reviewed_at DATETIME NULL AFTER reviewed_by";
}
if (!in_array('reapply_after', $columns)) {
    $alterClauses[] = "ADD COLUMN reapply_after DATE NULL DEFAULT NULL AFTER reviewed_at";
}

// Extend status enum
$statusEnumRow = $db->query("
    SELECT COLUMN_TYPE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'driver_applications'
    AND COLUMN_NAME = 'status'
")->fetch(PDO::FETCH_ASSOC);

$currentEnum = $statusEnumRow['COLUMN_TYPE'] ?? '';
$needsEnumExpansion = strpos($currentEnum, 'interview_requested') === false;
if ($needsEnumExpansion) {
    $alterClauses[] = "MODIFY COLUMN status ENUM('pending','under_review','interview_requested','interview_scheduled','approved','rejected') NOT NULL DEFAULT 'pending'";
}

if (!empty($alterClauses)) {
    $sql = "ALTER TABLE driver_applications " . implode(", ", $alterClauses);
    $db->exec($sql);
    echo "✓ driver_applications altered.\n";
} else {
    echo "  driver_applications already up to date.\n";
}

// ─── 2. Create driver_application_messages ───────────────────────────────────

$tableCheck = $db->query("SHOW TABLES LIKE 'driver_application_messages'")->rowCount();
if (!$tableCheck) {
    $db->exec("
        CREATE TABLE driver_application_messages (
          id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          application_id BIGINT UNSIGNED NOT NULL,
          sender_type    ENUM('admin','applicant') NOT NULL,
          sender_id      BIGINT UNSIGNED NULL,
          message        TEXT NOT NULL,
          is_read        TINYINT(1) NOT NULL DEFAULT 0,
          created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_app (application_id),
          INDEX idx_unread (application_id, is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ driver_application_messages table created.\n";
} else {
    echo "  driver_application_messages already exists.\n";
}

// ─── 3. Add 'driver' to leads.interest_type enum (if table exists) ───────────

$leadsExists = $db->query("SHOW TABLES LIKE 'leads'")->rowCount();
if ($leadsExists) {
    $leadsEnum = $db->query("
        SELECT COLUMN_TYPE FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'leads'
        AND COLUMN_NAME = 'interest_type'
    ")->fetch(PDO::FETCH_ASSOC);

    if ($leadsEnum && strpos($leadsEnum['COLUMN_TYPE'], 'driver') === false) {
        // Rebuild enum to include 'driver'
        $db->exec("
            ALTER TABLE leads
            MODIFY COLUMN interest_type
              ENUM('buyer','seller','supplier','driver','advertiser','affiliate','other') DEFAULT 'buyer'
        ");
        echo "✓ leads.interest_type enum extended with 'driver'.\n";
    } else {
        echo "  leads.interest_type already includes 'driver'.\n";
    }
}

// ─── 4. Extend users.status enum to include 'rejected' ───────────────────────

$usersStatusEnum = $db->query("
    SELECT COLUMN_TYPE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'status'
")->fetch(PDO::FETCH_ASSOC);

if ($usersStatusEnum && strpos($usersStatusEnum['COLUMN_TYPE'], 'rejected') === false) {
    $db->exec("
        ALTER TABLE users
        MODIFY COLUMN status ENUM('active','inactive','pending','suspended','rejected') NOT NULL DEFAULT 'active'
    ");
    echo "✓ users.status enum extended with 'pending' and 'rejected'.\n";
} else {
    echo "  users.status already has required values.\n";
}

echo "\nDriver hiring pipeline migration complete.\n";
