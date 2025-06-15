<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Configuration
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'default' => [

        /*
        |--------------------------------------------------------------------------
        | Directory
        |--------------------------------------------------------------------------
        |
        | Default directory 'Modules'
        |
        */

        'directory' => 'Modules',

        /*
        |--------------------------------------------------------------------------
        | Type Of Routing
        |--------------------------------------------------------------------------
        |
        | If you need / don't need different route files for web and api
        | you can change the array entries like you need them.
        |
        | Supported: "web", "api", "simple"
        |
        */

        'routing' => ['api'],

        /*
        |--------------------------------------------------------------------------
        | Module Structure
        |--------------------------------------------------------------------------
        |
        | In case your desired module structure differs
        | from the default structure defined here
        | feel free to change it the way you like it,
        |
        */

        'structure' => [
            'controllers' => 'Controllers',
            'resources' => 'Http/Resources',
            'requests' => 'Http/Requests',
            'models' => 'Models',
            'dto' => 'Http/DTOs',
            'actions' => 'Http/Actions',
            'mails' => 'Mail',
            'notifications' => 'Notifications',
            'events' => 'Events',
            'listeners' => 'Listeners',
            'observers' => 'Observers',
            'jobs' => 'Jobs',
            'translations' => 'Resources/lang',
            'routes' => 'routes',
            'migrations' => 'Database/Migrations',
            'seeds' => 'Database/Seeds',
            'factories' => 'Database/Factories',
            'helpers' => '',
            'filters' => 'Filters',
            'traits' => 'Traits',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Specific Configuration
    |--------------------------------------------------------------------------
    |
    | In the "specific" config you can disable individual modules
    | and override every "default" config from above
    | The array key needs to be the module name.
    |
    */

    'specific' => [

        /*
        |--------------------------------------------------------------------------
        | Example Module
        |--------------------------------------------------------------------------
        |
        |
        | 'ExampleModule' => [
        |     'enabled' => false,
        |     'routing' => [ 'simple' ],
        |     'structure' => [
        |         'controllers' => 'Controllers',
        |         'views' => 'Views',
        |         'translations' => 'Translations',
        |     ],
        | ],
        */

    ],
];
