<?php
/**
 * Migration: add 'buyer_terms' to legal_content.page_type.
 *
 * The General Terms of Service (Sec 3) name the Buyer Terms of Service as the
 * Role-Specific Agreement for Buyers, but no such page existed. page_type is
 * an ENUM, so the value must be added before any row can be inserted. The
 * current ENUM is read from the schema and extended, so values added on the
 * server since (e.g. 'accessibility') are kept.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $type = $db->query("
        SELECT COLUMN_TYPE FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'legal_content' AND COLUMN_NAME = 'page_type'
    ")->fetchColumn();

    if (!$type || stripos($type, 'enum(') !== 0) {
        throw new RuntimeException('legal_content.page_type is not an ENUM: ' . var_export($type, true));
    }
    if (strpos($type, "'buyer_terms'") !== false) {
        echo "page_type already includes buyer_terms, skipping.\n";
    } else {
        $new = rtrim($type, ')') . ",'buyer_terms')";
        $db->exec("ALTER TABLE legal_content MODIFY COLUMN page_type {$new} NOT NULL");
        echo "page_type extended: {$new}\n";
    }

    echo "Done.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
