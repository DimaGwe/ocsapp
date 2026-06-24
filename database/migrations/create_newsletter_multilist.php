<?php
/**
 * Migration: Multi-list newsletter system
 *
 * Turns the single flat newsletter_subscribers list into a multi-list system:
 *   - newsletter_lists         : the available newsletters (General + 5 portals)
 *   - newsletter_subscriptions : which subscriber is on which list (per-list CASL consent)
 *   - newsletter_campaigns     : admin-composed emails sent to a list (logged)
 *   - newsletter_subscribers   : + unsubscribe_token for tokenized unsubscribe/preferences
 *
 * Additive and idempotent — safe to re-run. Existing active subscribers are
 * migrated onto the General list so nobody loses their subscription.
 *
 * Run:  php database/migrations/create_newsletter_multilist.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

echo "== Newsletter multi-list migration ==\n";

/* ---------------------------------------------------------------------------
 * 1. newsletter_lists
 * ------------------------------------------------------------------------- */
$db->exec("
    CREATE TABLE IF NOT EXISTS `newsletter_lists` (
        `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `slug`           VARCHAR(50)  NOT NULL UNIQUE,
        `name_en`        VARCHAR(120) NOT NULL,
        `name_fr`        VARCHAR(120) NOT NULL,
        `description_en` VARCHAR(255) NULL,
        `description_fr` VARCHAR(255) NULL,
        `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
        `sort_order`     INT          NOT NULL DEFAULT 0,
        `created_at`     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_active` (`is_active`),
        INDEX `idx_sort`   (`sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Table newsletter_lists ready.\n";

// Seed the 6 lists (idempotent — slug is unique, update labels on re-run)
$lists = [
    ['general',      'OCSAPP Inc',         'OCSAPP Inc',           'Company news, updates and announcements from OCSAPP.',        'Nouvelles, mises à jour et annonces d\'OCSAPP.',                       0],
    ['buyer',        'Buyers',             'Acheteurs',            'Deals, new shops and products on the OCSAPP marketplace.',     'Aubaines, nouvelles boutiques et produits sur le marché OCSAPP.',     1],
    ['seller',       'Sellers',            'Vendeurs',             'Tips, features and updates for sellers on OCSAPP.',            'Conseils, fonctionnalités et mises à jour pour les vendeurs OCSAPP.', 2],
    ['supplier',     'Suppliers',          'Fournisseurs',         'B2B supply opportunities and supplier program updates.',       'Occasions d\'approvisionnement B2B et mises à jour du programme fournisseur.', 3],
    ['distribution', 'Distribution',       'Distribution',         'Bulk procurement and distribution program updates.',          'Approvisionnement en gros et mises à jour du programme de distribution.', 4],
    ['driver',       'Drivers',            'Livreurs',             'Driver opportunities, earnings and program news.',            'Occasions pour livreurs, gains et nouvelles du programme.',           5],
];

$seed = $db->prepare("
    INSERT INTO newsletter_lists (slug, name_en, name_fr, description_en, description_fr, sort_order)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        name_en = VALUES(name_en),
        name_fr = VALUES(name_fr),
        description_en = VALUES(description_en),
        description_fr = VALUES(description_fr),
        sort_order = VALUES(sort_order)
");
foreach ($lists as $l) {
    $seed->execute($l);
}
echo "Seeded " . count($lists) . " lists.\n";

/* ---------------------------------------------------------------------------
 * 2. newsletter_subscribers : add unsubscribe_token
 * ------------------------------------------------------------------------- */
try {
    $db->exec("ALTER TABLE newsletter_subscribers ADD COLUMN unsubscribe_token VARCHAR(64) NULL AFTER email");
    echo "Added column: newsletter_subscribers.unsubscribe_token\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column unsubscribe_token already exists - skipped\n";
    } else {
        throw $e;
    }
}

// Unique index on the token (guard against duplicate-index error on re-run)
try {
    $db->exec("ALTER TABLE newsletter_subscribers ADD UNIQUE KEY `idx_unsub_token` (`unsubscribe_token`)");
    echo "Added unique index on unsubscribe_token\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
        echo "Index idx_unsub_token already exists - skipped\n";
    } else {
        throw $e;
    }
}

// Ensure CASL consent columns exist (the consent migration may not have run on every
// environment). Mirrors add_casl_consent_columns.php so this migration is self-contained.
try {
    $db->exec("ALTER TABLE newsletter_subscribers ADD COLUMN consent_text TEXT NULL AFTER unsubscribe_token");
    echo "Added column: newsletter_subscribers.consent_text\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column consent_text already exists - skipped\n";
    } else {
        throw $e;
    }
}
try {
    $db->exec("ALTER TABLE newsletter_subscribers ADD COLUMN consent_version VARCHAR(20) NULL DEFAULT 'v1' AFTER consent_text");
    echo "Added column: newsletter_subscribers.consent_version\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column consent_version already exists - skipped\n";
    } else {
        throw $e;
    }
}

