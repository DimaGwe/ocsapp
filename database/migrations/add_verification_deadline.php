<?php
/**
 * Migration: Add verification_deadline column to suppliers table
 * Tracks the 30-day window for pending_verification suppliers to complete verification
 */
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Add verification_deadline column
    $stmt = $db->query("SHOW COLUMNS FROM suppliers LIKE 'verification_deadline'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE suppliers ADD COLUMN verification_deadline DATETIME NULL AFTER status");
        echo "Added 'verification_deadline' column to suppliers table.\n";
    } else {
        echo "'verification_deadline' column already exists.\n";
    }

    // Add verification_reminder_sent column to track which reminders were sent
    $stmt = $db->query("SHOW COLUMNS FROM suppliers LIKE 'verification_reminder_sent'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE suppliers ADD COLUMN verification_reminder_sent TINYINT DEFAULT 0 AFTER verification_deadline");
        echo "Added 'verification_reminder_sent' column to suppliers table.\n";
    } else {
        echo "'verification_reminder_sent' column already exists.\n";
    }

    // Add 'expired' to supplier_applications status enum
    $stmt = $db->query("SHOW COLUMNS FROM supplier_applications LIKE 'status'");
    $col = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($col && strpos($col['Type'], 'expired') === false) {
        $db->exec("ALTER TABLE supplier_applications MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'info_requested', 'expired') DEFAULT 'pending'");
        echo "Added 'expired' to supplier_applications status enum.\n";
    } else {
        echo "'expired' already in supplier_applications status enum.\n";
    }

    // Backfill: Set deadline for any existing pending_verification suppliers
    $updated = $db->exec("
        UPDATE suppliers
        SET verification_deadline = DATE_ADD(created_at, INTERVAL 30 DAY)
        WHERE status = 'pending_verification' AND verification_deadline IS NULL
    ");
    echo "Backfilled {$updated} existing pending_verification suppliers with 30-day deadline.\n";

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
