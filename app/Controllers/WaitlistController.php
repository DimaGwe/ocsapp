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
        $ref          = sanitize(post('ref', ''));

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
            $stmt = $this->db->prepare("SELECT id, referral_code FROM waitlist WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                $pos = $this->getPosition($existing['id']);
                $url = url('/waitlist') . '?joined=1&pos=' . $pos . '&myref=' . $existing['referral_code'] . '&role=' . $role;
                jsonResponse(['success' => true, 'redirect' => $url]);
                return;
            }

            // Validate referrer code
            $referredBy = null;
            if ($ref) {
                $stmt = $this->db->prepare("SELECT referral_code FROM waitlist WHERE referral_code = ?");
                $stmt->execute([$ref]);
                if ($stmt->fetch()) {
                    $referredBy = $ref;
                }
            }

            $refCode   = $this->generateCode();
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
            if ($ipAddress) {
                $ipAddress = substr(explode(',', $ipAddress)[0], 0, 45);
            }

            $stmt = $this->db->prepare("
                INSERT INTO waitlist (
                    email, phone, first_name, last_name, business_name, city_region, discovery_source, role, locale,
                    referral_code, referred_by, ip_address, marketing_consent,
                    seller_business_type, seller_online_store, supplier_products, supplier_service_area,
                    business_sector, business_need, driver_area, driver_vehicle, driver_availability,
                    buyer_interest, partner_interest, utm_source, utm_medium, utm_campaign, utm_content, referral_source
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $email, $phone, $firstName, $lastName, $businessName, $cityRegion, $discoverySource, $role, $preferredLang,
                $refCode, $referredBy, $ipAddress, $marketingConsent,
                $sellerBusinessType, $sellerOnlineStore, $supplierProducts, $supplierServiceArea,
                $businessSector, $businessNeed, $driverArea, $driverVehicle, $driverAvailability,
                $buyerInterest, $partnerInterest, $utmSource, $utmMedium, $utmCampaign, $utmContent, $referralSrc,
            ]);

            $newId = (int) $this->db->lastInsertId();
            $pos   = $this->getPosition($newId);

            $this->notifyAdmin($newId, $firstName, $lastName, $email, $role, $businessName);
            $this->sendConfirmation($email, $firstName, $role, $refCode, $pos, $fr, $businessName);

            $url = url('/waitlist') . '?joined=1&pos=' . $pos . '&myref=' . $refCode . '&role=' . $role;
            jsonResponse(['success' => true, 'redirect' => $url]);

        } catch (\PDOException $e) {
            logger('Waitlist store error: ' . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $fr ? 'Une erreur est survenue.' : 'An error occurred. Please try again.']);
        }
    }

    private function notifyAdmin(int $id, string $firstName, string $lastName, string $email, string $role, ?string $businessName): void
    {
        $roleLabels = [
            'buyer'    => 'Buyer',
            'seller'   => 'Seller',
            'supplier' => 'Supplier',
            'driver'   => 'Driver',
            'business' => 'Business',
            'partner'  => 'Partner',
        ];
        $roleLabel = $roleLabels[$role] ?? $role;
        $name      = trim("{$firstName} {$lastName}");
        $suffix    = $businessName ? " ({$businessName})" : '';

        \App\Helpers\NotificationHelper::add(
            'waitlist',
            "New Waitlist Signup: {$name}",
            "{$name}{$suffix} joined the waitlist as a {$roleLabel} - {$email}",
            ['link' => '/admin/waitlist', 'icon' => 'user-plus', 'priority' => 'normal']
        );
    }

    private function getPosition(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM waitlist WHERE id <= ?");
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
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

    private function sendConfirmation(string $email, string $firstName, string $role, string $refCode, int $pos, bool $fr, ?string $businessName = null): void
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
        $roleLabelFr = $roleLabelsFr[$role] ?? $role;
        $roleLabelEn = $roleLabelsEn[$role] ?? $role;

        // Bilingual subject (FR first per QC law)
        $subject = 'Vous êtes sur la liste ! / You\'re on the list! - OCSAPP';

        ob_start();
        require __DIR__ . '/../Views/emails/waitlist-confirmation.php';
        $body = ob_get_clean();

        EmailHelper::setNextMeta('waitlist_confirmation', 'waitlist', null);
        EmailHelper::send($email, $subject, $body);
    }
}
