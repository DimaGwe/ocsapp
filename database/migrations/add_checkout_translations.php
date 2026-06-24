<?php
/**
 * Migration: Add checkout page translation keys
 * Fixes bilingual compliance — checkout.php was fully hardcoded in English
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$keys = [
    'checkout_title'          => ['Checkout',                                   'Caisse'],
    'checkout_back_to_cart'   => ['Back to Cart',                               'Retour au panier'],
    'checkout_order_summary'  => ['Order Summary',                              'Résumé de la commande'],
    'checkout_item'           => ['item',                                       'article'],
    'checkout_items'          => ['items',                                      'articles'],
    'checkout_delivery_address' => ['Delivery Address',                         'Adresse de livraison'],
    'checkout_no_address'     => ['No saved addresses.',                        'Aucune adresse enregistrée.'],
    'checkout_add_address'    => ['Add one',                                    'En ajouter une'],
    'checkout_address_default' => ['Default',                                   'Par défaut'],
    'checkout_delivery_prefs' => ['Delivery Preferences',                       'Préférences de livraison'],
    'checkout_delivery_date'  => ['Delivery Date',                              'Date de livraison'],
    'checkout_delivery_time'  => ['Delivery Time',                              'Heure de livraison'],
    'checkout_time_morning'   => ['Morning (9 AM – 12 PM)',                     'Matin (9h – 12h)'],
    'checkout_time_afternoon' => ['Afternoon (12 PM – 3 PM)',                   'Après-midi (12h – 15h)'],
    'checkout_time_evening'   => ['Evening (3 PM – 6 PM)',                      'Soir (15h – 18h)'],
    'checkout_time_night'     => ['Night (6 PM – 9 PM)',                        'Nuit (18h – 21h)'],
    'checkout_order_notes'    => ['Order Notes (Optional)',                      'Notes de commande (facultatif)'],
    'checkout_notes_placeholder' => ['Special delivery instructions...',        'Instructions de livraison spéciales...'],
    'checkout_payment_method' => ['Payment Method',                             'Mode de paiement'],
    'checkout_pay_card'       => ['Credit / Debit Card',                        'Carte de crédit / débit'],
    'checkout_pay_card_sub'   => ['Visa, Mastercard, Amex',                     'Visa, Mastercard, Amex'],
    'checkout_pay_paypal'     => ['PayPal',                                     'PayPal'],
    'checkout_pay_paypal_sub' => ['Pay with your PayPal account',               'Payer avec votre compte PayPal'],
    'checkout_pay_interac'    => ['Interac e-Transfer',                         'Virement Interac'],
    'checkout_pay_interac_sub' => ['Send payment directly from your bank',      'Envoyez le paiement directement depuis votre banque'],
    'checkout_order_total'    => ['Order Total',                                'Total de la commande'],
    'checkout_subtotal'       => ['Subtotal',                                   'Sous-total'],
    'checkout_delivery_fee'   => ['Delivery Fee',                               'Frais de livraison'],
    'checkout_total'          => ['Total',                                      'Total'],
    'checkout_place_order'    => ['Place Order',                                'Passer la commande'],
    'checkout_processing'     => ['Processing...',                              'Traitement...'],
    'checkout_redirecting'    => ['Redirecting to payment...',                  'Redirection vers le paiement...'],
    'checkout_secure'         => ['Secure checkout – Your data is encrypted',   'Paiement sécurisé – Vos données sont chiffrées'],
    'checkout_qty'            => ['Qty',                                        'Qté'],
    'checkout_error_retry'    => ['Checkout failed. Please try again.',         'Échec de la commande. Veuillez réessayer.'],
    'checkout_error_occurred' => ['An error occurred. Please try again.',       'Une erreur est survenue. Veuillez réessayer.'],
];

try {
    $db = Database::getConnection();
    $inserted = 0;
    $skipped  = 0;

    $check = $db->prepare("SELECT COUNT(*) FROM translations WHERE `key` = ?");
    $insert = $db->prepare("
        INSERT INTO translations (`key`, category, en, fr)
        VALUES (?, 'checkout', ?, ?)
    ");

    foreach ($keys as $key => [$en, $fr]) {
        $check->execute([$key]);
        if ((int)$check->fetchColumn() > 0) {
            echo "SKIP: {$key} already exists\n";
            $skipped++;
        } else {
            $insert->execute([$key, $en, $fr]);
            echo "INSERT: {$key}\n";
            $inserted++;
        }
    }

    echo "\nDone — {$inserted} inserted, {$skipped} skipped.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
