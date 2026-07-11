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
    /*
    |--------------------------------------------------------------------------
    | Firebase Auth (pasajero/conductor)
    |--------------------------------------------------------------------------
    | Firebase verifica identidad (correo/Google). Laravel guarda users.id para
    | viajes, billetera, referidos y sesión web. Empresa/admin: solo Laravel.
    */
    'firebase' => [
        'project_id'        => env('FIREBASE_PROJECT_ID', 'tax-piya'),
        'use_firestore'     => env('TAXPIYA_USE_FIRESTORE', true),
        'use_firebase_auth' => env('TAXPIYA_USE_FIREBASE_AUTH', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asistente IA (Groq)
    |--------------------------------------------------------------------------
    */
    'assistant' => [
        'groq_api_key'  => env('GROQ_API_KEY', ''),
        'groq_model'    => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'groq_base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet / billetera conductores
    |--------------------------------------------------------------------------
    */
    'referrals' => [
        'enabled'      => env('TAXPIYA_REFERRALS_ENABLED', true),
        'bonus_amount' => (float) env('TAXPIYA_REFERRAL_BONUS', 5000),
    ],

    'registration' => [
        'docs_email' => env('TAXPIYA_DOCS_EMAIL', 'taxpiya20@gmail.com'),
    ],

    'session' => [
        'lifetime_minutes' => (int) env('SESSION_LIFETIME', 525600),
        'remember_default' => env('TAXPIYA_REMEMBER_DEFAULT', true),
    ],

    'wallet' => [
        'commission_percent'    => (float) env('TAXPIYA_WALLET_COMMISSION_PERCENT', 10),
        'commission_min'        => (float) env('TAXPIYA_WALLET_COMMISSION_MIN', 500),
        'fee_accept'            => (float) env('TAXPIYA_WALLET_FEE_ACCEPT', 0),
        'default_min_operativo' => (float) env('TAXPIYA_WALLET_MIN_OPERATIVO', 5000),
        'demo_initial_balance'  => (float) env('TAXPIYA_WALLET_DEMO_BALANCE', 100000),
        'auto_approve_requests' => env('TAXPIYA_WALLET_AUTO_APPROVE', false),
        'nequi' => [
            'numero'  => env('TAXPIYA_NEQUI_NUMERO', '3124959199'),
            'cedula'  => env('TAXPIYA_NEQUI_CEDULA', '1083875427'),
            'titular' => env('TAXPIYA_NEQUI_TITULAR', 'Medardo Torres'),
        ],
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

    /*
    |--------------------------------------------------------------------------
    | WhatsApp (Baileys sidecar en puerto local)
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'port' => (int) env('WHATSAPP_PORT', 8051),
    ],

    'keepalive_key' => env('TAXPIYA_KEEPALIVE_KEY', substr(hash('sha256', (string) env('APP_KEY', '') . 'taxpiya-keepalive'), 0, 40)),

];
