<?php
require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $pdo = Database::getConnection();

    $columns = [
        'phone'               => "VARCHAR(30) NULL AFTER email",
        'seller_business_type'=> "VARCHAR(150) NULL",
        'seller_online_store' => "ENUM('yes','no') NULL",
        'supplier_products'   => "VARCHAR(255) NULL",
        'supplier_service_area' => "VARCHAR(150) NULL",
        'business_sector'     => "VARCHAR(150) NULL",
        'business_need'       => "ENUM('procurement','employee','delivery','other') NULL",
        'driver_area'         => "VARCHAR(150) NULL",
        'driver_vehicle'      => "ENUM('bike','car','van','other') NULL",
        'driver_availability' => "VARCHAR(255) NULL",
        'buyer_interest'      => "TEXT NULL",
        'partner_interest'    => "TEXT NULL",
        'marketing_consent'   => "TINYINT(1) NOT NULL DEFAULT 0",
        'utm_source'          => "VARCHAR(150) NULL",
        'utm_medium'          => "VARCHAR(150) NULL",
        'utm_campaign'        => "VARCHAR(150) NULL",
        'utm_content'         => "VARCHAR(150) NULL",
        'referral_source'     => "VARCHAR(150) NULL",
    ];

    foreach ($columns as $name => $def) {
        $stmt = $pdo->query("SHOW COLUMNS FROM waitlist LIKE '{$name}'");
        if ($stmt->fetch()) {
            echo "Column {$name} already exists on waitlist - skipping.\n";
            continue;
        }
        $pdo->exec("ALTER TABLE waitlist ADD COLUMN {$name} {$def}");
        echo "Added {$name} column to waitlist table.\n";
    }

    // Add 'partner' to the role enum
    $stmt = $pdo->query("SHOW COLUMNS FROM waitlist LIKE 'role'");
    $roleCol = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($roleCol && strpos($roleCol['Type'], "'partner'") === false) {
        $pdo->exec("ALTER TABLE waitlist MODIFY role ENUM('buyer','seller','supplier','driver','business','partner') NOT NULL");
        echo "Added 'partner' to waitlist.role enum.\n";
    } else {
        echo "waitlist.role already includes 'partner' - skipping.\n";
    }

    // Rename heard_about -> discovery_source, expanding the option set (old values preserved for history)
    $stmt = $pdo->query("SHOW COLUMNS FROM waitlist LIKE 'discovery_source'");
    if ($stmt->fetch()) {
        echo "Column discovery_source already exists on waitlist - skipping.\n";
    } else {
        $pdo->exec("
            ALTER TABLE waitlist
            CHANGE heard_about discovery_source ENUM(
                'social_media','friend_family','google_search','press','other',
                'representative','social','referral','local_business','event','web'
            ) NULL
        ");
        echo "Renamed heard_about to discovery_source on waitlist table (options expanded).\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
