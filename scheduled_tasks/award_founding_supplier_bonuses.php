<?php
/**
 * Scheduled Task: Founding Supplier bonuses (Supplier Account Agreement
 * Sec 7.4.2 milestone, Sec 7.4.3 referral).
 *
 * Purchase Orders are marked completed from several places (driver app,
 * admin, supplier portal), so instead of hooking each one this job checks
 * every supplier that could still earn a bonus: Founding cohort members and
 * referred suppliers still inside their first 30 days (plus a margin, since a referred
 * supplier's 30 days start at approval, after its creation date).
 * FoundingSupplierHelper::evaluateBonuses() is
 * idempotent - a bonus already in the ledger is never awarded twice.
 *
 * A newly awarded bonus is 'pending' until the supplier's next invoice is
 * created (AdminPayablesController::createInvoiceForPO() adds it there).
 *
 * Cron: run daily.
 *   15 6 * * * cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/award_founding_supplier_bonuses.php >> /var/www/html/marketplace/storage/logs/cron-supplier-bonuses.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/Helpers/FoundingSupplierHelper.php';

use App\Helpers\FoundingSupplierHelper;

$db = Database::getConnection();
$now = date('Y-m-d H:i:s');
echo "[{$now}] award_founding_supplier_bonuses starting\n";

try {
    $window = max(FoundingSupplierHelper::MILESTONE_DAYS, FoundingSupplierHelper::REFERRAL_DAYS) + 30;
    $stmt = $db->prepare("
        SELECT id, name FROM suppliers
        WHERE (founding_partner_number IS NOT NULL OR referred_by_supplier_id IS NOT NULL)
          AND COALESCE(founding_partner_granted_at, created_at) >= DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $stmt->execute([$window]);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    foreach ($suppliers as $s) {
        $n = FoundingSupplierHelper::evaluateBonuses((int)$s['id']);
        if ($n > 0) {
            $total += $n;
            echo "[{$now}] Supplier #{$s['id']} ({$s['name']}): {$n} bonus(es) awarded.\n";
        }
    }
    echo "[{$now}] award_founding_supplier_bonuses complete: {$total} bonus(es) from " . count($suppliers) . " supplier(s) checked.\n";
} catch (\Exception $e) {
    error_log('award_founding_supplier_bonuses fatal error: ' . $e->getMessage());
    echo "[{$now}] FATAL: {$e->getMessage()}\n";
    exit(1);
}
