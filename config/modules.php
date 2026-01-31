<?php

declare(strict_types=1);


return [
    'default' => [
        'base_path' => base_path(env('MODULE_DIRECTORY', 'app/Modules')),
        'namespace' => env('MODULE_NAMESPACE', 'App\\Modules'),

        'routing' => ['api'],

        'routing_options' => [
            'api' => [
                'prefix' => 'api',
                'version' => env('API_VERSION', 'v1'), 
                'middleware' => ['api', 'throttle:api', 'auth:sanctum'], 
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
            'enabled' => true, 
        ],
        'User' => [
            'enabled' => true, 
        ],
        'Role' => [
            'enabled' => true, 
        ],
        'Permission' => [
            'enabled' => true, 
        ],
    ],
];
