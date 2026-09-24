<?php

namespace App\Helpers;

/**
 * Beta-mode signup gate.
 *
 * When settings.allow_registration is false, every public signup page (buyer,
 * seller, supplier, driver, business) shows the "join the waitlist" page instead,
 * unless the visitor arrived with a valid waitlist invite (?invite=TOKEN) for that
 * same role. The invite is remembered in the session so multi-step forms keep
 * working, and on submit the email must match the invited waitlist email, so a
 * forwarded link can't be used by someone else.
 *
 * Usage in a signup controller:
 *   GET handler:  BetaAccessHelper::guardPage('seller');
 *   POST handler: BetaAccessHelper::guardSubmit('seller', post('email', ''));
 */
class BetaAccessHelper
{
    /** Waitlist role => [signup path, admin-facing label]. Partner has no account type. */
    const SIGNUP_PATHS = [
        'buyer'    => 'register',
        'seller'   => 'seller/apply',
        'supplier' => 'supplier/apply',
        'driver'   => 'delivery/apply',
        'business' => 'distribution/register',
    ];

    const SESSION_KEY = 'beta_invite';

    public static function isOpen(): bool
    {
        $v = strtolower((string) setting('allow_registration', 'true'));
        return in_array($v, ['true', '1', 'yes', 'on'], true);
    }

    /** Invite link for a waitlist entry, or null for roles without an account type. */
    public static function inviteUrl(string $role, string $token): ?string
    {
        if (!isset(self::SIGNUP_PATHS[$role])) {
            return null;
        }
        return url(self::SIGNUP_PATHS[$role]) . '?invite=' . $token;
    }

    /**
     * GET: show the signup page only when open or invited for this role;
     * otherwise render the beta page and stop.
     */
    public static function guardPage(string $role): void
    {
        if (self::isOpen()) {
            return;
        }
        $token = preg_replace('/[^a-f0-9]/', '', strtolower((string) ($_GET['invite'] ?? '')));
        if ($token !== '') {
            $invite = self::findInvite($token);
            if ($invite) {
                $_SESSION[self::SESSION_KEY] = [
                    'token' => $token,
                    'role'  => $invite['role'],
                    'email' => strtolower($invite['email']),
                ];
            }
        }
        if (self::sessionInviteFor($role)) {
            return;
        }
        self::renderClosed($role, $token !== '');
    }

    /**
     * POST: allow when open, or when the session invite is for this role and the
     * submitted email is the invited one. Otherwise flash and send them back.
     */
    public static function guardSubmit(string $role, string $email): void
    {
        if (self::isOpen()) {
            return;
        }
        $invite = self::sessionInviteFor($role);
        $fr = (($_SESSION['language'] ?? 'fr') === 'fr');

        if (!$invite) {
            self::renderClosed($role, false);
        }
        if (strtolower(trim($email)) !== $invite['email']) {
            $msg = $fr
                ? "Pendant la période bêta, utilisez l'adresse courriel à laquelle votre invitation a été envoyée ({$invite['email']})."
                : "During beta, please use the email address your invitation was sent to ({$invite['email']}).";
            setFlash('error', $msg);
            if ($role === 'business') {
                // The business register form shows register_errors, not flash messages
                $_SESSION['register_errors'] = ['general' => $msg];
                redirect('distribution/register');
            }
            back();
            exit;
        }
    }

    private static function sessionInviteFor(string $role): ?array
    {
        $s = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$s || ($s['role'] ?? '') !== $role) {
            return null;
        }
        // Re-check against the DB each time so a revoked/regenerated invite stops working.
        $invite = self::findInvite($s['token']);
        if (!$invite || $invite['role'] !== $role) {
            unset($_SESSION[self::SESSION_KEY]);
            return null;
        }
        return $s;
    }

    private static function findInvite(string $token): ?array
    {
        if (strlen($token) !== 32) {
            return null;
        }
        try {
            $stmt = \Database::getConnection()->prepare("
                SELECT email, role FROM waitlist
                WHERE invite_token = ? AND invite_sent_at IS NOT NULL AND unsubscribed_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            logger('BetaAccessHelper invite lookup failed: ' . $e->getMessage(), 'error');
            return null;
        }
    }

    private static function renderClosed(string $role, bool $badInvite): void
    {
        http_response_code(200);
        view('pages/beta-closed', ['role' => $role, 'badInvite' => $badInvite]);
        exit;
    }
}
