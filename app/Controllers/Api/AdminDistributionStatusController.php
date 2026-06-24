<?php

namespace App\Controllers\Api;

/**
 * Lightweight endpoint for polling distribution request + PO statuses on admin view.
 * GET /api/admin/distribution/status?id=XX
 */
class AdminDistributionStatusController
{
    public function status(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');
        header('Cache-Control: no-cache');

        if (empty($_SESSION['admin_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $requestId = (int) ($_GET['id'] ?? 0);
        if (!$requestId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            exit;
        }

        try {
            $pdo = \Database::getConnection();

            // DR status
            $stmt = $pdo->prepare("SELECT status FROM distribution_requests WHERE id = ? LIMIT 1");
            $stmt->execute([$requestId]);
            $dr = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$dr) {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
                exit;
            }

            // PO statuses (id → status + admin_paid_at + supplier_accepted_at)
            $stmt = $pdo->prepare("
                SELECT id, status, admin_paid_at, supplier_accepted_at, supplier_declined_at
                FROM purchase_orders
                WHERE distribution_request_id = ?
                ORDER BY id ASC
            ");
            $stmt->execute([$requestId]);
            $pos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $poStatuses = [];
            foreach ($pos as $po) {
                $poStatuses[$po['id']] = [
                    'status'               => $po['status'],
                    'admin_paid_at'        => $po['admin_paid_at'] ?? null,
                    'supplier_accepted_at' => $po['supplier_accepted_at'] ?? null,
                    'supplier_declined_at' => $po['supplier_declined_at'] ?? null,
                ];
            }

            // Delivery assignment snapshot (picks up driver pickup + status)
            $stmt = $pdo->prepare("
                SELECT status, picked_up_at, delivered_at,
                       heading_to_supplier_at, en_route_to_customer_at
                FROM delivery_assignments
                WHERE distribution_request_id = ? AND delivery_type = 'distribution'
                  AND status NOT IN ('cancelled','failed')
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute([$requestId]);
            $da = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;

            echo json_encode([
                'success'     => true,
                'dr_status'   => $dr['status'],
                'po_statuses' => $poStatuses,
                'da'          => $da ? [
                    'status'                 => $da['status'],
                    'picked_up_at'           => $da['picked_up_at'] ?? null,
                    'delivered_at'           => $da['delivered_at'] ?? null,
                    'heading_to_supplier_at' => $da['heading_to_supplier_at'] ?? null,
                    'en_route_to_customer_at'=> $da['en_route_to_customer_at'] ?? null,
                ] : null,
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
        }
    }
}
