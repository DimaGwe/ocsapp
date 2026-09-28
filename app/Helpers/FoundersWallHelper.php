<?php
namespace App\Helpers;

/**
 * Founders' Wall: who may be shown publicly on the founding page, and the consent behind it.
 *
 * Legal basis (research 2026-09-28): Civil Code of Québec art. 36(5) (using a person's name without
 * consent is an invasion of privacy), Law 25 / private-sector Act s. 9.1 (highest privacy by default,
 * so opt-in) and s. 14, and the CAI guidelines 2023-1 on valid consent (express, separate, granular,
 * specific, time-limited, withdrawable at any time, documented).
 *
 * Rules enforced here:
 *  - consent is asked on the waitlist form as its own unchecked checkbox; default is NO;
 *  - a person is shown only when founding status is actually granted AND their latest choice is yes;
 *  - every choice is logged with the wording version, source, locale and IP (founders_wall_consent_log);
 *  - the choice can be changed or withdrawn at any time from a no-login link (wall_token);
 *  - the "you're a founder" email with that link is sent once (wall_notified_at).
 * Tables: see database/migrations/add_founders_wall_consent.php.
 */
class FoundersWallHelper
{
    /** Bump whenever the consent wording below changes, so the log shows which text each person saw. */
    const CONSENT_VERSION = 'fw-2026-09-28';

    /** Roles shown by business name; the others (buyer, driver) by first name + last initial. */
    const BUSINESS_ROLES = ['seller', 'supplier', 'business'];

    const PROGRAM_NAMES = [
        'buyer'    => ['Acheteur fondateur', 'Founding Buyer'],
        'seller'   => ['Vendeur fondateur', 'Founding Seller'],
        'supplier' => ['Fournisseur fondateur', 'Founding Supplier'],
        'driver'   => ['Livreur fondateur', 'Founding Driver'],
        'business' => ['Entreprise fondatrice', 'Founding Business'],
    ];

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /** The consent wording (title + body). Single source for the form, the preferences page and the log version. */
    public static function consentText(bool $fr): array
    {
        return $fr
            ? ['Afficher mon nom sur le Mur des fondateurs.',
               "Si je deviens membre fondateur, OCSAPP peut afficher publiquement sur ocsapp.ca mon prénom et l'initiale de mon nom (ou le nom de mon entreprise), ma ville et mon programme fondateur, jusqu'à la fin du programme fondateur. Facultatif. Je peux retirer mon consentement en tout temps."]
            : ["Show my name on the Founders' Wall.",
               'If I become a founding member, OCSAPP may publicly display on ocsapp.ca my first name and last initial (or my business name), my city and my founding program, until the end of the founding program. Optional. I can withdraw my consent at any time.'];
    }

