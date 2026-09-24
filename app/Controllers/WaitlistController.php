<?php

namespace App\Controllers;

use App\Helpers\EmailHelper;

class WaitlistController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    public function index(): void
    {
        $ref  = sanitize(get('ref', ''));
        $data = [
            'ref'     => $ref,
            'joined'  => (bool) get('joined', false),
            'pos'     => (int) get('pos', 0),
            'myRef'   => sanitize(get('myref', '')),
            'myRole'  => sanitize(get('role', '')),
        ];
        view('waitlist/index', $data);
    }

    /**
     * One-click unsubscribe from waitlist emails (link in the confirmation email footer).
     * Token is random per signup; unknown/missing tokens show the same neutral page.
     */
    public function unsubscribe(): void
    {
        $token = preg_replace('/[^a-f0-9]/', '', strtolower((string) get('t', '')));
        $done  = false;

        if (strlen($token) === 32) {
            try {
                $stmt = $this->db->prepare("
                    UPDATE waitlist
                    SET unsubscribed_at = COALESCE(unsubscribed_at, NOW()), marketing_consent = 0
                    WHERE unsubscribe_token = ?
                ");
                $stmt->execute([$token]);
                if ($stmt->rowCount() > 0) {
                    // Only on the click that actually unsubscribed them, not on repeat clicks
                    $who = $this->db->prepare("SELECT first_name, last_name, email, role FROM waitlist WHERE unsubscribe_token = ?");
                    $who->execute([$token]);
                    if ($w = $who->fetch()) {
                        $wName = trim($w['first_name'] . ' ' . $w['last_name']);
                        \App\Helpers\NotificationHelper::add(
                            'waitlist',
                            "Waitlist unsubscribe: {$wName}",
                            "{$wName} ({$w['email']}, " . (\App\Helpers\WaitlistHelper::ROLE_LABELS[$w['role']] ?? $w['role']) . ") unsubscribed from waitlist emails.",
                            ['link' => '/admin/waitlist?search=' . urlencode($w['email']), 'icon' => 'user-minus', 'priority' => 'low']
                        );
                    }
                }
                // rowCount() is 0 when already unsubscribed and nothing changed, so re-check
                $chk = $this->db->prepare("SELECT 1 FROM waitlist WHERE unsubscribe_token = ? AND unsubscribed_at IS NOT NULL");
                $chk->execute([$token]);
                $done = (bool) $chk->fetchColumn();
            } catch (\PDOException $e) {
                logger('Waitlist unsubscribe error: ' . $e->getMessage(), 'error');
            }
        }

        view('waitlist/unsubscribed', ['done' => $done]);
    }

    public function store(): void
    {
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $lang         = $_SESSION['language'] ?? 'fr';
        $fr           = ($lang === 'fr');
        $firstName    = sanitize(post('first_name', ''));
        $lastName     = sanitize(post('last_name', ''));
        $cityRegion   = sanitize(post('city_region', ''));
        $preferredLang = sanitize(post('preferred_language', ''));
        $email        = sanitize(post('email', ''));
        $phone        = sanitize(post('phone', ''));
        $role         = sanitize(post('role', ''));
        // Referral code: typed by hand or pre-filled from ?ref=. Codes are A-Z0-9,
        // so strip spaces/dashes people add when copying it from a message.
        $ref          = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) post('ref', '')));

        $discoverySource = sanitize(post('discovery_source', ''));
        $marketingConsent = post('marketing_consent', '') === 'yes' ? 1 : 0;
        $utmSource   = sanitize(post('utm_source', ''));
        $utmMedium   = sanitize(post('utm_medium', ''));
        $utmCampaign = sanitize(post('utm_campaign', ''));
        $utmContent  = sanitize(post('utm_content', ''));
        $referralSrc = sanitize(post('referral', ''));

        // Business name comes from a role-specific field; consolidated into one column.
        $businessNameFieldByRole = [
            'seller'   => 'seller_business_name',
            'supplier' => 'supplier_business_name',
            'business' => 'business_name',
        ];
        $businessName = isset($businessNameFieldByRole[$role])
            ? sanitize(post($businessNameFieldByRole[$role], ''))
            : '';

        $sellerBusinessType  = sanitize(post('seller_business_type', ''));
        $sellerOnlineStore   = sanitize(post('seller_online_store', ''));
        $supplierProducts    = sanitize(post('supplier_products', ''));
        $supplierServiceArea = sanitize(post('supplier_service_area', ''));
        $businessSector      = sanitize(post('business_sector', ''));
        $businessNeed        = sanitize(post('business_need', ''));
        $driverArea          = sanitize(post('driver_area', ''));
        $driverVehicle       = sanitize(post('driver_vehicle', ''));
        $driverAvailability  = sanitize(post('driver_availability', ''));
        $buyerInterest       = sanitize(post('buyer_interest', ''));
        $partnerInterest     = sanitize(post('partner_interest', ''));

        $validRoles          = ['buyer', 'seller', 'supplier', 'driver', 'business', 'partner'];
        $businessRoles       = ['seller', 'supplier', 'business'];
        $validLangs          = ['fr', 'en'];
        $validDiscovery      = ['social_media', 'friend_family', 'google_search', 'press', 'other', 'representative', 'social', 'referral', 'local_business', 'event', 'web'];
        $validOnlineStore    = ['yes', 'no'];
        $validBusinessNeed   = ['procurement', 'employee', 'delivery', 'other'];
        $validDriverVehicle  = ['bike', 'car', 'van', 'other'];

        if (empty($firstName) || empty($lastName) || empty($email) || empty($cityRegion) || empty($preferredLang) || empty($role)) {
            jsonResponse(['success' => false, 'message' => $fr ? 'Tous les champs obligatoires sont requis.' : 'All required fields must be filled.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => $fr ? 'Adresse courriel invalide.' : 'Invalid email address.']);
            return;
        }

        if (!in_array($role, $validRoles, true)) {
            jsonResponse(['success' => false, 'message' => $fr ? 'Rôle invalide.' : 'Invalid role selected.']);
            return;
        }

        if (in_array($role, $businessRoles, true) && empty($businessName)) {
            jsonResponse(['success' => false, 'message' => $fr ? "Le nom de l'entreprise est requis pour ce rôle." : 'Business name is required for this role.']);
            return;
        }

        $preferredLang       = in_array($preferredLang, $validLangs, true) ? $preferredLang : $lang;
        $discoverySource     = in_array($discoverySource, $validDiscovery, true) ? $discoverySource : null;
        $sellerOnlineStore   = in_array($sellerOnlineStore, $validOnlineStore, true) ? $sellerOnlineStore : null;
        $businessNeed        = in_array($businessNeed, $validBusinessNeed, true) ? $businessNeed : null;
        $driverVehicle       = in_array($driverVehicle, $validDriverVehicle, true) ? $driverVehicle : null;
        $businessName        = $businessName !== '' ? $businessName : null;
        $phone               = $phone !== '' ? $phone : null;
        $sellerBusinessType  = $sellerBusinessType !== '' ? $sellerBusinessType : null;
        $supplierProducts    = $supplierProducts !== '' ? $supplierProducts : null;
        $supplierServiceArea = $supplierServiceArea !== '' ? $supplierServiceArea : null;
        $businessSector      = $businessSector !== '' ? $businessSector : null;
        $driverArea          = $driverArea !== '' ? $driverArea : null;
        $driverAvailability  = $driverAvailability !== '' ? $driverAvailability : null;
        $buyerInterest       = $buyerInterest !== '' ? $buyerInterest : null;
        $partnerInterest     = $partnerInterest !== '' ? $partnerInterest : null;
        $utmSource           = $utmSource !== '' ? $utmSource : null;
        $utmMedium           = $utmMedium !== '' ? $utmMedium : null;
        $utmCampaign         = $utmCampaign !== '' ? $utmCampaign : null;
        $utmContent          = $utmContent !== '' ? $utmContent : null;
        $referralSrc         = $referralSrc !== '' ? $referralSrc : null;

        try {
            // Check duplicate
            $stmt = $this->db->prepare("SELECT id, role, signup_position, referral_code FROM waitlist WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Already on the list: show the number and role they were given, not the ones just submitted
                $pos = (int) ($existing['signup_position'] ?: $this->assignPosition((int) $existing['id'], $existing['role']));
                $url = url('/waitlist') . '?joined=1&pos=' . $pos . '&myref=' . $existing['referral_code'] . '&role=' . $existing['role'];
                jsonResponse(['success' => true, 'redirect' => $url]);
                return;
            }

            // Validate referrer code
            $referredBy = null;
            if ($ref !== '') {
                $stmt = $this->db->prepare("SELECT referral_code FROM waitlist WHERE referral_code = ?");
                $stmt->execute([$ref]);
                $referrer = $stmt->fetch();
                if (!$referrer) {
                    // Tell the user instead of silently dropping it, so a typo can be fixed.
                    jsonResponse(['success' => false, 'message' => $fr
                        ? 'Ce code de parrainage est introuvable. Vérifiez-le ou laissez le champ vide.'
                        : 'That referral code was not found. Check it or leave the field empty.']);
                    return;
                }
                $referredBy = $referrer['referral_code'];
            }

            $refCode   = $this->generateCode();
            $unsubToken = bin2hex(random_bytes(16));
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
            if ($ipAddress) {
                $ipAddress = substr(explode(',', $ipAddress)[0], 0, 45);
            }

            $stmt = $this->db->prepare("
                INSERT INTO waitlist (
                    email, phone, first_name, last_name, business_name, city_region, discovery_source, role, locale,
                    referral_code, referred_by, unsubscribe_token, ip_address, marketing_consent,
                    seller_business_type, seller_online_store, supplier_products, supplier_service_area,
                    business_sector, business_need, driver_area, driver_vehicle, driver_availability,
                    buyer_interest, partner_interest, utm_source, utm_medium, utm_campaign, utm_content, referral_source
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $email, $phone, $firstName, $lastName, $businessName, $cityRegion, $discoverySource, $role, $preferredLang,
                $refCode, $referredBy, $unsubToken, $ipAddress, $marketingConsent,
                $sellerBusinessType, $sellerOnlineStore, $supplierProducts, $supplierServiceArea,
                $businessSector, $businessNeed, $driverArea, $driverVehicle, $driverAvailability,
                $buyerInterest, $partnerInterest, $utmSource, $utmMedium, $utmCampaign, $utmContent, $referralSrc,
            ]);

            $newId = (int) $this->db->lastInsertId();
            $pos   = $this->assignPosition($newId, $role);

            $this->notifyAdmin($newId, $firstName, $lastName, $email, $role, $businessName, $referredBy, $pos);
            $this->sendConfirmation($email, $firstName, $role, $refCode, $pos, $fr, $businessName, $unsubToken);

            $url = url('/waitlist') . '?joined=1&pos=' . $pos . '&myref=' . $refCode . '&role=' . $role;
            jsonResponse(['success' => true, 'redirect' => $url]);

        } catch (\PDOException $e) {
            logger('Waitlist store error: ' . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $fr ? 'Une erreur est survenue.' : 'An error occurred. Please try again.']);
        }
    }

    /**
     * New waitlist signup: admin bell + email to the admin inbox (config/mail.php admin_email).
     */
    private function notifyAdmin(int $id, string $firstName, string $lastName, string $email, string $role, ?string $businessName, ?string $referredBy = null, int $pos = 0): void
    {
        $roleLabel = \App\Helpers\WaitlistHelper::ROLE_LABELS[$role] ?? $role;
        $name      = trim("{$firstName} {$lastName}");
        $suffix    = $businessName ? " ({$businessName})" : '';

        $referral = '';
        if ($referredBy) {
            $stmt = $this->db->prepare("SELECT first_name, last_name FROM waitlist WHERE referral_code = ? LIMIT 1");
            $stmt->execute([$referredBy]);
            $ref = $stmt->fetch();
            $refName  = $ref ? trim($ref['first_name'] . ' ' . $ref['last_name']) : '';
            $referral = $refName !== '' ? "{$refName} ({$referredBy})" : $referredBy;
        }

        $link = '/admin/waitlist?search=' . urlencode($email);

        \App\Helpers\NotificationHelper::add(
            'waitlist',
            "New Waitlist Signup: {$name}" . ($pos ? " ({$roleLabel} #{$pos})" : ''),
            "{$name}{$suffix} joined the waitlist as {$roleLabel} #{$pos} - {$email}" . ($referral ? " - referred by {$referral}" : ''),
            ['link' => $link, 'icon' => 'user-plus', 'priority' => 'normal']
        );

        try {
            $rows = ['Name' => $name, 'Email' => $email, 'Role' => $roleLabel, 'Position' => "{$roleLabel} #{$pos}"];
            if ($businessName) { $rows['Business'] = $businessName; }
            if ($referral)     { $rows['Referred by'] = $referral; }
            $rowsHtml = '';
            foreach ($rows as $k => $v) {
                $rowsHtml .= '<tr><td style="padding:6px 14px 6px 0;color:#6b7280;">' . $k . '</td>'
                           . '<td style="padding:6px 0;color:#111;font-weight:600;">' . htmlspecialchars($v) . '</td></tr>';
            }
            $adminUrl = htmlspecialchars(url(ltrim($link, '/')));
            $body = '<div style="font-family:Segoe UI,Arial,sans-serif;font-size:14px;color:#374151;">'
                  . '<h2 style="color:#00b207;font-size:18px;margin:0 0 12px;">New waitlist signup: ' . htmlspecialchars("{$roleLabel} #{$pos}") . '</h2>'
                  . '<table style="border-collapse:collapse;">' . $rowsHtml . '</table>'
                  . '<p style="margin:18px 0 0;"><a href="' . $adminUrl . '" style="background:#00b207;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600;">Open in Admin &gt; Waitlist</a></p>'
                  . '<p style="margin:14px 0 0;color:#9ca3af;font-size:12px;">During beta, send this person their account invite from Admin &gt; Waitlist.</p>'
                  . '</div>';
            EmailHelper::setNextMeta('waitlist_admin_alert', 'waitlist', $id);
            EmailHelper::send(\App\Helpers\WaitlistHelper::adminEmail(), "New waitlist signup: {$name} ({$roleLabel} #{$pos})", $body);
        } catch (\Throwable $e) {
            logger('Waitlist admin email failed: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Fix the entry's position within its role ("Seller #3") and store it, so later
     * deletions never change a number the person was already told. Counting same-role
     * ids <= this one stays unique even for simultaneous signups (ids only increase).
     */
    private function assignPosition(int $id, string $role): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM waitlist WHERE role = ? AND id <= ?");
        $stmt->execute([$role, $id]);
        $pos = (int) $stmt->fetchColumn();
        $this->db->prepare("UPDATE waitlist SET signup_position = ? WHERE id = ?")->execute([$pos, $id]);
        return $pos;
    }

    private function generateCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
            $stmt = $this->db->prepare("SELECT id FROM waitlist WHERE referral_code = ?");
            $stmt->execute([$code]);
        } while ($stmt->fetch());

        return $code;
    }

    private function sendConfirmation(string $email, string $firstName, string $role, string $refCode, int $pos, bool $fr, ?string $businessName = null, string $unsubToken = ''): void
    {
        // Both languages provided so the email is always bilingual (FR + EN),
        // regardless of which language the visitor used on the form.
        $roleLabelsFr = [
            'buyer'    => 'Acheteur',
            'seller'   => 'Vendeur',
            'supplier' => 'Fournisseur',
            'driver'   => 'Livreur',
            'business' => 'Client Distribution',
            'partner'  => 'Partenaire',
        ];
        $roleLabelsEn = [
            'buyer'    => 'Buyer',
            'seller'   => 'Seller',
            'supplier' => 'Supplier',
            'driver'   => 'Driver',
            'business' => 'Business Client',
            'partner'  => 'Partner',
        ];

        $refUrl      = url('/waitlist') . '?ref=' . $refCode;
        $unsubUrl    = url('/waitlist/unsubscribe') . '?t=' . $unsubToken;

        // Role-specific next step: link to that role's Central page
        $centralByRole = [
            'buyer'    => ['buyer-central',    'Acheteur Central',    'Buyer Central'],
            'seller'   => ['seller-central',   'Vendeur Central',     'Seller Central'],
            'supplier' => ['supplier-central', 'Fournisseur Central', 'Supplier Central'],
            'driver'   => ['driver-central',   'Livreur Central',     'Driver Central'],
            'business' => ['distribution',     'Entreprise Centrale', 'Business Central'],
        ];
        $central = $centralByRole[$role] ?? null;
        $centralUrl = $central ? url($central[0]) : null;
        $roleLabelFr = $roleLabelsFr[$role] ?? $role;
        $roleLabelEn = $roleLabelsEn[$role] ?? $role;

        // Bilingual subject (FR first per QC law)
        $subject = 'Bienvenue sur la liste d\'attente OCSAPP / Welcome to the OCSAPP waitlist';

        ob_start();
        require __DIR__ . '/../Views/emails/waitlist-confirmation.php';
        $body = ob_get_clean();

        EmailHelper::setNextMeta('waitlist_confirmation', 'waitlist', null);
        EmailHelper::send($email, $subject, $body);
    }
}
