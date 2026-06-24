<?php
/**
 * Newsletter Migration Runner
 */

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $pdo = db();
    $sql = file_get_contents(__DIR__ . '/create_newsletter_subscribers_table.sql');

    $pdo->exec($sql);
    echo "✅ Newsletter subscribers table created successfully!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
