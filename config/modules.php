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
            'controllers' => 'Infrastructure/Http/Controllers',
            'requests' => 'Infrastructure/Http/Requests',
            'models' => 'Infrastructure/Models',
            'dto' => 'Application/DTO',
            'actions' => 'Application/Actions',
            'policies' => 'Infrastructure/Policies',
            'routes' => 'Infrastructure/Routes',
            'migrations' => 'Database/Migrations',
            'factories' => 'Database/Factories',
        ],

        'scaffold' => [
            'use_dto' => true,
            'use_actions' => true,
            'use_policies' => true,
            'stubs_path' => base_path('stubs/module'),
        ],
    ],

    'specific' => [
        'Auth' => [
            'enabled' => false, // Disabled - using new Clean Architecture structure
        ],
        'User' => [
            'enabled' => false, // Disabled - using new Clean Architecture structure
        ],
        'Role' => [
            'enabled' => false, // Disabled - using new Clean Architecture structure
        ],
        'Permission' => [
            'enabled' => false, // Disabled - using new Clean Architecture structure
        ],
    ],
];
