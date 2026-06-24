<?php

namespace App\Helpers;

/**
 * Handles "Remember me for 30 days" across all portals.
 *
 * Portals:
 *  - User (buyer / admin / delivery)  → cookie: user_remember  → users.remember_token
 *  - Business (distribution portal)   → cookie: biz_remember   → users.biz_remember_token
 *  - Supplier                         → cookie: supplier_remember → suppliers.remember_token
 *
 * Call RememberMeHelper::restoreAll() in index.php before routing.
 */
class RememberMeHelper
{
    private const LIFETIME        = 2592000; // 30 days in seconds
    private const USER_COOKIE     = 'user_remember';
    private const BIZ_COOKIE      = 'biz_remember';
    private const SUPPLIER_COOKIE = 'supplier_remember';

    // ── Public setters (call on login when "remember me" checked) ─────────────

    public static function setUserToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        \Database::getConnection()
            ->prepare("UPDATE users SET remember_token = ? WHERE id = ?")
            ->execute([$token, $userId]);
        self::writeCookie(self::USER_COOKIE, $token);
    }

    public static function setBusinessToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        \Database::getConnection()
            ->prepare("UPDATE users SET biz_remember_token = ? WHERE id = ?")
            ->execute([$token, $userId]);
        self::writeCookie(self::BIZ_COOKIE, $token);
    }

    public static function setSupplierToken(int $supplierId): void
    {
        $token = bin2hex(random_bytes(32));
        \Database::getConnection()
            ->prepare("UPDATE suppliers SET remember_token = ? WHERE id = ?")
            ->execute([$token, $supplierId]);
        self::writeCookie(self::SUPPLIER_COOKIE, $token);
    }

    // ── Public clearers (call on logout) ──────────────────────────────────────

    public static function clearUserToken(int $userId): void
    {
        try {
            \Database::getConnection()
                ->prepare("UPDATE users SET remember_token = NULL WHERE id = ?")
                ->execute([$userId]);
        } catch (\Exception $e) {}
        self::eraseCookie(self::USER_COOKIE);
    }

    public static function clearBusinessToken(int $userId): void
    {
        try {
            \Database::getConnection()
                ->prepare("UPDATE users SET biz_remember_token = NULL WHERE id = ?")
                ->execute([$userId]);
        } catch (\Exception $e) {}
        self::eraseCookie(self::BIZ_COOKIE);
    }

    public static function clearSupplierToken(int $supplierId): void
    {
        try {
            \Database::getConnection()
                ->prepare("UPDATE suppliers SET remember_token = NULL WHERE id = ?")
                ->execute([$supplierId]);
        } catch (\Exception $e) {}
        self::eraseCookie(self::SUPPLIER_COOKIE);
    }

    // ── Restore all portals (call once per request before routing) ────────────

    public static function restoreAll(): void
    {
        self::restoreUserSession();
        self::restoreBusinessSession();
        self::restoreSupplierSession();
    }

    // ── Private restorers ─────────────────────────────────────────────────────

    private static function restoreUserSession(): void
    {
        // Only restore if not already logged in and cookie present
        if (isset($_SESSION['user']['id']) || empty($_COOKIE[self::USER_COOKIE])) {
            return;
        }

        $token = $_COOKIE[self::USER_COOKIE];

        try {
            $db   = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT id, email, first_name, last_name, role, avatar, status
                FROM users
                WHERE remember_token = ?
                  AND status = 'active'
                  AND role NOT IN ('business')
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                self::eraseCookie(self::USER_COOKIE);
                return;
            }

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'         => $user['id'],
                'email'      => $user['email'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'role'       => $user['role'],
                'avatar'     => $user['avatar'] ?? null,
                'status'     => $user['status'],
            ];

            // Renew cookie lifetime
            self::writeCookie(self::USER_COOKIE, $token);

        } catch (\Exception $e) {
            error_log('RememberMe restore user error: ' . $e->getMessage());
        }
    }

    private static function restoreBusinessSession(): void
    {
        if (isset($_SESSION['business']['id']) || empty($_COOKIE[self::BIZ_COOKIE])) {
            return;
        }

        $token = $_COOKIE[self::BIZ_COOKIE];

        try {
            $db   = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT u.id, u.email, u.first_name, u.last_name, u.status,
                       bp.id AS business_profile_id, bp.company_name, bp.status AS business_status
                FROM users u
                JOIN business_profiles bp ON bp.user_id = u.id
                WHERE u.biz_remember_token = ?
                  AND u.status = 'active'
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                self::eraseCookie(self::BIZ_COOKIE);
                return;
            }

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'         => $user['id'],
                'email'      => $user['email'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'role'       => 'business',
                'status'     => $user['status'],
            ];
            $_SESSION['business'] = [
                'id'           => $user['business_profile_id'],
                'company_name' => $user['company_name'],
                'user_id'      => $user['id'],
                'status'       => $user['business_status'],
            ];

            self::writeCookie(self::BIZ_COOKIE, $token);

        } catch (\Exception $e) {
            error_log('RememberMe restore business error: ' . $e->getMessage());
        }
    }

    private static function restoreSupplierSession(): void
    {
        if (isset($_SESSION['supplier_id']) || empty($_COOKIE[self::SUPPLIER_COOKIE])) {
            return;
        }

        $token = $_COOKIE[self::SUPPLIER_COOKIE];

        try {
            $db   = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT id, email, company_name, name, status, verification_deadline
                FROM suppliers
                WHERE remember_token = ?
                  AND status IN ('active', 'pending_verification')
                  AND can_login = 1
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$supplier) {
                self::eraseCookie(self::SUPPLIER_COOKIE);
                return;
            }

            session_regenerate_id(true);
            $_SESSION['supplier_id']                    = $supplier['id'];
            $_SESSION['supplier_email']                 = $supplier['email'];
            $_SESSION['supplier_name']                  = $supplier['company_name'] ?? $supplier['name'];
            $_SESSION['supplier_status']                = $supplier['status'];
            $_SESSION['supplier_verification_deadline'] = $supplier['verification_deadline'] ?? null;

            self::writeCookie(self::SUPPLIER_COOKIE, $token);

        } catch (\Exception $e) {
            error_log('RememberMe restore supplier error: ' . $e->getMessage());
        }
    }

    // ── Cookie helpers ────────────────────────────────────────────────────────

    private static function writeCookie(string $name, string $value): void
    {
        setcookie($name, $value, [
            'expires'  => time() + self::LIFETIME,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private static function eraseCookie(string $name): void
    {
        setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
