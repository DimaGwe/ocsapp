<?php

namespace App\Helpers;

/**
 * PayoutHelper - single source of truth for driver payout math.
 *
 * Replaces three independently-drifted copies of the 70/30 split
 * (DriverApiController, AdminDeliveryController) plus a legacy 20%/DOP
 * formula (DeliveryController::createEarningsRecord). Every surcharge
 * added to the fee engine (oversize, additional-stop, long-distance)
 * must flow through calculateDriverPayout() rather than forking again.
 *
 * Rates (configurable via env):
 *   DRIVER_BASE_PAY      - minimum base pay per delivery (default $5.00)
 *   DRIVER_PLATFORM_CUT  - platform commission 0-1 (default 0.30 = 30%)
 */
class PayoutHelper
{
    public static function basePay(): float
    {
        return (float)(getenv('DRIVER_BASE_PAY') ?: 5.00);
    }

    public static function platformCut(): float
    {
        return (float)(getenv('DRIVER_PLATFORM_CUT') ?: 0.30);
    }

    /**
     * Compute the full driver payout for a delivery, including any
     * fee-engine surcharges (all split 70/30 same as the base fee, per
     * Ecosystem Backend Requirements Sec 2.3).
     *
     * @param float $baseDeliveryFee    Order's base delivery_fee
     * @param float $additionalStopFee  Sec 2.2 surcharge share, if any
     * @param float $oversizeSurcharge  Sec 2.1 surcharge share, if any (not yet built)
     * @param float $distanceSurcharge  Sec 2.1b surcharge share, if any (not yet built)
     * @return array{
     *   base_component: float,
     *   surcharge_component: float,
     *   additional_stop_fee: float,
     *   oversize_surcharge: float,
     *   distance_surcharge: float,
     *   gross_pay: float,
     *   platform_cut_rate: float,
     *   driver_net_payout: float,
     *   platform_commission: float
     * }
     */
    public static function calculateDriverPayout(
        float $baseDeliveryFee,
        float $additionalStopFee = 0.00,
        float $oversizeSurcharge = 0.00,
        float $distanceSurcharge = 0.00
    ): array {
        $basePay     = self::basePay();
        $platformCut = self::platformCut();

        // Base component floors at the configured minimum base pay, same as before.
        $baseComponent      = max($basePay, $baseDeliveryFee);
        $surchargeComponent = round($additionalStopFee + $oversizeSurcharge + $distanceSurcharge, 2);
        $grossPay           = round($baseComponent + $surchargeComponent, 2);

        $driverNetPayout    = round($grossPay * (1 - $platformCut), 2);
        // Floor at base-minus-commission so surcharges never pull a driver below the old guarantee.
        $driverNetPayout    = max($driverNetPayout, round($basePay * (1 - $platformCut), 2));
        $platformCommission = round($grossPay - $driverNetPayout, 2);

        return [
            'base_component'      => $baseComponent,
            'surcharge_component' => $surchargeComponent,
            'additional_stop_fee' => round($additionalStopFee, 2),
            'oversize_surcharge'  => round($oversizeSurcharge, 2),
            'distance_surcharge'  => round($distanceSurcharge, 2),
            'gross_pay'           => $grossPay,
            'platform_cut_rate'   => $platformCut,
            'driver_net_payout'   => $driverNetPayout,
            'platform_commission' => $platformCommission,
        ];
    }

    /**
     * Reverse-engineer the commission/gross for a payout that was already
     * locked in on orders.driver_payout (e.g. admin-set, or computed at
     * accept-time before delivery). Used at delivery-completion time when
     * we need to record delivery_earnings from a stored net figure rather
     * than recomputing from scratch.
     *
     * @return array{commission: float, gross: float, platform_cut_rate: float}
     */
    public static function commissionFromNetPayout(float $netPayout): array
    {
        $platformCut = self::platformCut();
        $commission  = round($netPayout / (1 - $platformCut) * $platformCut, 2);
        $gross       = round($netPayout + $commission, 2);

        return [
            'commission'        => $commission,
            'gross'             => $gross,
            'platform_cut_rate' => $platformCut,
        ];
    }
}
