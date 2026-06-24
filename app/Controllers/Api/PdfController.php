<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * PDF Generation Controller
 * Converts HTML content to PDF using DOMPDF
 */
class PdfController
{
    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check authentication - must be logged in as any admin tier
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized. Please log in as admin.']);
            exit;
        }
    }

    /**
     * Generate PDF from HTML content
     */
    public function generate(): void
    {
        try {
            // Get POST data
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['html'])) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'HTML content is required']);
                return;
            }

            $html = $input['html'];
            $filename = $input['filename'] ?? 'document.pdf';

            // Ensure filename ends with .pdf
            if (!str_ends_with(strtolower($filename), '.pdf')) {
                $filename .= '.pdf';
            }

            // Configure DOMPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'sans-serif');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('isPhpEnabled', false);

            // Create DOMPDF instance
            $dompdf = new Dompdf($options);

            // Load HTML content
            $dompdf->loadHtml($html);

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF
            $dompdf->render();

            // Output PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo $dompdf->output();

        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'PDF generation failed: ' . $e->getMessage()]);
        }
    }
}
