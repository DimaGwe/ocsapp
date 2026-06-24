<?php

namespace App\Controllers\Api;

/**
 * Lightweight endpoint for polling distribution request status.
 * Used by the show page to detect status changes and trigger a live reload.
 * GET /api/distribution/request/status?id=XX
 */
class DistributionStatusController
{
    public function status(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');
        header('Cache-Control: no-cache');

        if (empty($_SESSION['business']['id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $businessId = (int) $_SESSION['business']['id'];
        $requestId  = (int) ($_GET['id'] ?? 0);

        if (!$requestId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $pdo  = \Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT status, payment_status
                FROM distribution_requests
                WHERE id = ? AND business_profile_id = ?
                LIMIT 1
            ");
            $stmt->execute([$requestId, $businessId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
                exit;
            }

            // Check if driver has accepted (stage 5 → 6 without status change)
            $driverAccepted = false;
            if ($row['status'] === 'ready') {
                $da = $pdo->prepare("
                    SELECT COUNT(*) FROM purchase_orders
                    WHERE distribution_request_id = ? AND driver_acceptance_status = 'accepted'
                ");
                $da->execute([$requestId]);
                $driverAccepted = (int)$da->fetchColumn() > 0;
            }

            echo json_encode([
                'success'         => true,
                'status'          => $row['status'],
                'payment_status'  => $row['payment_status'],
                'driver_accepted' => $driverAccepted,
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
    }
}
