<?php

namespace App\Helpers;

/**
 * CreditHelper - Business Credit Management System (Ecosystem Backend
 * Requirements Sec. 5).
 *
 * Net-30 does not exist anywhere else in this codebase - no column, no enum,
 * no controller branch (confirmed by full-repo grep before this was built).
 * This is the single place that owns the net-30 qualification/approval/
 * suspension state machine so the admin controller and the scheduled jobs
 * (auto-charge, auto-suspend) never duplicate this logic.
 *
 * Every state change is written to distribution_credit_events - this touches
 * real money exposure, so every automated decision needs a durable trail,
 * not just a mutated column.
 */
class CreditHelper
{
    /** Sec 5.1: 3 paid orders AND 30 days of account tenure, both required. */
    const QUALIFICATION_ORDER_COUNT = 3;
    const QUALIFICATION_ACCOUNT_AGE_DAYS = 30;

    /** Sec 5.3: 5 business days past due triggers auto-charge; 15 days triggers suspension. */
    const AUTO_CHARGE_DAYS_PAST_DUE = 5;
    const AUTO_SUSPEND_DAYS_PAST_DUE = 15;
    const AUTO_SUSPEND_LATE_PAYMENT_COUNT = 2;
    const AUTO_SUSPEND_LOOKBACK_MONTHS = 12;

