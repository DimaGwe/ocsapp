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
        $businessName = self::plain($businessName);
        if (in_array($role, self::BUSINESS_ROLES, true) && $businessName !== '') {
            return $businessName;
        }
        $first = self::plain($firstName);
        $last  = self::plain($lastName);
        return trim($first . ($last !== '' ? ' ' . mb_strtoupper(mb_substr($last, 0, 1)) . '.' : ''));
    }

    /**
     * Waitlist fields are stored sanitize()d (HTML-escaped), so "L'Île" sits in the DB as "L&#039;Île".
     * Decode to plain text here; every output (wall, toast, email) escapes it once itself.
     */
    private static function plain(?string $value): string
    {
        return trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /** City shown on the wall: the first part of what the person typed ("Laval, QC" -> "Laval"). */
    public static function displayCity(?string $cityRegion): string
    {
        $city = trim(explode(',', self::plain($cityRegion))[0]);
        return mb_substr($city, 0, 40);
    }

    /**
     * Founders' wall: EVERY founder whose status was granted, newest first. Named (first name + last
     * initial or business name, city) only when the waitlist row with the same email has consent = yes;
     * everyone else is an anonymous entry carrying only role + number (nothing that identifies them,
     * so no consent is needed), which keeps the wall in step with the spot counters.
     * Shape: name, city, role, daysAgo, number, anonymous. Anonymous rows have name = city = ''.
     */
    public static function founders(int $limit = 300): array
    {
        $sql = "
            SELECT * FROM (
                SELECT 'buyer' AS role, u.founding_buyer_number AS number, u.founding_buyer_granted_at AS granted_at,
                       NULL AS account_business, w.first_name, w.last_name, w.business_name, w.city_region, w.id AS wid
                FROM users u LEFT JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE u.founding_buyer = 1
              UNION ALL
                SELECT 'driver', u.founding_driver_number, u.founding_driver_granted_at,
                       NULL, w.first_name, w.last_name, w.business_name, w.city_region, w.id
                FROM users u LEFT JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE u.founding_driver = 1
              UNION ALL
                SELECT 'seller', s.founding_partner_number, s.founding_partner_granted_at,
                       s.name, w.first_name, w.last_name, w.business_name, w.city_region, w.id
                FROM shops s JOIN users u ON u.id = s.seller_id
                LEFT JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE s.founding_partner = 1
              UNION ALL
                SELECT 'supplier', sp.founding_partner_number, sp.founding_partner_granted_at,
                       sp.company_name, w.first_name, w.last_name, w.business_name, w.city_region, w.id
                FROM suppliers sp LEFT JOIN waitlist w ON w.email = sp.email AND w.wall_consent = 1
                WHERE sp.founding_partner = 1
              UNION ALL
                SELECT 'business', bp.founding_partner_number, bp.founding_partner_granted_at,
                       bp.company_name, w.first_name, w.last_name, w.business_name, w.city_region, w.id
                FROM business_profiles bp JOIN users u ON u.id = bp.user_id
                LEFT JOIN waitlist w ON w.email = u.email AND w.wall_consent = 1
                WHERE bp.founding_partner = 1
            ) f
            ORDER BY f.granted_at DESC, f.number DESC
            LIMIT " . max(1, (int) $limit);

        $today = new \DateTimeImmutable('today');
        $out = [];
        foreach (self::db()->query($sql)->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            // Consent = a matching waitlist row (the join only matches consented rows). The account's
            // own business name is only used for consented founders too.
            $name = $r['wid'] ? self::displayName($r['role'], $r['first_name'], $r['last_name'], $r['account_business'] ?: $r['business_name']) : '';
            $granted = $r['granted_at'] ? new \DateTimeImmutable(substr($r['granted_at'], 0, 10)) : $today;
            $out[] = [
                'name'      => $name,
                'city'      => $name !== '' ? self::displayCity($r['city_region']) : '',
                'role'      => $r['role'],
                'daysAgo'   => max(0, (int) $granted->diff($today)->days),
                'number'    => (int) $r['number'],
                'anonymous' => $name === '',
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

            // Template variables (plain text; the template escapes them)
            [$programFr, $programEn] = self::PROGRAM_NAMES[$role];
            $firstName = self::plain($w['first_name']);
            $wallShown = (int) $w['wall_consent'] === 1;
            $city      = self::displayCity($w['city_region']);
            $shownAs   = self::displayName($role, $w['first_name'], $w['last_name'], $w['business_name'])
                       . ($city !== '' ? ', ' . $city : '');
            $manageUrl = url('founders-wall/preferences') . '?t=' . $token;

            ob_start();
            require __DIR__ . '/../Views/emails/founding-status-granted.php';
            $body = ob_get_clean();

            require_once __DIR__ . '/EmailHelper.php';
            EmailHelper::setNextMeta('founding_status_granted', 'waitlist', (int) $w['id']);
            EmailHelper::send($email, "Vous êtes {$programFr} n° {$number} / You're {$programEn} #{$number}", $body);
        } catch (\Throwable $e) {
            logger('FoundersWallHelper::onFoundingGranted failed for ' . $email . ': ' . $e->getMessage(), 'warning');
        }
    }
}
