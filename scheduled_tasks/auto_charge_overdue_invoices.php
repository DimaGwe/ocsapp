<?php
/**
 * Scheduled Task: Auto-Charge Overdue Net-30 Invoices (Ecosystem Backend
 * Requirements Sec. 5.3)
 *
 * "Automatic card-on-file charge for any net-30 invoice unpaid 5 business
 * days past due, with prior notice to the billing contact." The "prior
 * notice" happens via the business_notification created when the invoice
 * was first issued (CreditHelper::tryNet30Fastpath()) plus whatever
 * reminders the business portal already surfaces for a 'sent' invoice - this
 * job is the actual charge attempt once the 5-business-day grace period has
 * elapsed, not a separate warning step.
 *
 * On success: invoice marked paid, distribution_request advanced if still
 * pending, event logged.
 * On failure (no card, or the charge itself fails): logged + business/admin
 * notified - does not retry same-day. A failed/overdue invoice still sitting
 * unpaid past 15 days (or 2 late payments in 12mo) is what
 * auto_suspend_net30_accounts.php acts on separately.
 *
 * Cron: run daily.
 *   0 7 * * * cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/auto_charge_overdue_invoices.php >> /var/www/html/marketplace/storage/logs/cron-overdue-charge.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/Helpers/StripeCustomerHelper.php';
require BASE_PATH . '/app/Helpers/CreditHelper.php';

use App\Helpers\StripeCustomerHelper;
use App\Helpers\CreditHelper;

$db = Database::getConnection();

$now = date('Y-m-d H:i:s');
echo "[{$now}] auto_charge_overdue_invoices starting\n";

try {
    // Only invoices tied to a net-30 account and still unpaid - a 'sent'
    // invoice on a prepay account should never exist (prepay orders are paid
    // at submission), but the payment_terms check guards against it anyway.
    $stmt = $db->prepare("
        SELECT di.id AS invoice_id, di.invoice_number, di.due_date, di.total_amount,
               di.business_profile_id, di.distribution_request_id,
               bp.company_name, bp.payment_terms
        FROM distribution_invoices di
        INNER JOIN business_profiles bp ON bp.id = di.business_profile_id
        WHERE di.status = 'sent' AND bp.payment_terms = 'net30'
    ");
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $due = array_filter($invoices, fn($inv) => CreditHelper::businessDaysPastDue($inv['due_date']) >= CreditHelper::AUTO_CHARGE_DAYS_PAST_DUE);

    if (empty($due)) {
        echo "[{$now}] No invoices >= " . CreditHelper::AUTO_CHARGE_DAYS_PAST_DUE . " business days overdue.\n";
        exit(0);
    }

    echo "[{$now}] " . count($due) . " invoice(s) due for auto-charge.\n";

    foreach ($due as $inv) {
        $invoiceId = (int)$inv['invoice_id'];
        $businessId = (int)$inv['business_profile_id'];

        try {
            $db->beginTransaction();

            $result = StripeCustomerHelper::chargeOffSession(
                $businessId,
                (float)$inv['total_amount'],
                "OCSAPP Invoice {$inv['invoice_number']} (overdue)"
            );

            if ($result['success']) {
                $db->prepare("UPDATE distribution_invoices SET status = 'paid', paid_at = NOW(), updated_at = NOW() WHERE id = ?")
                   ->execute([$invoiceId]);

                if (!empty($inv['distribution_request_id'])) {
                    $db->prepare("UPDATE distribution_requests SET payment_status = 'paid' WHERE id = ? AND payment_status != 'paid'")
                       ->execute([$inv['distribution_request_id']]);
                }

                CreditHelper::logEvent($businessId, 'overdue_invoice_auto_charged', 'system', null,
                    "Invoice {$inv['invoice_number']} (\${$inv['total_amount']}) auto-charged. PaymentIntent {$result['payment_intent_id']}.");

                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId, 'billing', 'Overdue invoice charged',
                    "Invoice {$inv['invoice_number']} (\${$inv['total_amount']}) was automatically charged to your card on file - it was past the net-30 due date.",
                    '/distribution/invoices', 'credit-card'
                );

                echo "[{$now}] Invoice {$inv['invoice_number']} ({$inv['company_name']}): charged successfully.\n";
            } else {
                CreditHelper::logEvent($businessId, 'overdue_invoice_charge_failed', 'system', null,
                    "Invoice {$inv['invoice_number']}: {$result['error']}");

                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId, 'billing', 'Overdue invoice - payment failed',
                    "We attempted to charge your card on file for overdue invoice {$inv['invoice_number']} (\${$inv['total_amount']}) and it failed. Please update your payment method or contact us.",
                    '/distribution/settings', 'exclamation-triangle'
                );

                echo "[{$now}] Invoice {$inv['invoice_number']} ({$inv['company_name']}): charge FAILED - {$result['error']}\n";
            }

            $db->commit();
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("auto_charge_overdue_invoices error for invoice #{$invoiceId}: " . $e->getMessage());
            echo "[{$now}] Invoice #{$invoiceId}: ERROR - " . $e->getMessage() . "\n";
        }
    }

    echo "[{$now}] auto_charge_overdue_invoices complete.\n";
} catch (\Exception $e) {
    error_log('auto_charge_overdue_invoices fatal error: ' . $e->getMessage());
    echo "[{$now}] FATAL: " . $e->getMessage() . "\n";
    exit(1);
}
