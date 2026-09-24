<?php

namespace App\Helpers;

/**
 * Closes the loop between the waitlist and real accounts.
 *
 * Call markConverted() wherever an account becomes real (email verified) for any
 * role. If that email is on the waitlist, the entry is set to 'converted' and the
 * admins get one "Waitlist conversion" bell, so a beta invite can be followed from
 * signup -> invite -> account without cross-checking lists.
 */
class WaitlistHelper
{
    const ROLE_LABELS = [
        'buyer'    => 'Buyer',
        'seller'   => 'Seller',
        'supplier' => 'Supplier',
        'driver'   => 'Driver',
        'business' => 'Business',
        'partner'  => 'Partner',
    ];

    /** Admin inbox for waitlist alerts: config/mail.php admin_email (info@ocsapp.ca). */
    public static function adminEmail(): string
    {
        $mail = require BASE_PATH . '/config/mail.php';
        return $mail['admin_email'] ?? 'info@ocsapp.ca';
    }

    /**
     * @param string $email      the verified account's email
     * @param string $accountRole role of the account that was just created
     * Never throws: a failure here must not break the verification flow.
     */
    public static function markConverted(string $email, string $accountRole): void
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT id, first_name, last_name, role, status, invite_sent_at
                FROM waitlist WHERE email = ? LIMIT 1
            ");
            $stmt->execute([trim($email)]);
            $entry = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$entry || $entry['status'] === 'converted') {
                return;
            }

            $db->prepare("UPDATE waitlist SET status = 'converted' WHERE id = ?")->execute([$entry['id']]);

            $name    = trim($entry['first_name'] . ' ' . $entry['last_name']);
            $role    = self::ROLE_LABELS[$accountRole] ?? $accountRole;
            $invited = $entry['invite_sent_at']
                ? 'invited ' . date('M j', strtotime($entry['invite_sent_at']))
                : 'not invited through the waitlist';
            $roleNote = ($entry['role'] !== $accountRole)
                ? ' (joined the waitlist as ' . (self::ROLE_LABELS[$entry['role']] ?? $entry['role']) . ')'
                : '';

            NotificationHelper::add(
                'waitlist',
                "Waitlist conversion: {$name}",
                "{$name} ({$email}) created a {$role} account from the waitlist, {$invited}{$roleNote}.",
                ['link' => '/admin/waitlist?search=' . urlencode($email), 'icon' => 'user-check', 'priority' => 'normal']
            );
        } catch (\Throwable $e) {
            logger('WaitlistHelper::markConverted failed for ' . $email . ': ' . $e->getMessage(), 'error');
        }
    }
}
