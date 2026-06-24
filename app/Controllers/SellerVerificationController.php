<?php

namespace App\Controllers;

use Database;

class SellerVerificationController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Show verification page
     */
    public function index(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        require view('seller/verification');
    }

    /**
     * Submit verification information
     */
    public function submit(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        try {
            $userId = userId();

            // Validate required fields
            $businessName = trim(post('business_name'));
            $businessAddress = trim(post('business_address'));

            if (empty($businessName) || empty($businessAddress)) {
                setFlash('error', 'Business name and address are required.');
                back();
                return;
            }

            // Handle document uploads
            $documents = [];
            if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
                $uploadDir = __DIR__ . '/../../storage/uploads/verification-documents/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                $maxFileSize = 5 * 1024 * 1024; // 5MB

                for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
                    if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileName = $_FILES['documents']['name'][$i];
                        $fileTmpName = $_FILES['documents']['tmp_name'][$i];
                        $fileSize = $_FILES['documents']['size'][$i];
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        // Validate extension
                        if (!in_array($fileExtension, $allowedExtensions)) {
                            setFlash('error', "Invalid file type for {$fileName}. Only PDF, JPG, and PNG files are allowed.");
                            back();
                            return;
                        }

                        // Validate size
                        if ($fileSize > $maxFileSize) {
                            setFlash('error', "{$fileName} is too large. Maximum file size is 5MB.");
                            back();
                            return;
                        }

                        // Generate unique filename
                        $newFileName = 'seller_' . $userId . '_' . uniqid() . '.' . $fileExtension;
                        $uploadPath = $uploadDir . $newFileName;

                        if (move_uploaded_file($fileTmpName, $uploadPath)) {
                            $documents[] = [
                                'name' => $fileName,
                                'path' => 'verification-documents/' . $newFileName,
                                'uploaded_at' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                }
            }

            // Get existing documents if any
            $stmt = $this->db->prepare("SELECT verification_documents FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $existingDocuments = !empty($user['verification_documents'])
                ? json_decode($user['verification_documents'], true)
                : [];

            // Merge with existing documents
            if (!empty($documents)) {
                $allDocuments = array_merge($existingDocuments, $documents);
            } else {
                $allDocuments = $existingDocuments;
            }

            // Require at least one document
            if (empty($allDocuments)) {
                setFlash('error', 'Please upload at least one verification document.');
                back();
                return;
            }

            // Update user record
            $stmt = $this->db->prepare("
                UPDATE users
                SET business_name = ?,
                    business_number = ?,
                    tax_id = ?,
                    business_address = ?,
                    verification_documents = ?,
                    verification_status = 'pending',
                    verification_submitted_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $businessName,
                post('business_number'),
                post('tax_id'),
                $businessAddress,
                json_encode($allDocuments),
                $userId
            ]);

            // Send notification email to admin
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $seller = $stmt->fetch();

                \App\Helpers\EmailHelper::sendSellerVerificationSubmitted($seller);

                // Admin bell notification
                \App\Helpers\NotificationHelper::sellerVerificationSubmitted($seller);
            } catch (\Exception $emailError) {
                logger("Failed to send verification notification email: " . $emailError->getMessage(), 'error');
            }

            setFlash('success', 'Verification submitted successfully! We\'ll review your information within 1-2 business days.');
            redirect('seller/verification');

        } catch (\PDOException $e) {
            logger("Verification submission error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }
}
