<?php

namespace App\Controllers;

/**
 * SellerAuthController
 * Rich seller application flow (business/NEQ/legal/address/package/documents),
 * mirroring SupplierAuthController's application behavior while staying inside
 * the existing users/roles/shops schema and session system.
 */
class SellerAuthController
{
    /**
     * Show the seller application form
     */
    public function apply(): void
    {
        \App\Middlewares\AuthMiddleware::guest();
        \App\Helpers\BetaAccessHelper::guardPage('seller');

        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        view('seller.apply', [
            'pageTitle' => 'Become a Seller - OCSAPP',
            'flash' => $flash,
            'old' => $_SESSION['_old_input'] ?? [],
        ]);

        unset($_SESSION['_old_input']);
    }

    /**
     * Process the seller application form submission
     */
    public function submitApplication(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }
        \App\Helpers\BetaAccessHelper::guardSubmit('seller', (string) post('email', ''));

        // Package selection -> commission rates (existing AdminShopController::updatePackage() map)
        $validPackages = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
        $rawPackage = trim(post('subscription_package', 'Essential'));
        $package = in_array($rawPackage, $validPackages, true) ? $rawPackage : 'Essential';
        $commissionMap = [
            'Essential'  => ['delivery' => 15.00, 'pickup' => 8.00],
            'Experience' => ['delivery' => 12.00, 'pickup' => 6.00],
            'Prestige'   => ['delivery' => 10.00, 'pickup' => 5.00],
            'Enterprise' => ['delivery' => 12.00, 'pickup' => 6.00],
        ];
        $commissionRate = $commissionMap[$package]['delivery'];
        $pickupCommissionRate = $commissionMap[$package]['pickup'];

        $data = [
            'first_name' => sanitize(post('first_name', '')),
            'last_name' => sanitize(post('last_name', '')),
            'email' => sanitize(post('email', '')),
            'phone' => sanitize(post('phone', '')),
            'business_name' => sanitize(post('business_name', '')),
            'neq_number' => trim(post('neq_number', '')),
            'legal_name' => sanitize(post('legal_name', '')),
            'operating_names' => sanitize(post('operating_names', '')),
            'registered_address_street' => sanitize(post('registered_address_street', '')),
            'registered_address_city' => sanitize(post('registered_address_city', '')),
            'registered_address_province' => sanitize(post('registered_address_province', 'Quebec')),
            'registered_address_postal' => sanitize(post('registered_address_postal', '')),
            'subscription_package' => $package,
        ];

        $_SESSION['_old_input'] = $data;

