<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available locales/languages
    |--------------------------------------------------------------------------
    |
    | Available locales for routing
    |
     */
    'locales' => [
        'fr' => [
            'name'             => 'English',
            'native_name'      => 'English',
            'flag'             => 'gb.svg',
            'locale'           => 'fr', // ISO 639-1
            'canonical_locale' => 'fr_FR', // ISO 3166-1
            'full_locale'      => 'fr_FR.UTF-8',
        ],
        // 'ar' => [
        //     'name'             => 'Arabic',
        //     'native_name'      => 'Arabic',
        //     'flag'             => 'sa.svg',
        //     'locale'           => 'ar', // ISO 639-1
        //     'canonical_locale' => 'ar_AR', // ISO 3166-1
        //     'full_locale'      => 'ar_AR.UTF-8',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback locale/language
    |--------------------------------------------------------------------------
    |
    | Fallback locale for routing
    |
     */
    'default_locale'    => 'fr',

    /*
    |--------------------------------------------------------------------------
    | Set Carbon locale
    |--------------------------------------------------------------------------
    |
    | Call Carbon::setLocale($locale) and set current locale in middleware
    |
     */
    'set_carbon_locale' => true,

    /*
    |--------------------------------------------------------------------------
    | Set System locale
    |--------------------------------------------------------------------------
    |
    | Call setlocale(LC_ALL, $locale) and set current locale in middleware
    |
     */
    'set_system_locale' => true,

    /*
    |--------------------------------------------------------------------------
    | Exclude segments from redirect
    |--------------------------------------------------------------------------
    |
    | Exclude segments from redirects in the middleware
    |
     */
    'exclude_segments'  => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Basic route
    |
     */
    'text-route'        => [
        'route'      => 'texts',
        'controller' => '\Longman\LaravelMultiLang\Controllers\TextsController',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache parameters
    |
     */
    'cache'             => [
        'enabled'  => true,
        'store'    => env('CACHE_DRIVER', 'default'),
        'lifetime' => 1440,
    ],

    /*
    |--------------------------------------------------------------------------
    | DB Configuration
    |--------------------------------------------------------------------------
    |
    | DB parameters
    |
     */
    'db'                => [
        'autosave'    => true, // Autosave missing texts in database. Only when environment is local
        'connection'  => env('DB_CONNECTION', 'default'),
        'texts_table' => 'texts',
    ],

];
