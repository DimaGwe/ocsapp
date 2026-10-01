<?php
/**
 * Scheduled Task: Generate shipments from Distribution recurring routes
 * (Business Account Agreement Sec 5.2.2).
 *
 * For every active route whose next pickup date (next_generation_date) is
 * within notify_days_before days, creates that occurrence's shipment:
 *   - auto_submit = 0: a 'draft' the business reviews and approves at
 *     /distribution/routes/draft (DistributionRouteController::approveDraft)
 *   - auto_submit = 1: 'submitted' straight away, admin notified for a quote
 * then advances next_generation_date to the following occurrence, or marks
 * the route 'completed' once it passes end_date.
 *
 * - One shipment per route per pickup date (idempotent if run twice).
 * - Pickup dates already in the past are skipped, not back-filled.
 * - Businesses that are not active, or no longer on an eligible plan
 *   (RecurringRouteHelper::canUseRecurringRoutes), are skipped and their
 *   route is left as is, so it picks up again after an upgrade.
 *
 * Cron: run daily.
 *   30 5 * * * cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/generate_route_shipments.php >> /var/www/html/marketplace/storage/logs/cron-route-shipments.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/Helpers/FoundingBusinessHelper.php';
require_once BASE_PATH . '/app/Helpers/RecurringRouteHelper.php';
require_once BASE_PATH . '/app/Helpers/NotificationHelper.php';

use App\Helpers\RecurringRouteHelper;
use App\Helpers\NotificationHelper;

$db = Database::getConnection();
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$dryRun = in_array('--dry-run', $argv ?? [], true);

echo "[{$now}] generate_route_shipments starting" . ($dryRun ? ' (dry run)' : '') . "\n";

$created = 0;
try {
    $stmt = $db->query("
        SELECT r.*, bp.status AS business_status, bp.company_name,
               u.first_name, u.last_name, u.phone
        FROM distribution_recurring_routes r
        INNER JOIN business_profiles bp ON bp.id = r.business_profile_id
        INNER JOIN users u ON u.id = bp.user_id
        WHERE r.status = 'active'
          AND r.next_generation_date <= DATE_ADD(CURDATE(), INTERVAL GREATEST(COALESCE(r.notify_days_before, 0), 0) DAY)
        ORDER BY r.next_generation_date, r.id
    ");
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($routes as $route) {
        $routeId = (int)$route['id'];
        $businessId = (int)$route['business_profile_id'];
        $tag = "Route #{$routeId} ({$route['route_name']}, business #{$businessId})";

        if ($route['business_status'] !== 'active') {
            echo "[{$now}] {$tag}: business not active, skipped.\n";
            continue;
        }
        if (!RecurringRouteHelper::canUseRecurringRoutes($businessId)) {
            echo "[{$now}] {$tag}: plan not eligible for recurring routes, skipped.\n";
            continue;
        }

        try {
            $db->beginTransaction();

            // Re-read under lock so two overlapping runs can't both generate
            $lock = $db->prepare("SELECT next_generation_date, status FROM distribution_recurring_routes WHERE id = ? FOR UPDATE");
            $lock->execute([$routeId]);
            $locked = $lock->fetch(PDO::FETCH_ASSOC);
            if (!$locked || $locked['status'] !== 'active' || $locked['next_generation_date'] !== $route['next_generation_date']) {
                $db->rollBack();
                continue;
            }

            $pickupDate = $route['next_generation_date'];

            // Missed occurrences (cron down, route resumed late): skip to the next future date
            while ($pickupDate < $today) {
                $pickupDate = RecurringRouteHelper::nextOccurrenceAfter($route, $pickupDate);
            }

            $pastEnd = $route['end_date'] && $pickupDate > $route['end_date'];
            $leadDays = max(0, (int)$route['notify_days_before']);
            $due = !$pastEnd && $pickupDate <= date('Y-m-d', strtotime("+{$leadDays} days"));

            if ($due) {
                $dupe = $db->prepare("
                    SELECT id FROM distribution_shipments
                    WHERE recurring_route_id = ? AND requested_pickup_date = ? AND status <> 'cancelled' LIMIT 1
                ");
                $dupe->execute([$routeId, $pickupDate]);

                if (!$dupe->fetchColumn()) {
                    $shipment = createRouteShipment($db, $route, $pickupDate, $dryRun);
                    $created++;
                    echo "[{$now}] {$tag}: {$shipment['status']} shipment {$shipment['number']} for {$pickupDate}.\n";
                } else {
                    echo "[{$now}] {$tag}: shipment for {$pickupDate} already exists.\n";
                }
                $nextDate = RecurringRouteHelper::nextOccurrenceAfter($route, $pickupDate);
            } else {
                $nextDate = $pickupDate;
            }

            $completed = $route['end_date'] && $nextDate > $route['end_date'];

            if (!$dryRun) {
                $db->prepare("
                    UPDATE distribution_recurring_routes
                    SET next_generation_date = ?, status = ?,
                        last_generated_at = IF(?, NOW(), last_generated_at), updated_at = NOW()
                    WHERE id = ?
                ")->execute([$nextDate, $completed ? 'completed' : 'active', $due ? 1 : 0, $routeId]);
            }

            $dryRun ? $db->rollBack() : $db->commit();

            if ($completed) {
                echo "[{$now}] {$tag}: passed end date {$route['end_date']}, marked completed.\n";
            }
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log("generate_route_shipments error for route #{$routeId}: " . $e->getMessage());
            echo "[{$now}] {$tag}: ERROR - {$e->getMessage()}\n";
        }
    }

    echo "[{$now}] generate_route_shipments complete: {$created} shipment(s) from " . count($routes) . " due route(s).\n";
} catch (\Exception $e) {
    error_log('generate_route_shipments fatal error: ' . $e->getMessage());
    echo "[{$now}] FATAL: {$e->getMessage()}\n";
    exit(1);
}

/**
 * Insert one occurrence of a route as a distribution_shipments row (plus
 * destinations for multi-drop), mirroring DistributionShipmentController::store().
 * Runs inside the caller's transaction. Notifications are only sent for real runs.
 */
