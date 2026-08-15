<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../Helpers/ChargebackHelper.php';
require_once __DIR__ . '/../Helpers/ReturnsDispatchHelper.php';

/**
 * AdminClaimsController - Returns & Refund Policy Automation admin review
 * queue (Ecosystem Backend Requirements Sec. 4). Every resolution action
 * here (confirm vendor/transit fault, deny, dispatch return) requires an
 * admin - the "automated fault determination" is a suggestion computed by
 * ClaimHelper, never an autonomous financial decision.
 */
class AdminClaimsController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    public function index(): void
    {
        $statusFilter = get('status', '');
        $sql = "
            SELECT oc.*, o.order_number, u.first_name, u.last_name, u.email
            FROM order_claims oc
            LEFT JOIN orders o ON o.id = oc.order_id
            LEFT JOIN users u ON u.id = oc.filed_by_user_id
        ";
        $params = [];
        if ($statusFilter) {
            $sql .= " WHERE oc.status = ?";
            $params[] = $statusFilter;
        }
        $sql .= " ORDER BY oc.created_at DESC LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $claims = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $counts = $this->db->query("SELECT status, COUNT(*) c FROM order_claims GROUP BY status")->fetchAll(\PDO::FETCH_KEY_PAIR);

        view('admin.claims.index', ['claims' => $claims, 'counts' => $counts, 'statusFilter' => $statusFilter]);
    }

    public function view(): void
    {
        $id = (int)get('id', 0);
        $stmt = $this->db->prepare("
            SELECT oc.*, o.order_number, o.shop_id, s.name AS shop_name,
                   u.first_name, u.last_name, u.email
            FROM order_claims oc
            LEFT JOIN orders o ON o.id = oc.order_id
            LEFT JOIN shops s ON s.id = o.shop_id
            LEFT JOIN users u ON u.id = oc.filed_by_user_id
            WHERE oc.id = ?
        ");
        $stmt->execute([$id]);
        $claim = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$claim) {
            setFlash('error', 'Claim not found.');
            redirect(url('admin/claims'));
            return;
        }

        $evidence = [];
        if ($claim['order_id']) {
            $ev = $this->db->prepare("SELECT pickup_photo_path, proof_of_delivery, signature_collected FROM delivery_assignments WHERE order_id = ? LIMIT 1");
            $ev->execute([$claim['order_id']]);
            $evidence = $ev->fetch(\PDO::FETCH_ASSOC) ?: [];
        }

        $chargebackStmt = $this->db->prepare("SELECT * FROM chargebacks WHERE order_claim_id = ? ORDER BY id DESC");
        $chargebackStmt->execute([$id]);
        $chargebacks = $chargebackStmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.claims.view', [
            'claim' => $claim,
            'fault_signals' => json_decode($claim['fault_signals'] ?? '{}', true),
            'evidence_photos' => json_decode($claim['evidence_photos'] ?? '[]', true),
            'evidence' => $evidence,
            'chargebacks' => $chargebacks,
        ]);
    }

    /**
     * Confirms vendor-caused fault and executes the Dynamic Chargeback.
     * party_id: shop_id (auto for Track A) or supplier_id (admin-specified
     * for Track B, since a distribution request can span multiple suppliers).
     */
    public function confirmVendorCaused(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/claims'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/claims');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $adminId = (int)($_SESSION['user']['id'] ?? 0);

        $claim = $this->db->prepare("SELECT track, order_id FROM order_claims WHERE id = ?");
        $claim->execute([$id]);
        $claimRow = $claim->fetch(\PDO::FETCH_ASSOC);

        if ($claimRow['track'] === 'A' && $claimRow['order_id']) {
            $shopStmt = $this->db->prepare("SELECT shop_id FROM orders WHERE id = ?");
            $shopStmt->execute([$claimRow['order_id']]);
            $shopId = (int)$shopStmt->fetchColumn();
            $result = \App\Helpers\ChargebackHelper::createVendorChargeback($id, $adminId, 'seller', $shopId);
        } else {
            $supplierId = (int)($_POST['supplier_id'] ?? 0);
            if (!$supplierId) {
                setFlash('error', 'Supplier ID is required for Track B chargebacks.');
                redirect(url('admin/claims/view?id=' . $id));
                return;
            }
            $result = \App\Helpers\ChargebackHelper::createVendorChargeback($id, $adminId, 'supplier', $supplierId);
        }

        setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'Chargeback applied.' : ($result['error'] ?? 'Failed.'));
        redirect(url('admin/claims/view?id=' . $id));
    }

    public function confirmTransitCaused(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/claims'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/claims');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $adminId = (int)($_SESSION['user']['id'] ?? 0);

        $result = \App\Helpers\ChargebackHelper::absorbTransitLoss($id, $adminId);
        setFlash($result['success'] ? 'success' : 'error', $result['success'] ? 'Loss recorded as absorbed by OCSAPP.' : ($result['error'] ?? 'Failed.'));
        redirect(url('admin/claims/view?id=' . $id));
    }

    public function deny(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/claims'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/claims');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $adminId = (int)($_SESSION['user']['id'] ?? 0);
        $notes = sanitize($_POST['admin_notes'] ?? '');

        $this->db->prepare("
            UPDATE order_claims SET fault_determination = 'buyer_caused', fault_determined_at = NOW(), fault_determined_by = ?,
                status = 'resolved', resolution = 'denied', admin_notes = ?, updated_at = NOW()
            WHERE id = ?
        ")->execute([$adminId, $notes, $id]);

        setFlash('success', 'Claim denied.');
        redirect(url('admin/claims/view?id=' . $id));
    }

    /**
     * Sec 4.4: after fault is resolved, dispatch the physical return (or
     * skip to a returnless refund) via the threshold rule.
     */
    public function dispatchReturn(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/claims'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/claims');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $result = \App\Helpers\ReturnsDispatchHelper::resolveReturnAction($id);

        if (!$result['success']) {
            setFlash('error', $result['error'] ?? 'Failed to dispatch return.');
        } else {
            $labels = [
                'exchange_dispatched' => 'Identical replacement dispatched - no refund issued (Sec A5).',
                'returnless_refund' => 'Buyer refunded directly (returnless).',
                'returnless_store_credit' => 'Store credit issued directly (returnless).',
                'reverse_pickup_dispatched' => 'Reverse pickup dispatched.',
            ];
            setFlash('success', $labels[$result['action']] ?? 'Return dispatched.');
        }
        redirect(url('admin/claims/view?id=' . $id));
    }
}