// Backfill tokens for any subscriber missing one
$missing = $db->query("SELECT id FROM newsletter_subscribers WHERE unsubscribe_token IS NULL OR unsubscribe_token = ''")->fetchAll(\PDO::FETCH_COLUMN);
if ($missing) {
    $upd = $db->prepare("UPDATE newsletter_subscribers SET unsubscribe_token = ? WHERE id = ?");
    foreach ($missing as $id) {
        $upd->execute([bin2hex(random_bytes(24)), $id]);
    }
    echo "Backfilled unsubscribe_token on " . count($missing) . " existing subscriber(s).\n";
} else {
    echo "No subscribers needed a token backfill.\n";
}

/* ---------------------------------------------------------------------------
 * 3. newsletter_subscriptions
 * ------------------------------------------------------------------------- */
$db->exec("
    CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
        `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `subscriber_id`   INT UNSIGNED NOT NULL,
        `list_id`         INT UNSIGNED NOT NULL,
        `status`          ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
        `consent_text`    TEXT NULL,
        `consent_version` VARCHAR(20) NULL DEFAULT 'v1',
        `subscribed_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL,
        UNIQUE KEY `uniq_sub_list` (`subscriber_id`, `list_id`),
        INDEX `idx_list_status` (`list_id`, `status`),
        CONSTRAINT `fk_ns_subscriber` FOREIGN KEY (`subscriber_id`)
            REFERENCES `newsletter_subscribers` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_ns_list` FOREIGN KEY (`list_id`)
            REFERENCES `newsletter_lists` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Table newsletter_subscriptions ready.\n";

// Migrate existing active subscribers onto the General list
$generalId = (int) $db->query("SELECT id FROM newsletter_lists WHERE slug = 'general' LIMIT 1")->fetchColumn();
if ($generalId) {
    $migrated = $db->exec("
        INSERT IGNORE INTO newsletter_subscriptions
            (subscriber_id, list_id, status, consent_text, consent_version, subscribed_at)
        SELECT s.id, {$generalId}, 'active', s.consent_text, COALESCE(s.consent_version, 'v1'), s.subscribed_at
        FROM newsletter_subscribers s
        WHERE s.status = 'active'
    ");
    echo "Migrated {$migrated} existing active subscriber(s) onto the General list.\n";
} else {
    echo "WARNING: General list not found - skipped subscriber migration.\n";
}

/* ---------------------------------------------------------------------------
 * 4. newsletter_campaigns
 * ------------------------------------------------------------------------- */
$db->exec("
    CREATE TABLE IF NOT EXISTS `newsletter_campaigns` (
        `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `list_id`         INT UNSIGNED NOT NULL,
        `subject_en`      VARCHAR(255) NOT NULL,
        `subject_fr`      VARCHAR(255) NOT NULL,
        `body_en`         LONGTEXT NOT NULL,
        `body_fr`         LONGTEXT NOT NULL,
        `status`          ENUM('draft','sending','sent','failed') NOT NULL DEFAULT 'draft',
        `recipient_count` INT NOT NULL DEFAULT 0,
        `sent_count`      INT NOT NULL DEFAULT 0,
        `created_by`      BIGINT UNSIGNED NULL,
        `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `sent_at`         TIMESTAMP NULL DEFAULT NULL,
        INDEX `idx_list`   (`list_id`),
        INDEX `idx_status` (`status`),
        CONSTRAINT `fk_nc_list` FOREIGN KEY (`list_id`)
            REFERENCES `newsletter_lists` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "Table newsletter_campaigns ready.\n";

echo "== Done. ==\n";
