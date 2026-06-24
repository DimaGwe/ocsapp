<?php

namespace App\Services;

use PDO;

/**
 * ScoringService
 *
 * Calculates performance scores (0-100) for each party in a completed
 * distribution request:
 *
 *   Supplier (per PO)
 *   ├─ Response speed     0-40  (time from PO sent → accepted vs 10-min window)
 *   ├─ On-time readiness  0-40  (PO completed without escalation)
 *   └─ Completion quality 0-20  (no decline / escalation penalty)
 *
 *   Driver
 *   ├─ Acceptance speed   0-20  (assigned_at → accepted_at)
 *   ├─ On-time delivery   0-60  (delivered_at vs order_deadline)
 *   └─ Pickup promptness  0-20  (picked_up_at relative to pickup window)
 *
 *   Business
 *   ├─ Order completion   0-40  (did order reach delivered vs cancelled)
 *   ├─ Invoice payment    0-40  (paid_at vs due_date on distribution_invoices)
 *   └─ Request quality    0-20  (full address + items provided)
 */
class ScoringService
{
    /**
     * Entry point — call this when a distribution request reaches delivered/completed.
     */
    public static function calculateForDistributionRequest(int $drId, PDO $db): void
    {
        try {
            // Load the distribution request
            $stmt = $db->prepare("
                SELECT dr.*,
                       bp.id AS bp_id,
                       u.id  AS user_id
                FROM distribution_requests dr
                JOIN business_profiles bp ON bp.id = dr.business_profile_id
                JOIN users u ON u.id = bp.user_id
                WHERE dr.id = ?
                LIMIT 1
            ");
            $stmt->execute([$drId]);
            $dr = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$dr) return;

            // ── Score each supplier via their POs ────────────────────────────
            $poStmt = $db->prepare("
                SELECT po.*, s.id AS s_id
                FROM purchase_orders po
                JOIN suppliers s ON s.id = po.supplier_id
                WHERE po.distribution_request_id = ?
            ");
            $poStmt->execute([$drId]);
            $pos = $poStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pos as $po) {
                $score = self::scoreSupplier($po, $dr);
                self::upsertScore($db, [
                    'distribution_request_id' => $drId,
                    'po_id'                   => $po['id'],
                    'entity_type'             => 'supplier',
                    'entity_id'               => $po['supplier_id'],
                ] + $score);
            }

            // ── Score the driver ─────────────────────────────────────────────
            $daStmt = $db->prepare("
                SELECT da.*, da.driver_id AS driver_id
                FROM delivery_assignments da
                WHERE da.distribution_request_id = ?
                  AND da.delivery_type = 'distribution'
                ORDER BY da.id DESC
                LIMIT 1
            ");
            $daStmt->execute([$drId]);
            $da = $daStmt->fetch(PDO::FETCH_ASSOC);

            if ($da && $da['driver_id']) {
                $score = self::scoreDriver($da, $dr);
                self::upsertScore($db, [
                    'distribution_request_id' => $drId,
                    'po_id'                   => null,
                    'entity_type'             => 'driver',
                    'entity_id'               => $da['driver_id'],
                ] + $score);
            }

            // ── Score the business ───────────────────────────────────────────
            $invoiceStmt = $db->prepare("
                SELECT * FROM distribution_invoices
                WHERE distribution_request_id = ?
                LIMIT 1
            ");
            $invoiceStmt->execute([$drId]);
            $invoice = $invoiceStmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $score = self::scoreBusiness($dr, $invoice);
            self::upsertScore($db, [
                'distribution_request_id' => $drId,
                'po_id'                   => null,
                'entity_type'             => 'business',
                'entity_id'               => $dr['business_profile_id'],
            ] + $score);

        } catch (\Throwable $e) {
            error_log("ScoringService error for DR #{$drId}: " . $e->getMessage());
        }
    }

    // ── Supplier scoring ─────────────────────────────────────────────────────

    private static function scoreSupplier(array $po, array $dr): array
    {
        $sentTs     = $po['created_at']            ? strtotime($po['created_at'])            : null;
        $acceptedTs = $po['supplier_accepted_at']  ? strtotime($po['supplier_accepted_at'])  : null;
        $declinedTs = $po['supplier_declined_at']  ? strtotime($po['supplier_declined_at'])  : null;
        $deadlineTs = $po['confirmation_deadline'] ? strtotime($po['confirmation_deadline']) : null;
        $escalation = (int)($po['escalation_attempt'] ?? 0);
        $status     = $po['status'] ?? 'cancelled';

        // 1. Response speed (0-40)
        $responseScore = 0;
        if ($acceptedTs && $sentTs) {
            $secsToAccept = $acceptedTs - $sentTs;
            if      ($secsToAccept <=  120) $responseScore = 40; // ≤ 2 min
            elseif  ($secsToAccept <=  300) $responseScore = 30; // ≤ 5 min
            elseif  ($secsToAccept <=  480) $responseScore = 20; // ≤ 8 min
            elseif  ($secsToAccept <=  600) $responseScore = 10; // ≤ 10 min
            else                            $responseScore =  5; // > 10 min but still responded
        } elseif ($declinedTs) {
            $responseScore = 0; // declined
        }

        // 2. On-time readiness (0-40) — did the PO complete without being cancelled/timed-out?
        $timelinessScore = 0;
        if (in_array($status, ['completed', 'picked_up', 'ready_for_pickup'])) {
            $orderDeadlineTs = $dr['order_deadline'] ? strtotime($dr['order_deadline']) : null;
            if (!$orderDeadlineTs) {
                $timelinessScore = 35;
            } else {
                $readyTs = $po['updated_at'] ? strtotime($po['updated_at']) : time();
                $secsBeforeDeadline = $orderDeadlineTs - $readyTs;
                if      ($secsBeforeDeadline >= 900)  $timelinessScore = 40; // ready 15+ min early
                elseif  ($secsBeforeDeadline >= 0)    $timelinessScore = 30; // ready before deadline
                elseif  ($secsBeforeDeadline >= -900) $timelinessScore = 15; // ≤ 15 min late
                else                                  $timelinessScore =  5; // > 15 min late
            }
        }

        // 3. Completion quality (0-20)
        $completionScore = 0;
        if (in_array($status, ['completed', 'picked_up'])) {
            $completionScore = match (true) {
                $escalation === 0 => 20,
                $escalation === 1 => 10,
                default           =>  0,
            };
        } elseif ($status === 'cancelled' && !$declinedTs) {
            $completionScore = 5; // system cancelled, not supplier fault
        }

        $total = min(100, $responseScore + $timelinessScore + $completionScore);

        return [
            'total_score'      => $total,
            'response_score'   => $responseScore,
            'timeliness_score' => $timelinessScore,
            'completion_score' => $completionScore,
            'details'          => json_encode([
                'secs_to_accept'    => $acceptedTs && $sentTs ? $acceptedTs - $sentTs : null,
                'escalation_attempt'=> $escalation,
                'po_status'         => $status,
                'declined'          => (bool)$declinedTs,
            ]),
        ];
    }

    // ── Driver scoring ───────────────────────────────────────────────────────

    private static function scoreDriver(array $da, array $dr): array
    {
        $assignedTs   = $da['assigned_at']   ? strtotime($da['assigned_at'])   : null;
        $acceptedTs   = $da['accepted_at']   ? strtotime($da['accepted_at'])   : null;
        $pickedUpTs   = $da['picked_up_at']  ? strtotime($da['picked_up_at'])  : null;
        $deliveredTs  = $da['delivered_at']  ? strtotime($da['delivered_at'])  : null;
        $orderDeadTs  = $dr['order_deadline']? strtotime($dr['order_deadline']): null;
        $submittedTs  = $dr['submitted_at']  ? strtotime($dr['submitted_at'])  : null;

        // 1. Acceptance speed (0-20)
        $responseScore = 0;
        if ($acceptedTs && $assignedTs) {
            $secs = $acceptedTs - $assignedTs;
            if      ($secs <=  120) $responseScore = 20;
            elseif  ($secs <=  300) $responseScore = 15;
            elseif  ($secs <=  600) $responseScore = 10;
            else                    $responseScore =  5;
        }

        // 2. On-time delivery (0-60)
        $timelinessScore = 0;
        if ($deliveredTs && $orderDeadTs) {
            $secsBeforeDeadline = $orderDeadTs - $deliveredTs;
            if      ($secsBeforeDeadline >= 0)    $timelinessScore = 60; // on time
            elseif  ($secsBeforeDeadline >= -900) $timelinessScore = 40; // ≤ 15 min late
            elseif  ($secsBeforeDeadline >= -1800)$timelinessScore = 20; // ≤ 30 min late
            else                                  $timelinessScore =  5; // > 30 min late
        } elseif ($deliveredTs) {
            $timelinessScore = 45; // delivered but no deadline set
        }

        // 3. Pickup promptness (0-20)
        // Expected pickup time ≈ 30-60 min after submission for ASAP, or start of window for others
        $completionScore = 0;
        $deliveryType = $dr['delivery_type'] ?? 'scheduled';
        if ($pickedUpTs && $submittedTs) {
            $secsToPickup = $pickedUpTs - $submittedTs;
            if ($deliveryType === 'express') {
                // For ASAP, pickup should happen within 60 min of submission
                if      ($secsToPickup <= 1800) $completionScore = 20; // ≤ 30 min
                elseif  ($secsToPickup <= 3600) $completionScore = 15; // ≤ 60 min
                elseif  ($secsToPickup <= 5400) $completionScore = 10; // ≤ 90 min
                else                            $completionScore =  5;
            } else {
                $completionScore = 20; // for scheduled/same_day just award if picked up
            }
        }

        $total = min(100, $responseScore + $timelinessScore + $completionScore);

        return [
            'total_score'      => $total,
            'response_score'   => $responseScore,
            'timeliness_score' => $timelinessScore,
            'completion_score' => $completionScore,
            'details'          => json_encode([
                'secs_to_accept'      => $acceptedTs && $assignedTs ? $acceptedTs - $assignedTs : null,
                'secs_before_deadline'=> $deliveredTs && $orderDeadTs ? $orderDeadTs - $deliveredTs : null,
                'secs_to_pickup'      => $pickedUpTs && $submittedTs ? $pickedUpTs - $submittedTs : null,
                'delivery_type'       => $deliveryType,
                'da_status'           => $da['status'],
            ]),
        ];
    }

    // ── Business scoring ─────────────────────────────────────────────────────

    private static function scoreBusiness(array $dr, array $invoice): array
    {
        $status = $dr['status'] ?? 'cancelled';

        // 1. Order completion (0-40)
        $completionScore = match (true) {
            in_array($status, ['delivered','completed']) => 40,
            $status === 'cancelled' && !empty($dr['cancelled_by']) && $dr['cancelled_by'] === 'business' => 0,
            $status === 'cancelled' => 20, // system/admin cancelled
            default => 30, // in progress
        };

        // 2. Invoice payment speed (0-40)
        $responseScore = 20; // neutral default (no invoice yet or N/A)
        if (!empty($invoice)) {
            $dueTs  = !empty($invoice['due_date'])  ? strtotime($invoice['due_date'])  : null;
            $paidTs = !empty($invoice['paid_at'])   ? strtotime($invoice['paid_at'])   : null;
            if ($paidTs && $dueTs) {
                $secsBeforeDue = $dueTs - $paidTs;
                if      ($secsBeforeDue >= 86400 * 3) $responseScore = 40; // paid 3+ days early
                elseif  ($secsBeforeDue >= 0)         $responseScore = 35; // paid on time
                elseif  ($secsBeforeDue >= -86400 * 3)$responseScore = 20; // ≤ 3 days late
                else                                  $responseScore =  5; // > 3 days late
            } elseif (!$paidTs && $invoice['status'] === 'overdue') {
                $responseScore = 0;
            }
        }

        // 3. Request quality (0-20)
        $timelinessScore = 0;
        if (!empty($dr['delivery_street'])) $timelinessScore += 10;
        if (!empty($dr['delivery_postal_code'])) $timelinessScore += 5;
        if (!empty($dr['notes']) || !empty($dr['request_name'])) $timelinessScore += 5;

        $total = min(100, $completionScore + $responseScore + $timelinessScore);

        return [
            'total_score'      => $total,
            'response_score'   => $responseScore,
            'timeliness_score' => $timelinessScore,
            'completion_score' => $completionScore,
            'details'          => json_encode([
                'dr_status'         => $status,
                'invoice_status'    => $invoice['status'] ?? null,
                'has_due_date'      => !empty($invoice['due_date']),
                'paid_on_time'      => !empty($invoice['paid_at']),
            ]),
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private static function upsertScore(PDO $db, array $data): void
    {
        // Replace existing score for the same entity+DR+PO combination
        $existing = $db->prepare("
            SELECT id FROM order_performance_scores
            WHERE distribution_request_id = ?
              AND entity_type = ?
              AND entity_id = ?
              AND (po_id = ? OR (po_id IS NULL AND ? IS NULL))
            LIMIT 1
        ");
        $existing->execute([
            $data['distribution_request_id'],
            $data['entity_type'],
            $data['entity_id'],
            $data['po_id'],
            $data['po_id'],
        ]);
        $existingId = $existing->fetchColumn();

        if ($existingId) {
            $db->prepare("
                UPDATE order_performance_scores
                SET total_score = ?, response_score = ?, timeliness_score = ?,
                    completion_score = ?, details = ?, calculated_at = NOW()
                WHERE id = ?
            ")->execute([
                $data['total_score'], $data['response_score'],
                $data['timeliness_score'], $data['completion_score'],
                $data['details'], $existingId,
            ]);
        } else {
            $db->prepare("
                INSERT INTO order_performance_scores
                    (distribution_request_id, po_id, entity_type, entity_id,
                     total_score, response_score, timeliness_score, completion_score, details)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                $data['distribution_request_id'], $data['po_id'],
                $data['entity_type'], $data['entity_id'],
                $data['total_score'], $data['response_score'],
                $data['timeliness_score'], $data['completion_score'],
                $data['details'],
            ]);
        }
    }

    // ── Aggregate helpers (used by admin views) ───────────────────────────────

    /**
     * Get 30-day rolling average score for a supplier.
     */
    public static function getSupplierAvgScore(int $supplierId, PDO $db): ?float
    {
        $stmt = $db->prepare("
            SELECT ROUND(AVG(total_score), 1)
            FROM order_performance_scores
            WHERE entity_type = 'supplier'
              AND entity_id = ?
              AND calculated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$supplierId]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (float)$val : null;
    }

    /**
     * Get 30-day rolling average score for a driver.
     */
    public static function getDriverAvgScore(int $driverId, PDO $db): ?float
    {
        $stmt = $db->prepare("
            SELECT ROUND(AVG(total_score), 1)
            FROM order_performance_scores
            WHERE entity_type = 'driver'
              AND entity_id = ?
              AND calculated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$driverId]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (float)$val : null;
    }

    /**
     * Get scores for a specific distribution request (all entities).
     */
    public static function getScoresForRequest(int $drId, PDO $db): array
    {
        $stmt = $db->prepare("
            SELECT ops.*,
                   CASE ops.entity_type
                     WHEN 'supplier' THEN (SELECT company_name FROM suppliers WHERE id = ops.entity_id)
                     WHEN 'driver'   THEN (SELECT CONCAT(first_name,' ',last_name) FROM users WHERE id = ops.entity_id)
                     WHEN 'business' THEN (SELECT company_name FROM business_profiles WHERE id = ops.entity_id)
                   END AS entity_name
            FROM order_performance_scores ops
            WHERE ops.distribution_request_id = ?
            ORDER BY ops.entity_type, ops.id
        ");
        $stmt->execute([$drId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Score label + colour for display.
     */
    public static function scoreLabel(int $score): array
    {
        return match (true) {
            $score >= 85 => ['label' => 'Excellent', 'color' => '#059669', 'bg' => '#d1fae5'],
            $score >= 70 => ['label' => 'Good',      'color' => '#2563eb', 'bg' => '#dbeafe'],
            $score >= 50 => ['label' => 'Fair',      'color' => '#d97706', 'bg' => '#fef3c7'],
            default      => ['label' => 'Poor',      'color' => '#dc2626', 'bg' => '#fee2e2'],
        };
    }
}
