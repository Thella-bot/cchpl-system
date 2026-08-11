<?php

use Illuminate\Support\Str;

return [

    'default' => env('CACHE_STORE', 'file'),

    'stores' => [

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => env('CACHE_DATABASE_CONNECTION', null),
            'lock_connection' => env('CACHE_LOCK_DATABASE_CONNECTION', null),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => env('REDIS_LOCK_CONNECTION', 'default'),
        ],

    ],

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),

];