    /** Sec 5.3: deposit collection threshold - single order exceeding 50% of credit limit. */
    const DEPOSIT_THRESHOLD_PCT = 0.50;
    const DEPOSIT_COLLECTION_PCT = 0.50;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    public static function logEvent(int $businessProfileId, string $eventType, string $changedByType, ?int $changedById = null, ?string $details = null): void
    {
        self::db()->prepare("
            INSERT INTO distribution_credit_events (business_profile_id, event_type, changed_by_type, changed_by_id, details, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ")->execute([$businessProfileId, $eventType, $changedByType, $changedById, $details]);
    }

    /**
     * Sec 5.1 qualification check: 3 paid orders AND 30 days of account age,
     * both required. Does not by itself grant net-30 - that still requires an
     * explicit approval (bureau or manual) via approveNet30().
     *
     * @return array{qualified: bool, order_count: int, account_age_days: int}
     */
    public static function checkQualification(int $businessProfileId): array
    {
        $db = self::db();

        $orderCount = (int)self::scalar($db, "
            SELECT COUNT(*) FROM distribution_requests WHERE business_profile_id = ? AND payment_status = 'paid'
        ", [$businessProfileId]);

        $createdAt = self::scalar($db, "SELECT created_at FROM business_profiles WHERE id = ?", [$businessProfileId]);
        $accountAgeDays = $createdAt ? (int)floor((time() - strtotime($createdAt)) / 86400) : 0;

        $qualified = $orderCount >= self::QUALIFICATION_ORDER_COUNT && $accountAgeDays >= self::QUALIFICATION_ACCOUNT_AGE_DAYS;

        return [
            'qualified' => $qualified,
            'order_count' => $orderCount,
            'account_age_days' => $accountAgeDays,
        ];
    }

    private static function scalar(\PDO $db, string $sql, array $params)
    {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Sec 5.3's auto-charge trigger is specifically "5 business days" past
     * due (the suspend trigger is calendar days - "15+ days past due" - so
     * this is deliberately only used for the auto-charge threshold). Counts
     * weekdays between $dueDate and today; does not account for Québec
     * statutory holidays - no holiday calendar exists anywhere in this
     * codebase to draw from.
     */
    public static function businessDaysPastDue(string $dueDate): int
    {
        $due = new \DateTime($dueDate);
        $today = new \DateTime('today');
        if ($due >= $today) return 0;

        $count = 0;
        $cursor = clone $due;
        $cursor->modify('+1 day');
        while ($cursor <= $today) {
            $dow = (int)$cursor->format('N'); // 1=Mon..7=Sun
            if ($dow < 6) $count++;
            $cursor->modify('+1 day');
        }
        return $count;
    }

    /**
     * Call after any distribution_requests row flips to payment_status='paid'.
     * Marks net30_eligible_at the first time qualification is met (idempotent -
     * doesn't overwrite an already-set date). Does not auto-approve net-30.
     */
    public static function markQualifiedIfEligible(int $businessProfileId): void
    {
        $db = self::db();
        $already = self::scalar($db, "SELECT net30_eligible_at FROM business_profiles WHERE id = ?", [$businessProfileId]);
        if ($already) return;

        $check = self::checkQualification($businessProfileId);
        if (!$check['qualified']) return;

        $db->prepare("UPDATE business_profiles SET net30_eligible_at = NOW() WHERE id = ?")->execute([$businessProfileId]);
        self::logEvent($businessProfileId, 'qualification_met', 'system', null,
            "Qualified after {$check['order_count']} paid orders, {$check['account_age_days']} days tenure.");
    }

    /**
     * Sec 5.1 bureau gate. No live Equifax CA / D&B CA integration exists (no
     * real API credentials to build against) - this checks whether one has
     * been configured via Settings > Integrations and, if not, degrades to
     * the same manual-admin-review pattern already used for NEQ verification
     * (regex + human PDF review, no live registry API) rather than either
     * silently approving or blocking forever.
     */
    public static function isBureauConfigured(): bool
    {
        return (bool)(setting('equifax_ca_client_id', '') || setting('dnb_ca_api_key', ''));
    }

    /**
     * Runs the credit check step. Real bureau call when configured (not
     * implemented - no credentials exist to build/test against); otherwise
     * flags for manual review and returns immediately.
     */
    public static function runCreditCheck(int $businessProfileId): void
    {
        $db = self::db();

        if (self::isBureauConfigured()) {
            // Live Equifax CA / D&B CA call would go here once Jack has real
            // credentials - deliberately not stubbed with fake logic since a
            // fake "always approve" or "always decline" would be worse than
            // an honest manual-review fallback.
            self::logEvent($businessProfileId, 'credit_check_bureau_configured_not_implemented', 'system', null,
                'Bureau credentials are configured but no live integration exists yet - falling back to manual review.');
        }

        $db->prepare("UPDATE business_profiles SET credit_review_status = 'pending_manual_review' WHERE id = ?")
           ->execute([$businessProfileId]);
        self::logEvent($businessProfileId, 'credit_review_flagged', 'system', null, 'Flagged for manual admin credit review.');
    }

    /**
     * Approve net-30 for a business account. $creditLimit is required for
     * negotiated (Enterprise) plans; otherwise defaults to the assigned
     * plan's credit_limit.
     */
    public static function approveNet30(int $businessProfileId, int $adminId, ?float $creditLimit = null): void
    {
        $db = self::db();

        if ($creditLimit !== null) {
            $db->prepare("UPDATE business_profiles SET credit_limit = ? WHERE id = ?")->execute([$creditLimit, $businessProfileId]);
        }

        $db->prepare("
            UPDATE business_profiles
            SET payment_terms = 'net30',
                net30_approved_at = NOW(),
                net30_approved_by = ?,
                credit_review_status = 'admin_approved',
                net30_suspended_at = NULL,
                net30_suspension_reason = NULL
            WHERE id = ?
        ")->execute([$adminId, $businessProfileId]);

        self::logEvent($businessProfileId, 'net30_approved', 'admin', $adminId,
            $creditLimit !== null ? "Credit limit set to \${$creditLimit}." : null);
    }

    public static function waiveCreditCheck(int $businessProfileId, int $adminId, string $notes = ''): void
    {
        self::db()->prepare("
            UPDATE business_profiles SET credit_review_status = 'admin_waived', credit_review_notes = ? WHERE id = ?
        ")->execute([$notes, $businessProfileId]);
        self::logEvent($businessProfileId, 'credit_check_waived', 'admin', $adminId, $notes ?: null);
    }

    /**
     * Sec 5.3: revert to prepay-only. $changedByType is 'system' for the
     * automated overdue/late-payment triggers, 'admin' for a manual override.
     */
    public static function suspendNet30(int $businessProfileId, string $reason, string $changedByType = 'system', ?int $changedById = null): void
    {
        self::db()->prepare("
            UPDATE business_profiles
            SET payment_terms = 'prepay', net30_suspended_at = NOW(), net30_suspension_reason = ?
            WHERE id = ? AND payment_terms = 'net30'
        ")->execute([$reason, $businessProfileId]);

        self::logEvent($businessProfileId, 'net30_suspended', $changedByType, $changedById, $reason);
    }

    /**
     * Sec 5.3: "requiring a fresh credit check to reinstate" - this only
     * clears the suspension flag and re-queues the credit review; it does
     * NOT restore payment_terms to 'net30' by itself. approveNet30() must be
     * called again after the fresh review, same as first-time approval.
     */
    public static function reinstateRequiresFreshCheck(int $businessProfileId, int $adminId): void
    {
        $db = self::db();
        $db->prepare("
            UPDATE business_profiles
            SET net30_suspended_at = NULL, net30_suspension_reason = NULL, credit_review_status = 'pending_manual_review'
            WHERE id = ?
        ")->execute([$businessProfileId]);

        self::logEvent($businessProfileId, 'reinstatement_requested', 'admin', $adminId,
            'Suspension cleared, fresh credit review required before net-30 can be re-approved.');
    }

    /**
     * Sec 5.3: count late payments (paid after due_date) in the trailing 12
     * months, for the "two late payments in a rolling 12-month window" auto-
     * suspend trigger.
     */
    public static function latePaymentCount12mo(int $businessProfileId): int
    {
        return (int)self::scalar(self::db(), "
            SELECT COUNT(*) FROM distribution_invoices
            WHERE business_profile_id = ? AND status = 'paid' AND paid_at IS NOT NULL AND due_date IS NOT NULL
              AND paid_at > due_date AND paid_at >= DATE_SUB(NOW(), INTERVAL " . self::AUTO_SUSPEND_LOOKBACK_MONTHS . " MONTH)
        ", [$businessProfileId]);
    }

    /**
     * Sec 5.3: deposit collection check at order-submission time. Returns the
     * deposit amount to collect (50% of order value) if this single order
     * exceeds 50% of the account's approved credit limit, else 0.
     */
    public static function requiredDeposit(int $businessProfileId, float $orderTotal): float
    {
        $creditLimit = (float)self::scalar(self::db(), "SELECT credit_limit FROM business_profiles WHERE id = ?", [$businessProfileId]);
        if ($creditLimit <= 0) return 0.00;
        if ($orderTotal <= $creditLimit * self::DEPOSIT_THRESHOLD_PCT) return 0.00;
        return round($orderTotal * self::DEPOSIT_COLLECTION_PCT, 2);
    }

    /**
     * Net-30 fast path for a distribution request (Sec 5.1/5.3). Called at
     * the single choke point where every distribution request transitions
     * from 'approved' to needing payment (SupplierProductController, once
     * all supplier POs are confirmed) - if this returns true, the caller
     * skips the whole awaiting_payment/payment-link flow entirely, since the
     * order is already cleared to proceed.
     *
     * Conditions, all required:
     *   - payment_terms = 'net30' (explicitly approved, see approveNet30())
     *   - card on file exists (Sec 5.3 "card-on-file requirement enforced at
     *     all times", even for net-30 accounts - it's what auto-charge/
     *     auto-deposit ultimately draw against)
     *   - no deposit required (order <= 50% of credit limit). If a deposit
     *     WOULD be required, this deliberately falls back to the normal
     *     full-prepayment flow rather than attempting a split
     *     deposit-now/remainder-net30 charge - that's a materially more
     *     complex payment flow this build does not implement.
     *
     * On success: creates a distribution_invoices row (due_date = +30 days),
     * moves the request straight to 'paid' (this codebase's existing
     * meaning for "payment obligation cleared, proceed to procurement" -
     * payment_status stays 'pending' since cash hasn't actually moved,
     * distinguishing it from a Stripe-collected payment), logs status
     * history + a credit event, and notifies the business.
     */
    public static function tryNet30Fastpath(int $distributionRequestId): bool
    {
        $db = self::db();

        $stmt = $db->prepare("
            SELECT dr.*, bp.company_name,
                   bp.payment_terms, bp.stripe_payment_method_id,
                   COALESCE(bp.billing_street,      bp.delivery_street)      AS bill_street,
                   COALESCE(bp.billing_city,        bp.delivery_city)        AS bill_city,
                   COALESCE(bp.billing_province,    bp.delivery_province)    AS bill_province,
                   COALESCE(bp.billing_postal_code, bp.delivery_postal_code) AS bill_postal,
                   u.email, u.first_name, u.last_name, u.phone AS u_phone
            FROM distribution_requests dr
            INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
            INNER JOIN users u ON bp.user_id = u.id
            WHERE dr.id = ? AND dr.status = 'approved'
            LIMIT 1
        ");
        $stmt->execute([$distributionRequestId]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return false;

        if ($r['payment_terms'] !== 'net30' || empty($r['stripe_payment_method_id'])) {
            return false;
        }

        $businessProfileId = (int)$r['business_profile_id'];
        $total = (float)($r['total_amount'] ?? 0);
        if (self::requiredDeposit($businessProfileId, $total) > 0) {
            return false; // falls back to full prepayment - see docblock
        }

        try {
            $db->beginTransaction();

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $subtotal = (float)($r['subtotal'] ?? 0);
            $taxTotal = (float)($r['gst_amount'] ?? 0) + (float)($r['qst_amount'] ?? 0);
            $taxRate = $subtotal > 0 ? round($taxTotal / $subtotal * 100, 2) : 14.98;
            $delivery = (float)($r['delivery_fee'] ?? 0);
            $dueDate = date('Y-m-d', strtotime('+30 days'));

            $db->prepare("
                INSERT INTO distribution_invoices
                (distribution_request_id, business_profile_id, invoice_number,
                 billing_company_name, billing_contact_name, billing_email, billing_phone,
                 billing_street, billing_city, billing_province, billing_postal_code, billing_country,
                 subtotal, tax_rate, tax_amount, delivery_fee, total_amount,
                 invoice_date, due_date, status, sent_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada',
                        ?, ?, ?, ?, ?, CURDATE(), ?, 'sent', NOW(), NOW(), NOW())
            ")->execute([
                $distributionRequestId, $businessProfileId, $invoiceNumber,
                $r['company_name'], trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
                $r['email'], $r['u_phone'] ?? '',
                $r['bill_street'] ?? '', $r['bill_city'] ?? '', $r['bill_province'] ?? '', $r['bill_postal'] ?? '',
                $subtotal, $taxRate, $taxTotal, $delivery, $total, $dueDate,
            ]);

            $db->prepare("
                UPDATE distribution_requests SET status = 'paid', paid_at = NOW(), updated_at = NOW() WHERE id = ?
            ")->execute([$distributionRequestId]);

            $db->prepare("
                INSERT INTO distribution_status_history
                    (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, 'approved', 'paid', 'system', NULL, ?, NOW())
            ")->execute([$distributionRequestId, "Net-30 terms - invoice {$invoiceNumber} due {$dueDate}, no upfront payment required."]);

            self::logEvent($businessProfileId, 'net30_order_invoiced', 'system', null,
                "Request #{$distributionRequestId}, invoice {$invoiceNumber}, \${$total} due {$dueDate}.");

            $db->commit();

            \App\Helpers\NotificationHelper::addBusinessNotification(
                $businessProfileId, 'net30_invoice', 'Order Confirmed - Net-30 Terms',
                "Your order #{$r['request_number']} is confirmed under net-30 terms. Invoice {$invoiceNumber} (\${$total}) is due {$dueDate}.",
                'distribution/invoices', 'file-invoice-dollar'
            );

            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('tryNet30Fastpath error: ' . $e->getMessage());
            return false;
        }
    }
}
