<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fare Calculation Settings
    |--------------------------------------------------------------------------
    */
    'fare' => [
        'base_fare' => env('TAXI_BASE_FARE', 5.00),
        'per_km_rate' => env('TAXI_PER_KM_RATE', 2.50),
        'per_minute_rate' => env('TAXI_PER_MINUTE_RATE', 0.50),
        'minimum_fare' => env('TAXI_MINIMUM_FARE', 10.00),
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Settings
    |--------------------------------------------------------------------------
    */
    'booking' => [
        'max_passengers' => env('TAXI_MAX_PASSENGERS', 8),
        'max_stops' => env('TAXI_MAX_STOPS', 5),
        'advance_booking_hours' => env('TAXI_ADVANCE_BOOKING_HOURS', 72),
        'cancellation_charge' => env('TAXI_CANCELLATION_CHARGE', 5.00),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Settings
    |--------------------------------------------------------------------------
    */
    'driver' => [
        'max_daily_hours' => env('TAXI_DRIVER_MAX_DAILY_HOURS', 12),
        'max_weekly_hours' => env('TAXI_DRIVER_MAX_WEEKLY_HOURS', 72),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'methods' => ['card', 'cash'],
        'currency' => env('TAXI_CURRENCY', 'USD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'sms' => [
            'enabled' => env('TAXI_SMS_NOTIFICATIONS', true),
            'provider' => env('TAXI_SMS_PROVIDER', 'twilio'),
        ],
        'email' => [
            'enabled' => env('TAXI_EMAIL_NOTIFICATIONS', true),
        ],
    ],
];
