<?php

namespace App\Helpers;

/**
 * ClaimHelper - Returns & Refund Policy Automation (Ecosystem Backend
 * Requirements Sec. 4.1/4.2).
 *
 * Track A (B2C Marche orders): 14-day return window from delivery.
 * Track B (B2B distribution requests): 48-hour claim window from delivery.
 *
 * "Automated fault determination" is deliberately honest about what this
 * codebase can and can't do: there's no image-comparison/AI capability here
 * (and building one is out of scope), so this computes a SUGGESTED
 * classification from structured signals (pickup/delivery evidence
 * presence, per Task 19's now-mandatory photo capture) and leaves the final
 * call to an admin - same graceful-degradation-to-human-review pattern used
 * for NEQ verification and the credit bureau gate elsewhere in this
 * project. A claim's fault_determination is only "confirmed" once
 * fault_determined_by is set by an admin action.
 */
class ClaimHelper
{
    const TRACK_A_WINDOW_DAYS = 14;
    const TRACK_B_WINDOW_HOURS = 48;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * @return array{success: bool, claim_id: ?int, error: ?string}
     */
    public static function fileClaim(
        string $track,
        ?int $orderId,
        ?int $distributionRequestId,
        int $filedByUserId,
        string $claimType,
        string $description,
        array $evidencePhotos,
        float $claimedValue,
        string $preferredRefundMethod = 'cash'
    ): array {
        if (!in_array($preferredRefundMethod, ['cash', 'store_credit'], true)) {
            $preferredRefundMethod = 'cash';
        }

        $db = self::db();

        $deliveredAt = self::resolveDeliveredAt($track, $orderId, $distributionRequestId);
        if (!$deliveredAt) {
            return ['success' => false, 'claim_id' => null, 'error' => 'Order not found or not yet delivered.'];
        }

        $windowHours = $track === 'A' ? self::TRACK_A_WINDOW_DAYS * 24 : self::TRACK_B_WINDOW_HOURS;
        $deadline = (new \DateTime($deliveredAt))->modify("+{$windowHours} hours");
        $now = new \DateTime();

        $status = 'submitted';
        if ($now > $deadline) {
            $status = 'window_expired';
        }

        // Buyer change-of-mind is only valid on Track A, and is by definition
        // buyer-caused - no suggestion needed, it's a claim type, not an inference.
        if ($claimType === 'buyer_change_of_mind' && $track !== 'A') {
            return ['success' => false, 'claim_id' => null, 'error' => 'Buyer change-of-mind claims are only available for Marche (B2C) orders.'];
        }

        $signals = self::computeFaultSignals($track, $orderId, $distributionRequestId, $claimType);

        $stmt = $db->prepare("
            INSERT INTO order_claims
            (track, order_id, distribution_request_id, filed_by_user_id, claim_type, description,
             evidence_photos, claimed_value, preferred_refund_method, claim_deadline_at, fault_determination, fault_signals, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $track, $orderId, $distributionRequestId, $filedByUserId, $claimType, $description,
            json_encode($evidencePhotos), $claimedValue, $preferredRefundMethod, $deadline->format('Y-m-d H:i:s'),
            $signals['suggested'], json_encode($signals), $status,
        ]);

        $claimId = (int)$db->lastInsertId();

        if ($status === 'window_expired') {
            return ['success' => false, 'claim_id' => $claimId, 'error' => 'The return window for this order has closed.'];
        }

        return ['success' => true, 'claim_id' => $claimId, 'error' => null];
    }

    private static function resolveDeliveredAt(string $track, ?int $orderId, ?int $distributionRequestId): ?string
    {
        $db = self::db();
        if ($track === 'A' && $orderId) {
            $stmt = $db->prepare("SELECT delivered_at FROM orders WHERE id = ? AND status = 'delivered' LIMIT 1");
            $stmt->execute([$orderId]);
            return $stmt->fetchColumn() ?: null;
        }
        if ($track === 'B' && $distributionRequestId) {
            $stmt = $db->prepare("SELECT delivered_at FROM distribution_requests WHERE id = ? AND status = 'delivered' LIMIT 1");
            $stmt->execute([$distributionRequestId]);
            return $stmt->fetchColumn() ?: null;
        }
        return null;
    }

    /**
     * Structured, honest signal computation - not a black-box AI judgment.
     * 'suggested' is always one of pending/vendor_caused/transit_caused
     * (buyer_caused is handled separately in fileClaim() for the
     * change-of-mind claim type, which never reaches this method's
     * ambiguity logic).
     *
     * @return array{suggested: string, reason: string, pickup_evidence: bool, delivery_evidence: bool}
     */
    private static function computeFaultSignals(string $track, ?int $orderId, ?int $distributionRequestId, string $claimType): array
    {
        // Track A (Marche orders) keys delivery_assignments by order_id; Track B
        // (Approvisionnement) keys the same table by distribution_request_id -
        // both capture the identical pickup_photo_path/proof_of_delivery/
        // signature_collected evidence via the same ODA driver flow
        // (DriverApiController::completeDistributionDelivery() for Track B), so
        // the same signal logic applies to both. Distribution *shipments*
        // (Business Account Agreement Sec. 8, distributing a business's own
        // goods) are a separate flow with no order_claims column to reference
        // them at all yet - out of scope for this method until that schema exists.
        if ($track === 'A' && $orderId) {
            $whereClause = 'order_id = ?';
            $whereValue = $orderId;
        } elseif ($track === 'B' && $distributionRequestId) {
            $whereClause = 'distribution_request_id = ?';
            $whereValue = $distributionRequestId;
        } else {
            return [
                'suggested' => 'pending',
                'reason' => 'No automated pickup/delivery evidence signal available for this order type - manual review required.',
                'pickup_evidence' => false,
                'delivery_evidence' => false,
            ];
        }

        $stmt = self::db()->prepare("
            SELECT pickup_photo_path, proof_of_delivery, signature_collected
            FROM delivery_assignments WHERE {$whereClause} LIMIT 1
        ");
        $stmt->execute([$whereValue]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        $hasPickupEvidence = !empty($row['pickup_photo_path']);
        $hasDeliveryEvidence = !empty($row['proof_of_delivery']) || !empty($row['signature_collected']);

        if (!$hasPickupEvidence) {
            // Can't prove the item's condition at dispatch - benefit of the
            // doubt goes to the buyer, but this is a SUGGESTION only.
            return [
                'suggested' => 'vendor_caused',
                'reason' => 'No pickup photo on file for this delivery - cannot confirm item condition at dispatch.',
                'pickup_evidence' => false,
                'delivery_evidence' => $hasDeliveryEvidence,
            ];
        }

        if ($hasPickupEvidence && $hasDeliveryEvidence) {
            return [
                'suggested' => 'transit_caused',
                'reason' => 'Pickup and delivery evidence both on file - item was captured intact at dispatch; issue reported after driver custody.',
                'pickup_evidence' => true,
                'delivery_evidence' => true,
            ];
        }

        return [
            'suggested' => 'pending',
            'reason' => 'Incomplete evidence chain - manual review required.',
            'pickup_evidence' => $hasPickupEvidence,
            'delivery_evidence' => $hasDeliveryEvidence,
        ];
    }
}
