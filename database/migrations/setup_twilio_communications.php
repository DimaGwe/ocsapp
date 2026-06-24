<?php
/**
 * Twilio Communications Database Setup
 * Creates tables for tracking calls and SMS messages in the CRM
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    echo "Setting up Twilio Communications tables...\n\n";

    // 1. Lead Communications Table - Stores all calls and SMS
    echo "Creating lead_communications table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS lead_communications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            lead_id BIGINT UNSIGNED NOT NULL,
            type ENUM('call', 'sms') NOT NULL,
            direction ENUM('inbound', 'outbound') NOT NULL DEFAULT 'outbound',
            phone_number VARCHAR(20) NOT NULL,

            -- Status tracking
            status VARCHAR(50) NOT NULL DEFAULT 'initiated',
            -- Call statuses: initiated, ringing, in-progress, completed, busy, no-answer, canceled, failed
            -- SMS statuses: queued, sent, delivered, undelivered, failed, received

            -- Call specific
            duration INT UNSIGNED NULL COMMENT 'Call duration in seconds',
            answered_by VARCHAR(50) NULL COMMENT 'human, machine, or null',
            recording_url VARCHAR(500) NULL,

            -- SMS specific
            content TEXT NULL COMMENT 'SMS body or call notes',

            -- Twilio reference
            twilio_sid VARCHAR(50) NULL,

            -- Metadata
            outcome VARCHAR(100) NULL COMMENT 'User-entered outcome: interested, not-interested, callback, etc.',
            notes TEXT NULL COMMENT 'Additional notes about the communication',

            -- Tracking
            created_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,

            INDEX idx_lead_id (lead_id),
            INDEX idx_type (type),
            INDEX idx_direction (direction),
            INDEX idx_status (status),
            INDEX idx_twilio_sid (twilio_sid),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ lead_communications table created\n\n";

    // 2. SMS Templates Table
    echo "Creating sms_templates table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS sms_templates (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            name_fr VARCHAR(100) NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            body TEXT NOT NULL,
            body_fr TEXT NULL,
            category VARCHAR(50) DEFAULT 'general',
            is_active BOOLEAN DEFAULT TRUE,
            use_count INT UNSIGNED DEFAULT 0,
            created_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_category (category),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ sms_templates table created\n\n";

    // 3. Insert default SMS templates
    echo "Inserting default SMS templates...\n";
    $templates = [
        [
            'name' => 'Follow-up',
            'name_fr' => 'Suivi',
            'slug' => 'follow_up',
            'body' => 'Hi {name}, following up on OCSAPP. Do you have 10 mins this week to chat? - {sender}',
            'body_fr' => 'Bonjour {name}, je fais suite à OCSAPP. Avez-vous 10 mins cette semaine pour discuter? - {sender}',
            'category' => 'outreach'
        ],
        [
            'name' => 'Meeting Reminder',
            'name_fr' => 'Rappel de rendez-vous',
            'slug' => 'meeting_reminder',
            'body' => 'Hi {name}, reminder about our meeting tomorrow. Looking forward to it! - {sender}',
            'body_fr' => 'Bonjour {name}, rappel de notre rendez-vous demain. Au plaisir! - {sender}',
            'category' => 'reminder'
        ],
        [
            'name' => 'Thank You',
            'name_fr' => 'Remerciement',
            'slug' => 'thank_you',
            'body' => 'Hi {name}, thanks for your time today! I\'ll send over the info we discussed. - {sender}',
            'body_fr' => 'Bonjour {name}, merci pour votre temps! Je vous envoie les infos discutées. - {sender}',
            'category' => 'follow_up'
        ],
        [
            'name' => 'Missed Call',
            'name_fr' => 'Appel manqué',
            'slug' => 'missed_call',
            'body' => 'Hi {name}, I just tried calling about OCSAPP. When\'s a good time to connect? - {sender}',
            'body_fr' => 'Bonjour {name}, j\'ai essayé de vous appeler. Quand puis-je vous rejoindre? - {sender}',
            'category' => 'outreach'
        ],
        [
            'name' => 'Quick Question',
            'name_fr' => 'Question rapide',
            'slug' => 'quick_question',
            'body' => 'Hi {name}, quick question - are you still interested in adding delivery to your business? - {sender}',
            'body_fr' => 'Bonjour {name}, question rapide - êtes-vous toujours intéressé par la livraison? - {sender}',
            'category' => 'outreach'
        ],
        [
            'name' => 'Founding Partner Invite',
            'name_fr' => 'Invitation Partenaire Fondateur',
            'slug' => 'founding_invite',
            'body' => 'Hi {name}, we have a few Founding Partner spots left for OCSAPP (8% commission). Interested? - {sender}',
            'body_fr' => 'Bonjour {name}, il reste quelques places de Partenaire Fondateur OCSAPP (8% commission). Intéressé? - {sender}',
            'category' => 'outreach'
        ],
        [
            'name' => 'Schedule Call',
            'name_fr' => 'Planifier un appel',
            'slug' => 'schedule_call',
            'body' => 'Hi {name}, would tomorrow at {time} work for a quick 10-min call about OCSAPP? - {sender}',
            'body_fr' => 'Bonjour {name}, demain à {time} vous conviendrait pour un appel de 10 min? - {sender}',
            'category' => 'scheduling'
        ],
        [
            'name' => 'Welcome Aboard',
            'name_fr' => 'Bienvenue',
            'slug' => 'welcome',
            'body' => 'Welcome to OCSAPP, {name}! Your onboarding specialist will contact you within 24hrs. Questions? Reply here! - OCSAPP',
            'body_fr' => 'Bienvenue chez OCSAPP, {name}! Votre spécialiste vous contactera dans 24h. Questions? Répondez ici! - OCSAPP',
            'category' => 'onboarding'
        ]
    ];

    $stmt = $db->prepare("
        INSERT IGNORE INTO sms_templates (name, name_fr, slug, body, body_fr, category)
        VALUES (:name, :name_fr, :slug, :body, :body_fr, :category)
    ");

    foreach ($templates as $template) {
        $stmt->execute($template);
    }
    echo "✓ Default SMS templates inserted\n\n";

    // 4. Twilio Settings Table
    echo "Creating twilio_settings table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS twilio_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT NULL,
            description VARCHAR(255) NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Insert default settings
    $settings = [
        ['auto_sms_after_missed_call', '0', 'Automatically send SMS after missed call (0=disabled, 1=enabled)'],
        ['auto_sms_template', 'missed_call', 'Template to use for auto-SMS after missed call'],
        ['call_recording_enabled', '0', 'Enable call recording (0=disabled, 1=enabled)'],
        ['default_caller_name', 'OCSAPP', 'Name to display in SMS signatures'],
        ['business_hours_start', '09:00', 'Start of business hours for calls'],
        ['business_hours_end', '18:00', 'End of business hours for calls'],
        ['sms_opt_out_keywords', 'STOP,UNSUBSCRIBE,CANCEL,ARRET', 'Keywords that trigger opt-out']
    ];

    $stmt = $db->prepare("
        INSERT IGNORE INTO twilio_settings (setting_key, setting_value, description)
        VALUES (:key, :value, :desc)
    ");

    foreach ($settings as [$key, $value, $desc]) {
        $stmt->execute(['key' => $key, 'value' => $value, 'desc' => $desc]);
    }
    echo "✓ twilio_settings table created with defaults\n\n";

    // 5. Add communication stats columns to leads table if not exists
    echo "Adding communication tracking columns to leads table...\n";

    $columns = [
        'total_calls' => "ALTER TABLE leads ADD COLUMN IF NOT EXISTS total_calls INT UNSIGNED DEFAULT 0",
        'total_sms' => "ALTER TABLE leads ADD COLUMN IF NOT EXISTS total_sms INT UNSIGNED DEFAULT 0",
        'last_call_at' => "ALTER TABLE leads ADD COLUMN IF NOT EXISTS last_call_at TIMESTAMP NULL",
        'last_sms_at' => "ALTER TABLE leads ADD COLUMN IF NOT EXISTS last_sms_at TIMESTAMP NULL",
        'sms_opt_out' => "ALTER TABLE leads ADD COLUMN IF NOT EXISTS sms_opt_out BOOLEAN DEFAULT FALSE"
    ];

    foreach ($columns as $col => $sql) {
        try {
            $db->exec($sql);
            echo "  ✓ Added column: $col\n";
        } catch (PDOException $e) {
            // Column might already exist in some MySQL versions
            if (strpos($e->getMessage(), 'Duplicate column') === false) {
                echo "  - Column $col may already exist or: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "\n";

    echo "✅ All Twilio Communications tables created successfully!\n\n";
    echo "Next steps:\n";
    echo "1. Add these to your .env file:\n";
    echo "   TWILIO_ACCOUNT_SID=your_account_sid\n";
    echo "   TWILIO_AUTH_TOKEN=your_auth_token\n";
    echo "   TWILIO_PHONE_NUMBER=+15141234567\n\n";
    echo "2. Configure webhook URLs in Twilio console:\n";
    echo "   SMS webhook: https://yourdomain.com/api/twilio/sms-webhook\n";
    echo "   Voice webhook: https://yourdomain.com/api/twilio/voice-webhook\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
