<?php
/**
 * OCS Vendor System Migration Runner
 *
 * Purpose: Execute vendor system database migration
 * This script creates all vendor-related tables and migrates existing brands to vendors
 *
 * Usage: php database/run_vendor_migration.php
 */

// Load environment from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Simple env() helper function
function env($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

echo "=====================================\n";
echo "OCS VENDOR SYSTEM MIGRATION\n";
echo "=====================================\n\n";

try {
    // Connect to database
    $host = env('DB_HOST');
    $dbname = env('DB_NAME');
    $username = env('DB_USER');
    $password = env('DB_PASS');

    echo "Connecting to database: {$dbname}@{$host}...\n";

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true // Allow multiple SQL statements
    ]);

    echo "✓ Connected successfully!\n\n";

    // Read migration SQL file
    $migrationFile = __DIR__ . '/migrations/create_vendor_system.sql';

    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: {$migrationFile}");
    }

    echo "Reading migration file...\n";
    $sql = file_get_contents($migrationFile);

    if (!$sql) {
        throw new Exception("Failed to read migration file");
    }

    echo "✓ Migration file loaded\n\n";

    // Execute migration
    echo "Executing vendor system migration...\n";
    echo "=====================================\n\n";

    // Split SQL into individual statements (simple approach)
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            // Filter out comments and empty statements
            return !empty($stmt) &&
                   !str_starts_with($stmt, '--') &&
                   !str_starts_with($stmt, '/*');
        }
    );

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $index => $statement) {
        // Skip comment lines
        if (str_starts_with(trim($statement), '--')) {
            continue;
        }

        try {
            $pdo->exec($statement);

            // Extract table name from CREATE TABLE or INSERT INTO
            if (preg_match('/CREATE TABLE.*?`(\w+)`/i', $statement, $matches)) {
                echo "✓ Created table: {$matches[1]}\n";
            } elseif (preg_match('/INSERT INTO.*?`(\w+)`/i', $statement, $matches)) {
                echo "✓ Migrated data to: {$matches[1]}\n";
            } elseif (preg_match('/ALTER TABLE.*?`(\w+)`/i', $statement, $matches)) {
                echo "✓ Altered table: {$matches[1]}\n";
            } elseif (preg_match('/SELECT/i', $statement)) {
                // This is the final success message query
                $result = $pdo->query($statement);
                if ($result) {
                    $row = $result->fetch();
                    if ($row) {
                        echo "\n=====================================\n";
                        echo "{$row['message']}\n";
                        echo "Total Vendors: {$row['total_vendors']}\n";
                        echo "Total Vendor Products: {$row['total_vendor_products']}\n";
                        echo "=====================================\n";
                    }
                }
            }

            $successCount++;

        } catch (PDOException $e) {
            // Check if error is due to table already existing
            if (str_contains($e->getMessage(), 'already exists')) {
                echo "⚠ Table already exists (skipped)\n";
            } elseif (str_contains($e->getMessage(), 'Duplicate entry')) {
                echo "⚠ Duplicate data (skipped)\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n\n";
                $errorCount++;
            }
        }
    }

    echo "\n=====================================\n";
    echo "MIGRATION SUMMARY\n";
    echo "=====================================\n";
    echo "Successful statements: {$successCount}\n";
    echo "Errors: {$errorCount}\n";

    if ($errorCount === 0) {
        echo "\n✓ Vendor system migration completed successfully!\n\n";

        // Display vendor statistics
        echo "=====================================\n";
        echo "VENDOR STATISTICS\n";
        echo "=====================================\n";

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM vendors");
        $vendorCount = $stmt->fetch()['count'];
        echo "Total Vendors: {$vendorCount}\n";

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM vendor_products");
        $productCount = $stmt->fetch()['count'];
        echo "Total Vendor Products: {$productCount}\n";

        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM vendors GROUP BY status");
        echo "\nVendors by Status:\n";
        while ($row = $stmt->fetch()) {
            echo "  - {$row['status']}: {$row['count']}\n";
        }

        echo "\n=====================================\n";
        echo "NEXT STEPS\n";
        echo "=====================================\n";
        echo "1. Review vendor accounts in database\n";
        echo "2. Set vendor passwords via admin panel\n";
        echo "3. Test vendor login at /vendor/login\n";
        echo "4. Configure vendor dashboard permissions\n";
        echo "5. Deploy to live server\n\n";

        exit(0);

    } else {
        echo "\n⚠ Migration completed with errors. Please review above.\n\n";
        exit(1);
    }

} catch (PDOException $e) {
    echo "\n✗ DATABASE ERROR:\n";
    echo $e->getMessage() . "\n\n";
    exit(1);

} catch (Exception $e) {
    echo "\n✗ ERROR:\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
}
