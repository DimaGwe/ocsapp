<?php

namespace App\Controllers\Api;

/**
 * Lightweight endpoint for polling supplier PO status.
 * Used by the order view page to detect status changes and trigger a live reload.
 * GET /api/supplier/order/status?id=XX
 */
class SupplierOrderStatusController
{
    public function status(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');
        header('Cache-Control: no-cache');

        $supplierId = (int) ($_SESSION['supplier_id'] ?? 0);
        if (!$supplierId) {
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
                SELECT status, driver_acceptance_status, assigned_driver_id, admin_paid_at
                FROM purchase_orders
                WHERE id = ? AND supplier_id = ?
                LIMIT 1
            ");
            $stmt->execute([$orderId, $supplierId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
                exit;
            }

            echo json_encode([
                'success'                 => true,
                'status'                  => $row['status'],
                'driver_acceptance_status'=> $row['driver_acceptance_status'],
                'assigned_driver_id'      => $row['assigned_driver_id'],
                'admin_paid_at'           => $row['admin_paid_at'] ?? null,
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
    }
}
