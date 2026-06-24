<?php
/**
 * Migration: Add French translations for supplier analytics page and products page
 * Fixes incomplete bilingual display on /supplier/analytics and /supplier/products
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $translations = [
        // Analytics header (may already exist — safe due to ON DUPLICATE KEY UPDATE)
        ['sup_analytics_insights',    'supplier', 'Analytics & Insights',                          'Analytique &amp; Aperçus'],
        ['sup_showing_all_time',      'supplier', 'Showing all time',                               'Affichage: toute la période'],
        ['sup_showing_ytd',           'supplier', 'Showing year to date',                           'Affichage: depuis le début de l\'année'],
        ['sup_last_x_days',           'supplier', 'Last %d days',                                   'Derniers %d jours'],
        ['sup_custom',                'supplier', 'Custom',                                         'Personnalisé'],
        ['sup_custom_range',          'supplier', 'Custom Range:',                                  'Plage personnalisée :'],
        ['sup_from',                  'supplier', 'From',                                           'Du'],
        ['sup_to_label',              'supplier', 'To',                                             'Au'],
        ['sup_apply',                 'supplier', 'Apply',                                          'Appliquer'],

        // Key metrics stat cards
        ['sup_total_revenue',         'supplier', 'Total Revenue',                                  'Revenus totaux'],
        ['sup_total_orders',          'supplier', 'Total Orders',                                   'Commandes totales'],
        ['sup_avg_order_value',       'supplier', 'Avg Order Value',                                'Valeur moy. / commande'],
        ['sup_acceptance_rate',       'supplier', 'Acceptance Rate',                                'Taux d\'acceptation'],

        // Order status breakdown section
        ['sup_order_status_breakdown','supplier', 'Order Status Breakdown',                         'Répartition des statuts'],
        ['sup_orders_pending',        'supplier', 'Pending',                                        'En attente'],
        ['sup_orders_receiving',      'supplier', 'Receiving',                                      'En réception'],
        ['sup_orders_completed',      'supplier', 'Completed',                                      'Complété'],
        ['sup_orders_cancelled',      'supplier', 'Cancelled',                                      'Annulé'],

        // Monthly trend chart
        ['sup_monthly_trend',         'supplier', 'Monthly Orders &amp; Revenue (Last 6 Months)',   'Commandes &amp; revenus mensuels (6 derniers mois)'],

        // Top products section
        ['sup_top_products',          'supplier', 'Top Performing Products',                        'Meilleurs produits'],
        ['sup_product_col',           'supplier', 'Product',                                        'Produit'],
        ['sup_orders_col',            'supplier', 'Orders',                                         'Commandes'],
        ['sup_quantity_col',          'supplier', 'Quantity',                                       'Quantité'],
        ['sup_revenue_col',           'supplier', 'Revenue',                                        'Revenu'],
        ['sup_no_product_data',       'supplier', 'No product data available for the selected period', 'Aucune donnée produit disponible pour la période sélectionnée'],

        // Recent activity section
        ['sup_recent_activity',       'supplier', 'Recent Activity',                                'Activité récente'],
        ['sup_amount_col',            'supplier', 'Amount',                                         'Montant'],
        ['sup_items_suffix',          'supplier', 'items',                                          'articles'],

        // Product catalog overview section
        ['sup_catalog_overview',      'supplier', 'Product Catalog Overview',                       'Aperçu du catalogue produits'],
        ['sup_unavailable',           'supplier', 'Unavailable',                                    'Non disponible'],

        // Products page (units, not set, days)
        ['sup_units',                 'supplier', 'units',                                          'unités'],
        ['sup_not_set',               'supplier', 'Not set',                                        'Non défini'],
        ['sup_days',                  'supplier', 'days',                                           'jours'],
    ];

    $stmt = $db->prepare("
        INSERT INTO translations (`key`, category, en, fr)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)
    ");

    $count = 0;
    foreach ($translations as $row) {
        $stmt->execute($row);
        $count++;
        echo "OK: {$row[0]}\n";
    }

    echo "\nDone — {$count} translations inserted/updated.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
