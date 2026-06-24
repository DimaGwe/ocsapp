<?php
/**
 * PresenceHelper — passive online/offline tracking for all user types.
 *
 * Called on every authenticated web request and every authenticated API call.
 * Upserts a row in user_sessions so admin can see who is currently active.
 *
 * Status thresholds:
 *   online  — last activity < 5 minutes
 *   away    — last activity 5–30 minutes
 *   offline — last activity > 30 minutes or never seen
 */
class PresenceHelper
{
    /**
     * Record presence for the currently authenticated user/supplier.
     * Silently swallows all exceptions — tracking must never break a request.
     */
    public static function track(): void
    {
        try {
            $userId     = null;
            $supplierId = null;
            $role       = 'buyer';

            if (isset($_SESSION['user'])) {
                $userId = (int)($_SESSION['user']['id'] ?? 0);
                $role   = $_SESSION['user']['role'] ?? 'buyer';
                if (!$userId) return;
            } elseif (isset($_SESSION['supplier_id'])) {
                $supplierId = (int)$_SESSION['supplier_id'];
                $role = 'supplier';
            } else {
                return; // Guest — nothing to track
            }

            $db   = \Database::getConnection();
            $page = substr($_SERVER['REQUEST_URI'] ?? '/', 0, 500);
            $ip   = $_SERVER['REMOTE_ADDR'] ?? null;
            $ua   = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
            $device = (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone'))
                      ? 'mobile' : 'desktop';
            $now  = date('Y-m-d H:i:s');

            if ($userId) {
                $db->prepare(
                    "INSERT INTO user_sessions (user_id, role, last_seen_at, current_page, ip_address, device)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                         role = VALUES(role),
                         last_seen_at = VALUES(last_seen_at),
                         current_page = VALUES(current_page),
                         ip_address   = VALUES(ip_address),
                         device       = VALUES(device)"
                )->execute([$userId, $role, $now, $page, $ip, $device]);
            } elseif ($supplierId) {
                $db->prepare(
                    "INSERT INTO user_sessions (supplier_id, role, last_seen_at, current_page, ip_address, device)
                     VALUES (?, 'supplier', ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                         last_seen_at = VALUES(last_seen_at),
                         current_page = VALUES(current_page),
                         ip_address   = VALUES(ip_address),
                         device       = VALUES(device)"
                )->execute([$supplierId, $now, $page, $ip, $device]);
            }
        } catch (\Exception $e) {
            // Silently fail — presence tracking must never interrupt a request
        }
    }

    /**
     * Track a driver authenticated via Bearer token (called from DriverApiController).
     */
    public static function trackDriver(int $userId): void
    {
        try {
            $db   = \Database::getConnection();
            $page = 'ODA App — ' . ($_SERVER['REQUEST_URI'] ?? '');
            $page = substr($page, 0, 500);
            $ip   = $_SERVER['REMOTE_ADDR'] ?? null;
            $now  = date('Y-m-d H:i:s');

            $db->prepare(
                "INSERT INTO user_sessions (user_id, role, last_seen_at, current_page, ip_address, device)
                 VALUES (?, 'driver', ?, ?, ?, 'mobile')
                 ON DUPLICATE KEY UPDATE
                     role         = 'driver',
                     last_seen_at = VALUES(last_seen_at),
                     current_page = VALUES(current_page),
                     ip_address   = VALUES(ip_address),
                     device       = VALUES(device)"
            )->execute([$userId, $now, $page, $ip]);
        } catch (\Exception $e) {}
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public static function getStatus(?string $lastSeen): string
    {
        if (!$lastSeen) return 'offline';
        $diff = time() - strtotime($lastSeen);
        if ($diff < 300)  return 'online';   // < 5 min
        if ($diff < 1800) return 'away';     // < 30 min
        return 'offline';
    }

    /** Inline colored dot (HTML string). */
    public static function dot(?string $lastSeen): string
    {
        $status = self::getStatus($lastSeen);
        $colors = [
            'online'  => '#10b981',
            'away'    => '#f59e0b',
            'offline' => '#d1d5db',
        ];
        $titles = [
            'online'  => 'Online now',
            'away'    => 'Away',
            'offline' => 'Offline',
        ];
        $c = $colors[$status];
        $t = $titles[$status];
        return "<span title=\"{$t}\" style=\""
             . "display:inline-block;width:9px;height:9px;border-radius:50%;"
             . "background:{$c};flex-shrink:0;"
             . "\"></span>";
    }

    /** Small text label with colour. */
    public static function label(?string $lastSeen): string
    {
        $status = self::getStatus($lastSeen);
        if ($status === 'online') {
            return '<span style="color:#10b981;font-size:11px;font-weight:600;">● Online</span>';
        }
        if ($status === 'away') {
            return '<span style="color:#f59e0b;font-size:11px;font-weight:600;">● Away</span>';
        }
        return '<span style="color:#9ca3af;font-size:11px;">● Offline</span>';
    }

    /** "Last seen X ago" human-readable string. */
    public static function lastSeen(?string $lastSeen): string
    {
        if (!$lastSeen) return 'Never';
        $diff = time() - strtotime($lastSeen);
        if ($diff < 60)   return 'Just now';
        if ($diff < 3600) return (int)($diff / 60) . 'm ago';
        if ($diff < 86400) return (int)($diff / 3600) . 'h ago';
        return (int)($diff / 86400) . 'd ago';
    }

    /**
     * Count currently online users by role (for dashboard widget).
     * Returns array: ['total' => N, 'buyer' => N, 'seller' => N, 'driver' => N, 'supplier' => N, 'admin' => N]
     */
    public static function onlineCounts(): array
    {
        try {
            $db   = \Database::getConnection();
            $stmt = $db->query(
                "SELECT role, COUNT(*) as cnt
                 FROM user_sessions
                 WHERE last_seen_at > NOW() - INTERVAL 5 MINUTE
                 GROUP BY role"
            );
            $rows   = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $counts = ['total' => 0, 'buyer' => 0, 'seller' => 0, 'driver' => 0, 'supplier' => 0, 'admin' => 0];
            foreach ($rows as $r) {
                $role = $r['role'];
                $n    = (int)$r['cnt'];
                $counts['total'] += $n;
                if (array_key_exists($role, $counts)) {
                    $counts[$role] += $n;
                }
            }
            return $counts;
        } catch (\Exception $e) {
            return ['total' => 0, 'buyer' => 0, 'seller' => 0, 'driver' => 0, 'supplier' => 0, 'admin' => 0];
        }
    }
}
