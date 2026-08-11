<?php

namespace App\Controllers\Api;

/**
 * Lightweight endpoint for polling buyer order status.
 * Used by the buyer order-detail page to detect status changes and trigger
 * a live reload - same pattern as Api\SupplierOrderStatusController.
 * GET /api/buyer/order/status?id=XX
 */
class BuyerOrderStatusController
{
    public function status(): void
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache');

        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $orderId = (int) ($_GET['id'] ?? 0);
        if (!$orderId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $pdo  = \Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT status, driver_status, updated_at
                FROM orders
                WHERE id = ? AND user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$orderId, userId()]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
                exit;
            }

            echo json_encode([
                'success'       => true,
                'status'        => $row['status'],
                'driver_status' => $row['driver_status'],
                'updated_at'    => $row['updated_at'],
            ]);
        } catch (\Exception $e) {
            logger("BuyerOrderStatusController::status() failed: " . $e->getMessage(), 'error');
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
        exit;
    }
}
