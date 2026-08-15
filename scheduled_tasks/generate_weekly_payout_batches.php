<?php
/**
 * Scheduled Task: Weekly Payout Batch Generation (Ecosystem Backend
 * Requirements Sec. 9 / contract-reconciliation finding #4)
 *
 * Seller Account Agreement Sec 7.1 and Driver Independent Contractor
 * Agreement Sec 8.5 both promise weekly Monday direct deposit. No real
 * bank-transfer rail exists anywhere in this codebase (Stripe here only
 * ever charges people, never pays them - confirmed by full-repo grep before
 * building this), so this does not move money. It automates the cadence
 * that was previously entirely missing: every Monday, groups all pending
 * seller_payouts and delivery_earnings rows into a dated batch and notifies
 * admin, who executes the real transfer outside the system and marks the
 * batch paid from /admin/seller-payouts or /admin/delivery/earnings (same
 * "admin marks paid" mechanism already used for supplier/distribution
 * payments - this just gives it a real weekly trigger instead of none).
 *
 * $25 seller rollover threshold (Sec 7.2): a shop whose accumulated pending
 * net total is under $25 is left out of this week's batch (batch_id stays
 * NULL, status stays 'pending') so it's automatically picked up once it
 * crosses $25 in a future run. No equivalent threshold exists for drivers -
 * confirmed by a full read of the Driver Agreement, every pending
 * delivery_earnings row is batched regardless of amount.
 *
 * Idempotent per calendar day via the batch_date UNIQUE constraint - if a
 * batch already exists for today, both sections are skipped so a manual
 * re-run or a duplicate cron firing can't double-assign rows.
 *
 * Cron: run once, Monday only, before the other 3 scheduled jobs at 6/7/8AM.
 *   0 5 * * 1 cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/generate_weekly_payout_batches.php >> /var/www/html/marketplace/storage/logs/cron-payout-batches.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';
require BASE_PATH . '/app/Helpers/NotificationHelper.php';

use App\Helpers\NotificationHelper;

$db = Database::getConnection();

$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
echo "[{$now}] generate_weekly_payout_batches starting for {$today}\n";

const SELLER_ROLLOVER_THRESHOLD = 25.00;

// --- Sellers ---------------------------------------------------------
try {
    $exists = $db->prepare("SELECT id FROM seller_payout_batches WHERE batch_date = ?");
    $exists->execute([$today]);
    if ($exists->fetch()) {
        echo "[{$now}] Seller batch for {$today} already exists. Skipping seller section.\n";
    } else {
        $stmt = $db->prepare("
            SELECT shop_id, SUM(net_payout_amount - chargeback_amount) AS pending_total
            FROM seller_payouts
            WHERE status = 'pending' AND batch_id IS NULL
            GROUP BY shop_id
            HAVING pending_total >= ?
        ");
        $stmt->execute([SELLER_ROLLOVER_THRESHOLD]);
        $eligibleShops = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($eligibleShops)) {
            echo "[{$now}] No shop met the \$" . SELLER_ROLLOVER_THRESHOLD . " rollover threshold. No seller batch created.\n";
        } else {
            $db->beginTransaction();

            $batchNumber = 'SPB-' . date('Ymd', strtotime($today));
            $db->prepare("
                INSERT INTO seller_payout_batches (batch_number, batch_date, status, created_at)
                VALUES (?, ?, 'open', NOW())
            ")->execute([$batchNumber, $today]);
            $batchId = (int)$db->lastInsertId();

            $shopIds = array_map(fn($r) => (int)$r['shop_id'], $eligibleShops);
            $placeholders = implode(',', array_fill(0, count($shopIds), '?'));
            $db->prepare("
                UPDATE seller_payouts
                SET batch_id = ?
                WHERE status = 'pending' AND batch_id IS NULL AND shop_id IN ({$placeholders})
            ")->execute(array_merge([$batchId], $shopIds));

            $totals = $db->prepare("
                SELECT COUNT(*) AS item_count, COUNT(DISTINCT shop_id) AS shop_count,
                       SUM(net_payout_amount - chargeback_amount) AS total_amount
                FROM seller_payouts WHERE batch_id = ?
            ");
            $totals->execute([$batchId]);
            $t = $totals->fetch(PDO::FETCH_ASSOC);

            $db->prepare("
                UPDATE seller_payout_batches
                SET item_count = ?, shop_count = ?, total_amount = ?
                WHERE id = ?
            ")->execute([(int)$t['item_count'], (int)$t['shop_count'], (float)$t['total_amount'], $batchId]);

            $db->commit();

            NotificationHelper::add(
                'payout_batch',
                'Weekly seller payout batch ready',
                "{$batchNumber}: " . (int)$t['shop_count'] . " shop(s), \$" . number_format((float)$t['total_amount'], 2) . " total. Review and mark paid once transfers are sent.",
                ['link' => url('admin/seller-payouts') . "?batch_id={$batchId}", 'icon' => 'cash-stack']
            );

            echo "[{$now}] Seller batch {$batchNumber} created: " . (int)$t['shop_count'] . " shops, " . (int)$t['item_count'] . " items, \${$t['total_amount']}.\n";
        }
    }
} catch (\Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log('generate_weekly_payout_batches (seller) error: ' . $e->getMessage());
    echo "[{$now}] SELLER SECTION ERROR: " . $e->getMessage() . "\n";
}

// --- Drivers -----------------------------------------------------------
try {
    $exists = $db->prepare("SELECT id FROM driver_payout_batches WHERE batch_date = ?");
    $exists->execute([$today]);
    if ($exists->fetch()) {
        echo "[{$now}] Driver batch for {$today} already exists. Skipping driver section.\n";
    } else {
        $pendingCount = (int)$db->query("
            SELECT COUNT(*) FROM delivery_earnings WHERE payment_status = 'pending' AND batch_id IS NULL
        ")->fetchColumn();

        if ($pendingCount === 0) {
            echo "[{$now}] No pending driver earnings. No driver batch created.\n";
        } else {
            $db->beginTransaction();

            $batchNumber = 'DPB-' . date('Ymd', strtotime($today));
            $db->prepare("
                INSERT INTO driver_payout_batches (batch_number, batch_date, status, created_at)
                VALUES (?, ?, 'open', NOW())
            ")->execute([$batchNumber, $today]);
            $batchId = (int)$db->lastInsertId();

            $db->prepare("
                UPDATE delivery_earnings
                SET batch_id = ?
                WHERE payment_status = 'pending' AND batch_id IS NULL
            ")->execute([$batchId]);

            $totals = $db->prepare("
                SELECT COUNT(*) AS item_count, COUNT(DISTINCT driver_id) AS driver_count,
                       SUM(net_earning) AS total_amount
                FROM delivery_earnings WHERE batch_id = ?
            ");
            $totals->execute([$batchId]);
            $t = $totals->fetch(PDO::FETCH_ASSOC);

            $db->prepare("
                UPDATE driver_payout_batches
                SET item_count = ?, driver_count = ?, total_amount = ?
                WHERE id = ?
            ")->execute([(int)$t['item_count'], (int)$t['driver_count'], (float)$t['total_amount'], $batchId]);

            $db->commit();

            NotificationHelper::add(
                'payout_batch',
                'Weekly driver payout batch ready',
                "{$batchNumber}: " . (int)$t['driver_count'] . " driver(s), \$" . number_format((float)$t['total_amount'], 2) . " total. Review and mark paid once transfers are sent.",
                ['link' => url('admin/delivery/earnings') . "?batch_id={$batchId}", 'icon' => 'cash-stack']
            );

            echo "[{$now}] Driver batch {$batchNumber} created: " . (int)$t['driver_count'] . " drivers, " . (int)$t['item_count'] . " items, \${$t['total_amount']}.\n";
        }
    }
} catch (\Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log('generate_weekly_payout_batches (driver) error: ' . $e->getMessage());
    echo "[{$now}] DRIVER SECTION ERROR: " . $e->getMessage() . "\n";
}

echo "[{$now}] generate_weekly_payout_batches complete.\n";
