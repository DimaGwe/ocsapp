<?php
/**
 * ContactCenterHelper - shared logic for the admin contact center
 * (Support Inbox, Call Log, Agent Dashboard, Twilio calls and SMS).
 */

namespace App\Helpers;

require_once __DIR__ . '/TwilioHelper.php';
require_once __DIR__ . '/NotificationHelper.php';

class ContactCenterHelper
{
    /** Keywords that opt a number out of SMS. Twilio handles the English ones itself; ARRET is ours. */
    public const OPT_OUT_KEYWORDS = ['STOP', 'STOPALL', 'UNSUBSCRIBE', 'CANCEL', 'END', 'QUIT', 'ARRET', 'ARRÊT'];
    public const OPT_IN_KEYWORDS  = ['START', 'UNSTOP', 'YES', 'DEBUT', 'DÉBUT'];

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    // ------------------------------------------------------------------ phones

    /** E.164 (+15145550100) or null */
    public static function e164(?string $phone): ?string
    {
        $phone = trim((string)$phone);
        return $phone === '' ? null : TwilioHelper::formatPhoneNumber($phone);
    }

    /** Last 10 digits, for matching numbers stored in any format */
    public static function last10(?string $phone): string
    {
        return substr(preg_replace('/\D/', '', (string)$phone), -10);
    }

