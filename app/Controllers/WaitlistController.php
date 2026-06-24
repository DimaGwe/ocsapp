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

        $lang      = $_SESSION['language'] ?? 'fr';
        $fr        = ($lang === 'fr');
        $firstName = sanitize(post('first_name', ''));
        $lastName  = sanitize(post('last_name', ''));
        $email     = sanitize(post('email', ''));
        $role      = sanitize(post('role', ''));
        $ref       = sanitize(post('ref', ''));

        $validRoles = ['buyer', 'seller', 'supplier', 'driver', 'business'];

        if (empty($firstName) || empty($email) || empty($role)) {
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
                INSERT INTO waitlist (email, first_name, last_name, role, locale, referral_code, referred_by, ip_address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$email, $firstName, $lastName ?: null, $role, $lang, $refCode, $referredBy, $ipAddress]);

            $newId = (int) $this->db->lastInsertId();
            $pos   = $this->getPosition($newId);

            $this->sendConfirmation($email, $firstName, $role, $refCode, $pos, $fr);

            $url = url('/waitlist') . '?joined=1&pos=' . $pos . '&myref=' . $refCode . '&role=' . $role;
            jsonResponse(['success' => true, 'redirect' => $url]);

        } catch (\PDOException $e) {
            logger('Waitlist store error: ' . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $fr ? 'Une erreur est survenue.' : 'An error occurred. Please try again.']);
        }
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

    private function sendConfirmation(string $email, string $firstName, string $role, string $refCode, int $pos, bool $fr): void
    {
        // Both languages provided so the email is always bilingual (FR + EN),
        // regardless of which language the visitor used on the form.
        $roleLabelsFr = [
            'buyer'    => 'Acheteur',
            'seller'   => 'Vendeur',
            'supplier' => 'Fournisseur',
            'driver'   => 'Livreur',
            'business' => 'Client Distribution',
        ];
        $roleLabelsEn = [
            'buyer'    => 'Buyer',
            'seller'   => 'Seller',
            'supplier' => 'Supplier',
            'driver'   => 'Driver',
            'business' => 'Business Client',
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
