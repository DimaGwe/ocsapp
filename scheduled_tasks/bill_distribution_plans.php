<?php
/**
 * Scheduled Task: Monthly Distribution Plan Billing (Ecosystem Backend
 * Requirements Sec. 5.2)
 *
 * Charges the card-on-file for every business account on a paid Distribution
 * plan (Débutant $49/mo, Pro $179/mo) whose next_billing_date has arrived.
 * Uses an off-session PaymentIntent (App\Helpers\StripeCustomerHelper), not
 * Stripe's native Subscription object - no Dashboard Product/Price setup
 * required, matches this codebase's existing direct-PaymentIntent pattern.
 *
 * On success: advances next_billing_date by 1 month, plan_status stays 'active'.
 * On failure (no card on file, or the charge itself fails): plan_status set
 * to 'past_due', business + admin notified. Does not auto-suspend or auto-
 * retry here - that's a business decision (Sec 5.3's auto-suspend logic is
 * specifically about net-30 invoice non-payment, not plan subscription fees;
 * a past_due plan doesn't by itself revoke Distribution access in this build).
 *
 * Cron: run daily.
 *   0 6 * * * cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/bill_distribution_plans.php >> /var/www/html/marketplace/storage/logs/cron-plan-billing.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

// bootstrap/init.php does not load Composer's autoloader itself (confirmed:
// no autoload/spl_autoload reference in it) - web requests get it for free
// via public/index.php, but a CLI-run scheduled task does not. Without this,
// any \App\Helpers\* class reference below fatals with "Class not found".
require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/Helpers/StripeCustomerHelper.php';
require BASE_PATH . '/app/Helpers/CreditHelper.php';

use App\Helpers\StripeCustomerHelper;
use App\Helpers\CreditHelper;

$db = Database::getConnection();

$now = date('Y-m-d H:i:s');
echo "[{$now}] bill_distribution_plans starting\n";

try {
    $stmt = $db->prepare("
        SELECT bp.id, bp.company_name, bp.next_billing_date, bp.stripe_payment_method_id,
               dp.name AS plan_name, dp.monthly_fee,
               u.email, u.first_name, u.last_name
        FROM business_profiles bp
        INNER JOIN distribution_plans dp ON dp.id = bp.distribution_plan_id
        INNER JOIN users u ON u.id = bp.user_id
        WHERE bp.plan_status = 'active'
          AND dp.monthly_fee > 0
          AND bp.next_billing_date IS NOT NULL
          AND bp.next_billing_date <= CURDATE()
    ");
    $stmt->execute();
    $due = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($due)) {
        echo "[{$now}] No plan billing due.\n";
        exit(0);
    }

    echo "[{$now}] " . count($due) . " account(s) due for plan billing.\n";

    foreach ($due as $account) {
        $businessId = (int)$account['id'];

        try {
            $db->beginTransaction();

            if (empty($account['stripe_payment_method_id'])) {
                $db->prepare("UPDATE business_profiles SET plan_status = 'past_due' WHERE id = ?")->execute([$businessId]);
                CreditHelper::logEvent($businessId, 'plan_billing_failed', 'system', null, 'No card on file.');
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId, 'billing', 'Payment method required',
                    "Your {$account['plan_name']} subscription (\${$account['monthly_fee']}/mo) could not be billed - no card on file. Please add one in Settings.",
                    '/distribution/settings', 'credit-card'
                );
                $db->commit();
                echo "[{$now}] Business #{$businessId} ({$account['company_name']}): no card on file, marked past_due.\n";
                continue;
            }

            $result = StripeCustomerHelper::chargeOffSession(
                $businessId,
                (float)$account['monthly_fee'],
                "OCSAPP {$account['plan_name']} - " . date('F Y')
            );

            if ($result['success']) {
                $nextDate = date('Y-m-d', strtotime($account['next_billing_date'] . ' +1 month'));
                $db->prepare("UPDATE business_profiles SET next_billing_date = ?, plan_status = 'active' WHERE id = ?")
                   ->execute([$nextDate, $businessId]);
                CreditHelper::logEvent($businessId, 'plan_billed', 'system', null,
                    "\${$account['monthly_fee']} charged, next billing {$nextDate}. PaymentIntent {$result['payment_intent_id']}.");
                echo "[{$now}] Business #{$businessId} ({$account['company_name']}): billed \${$account['monthly_fee']}, next {$nextDate}.\n";
            } else {
                $db->prepare("UPDATE business_profiles SET plan_status = 'past_due' WHERE id = ?")->execute([$businessId]);
                CreditHelper::logEvent($businessId, 'plan_billing_failed', 'system', null, $result['error'] ?? 'Charge failed.');
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId, 'billing', 'Payment failed',
                    "Your {$account['plan_name']} subscription payment of \${$account['monthly_fee']} failed. Please update your card in Settings.",
                    '/distribution/settings', 'exclamation-triangle'
                );
                echo "[{$now}] Business #{$businessId} ({$account['company_name']}): charge FAILED - {$result['error']}\n";
            }

            $db->commit();
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("bill_distribution_plans error for business #{$businessId}: " . $e->getMessage());
            echo "[{$now}] Business #{$businessId}: ERROR - " . $e->getMessage() . "\n";
        }
    }

    echo "[{$now}] bill_distribution_plans complete.\n";
} catch (\Exception $e) {
    error_log('bill_distribution_plans fatal error: ' . $e->getMessage());
    echo "[{$now}] FATAL: " . $e->getMessage() . "\n";
    exit(1);
}
