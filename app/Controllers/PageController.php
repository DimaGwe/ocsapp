<?php

namespace App\Controllers;

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
     * About Us page - Load from CMS database
     */
    public function about(): void
    {
        try {
            $language = $_SESSION['language'] ?? 'fr';

            // Load about page from database
            $stmt = $this->db->prepare("
                SELECT * FROM content_pages
                WHERE page_type = 'about'
                AND language = ?
                AND is_published = 1
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            // Fallback to English if language version not found
            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM content_pages
                    WHERE page_type = 'about'
                    AND language = 'en'
                    AND is_published = 1
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            if (!$page) {
                http_response_code(404);
                echo "About Us page not found";
                return;
            }

            require __DIR__ . '/../Views/pages/content-page.php';
        } catch (\Exception $e) {
            error_log("Error loading about page: " . $e->getMessage());
            http_response_code(500);
            echo "Error loading page";
        }
    }

    /**
     * Contact page - Load from CMS database
     */
    public function contact(): void
    {
        try {
            $language = $_SESSION['language'] ?? 'fr';

            // Load contact page from database
            $stmt = $this->db->prepare("
                SELECT * FROM content_pages
                WHERE page_type = 'contact'
                AND language = ?
                AND is_published = 1
                LIMIT 1
            ");
            $stmt->execute([$language]);
            $page = $stmt->fetch();

            // Fallback to English if language version not found
            if (!$page && $language !== 'en') {
                $stmt = $this->db->prepare("
                    SELECT * FROM content_pages
                    WHERE page_type = 'contact'
                    AND language = 'en'
                    AND is_published = 1
                    LIMIT 1
                ");
                $stmt->execute();
                $page = $stmt->fetch();
            }

            if (!$page) {
                http_response_code(404);
                echo "Contact page not found";
                return;
            }

            require __DIR__ . '/../Views/pages/content-page.php';
        } catch (\Exception $e) {
            error_log("Error loading contact page: " . $e->getMessage());
            http_response_code(500);
            echo "Error loading page";
        }
    }

    /**
     * Seller Central - Public landing page for sellers
     */
    public function sellerCentral(): void
    {
        view('pages/seller-central');
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

        $name = sanitize(post('name', ''));
        $email = sanitize(post('email', ''));
        $subject = sanitize(post('subject', ''));
        $message = sanitize(post('message', ''));

        // Validate inputs
        if (empty($name) || empty($email) || empty($message)) {
            jsonResponse(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Invalid email address']);
            return;
        }

        try {
            // Save to database
            $stmt = $this->db->prepare("
                INSERT INTO contact_messages
                (name, email, subject, message, created_at)
                VALUES (:name, :email, :subject, :message, NOW())
            ");

            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'subject' => $subject ?: 'General Inquiry',
                'message' => $message
            ]);

            logger("Contact form submitted by {$name} ({$email})", 'info');

            // Send notification email to admin
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';

                $adminEmail = env('MAIL_FROM_ADDRESS', 'support@ocsapp.ca');
                $emailSubject = "New Contact Form: " . ($subject ?: 'General Inquiry');
                $emailBody = "
                    <h2>New Contact Form Submission</h2>
                    <p><strong>Name:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Subject:</strong> " . ($subject ?: 'General Inquiry') . "</p>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                ";

                // Simple email send (you can enhance this with EmailHelper)
                mail($adminEmail, $emailSubject, $emailBody, "From: {$email}\r\nContent-Type: text/html; charset=UTF-8");

            } catch (\Exception $e) {
                logger("Failed to send contact notification email: " . $e->getMessage(), 'warning');
            }

            jsonResponse([
                'success' => true,
                'message' => 'Message sent successfully! We will get back to you soon.'
            ]);

        } catch (\PDOException $e) {
            logger("Error saving contact message: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to send message. Please try again.']);
        }
    }
}
