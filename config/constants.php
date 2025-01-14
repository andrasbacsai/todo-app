<?php

return [
    'core' => [
        'version' => '1.0.0',
        'self_hosted' => env('SELF_HOSTED', true),
    ],

    'pusher' => [
        'host' => env('PUSHER_HOST'),
        'port' => env('PUSHER_PORT'),
        'app_key' => env('PUSHER_APP_KEY'),
    ],

    'migration' => [
        'is_migration_enabled' => env('MIGRATION_ENABLED', true),
    ],

    'seeder' => [
        'is_seeder_enabled' => env('SEEDER_ENABLED', true),
    ],

    'horizon' => [
        'is_horizon_enabled' => env('HORIZON_ENABLED', true),
        'is_scheduler_enabled' => env('SCHEDULER_ENABLED', true),
    ],

    'stripe' => [
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'api_key' => env('STRIPE_API_KEY'),
        'webhook_url' => env('STRIPE_WEBHOOK_URL'),
    ],
];