function createRouteShipment(PDO $db, array $route, string $pickupDate, bool $dryRun): array
{
    $destinations = json_decode($route['destinations_template'] ?? '[]', true) ?: [];
    $isMultiDrop = count($destinations) > 1;
    $single = $isMultiDrop ? [] : ($destinations[0] ?? []);
    $totalPackages = max(1, array_sum(array_map(fn($d) => (int)($d['packages_count'] ?? 1), $destinations)));
    $status = (int)$route['auto_submit'] === 1 ? 'submitted' : 'draft';
    $number = 'SHP-' . date('Ymd', strtotime($pickupDate)) . '-' . strtoupper(substr(uniqid(), -6));
    $contactName = trim(($route['first_name'] ?? '') . ' ' . ($route['last_name'] ?? '')) ?: null;

    $db->prepare("
        INSERT INTO distribution_shipments
        (business_profile_id, shipment_number, shipment_type, status, is_multi_drop,
         pickup_street, pickup_city, pickup_province, pickup_postal_code,
         pickup_contact_name, pickup_contact_phone,
         requested_pickup_date, requested_pickup_time_start, requested_pickup_time_end,
         destination_street, destination_city, destination_province, destination_postal_code,
         destination_contact_name, destination_contact_phone, destination_instructions,
         total_packages, recurring_route_id, is_recurring_instance, business_notes,
         submitted_at, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, 1, ?,
                ?, NOW(), NOW())
    ")->execute([
        $route['business_profile_id'],
        $number,
        $isMultiDrop ? 'multi_drop' : 'parcel',
        $status,
        $isMultiDrop ? 1 : 0,
        $route['pickup_street'],
        $route['pickup_city'],
        $route['pickup_province'],
        $route['pickup_postal_code'],
        $contactName,
        $route['phone'] ?: null,
        $pickupDate,
        $route['pickup_time_start'],
        $route['pickup_time_end'],
        $single['street'] ?? null,
        $single['city'] ?? null,
        $single['province'] ?? null,
        $single['postal_code'] ?? null,
        ($single['contact_name'] ?? '') ?: null,
        ($single['contact_phone'] ?? '') ?: null,
        ($single['delivery_instructions'] ?? '') ?: null,
        $totalPackages,
        $route['id'],
        'Generated from recurring route: ' . $route['route_name'],
        $status === 'submitted' ? date('Y-m-d H:i:s') : null,
    ]);
    $shipmentId = (int)$db->lastInsertId();

    if ($isMultiDrop) {
        $destStmt = $db->prepare("
            INSERT INTO distribution_shipment_destinations
            (shipment_id, sequence_order, destination_name, street, city, province, postal_code,
             contact_name, contact_phone, delivery_instructions, packages_count, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        foreach (array_values($destinations) as $i => $dest) {
            $destStmt->execute([
                $shipmentId,
                (int)($dest['sequence_order'] ?? $i + 1),
                ($dest['destination_name'] ?? '') ?: null,
                $dest['street'] ?? '',
                $dest['city'] ?? '',
                $dest['province'] ?? '',
                $dest['postal_code'] ?? '',
                ($dest['contact_name'] ?? '') ?: null,
                ($dest['contact_phone'] ?? '') ?: null,
                ($dest['delivery_instructions'] ?? '') ?: null,
                (int)($dest['packages_count'] ?? 1),
            ]);
        }
    }

    $db->prepare("
        INSERT INTO distribution_shipment_status_history
        (shipment_id, old_status, new_status, changed_by_type, notes, created_at)
        VALUES (?, NULL, ?, 'system', ?, NOW())
    ")->execute([$shipmentId, $status, 'Generated from recurring route #' . $route['id']]);

    if (!$dryRun) {
        $dateLabel = date('Y-m-d', strtotime($pickupDate));
        if ($status === 'draft') {
            NotificationHelper::addBusinessNotification(
                (int)$route['business_profile_id'], 'shipment',
                'Recurring shipment ready for review / Envoi récurrent prêt à approuver',
                "Your route \"{$route['route_name']}\" generated a shipment for pickup on {$dateLabel}. Review and approve it to submit. / "
                . "Votre route « {$route['route_name']} » a généré un envoi pour la cueillette du {$dateLabel}. Vérifiez-le et approuvez-le pour le soumettre.",
                '/distribution/routes/draft?id=' . $shipmentId, 'route'
            );
        } else {
            NotificationHelper::addBusinessNotification(
                (int)$route['business_profile_id'], 'shipment',
                'Recurring shipment submitted / Envoi récurrent soumis',
                "Your route \"{$route['route_name']}\" submitted shipment {$number} for pickup on {$dateLabel}. You will receive a quote shortly. / "
                . "Votre route « {$route['route_name']} » a soumis l'envoi {$number} pour la cueillette du {$dateLabel}. Vous recevrez une soumission sous peu.",
                '/distribution/shipments/show?id=' . $shipmentId, 'route'
            );
            NotificationHelper::add(
                'distribution_shipment',
                'Recurring shipment submitted',
                "{$route['company_name']}: shipment {$number} auto-submitted from route \"{$route['route_name']}\" for pickup {$dateLabel}. Needs a quote.",
                ['link' => '/admin/shipments/view?id=' . $shipmentId]
            );
        }
    }

    return ['id' => $shipmentId, 'number' => $number, 'status' => $status];
}
