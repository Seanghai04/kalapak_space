<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY'),
            'secret' => env('SUPABASE_SECRET_KEY'),
            'region' => env('SUPABASE_REGION', 'us-east-1'),
            'bucket' => env('SUPABASE_BUCKET', 'kalapak-assets'),
            'endpoint' => env('SUPABASE_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw' => false,
            'url' => env('SUPABASE_URL') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET', 'kalapak-assets'),
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
