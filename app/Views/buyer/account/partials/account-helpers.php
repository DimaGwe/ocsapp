<?php
/**
 * Shared bilingual formatting for the buyer account pages (/account and sub-pages).
 * fr-CA: "37,92 $" and "28 sept. 2026"; en: "$37.92" and "Sep 28, 2026". No em dashes.
 */
if (!function_exists('acct_money')) {
    function acct_money($amount, bool $fr): string
    {
        $n = (float) $amount;
        return $fr ? number_format($n, 2, ',', "\u{00A0}") . "\u{00A0}$" : '$' . number_format($n, 2);
    }

    function acct_date($date, bool $fr): string
    {
        $ts = strtotime((string) $date);
        if (!$ts) {
            return '';
        }
        if (!$fr) {
            return date('M j, Y', $ts);
        }
        $months = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juill.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
        return date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }

    /** Order status label; unknown values fall back to a readable version of the raw value. */
    function acct_status(string $status, bool $fr): string
    {
        $labels = [
            'pending'          => ['En attente', 'Pending'],
            'confirmed'        => ['Confirmée', 'Confirmed'],
            'processing'       => ['En préparation', 'Processing'],
            'ready'            => ['Prête', 'Ready'],
            'out_for_delivery' => ['En livraison', 'Out for delivery'],
            'delivered'        => ['Livrée', 'Delivered'],
            'completed'        => ['Terminée', 'Completed'],
            'cancelled'        => ['Annulée', 'Cancelled'],
            'refunded'         => ['Remboursée', 'Refunded'],
        ];
        return isset($labels[$status]) ? $labels[$status][$fr ? 0 : 1] : ucfirst(str_replace('_', ' ', $status));
    }
}
