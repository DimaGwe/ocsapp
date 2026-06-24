<?php
/**
 * Add driver_status column to orders table.
 *
 * The main `status` column tracks the overall order lifecycle (web-side).
 * `driver_status` tracks the fine-grained ODA delivery step:
 *   accepted → heading_to_merchant → arrived_merchant → picked_up
 *   → en_route → arrived_customer → delivered
 *
 * Run: php database/migrations/add_driver_status_to_orders.php
 */

$envFile = __DIR__ . '/../../.env';
if (!file_exists($envFile)) {
    die("ERROR: .env file not found at $envFile\n");
}
$env = parse_ini_file($envFile);

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $env['DB_HOST'], $env['DB_PORT'] ?? 3306, $env['DB_NAME']);

try {
    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage() . "\n");
}

// Add driver_status column
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN driver_status VARCHAR(50) NULL DEFAULT NULL AFTER status");
    echo "OK: Added driver_status column to orders table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "SKIP: driver_status column already exists.\n";
    } else {
        die("ERROR: " . $e->getMessage() . "\n");
    }
}

// Add index for faster driver queries
try {
    $pdo->exec("ALTER TABLE orders ADD INDEX idx_driver_status (driver_status)");
    echo "OK: Added index on driver_status.\n";
} catch (PDOException $e) {
    echo "SKIP: Index may already exist.\n";
}

echo "Migration complete.\n";
