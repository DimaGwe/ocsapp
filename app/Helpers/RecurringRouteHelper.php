<?php

namespace App\Helpers;

/**
 * RecurringRouteHelper - Distribution recurring routes (Business Account
 * Agreement Sec 5.2.2): plan eligibility and schedule math, shared by
 * DistributionRouteController and scheduled_tasks/generate_route_shipments.php.
 *
 * A route's next_generation_date is the pickup date of its next occurrence.
 * The cron creates that occurrence's shipment notify_days_before days ahead,
 * then advances next_generation_date with nextOccurrenceAfter().
 */
class RecurringRouteHelper
{
    const ELIGIBLE_PLANS = ['pro', 'enterprise'];

    /**
     * Pro and Enterprise plans, plus Founding Partners during their Founding
     * period (Sec 8.12), even though those are billed at the Debutant rate.
     */
    public static function canUseRecurringRoutes(int $businessId): bool
    {
        FoundingBusinessHelper::applyLazyExpiryIfNeeded($businessId);

        $stmt = \Database::getConnection()->prepare("
            SELECT bp.founding_partner, bp.founding_commission_rate_override, dp.code AS plan_code
            FROM business_profiles bp
            LEFT JOIN distribution_plans dp ON bp.distribution_plan_id = dp.id
            WHERE bp.id = ? LIMIT 1
        ");
        $stmt->execute([$businessId]);
        $biz = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$biz) return false;

        return in_array($biz['plan_code'] ?? null, self::ELIGIBLE_PLANS, true)
            || FoundingBusinessHelper::isActive($biz);
    }

    /**
     * First occurrence strictly after $after (Y-m-d) for a route row.
     */
    public static function nextOccurrenceAfter(array $route, string $after): string
    {
        $date = new \DateTime($after);

        switch ($route['frequency']) {
            case 'daily':
                return $date->modify('+1 day')->format('Y-m-d');

            case 'biweekly':
                return $date->modify('+14 days')->format('Y-m-d');

            case 'weekly':
                $days = json_decode($route['days_of_week'] ?? '[]', true) ?: [];
                $dayMap = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];
                $selected = array_values(array_unique(array_filter(
                    array_map(fn($d) => $dayMap[strtolower((string)$d)] ?? null, $days),
                    fn($d) => $d !== null
                )));
                if (!$selected) {
                    return $date->modify('+7 days')->format('Y-m-d');
                }
                for ($i = 1; $i <= 7; $i++) {
                    $candidate = (clone $date)->modify("+{$i} days");
                    if (in_array((int)$candidate->format('w'), $selected, true)) {
                        return $candidate->format('Y-m-d');
                    }
                }
                return $date->modify('+7 days')->format('Y-m-d');

            case 'monthly':
                // Day 29-31 falls back to the month's last day (Feb 30 -> Feb 28/29)
                $target = (int)($route['day_of_month'] ?? 0) ?: (int)$date->format('j');
                $sameMonth = min($target, (int)$date->format('t'));
                if ($sameMonth > (int)$date->format('j')) {
                    return $date->format('Y-m-') . sprintf('%02d', $sameMonth);
                }
                $next = (new \DateTime($date->format('Y-m-01')))->modify('+1 month');
                return $next->format('Y-m-') . sprintf('%02d', min($target, (int)$next->format('t')));

            default:
                return $date->modify('+7 days')->format('Y-m-d');
        }
    }
}
