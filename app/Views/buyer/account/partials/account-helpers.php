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

    /** "28 sept. 2026 à 21 h 01" / "Sep 28, 2026 at 9:01 PM" */
    function acct_datetime($date, bool $fr): string
    {
        $ts = strtotime((string) $date);
        if (!$ts) {
            return '';
        }
        return acct_date($date, $fr) . ($fr ? ' à ' . date('G', $ts) . ' h ' . date('i', $ts) : ' at ' . date('g:i A', $ts));
    }

    /** Address fields are stored sanitize()d (HTML-escaped): decode to plain text; output escapes once. */
    function acct_plain_row($row): array
    {
        return is_array($row) ? array_map(fn($v) => is_string($v) ? html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $v, $row) : [];
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
