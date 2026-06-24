<?php
/**
 * Add driver_acceptance_status to purchase_orders.
 *
 * NULL     = newly assigned, driver hasn't responded yet
 * accepted = driver accepted — heading to supplier
 * declined = driver declined — auto-reassigned or admin notified
 *
 * Run: php database/migrations/add_pickup_acceptance_status.php
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

try {
    $pdo->exec("
        ALTER TABLE purchase_orders
        ADD COLUMN driver_acceptance_status ENUM('accepted','declined') NULL DEFAULT NULL
            AFTER assigned_driver_id
    ");
    echo "OK: Added driver_acceptance_status column.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "SKIP: Column already exists.\n";
    } else {
        die("ERROR: " . $e->getMessage() . "\n");
    }
}

try {
    $pdo->exec("
        ALTER TABLE purchase_orders
        ADD INDEX idx_driver_acceptance (assigned_driver_id, driver_acceptance_status)
    ");
    echo "OK: Added index.\n";
} catch (PDOException $e) {
    echo "SKIP: Index may already exist.\n";
}

echo "Migration complete.\n";
