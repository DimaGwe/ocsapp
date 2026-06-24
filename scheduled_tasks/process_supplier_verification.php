<?php
/**
 * Supplier Verification Deadline Processor
 * Cron: 0 9 * * * (Daily at 9am)
 * Run: php scheduled_tasks/process_supplier_verification.php
 *
 * Actions:
 * 1. Day 7 reminder email (23 days remaining)
 * 2. Day 23 urgent warning email (7 days remaining)
 * 3. Day 30+ deactivation (expired)
 */

require_once dirname(__DIR__) . '/bootstrap/init.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/Helpers/EmailHelper.php';

use App\Helpers\EmailHelper;

try {
    $db = Database::getConnection();
    $now = date('Y-m-d H:i:s');

    echo "=== Supplier Verification Processor ===\n";
    echo "Running at: {$now}\n\n";

    // Load reminder email template
    $reminderTemplate = file_get_contents(dirname(__DIR__) . '/app/Views/emails/supplier-verification-reminder.php');
    $expiredTemplate = file_get_contents(dirname(__DIR__) . '/app/Views/emails/supplier-verification-expired.php');

    // ─── 1. SEND DAY 7 REMINDERS (verification_reminder_sent = 0, 7+ days since created) ───
    echo "--- Day 7 Reminders ---\n";
    $stmt = $db->prepare("
        SELECT id, email, contact_person, company_name, name, verification_deadline, created_at
        FROM suppliers
        WHERE status = 'pending_verification'
        AND verification_reminder_sent = 0
        AND DATEDIFF(NOW(), created_at) >= 7
        AND verification_deadline > NOW()
    ");
    $stmt->execute();
    $day7Suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo "Found " . count($day7Suppliers) . " suppliers for day 7 reminder.\n";

    foreach ($day7Suppliers as $supplier) {
        $daysRemaining = max(0, (int)ceil((strtotime($supplier['verification_deadline']) - time()) / 86400));
        $deadlineFormatted = date('F j, Y', strtotime($supplier['verification_deadline']));
        $contactName = $supplier['contact_person'] ?: $supplier['name'];

        $html = str_replace([
            '{{title_fr}}',
            '{{title_en}}',
            '{{contact_person}}',
            '{{message_fr}}',
            '{{message_en}}',
            '{{box_bg}}', '{{box_border}}', '{{box_color}}',
            '{{deadline_date}}',
            '{{days_remaining}}',
            '{{current_year}}',
        ], [
            'Rappel de vérification',
            'Verification Reminder',
            htmlspecialchars($contactName),
            'Votre compte fournisseur pour <strong>' . htmlspecialchars($supplier['company_name'] ?: $supplier['name']) . '</strong> est en attente de vérification. Il vous reste <strong>' . $daysRemaining . ' jours</strong> pour compléter le processus.',
            'Your supplier account for <strong>' . htmlspecialchars($supplier['company_name'] ?: $supplier['name']) . '</strong> is pending verification. You still have <strong>' . $daysRemaining . ' days</strong> to complete the process.',
            '#eff6ff', '#3b82f6', '#1e40af',
            $deadlineFormatted,
            $daysRemaining,
            date('Y'),
        ], $reminderTemplate);

        try {
            EmailHelper::sendRaw($supplier['email'], "Rappel de vérification / Verification Reminder — {$daysRemaining} days remaining", $html);
            $db->prepare("UPDATE suppliers SET verification_reminder_sent = 1 WHERE id = ?")->execute([$supplier['id']]);
            echo "  Sent day 7 reminder to {$supplier['email']}\n";
        } catch (\Exception $e) {
            echo "  FAILED for {$supplier['email']}: " . $e->getMessage() . "\n";
        }

        usleep(200000);
    }

    // ─── 2. SEND DAY 23 URGENT WARNINGS (verification_reminder_sent = 1, 7 or fewer days left) ───
    echo "\n--- Day 23 Urgent Warnings ---\n";
    $stmt = $db->prepare("
        SELECT id, email, contact_person, company_name, name, verification_deadline
        FROM suppliers
        WHERE status = 'pending_verification'
        AND verification_reminder_sent = 1
        AND DATEDIFF(verification_deadline, NOW()) <= 7
        AND verification_deadline > NOW()
    ");
    $stmt->execute();
    $urgentSuppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo "Found " . count($urgentSuppliers) . " suppliers for urgent warning.\n";

    foreach ($urgentSuppliers as $supplier) {
        $daysRemaining = max(0, (int)ceil((strtotime($supplier['verification_deadline']) - time()) / 86400));
        $deadlineFormatted = date('F j, Y', strtotime($supplier['verification_deadline']));
        $contactName = $supplier['contact_person'] ?: $supplier['name'];

        $html = str_replace([
            '{{title_fr}}',
            '{{title_en}}',
            '{{contact_person}}',
            '{{message_fr}}',
            '{{message_en}}',
            '{{box_bg}}', '{{box_border}}', '{{box_color}}',
            '{{deadline_date}}',
            '{{days_remaining}}',
            '{{current_year}}',
        ], [
            'Urgent : Vérification bientôt expirée',
            'Urgent: Verification Expiring Soon',
            htmlspecialchars($contactName),
            'Votre délai de vérification pour <strong>' . htmlspecialchars($supplier['company_name'] ?: $supplier['name']) . '</strong> est sur le point d\'expirer. Si votre compte n\'est pas vérifié dans <strong>' . $daysRemaining . ' jours</strong>, il sera désactivé.',
            'Your verification window for <strong>' . htmlspecialchars($supplier['company_name'] ?: $supplier['name']) . '</strong> is about to expire. If your account is not verified within <strong>' . $daysRemaining . ' days</strong>, it will be deactivated.',
            '#fef2f2', '#ef4444', '#991b1b',
            $deadlineFormatted,
            $daysRemaining,
            date('Y'),
        ], $reminderTemplate);

        try {
            EmailHelper::sendRaw($supplier['email'], "Urgent : Vérification bientôt expirée / Verification expires in {$daysRemaining} days", $html);
            $db->prepare("UPDATE suppliers SET verification_reminder_sent = 2 WHERE id = ?")->execute([$supplier['id']]);
            echo "  Sent urgent warning to {$supplier['email']}\n";
        } catch (\Exception $e) {
            echo "  FAILED for {$supplier['email']}: " . $e->getMessage() . "\n";
        }

        usleep(200000);
    }

    // ─── 3. DEACTIVATE EXPIRED ACCOUNTS ───
    echo "\n--- Deactivating Expired Accounts ---\n";
    $stmt = $db->prepare("
        SELECT id, email, contact_person, company_name, name, verification_deadline
        FROM suppliers
        WHERE status = 'pending_verification'
        AND verification_deadline <= NOW()
    ");
    $stmt->execute();
    $expiredSuppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo "Found " . count($expiredSuppliers) . " expired suppliers.\n";

    foreach ($expiredSuppliers as $supplier) {
        $contactName = $supplier['contact_person'] ?: $supplier['name'];
        $companyName = $supplier['company_name'] ?: $supplier['name'];

        // Deactivate the account
        $db->prepare("UPDATE suppliers SET status = 'inactive', can_login = 0 WHERE id = ?")->execute([$supplier['id']]);

        // Update linked application status
        $db->prepare("UPDATE supplier_applications SET status = 'expired' WHERE supplier_id = ? AND status = 'pending'")->execute([$supplier['id']]);

        // Send expiry notification
        $html = str_replace([
            '{{contact_person}}',
            '{{company_name}}',
            '{{current_year}}',
        ], [
            htmlspecialchars($contactName),
            htmlspecialchars($companyName),
            date('Y'),
        ], $expiredTemplate);

        try {
            EmailHelper::sendRaw($supplier['email'], "Compte désactivé / Your OCSAPP Supplier Account Has Been Deactivated", $html);
            echo "  Deactivated & notified: {$supplier['email']}\n";
        } catch (\Exception $e) {
            echo "  Deactivated but email failed for {$supplier['email']}: " . $e->getMessage() . "\n";
        }

        // Log it
        if (function_exists('logger')) {
            logger("Supplier account expired and deactivated: {$supplier['email']} (ID: {$supplier['id']})", 'info');
        }

        // Send admin notification
        try {
            EmailHelper::send(
                'info@ocsapp.ca',
                "Supplier Verification Expired: {$companyName}",
                "<div style='font-family:Arial,sans-serif;padding:20px;'>
                    <h2 style='color:#dc2626;'>Supplier Verification Expired</h2>
                    <p>The following supplier account has been automatically deactivated due to an expired verification window:</p>
                    <ul>
                        <li><strong>Company:</strong> {$companyName}</li>
                        <li><strong>Contact:</strong> {$contactName}</li>
                        <li><strong>Email:</strong> {$supplier['email']}</li>
                        <li><strong>Deadline was:</strong> " . date('M j, Y', strtotime($supplier['verification_deadline'])) . "</li>
                    </ul>
                    <p>The account has been set to inactive. You can reactivate it from the admin panel if needed.</p>
                </div>"
            );
        } catch (\Exception $e) {
            echo "  Admin notification failed: " . $e->getMessage() . "\n";
        }

        usleep(200000);
    }

    // ─── 4. MARK OVERDUE INVOICES ───
    echo "\n--- Marking Overdue Invoices ---\n";
    $overdueCount = $db->exec("
        UPDATE supplier_invoices
        SET status = 'overdue'
        WHERE status IN ('sent', 'partial')
        AND due_date < CURDATE()
    ");
    echo "Marked {$overdueCount} invoices as overdue.\n";

    echo "\n=== Done! ===\n";

} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
