<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ciudad por defecto para tarifas
    |--------------------------------------------------------------------------
    */
    'default_city' => env('TAXPIYA_DEFAULT_CITY', 'Medellín'),

    /*
    |--------------------------------------------------------------------------
    | Radio de búsqueda de conductores (km)
    |--------------------------------------------------------------------------
    */
    'search_radius_km' => (float) env('TAXPIYA_SEARCH_RADIUS_KM', 8),

    /*
    |--------------------------------------------------------------------------
    | Disponibilidad conductores / caducidad de solicitudes
    |--------------------------------------------------------------------------
    */
    'driver_position_ttl_minutes' => (int) env('TAXPIYA_DRIVER_POSITION_TTL_MINUTES', 5),
    'trip_search_ttl_minutes'     => (int) env('TAXPIYA_TRIP_SEARCH_TTL_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Mapas — OpenStreetMap (gratis, sin API key)
    |--------------------------------------------------------------------------
    */
    'map_provider' => env('TAXPIYA_MAP_PROVIDER', 'osm'),

    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key (opcional, legacy)
    |--------------------------------------------------------------------------
    */
    'google_maps_key' => env('GOOGLE_MAPS_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase (migración futura)
    |--------------------------------------------------------------------------
    */
    'firebase' => [
        'project_id'        => env('FIREBASE_PROJECT_ID', 'tax-piya'),
        'use_firestore'     => env('TAXPIYA_USE_FIRESTORE', true),
        'use_firebase_auth' => env('TAXPIYA_USE_FIREBASE_AUTH', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet / billetera conductores
    |--------------------------------------------------------------------------
    */
    'referrals' => [
        'enabled' => env('TAXPIYA_REFERRALS_ENABLED', true),
    ],

    'wallet' => [
        'commission_percent'    => (float) env('TAXPIYA_WALLET_COMMISSION_PERCENT', 10),
        'commission_min'        => (float) env('TAXPIYA_WALLET_COMMISSION_MIN', 500),
        'fee_accept'            => (float) env('TAXPIYA_WALLET_FEE_ACCEPT', 0),
        'default_min_operativo' => (float) env('TAXPIYA_WALLET_MIN_OPERATIVO', 5000),
        'demo_initial_balance'  => (float) env('TAXPIYA_WALLET_DEMO_BALANCE', 100000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistencia SQLite (Render — respaldo gratis en GitHub, sin pagar)
    |--------------------------------------------------------------------------
    */
    'persistence' => [
        'enabled'          => env('TAXPIYA_GITHUB_BACKUP', true),
        'backup_minutes'   => (int) env('TAXPIYA_GITHUB_BACKUP_MINUTES', 5),
        'github_token'     => env('GITHUB_BACKUP_TOKEN', ''),
        'github_owner'     => env('GITHUB_BACKUP_OWNER', 'sam3miro22-pixel'),
        'github_repo'      => env('GITHUB_BACKUP_REPO', 'taxpiya-db-backup'),
        'github_path'      => env('GITHUB_BACKUP_PATH', 'taxpiya.sqlite'),
        'public_restore_fallback' => env('TAXPIYA_GITHUB_PUBLIC_RESTORE', true),
        'dump_key'         => env('TAXPIYA_DUMP_KEY', substr(hash('sha256', (string) env('APP_KEY', '') . 'taxpiya-dump'), 0, 40)),
    ],

];
