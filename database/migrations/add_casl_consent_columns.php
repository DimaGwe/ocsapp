<?php
/**
 * Migration: Add CASL consent columns to newsletter_subscribers
 *
 * CASL (Canada's Anti-Spam Legislation) requires that consent records
 * include the exact consent text shown to the subscriber and a version
 * identifier so that re-consent can be triggered when the text changes.
 */
require_once __DIR__ . '/../../bootstrap/init.php';

$db = Database::getConnection();

// Add consent_text — stores the exact wording displayed at subscribe time
try {
    $db->exec("ALTER TABLE newsletter_subscribers ADD COLUMN consent_text TEXT NULL AFTER email");
    echo "Added column: consent_text\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column consent_text already exists — skipped\n";
    } else {
        throw $e;
    }
}

// Add consent_version — e.g. "v1", "v2" bumped when consent wording changes
try {
    $db->exec("ALTER TABLE newsletter_subscribers ADD COLUMN consent_version VARCHAR(20) NULL DEFAULT 'v1' AFTER consent_text");
    echo "Added column: consent_version\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column consent_version already exists — skipped\n";
    } else {
        throw $e;
    }
}

// Back-fill existing rows so they have a version marker
$db->exec("UPDATE newsletter_subscribers SET consent_version = 'v1' WHERE consent_version IS NULL");
echo "Back-filled consent_version = 'v1' on existing rows\n";

echo "Done.\n";
