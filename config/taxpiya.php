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
    | Google Maps API Key
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

];
