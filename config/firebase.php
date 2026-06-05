<?php

return [

    'project_id' => env('FIREBASE_PROJECT_ID', 'tax-piya'),

    'credentials' => env('FIREBASE_CREDENTIALS') ?: storage_path('app/firebase/service-account.json'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Web SDK (cliente — valores públicos)
    |--------------------------------------------------------------------------
    */
    'web' => [
        'api_key'             => env('FIREBASE_API_KEY', ''),
        'auth_domain'         => env('FIREBASE_AUTH_DOMAIN', 'tax-piya.firebaseapp.com'),
        'storage_bucket'      => env('FIREBASE_STORAGE_BUCKET', 'tax-piya.firebasestorage.app'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', ''),
        'app_id'              => env('FIREBASE_APP_ID', ''),
        'measurement_id'      => env('FIREBASE_MEASUREMENT_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | FCM (notificaciones push)
    |--------------------------------------------------------------------------
    */
    'fcm_scope' => env('FCM_SCOPE', 'prod'),

];
