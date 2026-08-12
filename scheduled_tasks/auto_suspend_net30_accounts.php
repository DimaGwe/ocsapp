<?php
/**
 * Scheduled Task: Auto-Suspend Net-30 Accounts (Ecosystem Backend
 * Requirements Sec. 5.3)
 *
 * "Automatic net-30 suspension (revert to prepay-only) after 15+ days past
 * due or two late payments in a rolling 12-month window, requiring a fresh
 * credit check to reinstate."
 *
 * Two independent triggers, either one suspends:
 *   (a) any 'sent' (unpaid) invoice more than 15 calendar days past its
 *       due_date (calendar days here, not business days - the doc only
 *       specifies business days for the Sec 5.3 auto-charge trigger, not
 *       this one)
 *   (b) 2+ invoices paid late (paid_at > due_date) in the trailing 12
 *       months (CreditHelper::latePaymentCount12mo())
 *
 * Suspension itself (CreditHelper::suspendNet30()) flips payment_terms back
 * to 'prepay' and requires an explicit approveNet30() call after a fresh
 * credit review to restore net-30 - this job never re-approves.
 *
 * Cron: run daily.
 *   0 8 * * * cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/auto_suspend_net30_accounts.php >> /var/www/html/marketplace/storage/logs/cron-net30-suspend.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/Helpers/CreditHelper.php';

use App\Helpers\CreditHelper;

$db = Database::getConnection();

$now = date('Y-m-d H:i:s');
echo "[{$now}] auto_suspend_net30_accounts starting\n";

try {
    $stmt = $db->query("
        SELECT id, company_name FROM business_profiles
        WHERE payment_terms = 'net30' AND net30_suspended_at IS NULL
    ");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($accounts)) {
        echo "[{$now}] No active net-30 accounts to check.\n";
        exit(0);
    }

    echo "[{$now}] Checking " . count($accounts) . " active net-30 account(s).\n";

    $daysPastDueLimit = CreditHelper::AUTO_SUSPEND_DAYS_PAST_DUE;

    foreach ($accounts as $account) {
        $businessId = (int)$account['id'];

        try {
            // Trigger (a): any unpaid invoice > 15 calendar days past due.
            $overdueStmt = $db->prepare("
                SELECT invoice_number, due_date, DATEDIFF(CURDATE(), due_date) AS days_over
                FROM distribution_invoices
                WHERE business_profile_id = ? AND status = 'sent' AND due_date IS NOT NULL
                  AND DATEDIFF(CURDATE(), due_date) > ?
                ORDER BY due_date ASC LIMIT 1
            ");
            $overdueStmt->execute([$businessId, $daysPastDueLimit]);
            $overdueInvoice = $overdueStmt->fetch(PDO::FETCH_ASSOC);

            if ($overdueInvoice) {
                CreditHelper::suspendNet30(
                    $businessId,
                    "Invoice {$overdueInvoice['invoice_number']} is {$overdueInvoice['days_over']} days past due.",
                    'system'
                );
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId, 'billing', 'Net-30 suspended',
                    "Your net-30 terms have been suspended - invoice {$overdueInvoice['invoice_number']} is {$overdueInvoice['days_over']} days past due. Your account has reverted to prepay-only. Contact us to arrange payment and request reinstatement.",
                    '/distribution/invoices', 'ban'
                );
                echo "[{$now}] Business #{$businessId} ({$account['company_name']}): SUSPENDED - overdue invoice {$overdueInvoice['invoice_number']}.\n";
                continue;
            }

            // Trigger (b): 2+ late payments in the trailing 12 months.
            $lateCount = CreditHelper::latePaymentCount12mo($businessId);
            if ($lateCount >= CreditHelper::AUTO_SUSPEND_LATE_PAYMENT_COUNT) {
                CreditHelper::suspendNet30(
                    $businessId,
                    "{$lateCount} late payments in the trailing 12 months.",
                    'system'
                );
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId, 'billing', 'Net-30 suspended',
                    "Your net-30 terms have been suspended after {$lateCount} late payments in the past 12 months. Your account has reverted to prepay-only. Contact us to request reinstatement.",
                    '/distribution/invoices', 'ban'
                );
                echo "[{$now}] Business #{$businessId} ({$account['company_name']}): SUSPENDED - {$lateCount} late payments in 12mo.\n";
            }
        } catch (\Exception $e) {
            error_log("auto_suspend_net30_accounts error for business #{$businessId}: " . $e->getMessage());
            echo "[{$now}] Business #{$businessId}: ERROR - " . $e->getMessage() . "\n";
        }
    }

    echo "[{$now}] auto_suspend_net30_accounts complete.\n";
} catch (\Exception $e) {
    error_log('auto_suspend_net30_accounts fatal error: ' . $e->getMessage());
    echo "[{$now}] FATAL: " . $e->getMessage() . "\n";
    exit(1);
}
