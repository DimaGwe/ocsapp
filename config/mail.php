<?php

/**
 * Email Configuration for OCSAPP
 * Using Hostinger SMTP
 */

return [
    // Test mode - set to false for production
    'test_mode' => false,

    // SMTP Configuration (Hostinger)
    'smtp' => [
        'host' => 'smtp.hostinger.com',
        'port' => 465,
        'encryption' => 'ssl', // SSL on port 465
        'username' => 'info@ocsapp.ca',
        'password' => 'JdF$ocs_2026',
        'from_address' => 'info@ocsapp.ca',
        'from_name' => 'OCSAPP',
    ],

    // Default settings
    'defaults' => [
        'charset' => 'UTF-8',
        'reply_to' => 'info@ocsapp.ca', // Now using the actual Hostinger email
    ],

    // Email templates path
    'templates_path' => __DIR__ . '/../app/Views/emails/',

    // Email notifications settings
    'notifications' => [
        'order_confirmation' => [
            'enabled' => true,
            'subject' => 'Order Confirmation - Order #{order_number}',
        ],
        'order_status_update' => [
            'enabled' => true,
            'subject' => 'Order Update - Order #{order_number}',
        ],
        'order_cancelled' => [
            'enabled' => true,
            'subject' => 'Order Cancelled - Order #{order_number}',
        ],
        'low_stock_alert' => [
            'enabled' => true,
            'subject' => 'Low Stock Alert - {product_name}',
        ],
    ],

    // Email logging
    'log' => [
        'enabled' => true,
        'path' => __DIR__ . '/../storage/logs/email.log',
    ],
];