    public static function newToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Store a choice (given, refused or withdrawn) on the waitlist row and in the proof log.
     * The waitlist form logs refusals too, so there is a record of what was asked and answered.
     */
    public static function recordConsent(int $waitlistId, bool $consent, string $source, ?string $locale = null): void
    {
        $db = self::db();
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $ip = $ip ? substr(trim(explode(',', $ip)[0]), 0, 45) : null;
        $locale = in_array($locale, ['fr', 'en'], true) ? $locale : null;

        $db->prepare("
            UPDATE waitlist
            SET wall_consent = ?, wall_consent_at = NOW(), wall_consent_version = ?,
                wall_token = COALESCE(wall_token, ?)
            WHERE id = ?
        ")->execute([$consent ? 1 : 0, self::CONSENT_VERSION, self::newToken(), $waitlistId]);

        $db->prepare("
            INSERT INTO founders_wall_consent_log (waitlist_id, consent, consent_version, source, locale, ip_address)
            VALUES (?, ?, ?, ?, ?, ?)
        ")->execute([$waitlistId, $consent ? 1 : 0, self::CONSENT_VERSION, $source, $locale, $ip]);
    }

    /** "Marie-Ève L." for individuals; the business name for business roles (falls back to the person). */
    public static function displayName(string $role, ?string $firstName, ?string $lastName, ?string $businessName = null): string
    {
        $businessName = trim((string) $businessName);
        if (in_array($role, self::BUSINESS_ROLES, true) && $businessName !== '') {
            return $businessName;
        }
        $first = trim((string) $firstName);
        $last  = trim((string) $lastName);
        return trim($first . ($last !== '' ? ' ' . mb_strtoupper(mb_substr($last, 0, 1)) . '.' : ''));
    }

    /** City shown on the wall: the first part of what the person typed ("Laval, QC" -> "Laval"). */
    public static function displayCity(?string $cityRegion): string
    {
        $city = trim(explode(',', (string) $cityRegion)[0]);
        return mb_substr($city, 0, 40);
    }

    /**
     * Real founders' wall: founding status granted AND consent = yes on the waitlist row with the
     * same email. Newest first. Same shape as the sample data: name, city, role, daysAgo, number.
     */
    public static function founders(int $limit = 60): array
    {
        $sql = "
            SELECT * FROM (
                SELECT 'buyer' AS role, u.founding_buyer_number AS number, u.founding_buyer_granted_at AS granted_at,
                       NULL AS account_business, w.first_name, w.last_name, w.business_name, w.city_region
                FROM users u JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE u.founding_buyer = 1
              UNION ALL
                SELECT 'driver', u.founding_driver_number, u.founding_driver_granted_at,
                       NULL, w.first_name, w.last_name, w.business_name, w.city_region
                FROM users u JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE u.founding_driver = 1
              UNION ALL
                SELECT 'seller', s.founding_partner_number, s.founding_partner_granted_at,
                       s.name, w.first_name, w.last_name, w.business_name, w.city_region
                FROM shops s JOIN users u ON u.id = s.seller_id
                JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE s.founding_partner = 1
              UNION ALL
                SELECT 'supplier', sp.founding_partner_number, sp.founding_partner_granted_at,
                       sp.company_name, w.first_name, w.last_name, w.business_name, w.city_region
                FROM suppliers sp JOIN waitlist w ON w.email = sp.email AND w.wall_consent = 1
                WHERE sp.founding_partner = 1
              UNION ALL
                SELECT 'business', bp.founding_partner_number, bp.founding_partner_granted_at,
                       bp.company_name, w.first_name, w.last_name, w.business_name, w.city_region
                FROM business_profiles bp JOIN users u ON u.id = bp.user_id
                JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE bp.founding_partner = 1
            ) f
            ORDER BY f.granted_at DESC
            LIMIT " . max(1, (int) $limit);

        $today = new \DateTimeImmutable('today');
        $out = [];
        foreach (self::db()->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            $name = self::displayName($r['role'], $r['first_name'], $r['last_name'], $r['account_business'] ?: $r['business_name']);
            if ($name === '') {
                continue;
            }
            $granted = $r['granted_at'] ? new \DateTimeImmutable(substr($r['granted_at'], 0, 10)) : $today;
            $out[] = [
                'name'    => $name,
                'city'    => self::displayCity($r['city_region']),
                'role'    => $r['role'],
                'daysAgo' => max(0, (int) $granted->diff($today)->days),
                'number'  => (int) $r['number'],
            ];
        }
        return $out;
    }

    /** Same as onFoundingGranted() for flows that only have the user id (buyer checkout, driver approval). */
    public static function onFoundingGrantedForUser(int $userId, string $role, int $number): void
    {
        try {
            $stmt = self::db()->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $email = (string) $stmt->fetchColumn();
            if ($email !== '') {
                self::onFoundingGranted($email, $role, $number);
            }
        } catch (\Throwable $e) {
            logger('FoundersWallHelper::onFoundingGrantedForUser failed for user #' . $userId . ': ' . $e->getMessage(), 'warning');
        }
    }

    /**
     * Founding status was just granted: email the person their Founders' Wall choice with the link to
     * change it. Sent once per waitlist row; people who never joined the waitlist have no choice on
     * file, so nothing is sent and they are not shown. Never throws (called from approval/checkout flows).
     */
    public static function onFoundingGranted(string $email, string $role, int $number): void
    {
        try {
            $db = self::db();
            $stmt = $db->prepare("SELECT * FROM waitlist WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $w = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$w || !empty($w['wall_notified_at']) || !isset(self::PROGRAM_NAMES[$role])) {
                return;
            }
            $token = $w['wall_token'] ?: self::newToken();
            $db->prepare("UPDATE waitlist SET wall_token = ?, wall_notified_at = NOW() WHERE id = ? AND wall_notified_at IS NULL")
               ->execute([$token, $w['id']]);

            $link   = url('founders-wall/preferences') . '?t=' . $token;
            $shown  = (int) $w['wall_consent'] === 1;
            $as     = htmlspecialchars(self::displayName($role, $w['first_name'], $w['last_name'], $w['business_name'])
                    . (self::displayCity($w['city_region']) !== '' ? ', ' . self::displayCity($w['city_region']) : ''));
            [$progFr, $progEn] = self::PROGRAM_NAMES[$role];
            $first  = htmlspecialchars(trim((string) $w['first_name']));

            $fr = "<p>Bonjour {$first},</p>"
                . "<p>Félicitations : vous êtes <strong>{$progFr} n<sup>o</sup> {$number}</strong> d'OCSAPP.</p>"
                . ($shown
                    ? "<p>Lors de votre inscription, vous avez accepté d'apparaître sur le Mur des fondateurs sous le nom <strong>{$as}</strong>. Vous pouvez modifier ou retirer ce choix en tout temps.</p>"
                    : "<p>Lors de votre inscription, vous avez choisi de ne pas apparaître sur le Mur des fondateurs. Votre nom ne sera pas affiché. Si vous changez d'avis, vous pouvez l'activer en tout temps.</p>")
                . "<p><a href=\"{$link}\" style=\"color:#00b207;font-weight:600\">Gérer mon choix pour le Mur des fondateurs</a></p>";
            $en = "<p>Hi {$first},</p>"
                . "<p>Congratulations: you are OCSAPP <strong>{$progEn} #{$number}</strong>.</p>"
                . ($shown
                    ? "<p>When you signed up, you agreed to appear on the Founders' Wall as <strong>{$as}</strong>. You can change or withdraw this choice at any time.</p>"
                    : "<p>When you signed up, you chose not to appear on the Founders' Wall. Your name will not be shown. If you change your mind, you can turn it on at any time.</p>")
                . "<p><a href=\"{$link}\" style=\"color:#00b207;font-weight:600\">Manage my Founders' Wall choice</a></p>";

            $body = "<div style=\"font-family:Arial,sans-serif;font-size:15px;line-height:1.6;color:#142017;max-width:560px\">"
                  . $fr . "<hr style=\"border:none;border-top:1px solid #e5e7e4;margin:24px 0\">" . $en
                  . "<p style=\"font-size:12px;color:#6b7280;margin-top:24px\">OCSAPP Inc. · Laval, Québec</p></div>";

            require_once __DIR__ . '/EmailHelper.php';
            EmailHelper::sendRaw($email, "Vous êtes {$progFr} n° {$number} / You're {$progEn} #{$number}", $body);
        } catch (\Throwable $e) {
            logger('FoundersWallHelper::onFoundingGranted failed for ' . $email . ': ' . $e->getMessage(), 'warning');
        }
    }
}
