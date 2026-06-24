<?php
/**
 * Weekly Driver Earnings Summary Email
 * Cron: 0 9 * * 1 (Monday 9am)
 * Run: php scheduled_tasks/send_driver_earnings_summary.php
 */

require_once dirname(__DIR__) . '/bootstrap/init.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Helpers/EmailHelper.php';

use App\Helpers\EmailHelper;

try {
    $db = Database::getConnection();

    $weekStart = date('Y-m-d', strtotime('last monday'));
    $weekEnd = date('Y-m-d', strtotime('last sunday'));
    $weekPeriod = date('M j', strtotime($weekStart)) . ' - ' . date('M j, Y', strtotime($weekEnd));

    echo "Sending weekly earnings summaries for {$weekPeriod}...\n";

    // Get all active delivery drivers with earnings in the past week
    $stmt = $db->prepare("
        SELECT
            u.id as driver_id,
            u.first_name,
            u.email,
            COUNT(de.id) as deliveries_completed,
            COALESCE(SUM(de.total_earning), 0) as gross_earnings,
            COALESCE(SUM(de.platform_commission), 0) as commission,
            COALESCE(SUM(de.net_earning), 0) as net_earnings,
            COALESCE(SUM(de.tip), 0) as tips
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        LEFT JOIN delivery_earnings de ON u.id = de.driver_id
            AND de.created_at >= ? AND de.created_at < ?
        WHERE r.name = 'delivery'
        GROUP BY u.id, u.first_name, u.email
        HAVING deliveries_completed > 0
        ORDER BY net_earnings DESC
    ");
    $stmt->execute([$weekStart . ' 00:00:00', date('Y-m-d', strtotime($weekEnd . ' +1 day')) . ' 00:00:00']);
    $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($drivers) . " drivers with earnings this week.\n";

    if (empty($drivers)) {
        echo "No drivers with earnings. Exiting.\n";
        exit(0);
    }

    // Load email template
    $templatePath = dirname(__DIR__) . '/app/Views/emails/driver-earnings-summary.php';
    if (!file_exists($templatePath)) {
        echo "Email template not found: {$templatePath}\n";
        exit(1);
    }
    $template = file_get_contents($templatePath);

    $sent = 0;
    $failed = 0;

    foreach ($drivers as $driver) {
        // Get pending balance for this driver
        $stmt2 = $db->prepare("
            SELECT COALESCE(SUM(net_earning), 0) as pending
            FROM delivery_earnings
            WHERE driver_id = ? AND payment_status = 'pending'
        ");
        $stmt2->execute([$driver['driver_id']]);
        $pendingBalance = $stmt2->fetchColumn();

        $html = str_replace([
            '{{first_name}}',
            '{{week_period}}',
            '{{deliveries_completed}}',
            '{{gross_earnings}}',
            '{{net_earnings}}',
            '{{commission}}',
            '{{tips}}',
            '{{pending_balance}}',
            '{{current_year}}'
        ], [
            htmlspecialchars($driver['first_name']),
            $weekPeriod,
            $driver['deliveries_completed'],
            '$' . number_format($driver['gross_earnings'], 2),
            '$' . number_format($driver['net_earnings'], 2),
            '$' . number_format($driver['commission'], 2),
            '$' . number_format($driver['tips'], 2),
            '$' . number_format($pendingBalance, 2),
            date('Y')
        ], $template);

        try {
            $result = EmailHelper::sendRaw(
                $driver['email'],
                "Your Weekly Earnings Summary — {$weekPeriod}",
                $html
            );

            if ($result) {
                $sent++;
                echo "  Sent to {$driver['first_name']} ({$driver['email']})\n";
            } else {
                $failed++;
                echo "  FAILED for {$driver['first_name']} ({$driver['email']})\n";
            }
        } catch (Exception $e) {
            $failed++;
            echo "  ERROR for {$driver['email']}: " . $e->getMessage() . "\n";
        }

        usleep(200000); // 200ms between emails to avoid rate limits
    }

    echo "\nDone! Sent: {$sent}, Failed: {$failed}\n";

} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
