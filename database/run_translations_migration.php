<?php
/**
 * Run Translations Table Migration
 *
 * Usage: php run_translations_migration.php
 * Or via browser: https://ocsapp.ca/database/run_translations_migration.php
 */

// Prevent web access in production (optional - remove if you want browser access)
// if (php_sapi_name() !== 'cli') {
//     die('CLI only');
// }

echo "<pre>\n";
echo "===========================================\n";
echo "OCS Translations Table Migration\n";
echo "===========================================\n\n";

// Load environment
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database connection
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'marketplace_db';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

echo "Connecting to database: {$dbname}@{$host}\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    echo "Connected successfully!\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Read migration file
$migrationFile = __DIR__ . '/migrations/create_translations_table.sql';
if (!file_exists($migrationFile)) {
    die("Migration file not found: {$migrationFile}\n");
}

echo "Reading migration file...\n";
$sql = file_get_contents($migrationFile);

// Split into statements
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function($s) { return !empty($s) && strpos($s, '--') !== 0; }
);

echo "Found " . count($statements) . " SQL statements to execute.\n\n";

// Execute each statement
$successCount = 0;
$errorCount = 0;

foreach ($statements as $i => $statement) {
    if (empty(trim($statement))) continue;

    // Get first line for display
    $firstLine = strtok($statement, "\n");
    echo "Executing: " . substr($firstLine, 0, 60) . "...\n";

    try {
        $pdo->exec($statement);
        $successCount++;
        echo "  OK\n";
    } catch (PDOException $e) {
        // Check if it's a duplicate key error (already exists)
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo "  SKIPPED (already exists)\n";
            $successCount++;
        } else {
            echo "  ERROR: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
}

echo "\n===========================================\n";
echo "Migration Complete!\n";
echo "Success: {$successCount}\n";
echo "Errors: {$errorCount}\n";
echo "===========================================\n";

// Verify table
echo "\nVerifying translations table...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM translations");
    $result = $stmt->fetch();
    echo "Translations table has {$result['count']} records.\n";

    // Show categories
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM translations GROUP BY category ORDER BY category");
    $categories = $stmt->fetchAll();

    echo "\nTranslations by category:\n";
    foreach ($categories as $cat) {
        echo "  - {$cat['category']}: {$cat['count']} keys\n";
    }

} catch (PDOException $e) {
    echo "Error verifying: " . $e->getMessage() . "\n";
}

echo "\n</pre>";
