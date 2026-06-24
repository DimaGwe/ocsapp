<?php
/**
 * Database Configuration and Connection Class
 */

class Database {
    private static $connection = null;

    /**
     * Get PDO database connection (Singleton)
     */
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $host = env('DB_HOST', 'localhost');
            $dbname = env('DB_NAME', 'ocs_marketplace');
            $username = env('DB_USER', 'root');
            $password = env('DB_PASS', '');
            $port = env('DB_PORT', '3306');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            try {
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                // Ensure UTF-8 encoding
                self::$connection->exec("SET NAMES utf8mb4");
                self::$connection->exec("SET CHARACTER SET utf8mb4");

                // Set session timezone using UTC offset (avoids MySQL needing named timezone tables)
                $phpTz = new DateTimeZone(env('TIMEZONE', 'America/Toronto'));
                $offsetSec = $phpTz->getOffset(new DateTime());
                $sign = $offsetSec >= 0 ? '+' : '-';
                $tzOffset = sprintf('%s%02d:%02d', $sign, abs((int)($offsetSec / 3600)), abs(($offsetSec % 3600) / 60));
                self::$connection->exec("SET time_zone = '{$tzOffset}'");
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection failed");
            }
        }

        return self::$connection;
    }
}

/**
 * Helper function to get database connection
 */
function db(): PDO {
    return Database::getConnection();
}
