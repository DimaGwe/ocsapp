<?php
/**
 * Application Configuration
 */

return [
    'debug' => env('APP_DEBUG', false),
    'name' => env('APP_NAME', 'OCSAPP'),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('TIMEZONE', 'America/Toronto'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    // Delivery fee in CAD — update via admin settings or .env DELIVERY_FEE
    'delivery_fee' => (float) env('DELIVERY_FEE', '5.00'),
    // Google Maps API key (Maps JS API + Directions API enabled)
    'google_maps_key' => env('GOOGLE_MAPS_KEY', 'AIzaSyB43koHaoLagCIiwoEydQXPoQAfglYGTqY'),
];
