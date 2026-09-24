<?php
/**
 * Migration: Twilio-powered contact center.
 * - call_logs: Twilio call tracking (sid, live status, recording), agent optional for missed inbound calls,
 *   needs_outcome flag for auto-logged calls waiting for the agent's disposition
 * - support_agent_status.phone: the phone Twilio rings for an agent (click-to-call bridge, inbound calls)
 * - support_ticket_messages.channel: how a message travelled (sms, voicemail...)
 * - support_tickets.channel: adds 'sms'
 * - sms_opt_outs: numbers that replied STOP/ARRET, never texted again until they reply START
 *
 * Run: php database/migrations/add_contact_center_twilio.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_contact_center_twilio\n";

function hasColumn(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare("
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (bool)$stmt->fetch();
}

function columnType(PDO $db, string $table, string $column): string
{
    $stmt = $db->prepare("
        SELECT COLUMN_TYPE FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (string)$stmt->fetchColumn();
}

try {
    // call_logs -------------------------------------------------------------
    $db->exec("ALTER TABLE call_logs MODIFY agent_id INT UNSIGNED NULL");
    echo "  [OK] call_logs.agent_id nullable (missed inbound calls)\n";

    $callCols = [
        'twilio_call_sid'    => "VARCHAR(40) NULL DEFAULT NULL AFTER duration_seconds",
        'call_status'        => "VARCHAR(30) NULL DEFAULT NULL COMMENT 'Live Twilio state; NULL for manually logged calls' AFTER twilio_call_sid",
        'answered_at'        => "DATETIME NULL DEFAULT NULL AFTER call_status",
        'recording_sid'      => "VARCHAR(40) NULL DEFAULT NULL AFTER answered_at",
        'recording_duration' => "SMALLINT UNSIGNED NULL DEFAULT NULL AFTER recording_sid",
        'needs_outcome'      => "TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Auto-logged call waiting for the agent disposition' AFTER recording_duration",
    ];
    foreach ($callCols as $col => $def) {
        if (!hasColumn($db, 'call_logs', $col)) {
            $db->exec("ALTER TABLE call_logs ADD COLUMN $col $def");
            echo "  [OK] Added call_logs.$col\n";
        } else {
            echo "  [SKIP] call_logs.$col exists\n";
        }
    }
    $idx = $db->query("SHOW INDEX FROM call_logs WHERE Key_name = 'idx_twilio_call_sid'")->fetch();
    if (!$idx) {
        $db->exec("ALTER TABLE call_logs ADD INDEX idx_twilio_call_sid (twilio_call_sid)");
        echo "  [OK] Added call_logs index on twilio_call_sid\n";
    }

    // support_agent_status.phone --------------------------------------------
    if (!hasColumn($db, 'support_agent_status', 'phone')) {
        $db->exec("ALTER TABLE support_agent_status ADD COLUMN phone VARCHAR(20) NULL DEFAULT NULL COMMENT 'Phone Twilio rings for this agent' AFTER status");
        echo "  [OK] Added support_agent_status.phone\n";
    } else {
        echo "  [SKIP] support_agent_status.phone exists\n";
    }

    // support_ticket_messages.channel ---------------------------------------
    if (!hasColumn($db, 'support_ticket_messages', 'channel')) {
        $db->exec("ALTER TABLE support_ticket_messages ADD COLUMN channel VARCHAR(20) NULL DEFAULT NULL COMMENT 'sms, voicemail, call; NULL = in-app' AFTER is_internal");
        echo "  [OK] Added support_ticket_messages.channel\n";
    } else {
        echo "  [SKIP] support_ticket_messages.channel exists\n";
    }

    // support_tickets.channel adds 'sms' ------------------------------------
    $type = columnType($db, 'support_tickets', 'channel');
    if (strpos($type, "'sms'") === false) {
        $db->exec("ALTER TABLE support_tickets MODIFY channel ENUM('phone','email','web_form','walk_in','chat','sms') NOT NULL DEFAULT 'phone'");
        echo "  [OK] support_tickets.channel now includes 'sms'\n";
    } else {
        echo "  [SKIP] support_tickets.channel already has 'sms'\n";
    }

    // sms_opt_outs ----------------------------------------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS sms_opt_outs (
            phone VARCHAR(20) NOT NULL COMMENT 'E.164',
            keyword VARCHAR(20) NULL,
            opted_out_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (phone)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  [OK] sms_opt_outs ready\n";

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
