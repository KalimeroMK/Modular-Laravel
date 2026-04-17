<?php

declare(strict_types=1);

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'L5 Swagger UI',
            ],

            'routes' => [

                'api' => 'api/documentation',
            ],
            'paths' => [

                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                'docs_json' => 'api-docs.json',

                'docs_yaml' => 'api-docs.yaml',

                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                'annotations' => [
                    base_path('app'),
                    base_path('app/Modules'),
                ],
            ],
        ],
    ],
    'defaults' => [
        'routes' => [

            'docs' => 'docs',

            'oauth2_callback' => 'api/oauth2-callback',

            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            'group_options' => [],
        ],

        'paths' => [

            'docs' => storage_path('api-docs'),

            'views' => base_path('resources/views/vendor/l5-swagger'),

            'base' => env('L5_SWAGGER_BASE_PATH'),

            'excludes' => [],
        ],

        'scanOptions' => [

            'default_processors_configuration' => [

            ],

            'analyser' => new OpenApi\Analysers\ReflectionAnalyser([
                new OpenApi\Analysers\DocBlockAnnotationFactory(),
            ]),

            'analysis' => null,

            'processors' => [

            ],

            'pattern' => null,

            'exclude' => [],

            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        'securityDefinitions' => [
            'securitySchemes' => [

            ],
            'security' => [

                [

                ],
            ],
        ],

        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        'proxy' => false,

        'additional_config_url' => null,

        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT'),

        'validator_url' => null,

        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),

                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),

                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [

                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),

                'oauth2' => [

                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://my-default-host.com'),
        ],
    ],
];