    /** SQL expression that strips common separators from a phone column */
    private static function digitsSql(string $column): string
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE($column, '-', ''), ' ', ''), '(', ''), ')', ''), '+', ''), '.', '')";
    }

    public static function normalizeKeyword(string $body): string
    {
        return trim(preg_replace('/[^\p{L}]/u', '', mb_strtoupper(trim($body), 'UTF-8')));
    }

    // ---------------------------------------------------------------- contacts

    /**
     * Who owns this number? Returns ['type','id','name','email'] (type matches the
     * call_logs / support_tickets contact_type enums) or null.
     * Leads first, then platform users by role. Admins are skipped (team, not contacts).
     */
    public static function findContactByPhone(string $phone): ?array
    {
        $last10 = self::last10($phone);
        if (strlen($last10) < 10) {
            return null;
        }
        $db = self::db();

        $stmt = $db->prepare("
            SELECT id, first_name, last_name, email, company_name
            FROM leads
            WHERE " . self::digitsSql('phone') . " LIKE ?
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute(['%' . $last10]);
        if ($lead = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $name = trim($lead['first_name'] . ' ' . $lead['last_name']) ?: ($lead['company_name'] ?? '');
            return ['type' => 'lead', 'id' => (int)$lead['id'], 'name' => $name, 'email' => $lead['email'] ?? ''];
        }

        $stmt = $db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, GROUP_CONCAT(r.name) AS roles
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            WHERE " . self::digitsSql('u.phone') . " LIKE ?
            GROUP BY u.id
            ORDER BY u.id DESC
        ");
        $stmt->execute(['%' . $last10]);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $u) {
            $roles = explode(',', (string)$u['roles']);
            if (array_intersect($roles, ['super_admin', 'admin', 'admin_staff'])) {
                continue;
            }
            $type = 'unknown';
            foreach (['seller' => 'seller', 'supplier' => 'supplier', 'delivery' => 'driver', 'buyer' => 'buyer'] as $role => $t) {
                if (in_array($role, $roles, true)) { $type = $t; break; }
            }
            return ['type' => $type, 'id' => (int)$u['id'], 'name' => trim($u['first_name'] . ' ' . $u['last_name']), 'email' => $u['email'] ?? ''];
        }

        return null;
    }

    // ------------------------------------------------------------------ agents

    /** Active admin-tier users (role from user_roles, not users.role) */
    public static function agents(): array
    {
        return self::db()->query("
            SELECT DISTINCT u.id, u.first_name, u.last_name
            FROM users u
            JOIN user_roles ur ON ur.user_id = u.id
            JOIN roles r ON r.id = ur.role_id
            WHERE r.name IN ('super_admin', 'admin', 'admin_staff') AND u.status = 'active'
            ORDER BY u.first_name, u.last_name
        ")->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function agentPhone(int $userId): ?string
    {
        $stmt = self::db()->prepare("SELECT phone FROM support_agent_status WHERE user_id = ?");
        $stmt->execute([$userId]);
        return self::e164($stmt->fetchColumn() ?: null);
    }

    public static function setAgentPhone(int $userId, ?string $phone): void
    {
        self::db()->prepare("
            INSERT INTO support_agent_status (user_id, status, phone) VALUES (?, 'offline', ?)
            ON DUPLICATE KEY UPDATE phone = VALUES(phone)
        ")->execute([$userId, $phone]);
    }

    /** Twilio Client identity for an agent's browser Phone window */
    public static function agentIdentity(int $userId): string
    {
        return 'agent_' . $userId;
    }

    /** User id from a Twilio "client:agent_119" / "agent_119" caller, or 0 */
    public static function userIdFromIdentity(string $identity): int
    {
        return preg_match('/(?:^|client:)agent_(\d+)$/', $identity, $m) ? (int)$m[1] : 0;
    }

    /** Phone window heartbeat: online = Available + seen now; offline = Offline */
    public static function setSoftphonePresence(int $userId, bool $online): void
    {
        self::db()->prepare("
            INSERT INTO support_agent_status (user_id, status, softphone_seen_at) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status), softphone_seen_at = VALUES(softphone_seen_at)
        ")->execute([$userId, $online ? 'available' : 'offline', $online ? date('Y-m-d H:i:s') : null]);
    }

    /**
     * Agents marked Available who can take a call:
     * [['id','name','browser' => bool (Phone window online in the last 90s),'phone' => E.164|null]]
     * Browser agents are rung in the browser only; others on their phone.
     */
    public static function availableAgents(): array
    {
        $rows = self::db()->query("
            SELECT u.id, u.first_name, u.last_name, s.phone,
                   (s.softphone_seen_at IS NOT NULL AND s.softphone_seen_at > NOW() - INTERVAL 90 SECOND) AS browser
            FROM support_agent_status s
            JOIN users u ON u.id = s.user_id
            WHERE s.status = 'available' AND u.status = 'active'
            ORDER BY s.updated_at DESC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $agents = [];
        foreach ($rows as $r) {
            $browser = (bool)$r['browser'];
            $phone = self::e164($r['phone']);
            if ($browser || $phone) {
                $agents[] = [
                    'id' => (int)$r['id'],
                    'name' => trim($r['first_name'] . ' ' . $r['last_name']),
                    'browser' => $browser,
                    'phone' => $browser ? null : $phone
                ];
            }
        }
        return $agents;
    }

    // ----------------------------------------------------------------- tickets

    /** TKT-2026-00042; based on the highest existing number so deletions never cause duplicates */
    public static function nextTicketNumber(): string
    {
        $year = date('Y');
        $stmt = self::db()->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(ticket_number, '-', -1) AS UNSIGNED)) FROM support_tickets WHERE ticket_number LIKE ?");
        $stmt->execute(["TKT-$year-%"]);
        return 'TKT-' . $year . '-' . str_pad((string)((int)$stmt->fetchColumn() + 1), 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create a ticket. $t keys: subject, channel, category, priority, contact (findContactByPhone shape),
     * contact_phone, description, assigned_to, created_by. Returns the ticket id.
     */
    public static function createTicket(array $t): int
    {
        $contact = $t['contact'] ?? null;
        $db = self::db();

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $db->prepare("
                    INSERT INTO support_tickets
                        (ticket_number, subject, channel, category, priority, status,
                         contact_type, contact_id, contact_name, contact_email, contact_phone,
                         assigned_to, created_by, description)
                    VALUES (?, ?, ?, ?, ?, 'open', ?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    self::nextTicketNumber(),
                    mb_substr($t['subject'], 0, 255),
                    $t['channel'] ?? 'phone',
                    $t['category'] ?? 'general',
                    $t['priority'] ?? 'medium',
                    $contact['type'] ?? 'unknown',
                    $contact['id'] ?? null,
                    $contact['name'] ?? null,
                    $contact['email'] ?? null,
                    $t['contact_phone'] ?? null,
                    $t['assigned_to'] ?? null,
                    (int)($t['created_by'] ?? 0),
                    $t['description'] ?? null,
                ]);
                return (int)$db->lastInsertId();
            } catch (\PDOException $e) {
                // Duplicate ticket number from a concurrent insert: take the next one
                if ($e->getCode() !== '23000' || $attempt === 2) {
                    throw $e;
                }
            }
        }
        return 0;
    }

    public static function addTicketMessage(int $ticketId, string $message, string $senderType, ?int $senderId, ?string $senderName, bool $internal = false, ?string $channel = null): void
    {
        self::db()->prepare("
            INSERT INTO support_ticket_messages (ticket_id, message, sender_type, sender_id, sender_name, is_internal, channel)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ")->execute([$ticketId, $message, $senderType, $senderId, $senderName, $internal ? 1 : 0, $channel]);

        self::db()->prepare("UPDATE support_tickets SET updated_at = NOW() WHERE id = ?")->execute([$ticketId]);
    }

    /** Most recent ticket for this number that is not closed (resolved tickets reopen on a reply) */
    public static function findActiveTicketByPhone(string $phone): ?array
    {
        $last10 = self::last10($phone);
        if (strlen($last10) < 10) {
            return null;
        }
        $stmt = self::db()->prepare("
            SELECT * FROM support_tickets
            WHERE " . self::digitsSql('contact_phone') . " LIKE ? AND status <> 'closed'
            ORDER BY updated_at DESC LIMIT 1
        ");
        $stmt->execute(['%' . $last10]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    // --------------------------------------------------------------- opt-outs

    public static function isOptedOut(string $phone): bool
    {
        $e164 = self::e164($phone);
        if (!$e164) {
            return false;
        }
        $stmt = self::db()->prepare("SELECT 1 FROM sms_opt_outs WHERE phone = ?");
        $stmt->execute([$e164]);
        return (bool)$stmt->fetchColumn();
    }

    public static function optOut(string $phone, string $keyword): void
    {
        if ($e164 = self::e164($phone)) {
            self::db()->prepare("INSERT INTO sms_opt_outs (phone, keyword) VALUES (?, ?) ON DUPLICATE KEY UPDATE keyword = VALUES(keyword), opted_out_at = NOW()")
                ->execute([$e164, mb_substr($keyword, 0, 20)]);
        }
    }

    public static function optIn(string $phone): void
    {
        if ($e164 = self::e164($phone)) {
            self::db()->prepare("DELETE FROM sms_opt_outs WHERE phone = ?")->execute([$e164]);
        }
    }

    /**
     * Send an SMS respecting opt-outs. Returns TwilioHelper::sendSMS shape.
     */
    public static function sendSms(string $to, string $body): array
    {
        if (!TwilioHelper::isConfigured()) {
            return ['success' => false, 'error' => 'SMS is not configured (Twilio settings missing)'];
        }
        if (self::isOptedOut($to)) {
            return ['success' => false, 'error' => 'This number has opted out of SMS (replied STOP or ARRET)'];
        }
        return TwilioHelper::sendSMS($to, $body, [
            'statusCallback' => TwilioHelper::appUrl() . '/api/twilio/sms-status'
        ]);
    }

    // --------------------------------------------------------------- rendering

    /**
     * Ticket message body as safe HTML: escaped, line breaks kept, voicemail links become an audio player
     */
    public static function messageHtml(?string $text): string
    {
        $html = nl2br(htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8'));
        return preg_replace(
            '#Listen: /api/twilio/recording\?call=(\d+)#',
            '<audio controls preload="none" src="/api/twilio/recording?call=$1" style="display:block;margin-top:6px;max-width:100%;"></audio>',
            $html
        );
    }

    /** Small label for messages that travelled by SMS / voicemail / call */
    public static function channelBadge(?string $channel): string
    {
        $labels = ['sms' => 'SMS', 'voicemail' => 'VOICEMAIL', 'call' => 'CALL'];
        if (!isset($labels[$channel])) {
            return '';
        }
        return '<span style="font-size:9px;font-weight:700;background:rgba(0,0,0,.08);padding:1px 6px;border-radius:10px;margin-left:6px;vertical-align:middle;">' . $labels[$channel] . '</span>';
    }

    // ---------------------------------------------------------------- notices

    /** Admin bell notification for the whole team, or one agent when $userId is set */
    public static function notify(string $title, string $message, string $link, ?int $userId = null, string $icon = 'headset'): void
    {
        $options = ['link' => $link, 'icon' => $icon, 'priority' => NotificationHelper::PRIORITY_HIGH];
        if ($userId) {
            $options['user_id'] = $userId;
        }
        NotificationHelper::add(NotificationHelper::TYPE_SYSTEM, $title, $message, $options);
    }
}
