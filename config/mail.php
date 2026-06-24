<?php

/**
 * Email Configuration for OCSAPP
 * Using Hostinger SMTP
 */

return [
    // Test mode - set to false for production
    'test_mode' => false,

    // SMTP Configuration - credentials loaded from .env
    'smtp' => [
        'host' => env('MAIL_HOST', 'smtp.hostinger.com'),
        'port' => (int) env('MAIL_PORT', 465),
        'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
        'username' => env('MAIL_USERNAME', ''),
        'password' => env('MAIL_PASSWORD', ''),
        'from_address' => env('MAIL_FROM_ADDRESS', ''),
        'from_name' => env('MAIL_FROM_NAME', 'OCSAPP Marketplace'),
    ],

    // Default settings
    'defaults' => [
        'charset' => 'UTF-8',
        'reply_to' => 'info@ocsapp.ca', // Professional email address
    ],

    // Email templates path
    'templates_path' => __DIR__ . '/../app/Views/emails/',

    // Admin email for notifications
    'admin_email' => 'info@ocsapp.ca',

    // Email notifications settings
    'notifications' => [
        'order_confirmation' => [
            'enabled' => true,
            'subject' => 'Commande confirmée #{order_number} / Order Confirmed #{order_number}',
        ],
        'order_status_update' => [
            'enabled' => true,
            'subject' => 'Mise à jour de commande #{order_number} / Order Update #{order_number}',
        ],
        'order_cancelled' => [
            'enabled' => true,
            'subject' => 'Commande annulée #{order_number} / Order Cancelled #{order_number}',
        ],
        'low_stock_alert' => [
            'enabled' => true,
            'subject' => 'Alerte stock faible — {product_name} / Low Stock Alert — {product_name}',
        ],
    ],

    // Email logging
    'log' => [
        'enabled' => true,
        'path' => __DIR__ . '/../storage/logs/email.log',
    ],
];
