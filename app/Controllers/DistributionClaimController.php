<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/ClaimHelper.php';

/**
 * DistributionClaimController - Track B claim filing (Returns Policy Sec.
 * B1-B5), for businesses filing a claim against a delivered/completed
 * Approvisionnement request or Distribution shipment. Mirrors
 * ClaimController (Track A, buyer-facing) but scoped to the Distribution
 * portal per Dima's scoping call - no equivalent was built anywhere before
 * this (confirmed: no Track B filing UI existed in any portal).
 */
class DistributionClaimController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    private function isBusinessLoggedIn(): bool
    {
        if (!isset($_SESSION['user']['role'], $_SESSION['business']['id'])) {
            return false;
        }
        return in_array($_SESSION['user']['role'], ['business', 'admin', 'super_admin'], true);
    }

    /**
     * GET /distribution/claims/new?type=request|shipment&id=X
     */
    public function newClaim(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $type = get('type', '') === 'shipment' ? 'shipment' : 'request';
        $id = (int)get('id', 0);
        $businessId = $_SESSION['business']['id'];

        if ($type === 'shipment') {
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipments WHERE id = ? AND business_profile_id = ?");
            $stmt->execute([$id, $businessId]);
            $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM distribution_requests WHERE id = ? AND business_profile_id = ?");
            $stmt->execute([$id, $businessId]);
            $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        if (!$record) {
            setFlash('error', 'Record not found.');
            redirect(url('distribution/requests'));
            return;
        }

        if (!in_array($record['status'], ['delivered', 'completed'], true)) {
            setFlash('error', 'Claims can only be filed once a request or shipment has been delivered.');
            redirect(url($type === 'shipment' ? 'distribution/shipments/show?id=' . $id : 'distribution/requests/show?id=' . $id));
            return;
        }

        view('distribution.claims.new', ['record' => $record, 'type' => $type]);
    }

    /**
     * POST /distribution/claims/store
     */
    public function submitClaim(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login to continue']);
            return;
        }

        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $businessId = $_SESSION['business']['id'];
        $filedByUserId = (int)($_SESSION['business']['user_id'] ?? 0);
        $type = post('type', '') === 'shipment' ? 'shipment' : 'request';
        $recordId = (int)post('record_id', 0);
        $claimType = sanitize(post('claim_type', ''));
        $description = sanitize(post('description', ''));
        $claimedValue = (float)post('claimed_value', 0);
        $preferredRefundMethod = post('preferred_refund_method', 'cash') === 'credit_note' ? 'credit_note' : 'cash';

        $validTypes = ['damaged', 'missing_item', 'wrong_item', 'not_as_described', 'other'];
        if (!in_array($claimType, $validTypes, true)) {
            jsonResponse(['success' => false, 'message' => 'Invalid claim type.']);
            return;
        }

        // Verify ownership before filing.
        if ($type === 'shipment') {
            $chk = $this->db->prepare("SELECT id FROM distribution_shipments WHERE id = ? AND business_profile_id = ?");
        } else {
            $chk = $this->db->prepare("SELECT id FROM distribution_requests WHERE id = ? AND business_profile_id = ?");
        }
        $chk->execute([$recordId, $businessId]);
        if (!$chk->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Record not found.']);
            return;
        }

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
                $filename = "claim_b_{$recordId}_{$businessId}_" . time() . "_{$i}.{$allowed[$mime]}";
                if (move_uploaded_file($tmpPath, $destDir . $filename)) {
                    $evidencePaths[] = 'uploads/claims/' . $filename;
                }
            }
        }

        $result = \App\Helpers\ClaimHelper::fileClaim(
            'B',
            null,
            $type === 'request' ? $recordId : null,
            $filedByUserId,
            $claimType,
            $description,
            $evidencePaths,
            $claimedValue,
            $preferredRefundMethod,
            $type === 'shipment' ? $recordId : null
        );

        if (!$result['success']) {
            jsonResponse(['success' => false, 'message' => $result['error']]);
            return;
        }

        require_once __DIR__ . '/../Helpers/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'claim',
            '📋 New Track B Claim Filed',
            ucfirst($type) . " #{$recordId}: {$claimType} claim filed, needs review.",
            ['link' => '/admin/claims/view?id=' . $result['claim_id'], 'icon' => 'file-invoice', 'priority' => 'normal']
        );

        jsonResponse(['success' => true, 'message' => 'Your claim has been submitted.', 'redirect' => url('distribution/claims')]);
    }

    /**
     * GET /distribution/claims - business's own claim list
     */
    public function myClaims(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        $stmt = $this->db->prepare("
            SELECT oc.*, dr.request_number, dsh.shipment_number
            FROM order_claims oc
            LEFT JOIN distribution_requests dr ON dr.id = oc.distribution_request_id
            LEFT JOIN distribution_shipments dsh ON dsh.id = oc.distribution_shipment_id
            WHERE (dr.business_profile_id = ? OR dsh.business_profile_id = ?)
              AND oc.track = 'B'
            ORDER BY oc.created_at DESC
        ");
        $stmt->execute([$businessId, $businessId]);
        $claims = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('distribution.claims.list', ['claims' => $claims]);
    }
}