        $required = [
            'first_name', 'last_name', 'email', 'business_name',
            'neq_number', 'legal_name',
            'registered_address_street', 'registered_address_city', 'registered_address_postal',
        ];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                setFlash('error', 'Please fill in all required fields.');
                back();
                return;
            }
        }

        if (!validateEmail($data['email'])) {
            setFlash('error', 'Invalid email format');
            back();
            return;
        }

        if (!preg_match('/^[0-9]{10}$/', $data['neq_number'])) {
            setFlash('error', 'NEQ must be exactly 10 digits.');
            back();
            return;
        }

        if (post('terms', '') !== 'on') {
            setFlash('error', 'You must accept the Terms of Service and Privacy Policy');
            back();
            return;
        }

        if (post('seller_agreement', '') !== 'on') {
            setFlash('error', 'You must read and accept the Seller Agreement.');
            back();
            return;
        }

        $password = post('password', '');
        $passwordConfirmation = post('password_confirmation', '');
        $pwErrors = validatePasswordStrength($password);
        if (!empty($pwErrors)) {
            setFlash('error', passwordStrengthMessage($pwErrors));
            back();
            return;
        }
        if ($password !== $passwordConfirmation) {
            setFlash('error', passwordMismatchMessage());
            back();
            return;
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                setFlash('error', 'An account with this email already exists. Please <a href="' . url('login') . '">log in</a> instead.');
                back();
                return;
            }

            $banStmt = $db->prepare("SELECT id FROM deleted_users WHERE email = ? AND can_rejoin = 0 LIMIT 1");
            $banStmt->execute([$data['email']]);
            if ($banStmt->fetch()) {
                setFlash('error', 'This account has been disabled. Please contact us at info@ocsapp.ca for assistance.');
                back();
                return;
            }

            // Document uploads - same validated pattern as SupplierAuthController::submitApplication()
            // Private folder (storage/), served via PrivateDocumentController
            $uploadDir = 'uploads/seller-applications';
            $fullUploadDir = \App\Helpers\PrivateUploadHelper::dir('seller-applications');

            $docFields = ['doc_certificate_incorporation', 'doc_declaration_registration', 'doc_enterprise_register'];
            $docPaths = [];
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            foreach ($docFields as $fieldName) {
                $docPaths[$fieldName] = null;

                if (!empty($_FILES[$fieldName]['tmp_name']) && is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
                    $file = $_FILES[$fieldName];

                    if ($file['size'] > $maxSize) {
                        setFlash('error', 'Document file size must be less than 5MB.');
                        back();
                        return;
                    }

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExts, true)) {
                        setFlash('error', 'Only PDF, JPG, and PNG files are allowed for documents.');
                        back();
                        return;
                    }

                    $filename = basename($file['name']);
                    if (preg_match('/\.(php|phtml|php3|php4|php5|phar|exe|sh|bat|cmd)/i', pathinfo($filename, PATHINFO_FILENAME))) {
                        logger("Suspicious seller doc upload blocked: {$filename}", 'error');
                        setFlash('error', 'Invalid file detected.');
                        back();
                        return;
                    }

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    if (!in_array($mimeType, $allowedMimes, true)) {
                        setFlash('error', 'Invalid file type detected.');
                        back();
                        return;
                    }

                    $safeFilename = 'sellapp_' . uniqid('', true) . '_' . time() . '.' . $ext;
                    $destPath = $fullUploadDir . '/' . $safeFilename;

                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        chmod($destPath, 0644);
                        $docPaths[$fieldName] = $uploadDir . '/' . $safeFilename;
                    }
                }
            }

            $db->beginTransaction();

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $registrationIp = $_SERVER['HTTP_CF_CONNECTING_IP']
                ?? $_SERVER['HTTP_X_FORWARDED_FOR']
                ?? $_SERVER['REMOTE_ADDR']
                ?? null;
            if ($registrationIp && strpos($registrationIp, ',') !== false) {
                $registrationIp = trim(explode(',', $registrationIp)[0]);
            }

            // Same convention as AuthController::register(): unverified until email code confirmed
            $stmt = $db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, terms_accepted_at, terms_accepted_ip, status, role, seller_agreement_accepted_at)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, 'unverified', 'seller', NOW())
            ");
            $stmt->execute([
                $data['email'], $hashedPassword, $data['first_name'], $data['last_name'],
                $data['phone'], $registrationIp,
            ]);
            $userId = (int) $db->lastInsertId();

            $stmt = $db->prepare("SELECT id FROM roles WHERE name = 'seller' LIMIT 1");
            $stmt->execute();
            $role = $stmt->fetch();
            if (!$role) {
                throw new \Exception("Role 'seller' not found in database. Please run database seeders.");
            }
            $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$userId, $role['id']]);

            // Shop row created immediately, same is_approved/is_active=0 gate ShopController::store() uses today
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['business_name'])) . '-' . time();
            $stmt = $db->prepare("
                INSERT INTO shops (
                    seller_id, name, slug,
                    neq_number, legal_name, operating_names,
                    registered_address_street, registered_address_city, registered_address_province, registered_address_postal,
                    doc_certificate_incorporation, doc_declaration_registration, doc_enterprise_register,
                    subscription_package, commission_rate, pickup_commission_rate,
                    is_approved, is_active, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW(), NOW())
            ");
            $stmt->execute([
                $userId, $data['business_name'], $slug,
                $data['neq_number'], $data['legal_name'], $data['operating_names'],
                $data['registered_address_street'], $data['registered_address_city'],
                $data['registered_address_province'], $data['registered_address_postal'],
                $docPaths['doc_certificate_incorporation'], $docPaths['doc_declaration_registration'], $docPaths['doc_enterprise_register'],
                $package, $commissionRate, $pickupCommissionRate,
            ]);

            // Generate email verification code - reuses the SAME /verify-email flow as AuthController::register()
            $verificationCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $db->prepare("
                UPDATE users SET email_verification_code = ?, email_verification_expires_at = ? WHERE id = ?
            ")->execute([$verificationCode, $verificationExpires, $userId]);

            $db->commit();

            $_SESSION['pending_user_verification'] = [
                'user_id' => $userId,
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'role' => 'seller',
            ];
            $_SESSION['verification_attempts'] = 0;
            unset($_SESSION['_old_input']);

            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendUserVerificationCode([
                    'first_name' => $data['first_name'],
                    'email' => $data['email'],
                    'verification_code' => $verificationCode,
                    'verify_url_fr' => $appUrl . '/verify-email?lang=fr',
                    'verify_url_en' => $appUrl . '/verify-email?lang=en',
                    'magic_link_url_fr' => $appUrl . '/verify-email/auto?uid=' . $userId . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/verify-email/auto?uid=' . $userId . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
            } catch (\Exception $e) {
                logger("Failed to send seller verification code to {$data['email']}: " . $e->getMessage(), 'warning');
            }

            redirect(url('verify-email'));

        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            logger("SellerAuthController submitApplication error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred while submitting your application. Please try again.');
            back();
        }
    }
}
