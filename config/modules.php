<?php

declare(strict_types=1);

// config/modules.php
return [
    'default' => [
        'base_path' => base_path(env('MODULE_DIRECTORY', 'app/Modules')),
        'namespace' => env('MODULE_NAMESPACE', 'App\\Modules'),

        'routing' => ['api'],

        'routing_options' => [
            'api' => [
                'prefix' => 'api',
                'version' => env('API_VERSION', 'v1'), // api/v1/...
                'middleware' => ['api', 'throttle:api', 'auth:sanctum'], // или passport/jwt
                'files' => ['api.php'],
            ],
        ],

        'structure' => [
            'controllers' => 'Http/Controllers',
            'resources' => 'Http/Resources',
            'requests' => 'Http/Requests',
            'models' => 'Models',
            'dto' => 'Http/DTOs',
            'actions' => 'Http/Actions',
            'queries' => 'Http/Queries',
            'policies' => 'Policies',
            'events' => 'Events',
            'listeners' => 'Listeners',
            'jobs' => 'Jobs',
            'routes' => 'routes',
            'migrations' => 'database/migrations',
            'seeders' => 'database/seeders',
            'factories' => 'database/factories',
            'config' => 'config',
            'support' => 'Support',
            'tests' => 'Tests',
        ],

        'scaffold' => [
            'use_dto' => true,
            'use_actions' => true,
            'use_queries' => true,
            'use_policies' => true,
            'stubs_path' => base_path('stubs/module'),
        ],
    ],

    'specific' => [
    ],
];
