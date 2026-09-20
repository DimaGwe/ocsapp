<?php

namespace App\Controllers;

use App\Helpers\EmailHelper;

/**
 * PageController - Static Pages (Terms, Privacy, About, Contact)
 * All pages support ES/EN/HT trilingual interface
 */
class PageController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Terms of Service page
     */
    public function terms(): void
    {
        try {
            // Get language from session or default to English
            $language = $_SESSION['language'] ?? 'fr';

            // Load terms from database
            $stmt = $this->db->prepare("
                SELECT * FROM legal_content
                WHERE page_type = 'terms'
                AND language = ?
                AND is_published = 1
                ORDER BY version DESC
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            // Fallback to English if language version not found
            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM legal_content
                    WHERE page_type = 'terms'
                    AND language = 'en'
                    AND is_published = 1
                    ORDER BY version DESC
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            // Fallback to static view if not in database
            if (!$page) {
                view('legal/terms');
                return;
            }

            view('legal/dynamic', ['page' => $page]);

        } catch (\PDOException $e) {
            logger("Error loading terms: " . $e->getMessage(), 'error');
            view('legal/terms'); // Fallback to static view
        }
    }

    /**
     * Privacy Policy page
     */
    public function privacy(): void
    {
        try {
            // Get language from session or default to English
            $language = $_SESSION['language'] ?? 'fr';

            // Load privacy policy from database
            $stmt = $this->db->prepare("
                SELECT * FROM legal_content
                WHERE page_type = 'privacy'
                AND language = ?
                AND is_published = 1
                ORDER BY version DESC
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            // Fallback to English if language version not found
            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM legal_content
                    WHERE page_type = 'privacy'
                    AND language = 'en'
                    AND is_published = 1
                    ORDER BY version DESC
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            // Fallback to static view if not in database
            if (!$page) {
                view('legal/privacy');
                return;
            }

            view('legal/dynamic', ['page' => $page]);

        } catch (\PDOException $e) {
            logger("Error loading privacy policy: " . $e->getMessage(), 'error');
            view('legal/privacy'); // Fallback to static view
        }
    }

    /**
     * Cookie Policy page
     */
    public function cookies(): void
    {
        try {
            $language = $_SESSION['language'] ?? 'fr';

            $stmt = $this->db->prepare("
                SELECT * FROM legal_content
                WHERE page_type = 'cookies'
                AND language = ?
                AND is_published = 1
                ORDER BY version DESC
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM legal_content
                    WHERE page_type = 'cookies'
                    AND language = 'en'
                    AND is_published = 1
                    ORDER BY version DESC
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            if (!$page) {
                view('legal/cookies');
                return;
            }

            view('legal/dynamic', ['page' => $page]);

        } catch (\PDOException $e) {
            logger("Error loading cookie policy: " . $e->getMessage(), 'error');
            view('legal/cookies'); // Fallback to static view
        }
    }

    public function accessibility(): void
    {
        try {
            $language = $_SESSION['language'] ?? 'fr';

            $stmt = $this->db->prepare("
                SELECT * FROM legal_content
                WHERE page_type = 'accessibility'
                AND language = ?
                AND is_published = 1
                ORDER BY version DESC
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM legal_content
                    WHERE page_type = 'accessibility'
                    AND language = 'en'
                    AND is_published = 1
                    ORDER BY version DESC
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            if (!$page) {
                view('legal/accessibility');
                return;
            }

            view('legal/dynamic', ['page' => $page]);

        } catch (\PDOException $e) {
            logger("Error loading accessibility statement: " . $e->getMessage(), 'error');
            view('legal/accessibility'); // Fallback to static view
        }
    }

    /**
     * Return & Refund Policy page
     */
    public function returns(): void
    {
        try {
            $language = $_SESSION['language'] ?? 'en';

            $stmt = $this->db->prepare("
                SELECT * FROM legal_content
                WHERE page_type = 'refund'
                AND language = ?
                AND is_published = 1
                ORDER BY version DESC
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM legal_content
                    WHERE page_type = 'refund'
                    AND language = 'en'
                    AND is_published = 1
                    ORDER BY version DESC
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            if (!$page) {
                view('legal/returns');
                return;
            }

            view('legal/dynamic', ['page' => $page]);

        } catch (\PDOException $e) {
            logger("Error loading returns policy: " . $e->getMessage(), 'error');
            view('legal/returns');
        }
    }

    /**
     * Seller Agreement page
     */
    public function sellerAgreement(): void
    {
        $this->loadLegalPage('seller_agreement', 'Seller Agreement');
    }

    /**
     * Supplier Agreement page
     */
    public function supplierAgreement(): void
    {
        $this->loadLegalPage('supplier_agreement', 'Supplier Agreement');
    }

    /**
     * Driver / Contractor Agreement page
     */
    public function driverAgreement(): void
    {
        $this->loadLegalPage('driver_agreement', 'Driver Agreement');
    }

    /**
     * Distribution Service Agreement page
     */
    public function distributionAgreement(): void
    {
        $this->loadLegalPage('distribution_agreement', 'Distribution Agreement');
    }

    /**
     * Non-Disclosure Agreement page
     */
    public function nda(): void
    {
        $this->loadLegalPage('nda', 'Non-Disclosure Agreement');
    }

    /**
     * Generic legal page loader from legal_content table
     */
    private function loadLegalPage(string $pageType, string $fallbackTitle): void
    {
        try {
            $language = $_SESSION['language'] ?? 'en';

            $stmt = $this->db->prepare("
                SELECT * FROM legal_content
                WHERE page_type = ?
                AND language = ?
                AND is_published = 1
                ORDER BY version DESC
                LIMIT 1
            ");
            $stmt->execute([$pageType, $language]);
            $page = $stmt->fetch();

            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM legal_content
                    WHERE page_type = ?
                    AND language = 'en'
                    AND is_published = 1
                    ORDER BY version DESC
                    LIMIT 1
                ");
                $stmt->execute([$pageType]);
                $page = $stmt->fetch();
            }

            if ($page) {
                view('legal/dynamic', ['page' => $page]);
            } else {
                http_response_code(404);
                echo htmlspecialchars($fallbackTitle) . ' not found.';
            }

        } catch (\PDOException $e) {
            logger("Error loading {$pageType}: " . $e->getMessage(), 'error');
            http_response_code(500);
            echo 'Error loading page.';
        }
    }

    /**
     * About Us page - Load from CMS database
     */
    public function about(): void
    {
        view('pages/about');
    }

    /**
     * Contact page - Load from CMS database
     */
    public function contact(): void
    {
        view('pages/contact');
    }

    /**
     * Seller Central - Public landing page for sellers
     */
    public function sellerCentral(): void
    {
        view('pages/seller-central');
    }

    /**
     * Buyer Central - Public landing page for buyers
     */
    public function buyerCentral(): void
    {
        view('pages/buyer-central');
    }

    /**
     * Driver Central - Public landing page for delivery drivers
     */
    public function driverCentral(): void
    {
        view('pages/driver-central');
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(): void
    {
        // Validate CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $lang = $_SESSION['language'] ?? 'en';
        $fr = ($lang === 'fr');

        $name = sanitize(post('name', ''));
        $email = sanitize(post('email', ''));
        $phone = sanitize(post('phone', ''));
        $contactMethod = sanitize(post('contact_method', 'Email'));
        $role = sanitize(post('role', ''));
        $organization = sanitize(post('organization', ''));
        $subject = sanitize(post('subject', ''));
        $priority = sanitize(post('priority', 'Normal'));
        $reference = sanitize(post('reference', ''));
        $message = sanitize(post('message', ''));
        $privacyConsent = post('privacy_consent', '') === 'accepted';

        // Validate inputs
        if (empty($name) || empty($email) || empty($role) || empty($subject) || empty($message)) {
            jsonResponse(['success' => false, 'message' => $fr ? 'Tous les champs obligatoires doivent être remplis.' : 'Please fill in all required fields.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => $fr ? 'Adresse courriel invalide.' : 'Invalid email address.']);
            return;
        }

        if (!$privacyConsent) {
            jsonResponse(['success' => false, 'message' => $fr ? 'Veuillez accepter la Politique de confidentialité.' : 'Please accept the Privacy Policy.']);
            return;
        }

        try {
            // Save to database
            $stmt = $this->db->prepare("
                INSERT INTO contact_messages
                (name, email, phone, contact_method, role, organization, subject, priority, reference_number, message, privacy_consent, language, created_at)
                VALUES (:name, :email, :phone, :contact_method, :role, :organization, :subject, :priority, :reference_number, :message, :privacy_consent, :language, NOW())
            ");

            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone ?: null,
                'contact_method' => $contactMethod ?: 'Email',
                'role' => $role,
                'organization' => $organization ?: null,
                'subject' => $subject ?: 'General Inquiry',
                'priority' => $priority ?: 'Normal',
                'reference_number' => $reference ?: null,
                'message' => $message,
                'privacy_consent' => 1,
                'language' => $lang
            ]);

            logger("Contact form submitted by {$name} ({$email})", 'info');

            // Send notification email to admin
            try {
                $adminEmail = env('MAIL_FROM_ADDRESS', 'support@ocsapp.ca');
                $emailSubject = "New Contact Form [{$priority}]: " . ($subject ?: 'General Inquiry');
                $emailBody = "
                    <h2>New Contact Form Submission</h2>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Phone:</strong> " . ($phone ?: 'N/A') . "</p>
                    <p><strong>Preferred contact method:</strong> {$contactMethod}</p>
                    <p><strong>Role / Central:</strong> {$role}</p>
                    <p><strong>Organization:</strong> " . ($organization ?: 'N/A') . "</p>
                    <p><strong>Subject:</strong> " . ($subject ?: 'General Inquiry') . "</p>
                    <p><strong>Priority:</strong> {$priority}</p>
                    <p><strong>Reference:</strong> " . ($reference ?: 'N/A') . "</p>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                ";

                EmailHelper::sendRaw($adminEmail, $emailSubject, $emailBody);
            } catch (\Exception $e) {
                logger("Failed to send contact notification email: " . $e->getMessage(), 'warning');
            }

            jsonResponse([
                'success' => true,
                'message' => $fr ? 'Merci! Votre demande a été envoyée. Notre équipe vous répondra dans les meilleurs délais.' : 'Thank you! Your request has been sent. Our team will get back to you as soon as possible.'
            ]);

        } catch (\PDOException $e) {
            logger("Error saving contact message: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $fr ? 'Échec de l\'envoi. Veuillez réessayer.' : 'Failed to send message. Please try again.']);
        }
    }
}
