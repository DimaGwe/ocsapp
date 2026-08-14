<?php

namespace App\Helpers;

/**
 * DistributionFeeHelper — Business Account Agreement Section 8 / Schedule B.
 *
 * Automates the Distribution Fee, flat B2B delivery fee, Oversize Surcharge
 * (Sec. 8.10) and Long-Distance Surcharge (Sec. 8.10a) for Débutant/Pro tier
 * shipments, per Jack's direction (2026-08-14). Enterprise (Sec. 8.5) is
 * explicitly excluded — it stays a manually negotiated custom quote, this
 * helper is never called for that tier.
 */
class DistributionFeeHelper
{
    /**
     * @param array $shipment A distribution_shipments row (associative array),
     *   plus 'stop_count' (int, 1 for single-destination, count of
     *   distribution_shipment_destinations for multi-drop).
     * @param float $commissionRate The business's distribution_plans.commission_rate (5.00 or 7.00).
     *
     * @return array{
     *   zone_code: ?string,
     *   routed_distance_km: ?float,
     *   declared_value: float,
     *   distribution_fee_amount: float,
     *   delivery_fee: float,
     *   oversize_base_surcharge: float,
     *   oversize_increment_surcharge: float,
     *   oversize_increment_count: int,
     *   long_distance_base_surcharge: float,
     *   long_distance_increment_surcharge: float,
     *   long_distance_increment_count: int,
     *   additional_stop_fee: float,
     *   hard_cap_exceeded: bool,
     *   subtotal: float,
     *   tax_amount: float,
     *   total_amount: float
     * }
     */
    public static function calculateShipmentFees(array $shipment, float $commissionRate): array
    {
        require_once __DIR__ . '/functions.php';
        require_once __DIR__ . '/GeocodingHelper.php';

        $zoneCode = resolveB2BZoneCode($shipment['pickup_city'] ?? null);
        $rates = resolveB2BDistributionRates($zoneCode);

        $declaredValue = (float)($shipment['declared_value'] ?? 0);
        $distributionFeeAmount = round($declaredValue * ($commissionRate / 100), 2);
        $deliveryFee = $rates['delivery_fee'];

        // Routed distance: lazily geocode pickup and destination, cache onto the row.
        $pickupCoords = resolveB2BPointCoords([
            'lat' => $shipment['pickup_latitude'] ?? null,
            'lng' => $shipment['pickup_longitude'] ?? null,
            'street' => $shipment['pickup_street'] ?? null,
            'city' => $shipment['pickup_city'] ?? null,
            'province' => $shipment['pickup_province'] ?? null,
            'postal_code' => $shipment['pickup_postal_code'] ?? null,
            'id' => $shipment['id'] ?? null,
            'table' => 'distribution_shipments',
            'lat_col' => 'pickup_latitude',
            'lng_col' => 'pickup_longitude',
        ]);

        // Multi-drop shipments don't have one single destination - distance surcharge
        // only applies to single-destination shipments, matching Sec 8.10a's "pickupto-delivery distance" wording (a multi-drop route's per-leg distance isn't a single
        // number the same way).
        $isMultiDrop = !empty($shipment['is_multi_drop']);
        $distanceKm = null;
        if (!$isMultiDrop && $pickupCoords) {
            $destCoords = resolveB2BPointCoords([
                'lat' => $shipment['destination_latitude'] ?? null,
                'lng' => $shipment['destination_longitude'] ?? null,
                'street' => $shipment['destination_street'] ?? null,
                'city' => $shipment['destination_city'] ?? null,
                'province' => $shipment['destination_province'] ?? null,
                'postal_code' => $shipment['destination_postal_code'] ?? null,
                'id' => $shipment['id'] ?? null,
                'table' => 'distribution_shipments',
                'lat_col' => 'destination_latitude',
                'lng_col' => 'destination_longitude',
            ]);

            if ($destCoords) {
                $waypoints = [$pickupCoords, $destCoords];
                $route = \GeocodingHelper::getGoogleDirectionsRoute($waypoints)
                      ?? \GeocodingHelper::getOSRMRoute($waypoints);
                $distanceKm = $route['distance_km'] ?? null;
            }
        }

        $oversize = calculateB2BOversizeSurcharge($zoneCode, (float)($shipment['total_weight_kg'] ?? 0));
        $longDistance = calculateB2BLongDistanceSurcharge($zoneCode, $distanceKm);
        $stopCount = (int)($shipment['stop_count'] ?? 1);
        $stopFee = calculateB2BAdditionalStopFee($zoneCode, $stopCount);

        $hardCapExceeded = $oversize['hard_cap_exceeded'] || $longDistance['hard_cap_exceeded'];

        $subtotal = round(
            $distributionFeeAmount + $deliveryFee
            + $oversize['total_surcharge'] + $longDistance['total_surcharge']
            + $stopFee['total_fee'],
            2
        );
        $taxRate = 14.975;
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        return [
            'zone_code' => $zoneCode,
            'routed_distance_km' => $distanceKm,
            'declared_value' => round($declaredValue, 2),
            'distribution_fee_amount' => $distributionFeeAmount,
            'delivery_fee' => $deliveryFee,
            'oversize_base_surcharge' => $oversize['base_surcharge'],
            'oversize_increment_surcharge' => $oversize['increment_surcharge'],
            'oversize_increment_count' => $oversize['increment_count'],
            'long_distance_base_surcharge' => $longDistance['base_surcharge'],
            'long_distance_increment_surcharge' => $longDistance['increment_surcharge'],
            'long_distance_increment_count' => $longDistance['increment_count'],
            'additional_stop_fee' => $stopFee['total_fee'],
            'hard_cap_exceeded' => $hardCapExceeded,
            'tax_rate' => $taxRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ];
    }
}
