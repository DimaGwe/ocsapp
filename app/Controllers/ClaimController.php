<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/ClaimHelper.php';

/**
 * ClaimController - buyer-facing return/claim submission (Ecosystem
 * Backend Requirements Sec. 4.1/4.2, Track A only - B2C Marche orders).
 * Track B (business/distribution claims) would live in the Distribution
 * portal controllers, not built this round - see project tracker.
 */
class ClaimController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * GET /account/orders/claim?order_id=X - claim submission form
     */
    public function newClaim(): void
    {
        if (!isLoggedIn()) {
            redirect(url('login'));
            return;
        }

        $orderId = (int)get('order_id', 0);
        $userId = userId();

        $stmt = $this->db->prepare("
            SELECT o.*, s.name AS shop_name
            FROM orders o LEFT JOIN shops s ON s.id = o.shop_id
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            setFlash('error', 'Order not found.');
            redirect(url('account/orders'));
            return;
        }

        if ($order['status'] !== 'delivered') {
            setFlash('error', 'Claims can only be filed on delivered orders.');
            redirect(url('account/orders/detail?id=' . $orderId));
            return;
        }

        $itemsStmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

        view('buyer/claim-new', ['order' => $order, 'items' => $items]);
    }

    /**
     * POST /account/orders/claim
     */
    public function submitClaim(): void
    {
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login to continue']);
            return;
        }

        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $userId = userId();
        $orderId = (int)post('order_id', 0);
        $claimType = sanitize(post('claim_type', ''));
        $description = sanitize(post('description', ''));
        $claimedValue = (float)post('claimed_value', 0);

        $validTypes = ['damaged', 'missing_item', 'wrong_item', 'not_as_described', 'buyer_change_of_mind', 'other'];
        if (!in_array($claimType, $validTypes, true)) {
            jsonResponse(['success' => false, 'message' => 'Invalid claim type.']);
            return;
        }

        // Verify the order belongs to this buyer
        $chk = $this->db->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
        $chk->execute([$orderId, $userId]);
        if (!$chk->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Order not found.']);
            return;
        }

        // Evidence photo uploads (optional, multiple)
        $evidencePaths = [];
        if (!empty($_FILES['evidence']) && is_array($_FILES['evidence']['tmp_name'])) {
            $destDir = __DIR__ . '/../../public/uploads/claims/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            foreach ($_FILES['evidence']['tmp_name'] as $i => $tmpPath) {
                if ($_FILES['evidence']['error'][$i] !== UPLOAD_ERR_OK) continue;
                if ($_FILES['evidence']['size'][$i] > 10 * 1024 * 1024) continue;
                $mime = mime_content_type($tmpPath);
                if (!isset($allowed[$mime])) continue;
                $filename = "claim_{$orderId}_{$userId}_" . time() . "_{$i}.{$allowed[$mime]}";
                if (move_uploaded_file($tmpPath, $destDir . $filename)) {
                    $evidencePaths[] = 'uploads/claims/' . $filename;
                }
            }
        }

        $result = \App\Helpers\ClaimHelper::fileClaim(
            'A', $orderId, null, $userId, $claimType, $description, $evidencePaths, $claimedValue
        );

        if (!$result['success']) {
            jsonResponse(['success' => false, 'message' => $result['error']]);
            return;
        }

        // Notify admin
        require_once __DIR__ . '/../Helpers/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'claim',
            '📋 New Return Claim Filed',
            "Order #{$orderId}: {$claimType} claim filed, needs review.",
            ['link' => '/admin/claims/view?id=' . $result['claim_id'], 'icon' => 'file-invoice', 'priority' => 'normal']
        );

        jsonResponse(['success' => true, 'message' => 'Your claim has been submitted.', 'redirect' => url('account/claims')]);
    }

    /**
     * GET /account/claims - buyer's own claim list
     */
    public function myClaims(): void
    {
        if (!isLoggedIn()) {
            redirect(url('login'));
            return;
        }

        $stmt = $this->db->prepare("
            SELECT oc.*, o.order_number
            FROM order_claims oc
            LEFT JOIN orders o ON o.id = oc.order_id
            WHERE oc.filed_by_user_id = ?
            ORDER BY oc.created_at DESC
        ");
        $stmt->execute([userId()]);
        $claims = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('buyer/claims-list', ['claims' => $claims]);
    }
}
