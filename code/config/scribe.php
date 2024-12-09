<?php

use Knuckles\Scribe\Extracting\Strategies;

return [
    'theme' => 'default',

    'title' => 'API Documentation',

    'description' => 'Documentation for the Git Assistant API',

    'base_url' => env('APP_URL'),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
                'versions' => ['v1'],
            ],
            'include' => ['*'],
            'exclude' => [
                // Exclude endpoints that shouldn't be documented
            ],
        ],
    ],

    'type' => 'static',
    'static' => [
        'output_path' => 'public/docs',
    ],

    'auth' => [
        'enabled' => true,
        'default' => true,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{TOKEN}',
        'extra_info' => 'You can retrieve your token by logging in or registering',
    ],

    'examples' => [
        'faker_seed' => null,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            Strategies\Metadata\GetFromDocBlocks::class,
        ],
        'urlParameters' => [
            Strategies\UrlParameters\GetFromUrlParamTag::class,
        ],
        'queryParameters' => [
            Strategies\QueryParameters\GetFromQueryParamTag::class,
        ],
        'headers' => [
            Strategies\Headers\GetFromRouteRules::class,
        ],
        'bodyParameters' => [
            Strategies\BodyParameters\GetFromFormRequest::class,
        ],
        'responses' => [
            Strategies\Responses\UseResponseTag::class,
            Strategies\Responses\UseResponseFileTag::class,
            Strategies\Responses\UseApiResourceTags::class,
            Strategies\Responses\UseTransformerTags::class,
        ],
    ],
    'laravel' => [
        // Whether to automatically create a docs endpoint for you to view your generated docs.
        // If this is false, you can still set up routing manually.
        'add_routes' => true,
        // URL path to use for the docs endpoint (if `add_routes` is true).
        // By default, `/docs` opens the HTML page, `/docs.postman` opens the Postman collection, and `/docs.openapi` the OpenAPI spec.
        'docs_url' => '/docs',
        // Directory within `public` in which to store CSS and JS assets.
        // By default, assets are stored in `public/vendor/scribe`.
        // If set, assets will be stored in `public/{{assets_directory}}`
        'assets_directory' => null,
        // Middleware to attach to the docs endpoint (if `add_routes` is true).
        'middleware' => [],
    ],
    'external' => ['html_attributes' => []],
    'try_it_out' => [
        // Add a Try It Out button to your endpoints so consumers can test endpoints right from their browser.
        // Don't forget to enable CORS headers for your endpoints.
        'enabled' => true,
        // The base URL for the API tester to use (for example, you can set this to your staging URL).
        // Leave as null to use the current app URL when generating (config("app.url")).
        'base_url' => null,
        // [Laravel Sanctum] Fetch a CSRF token before each request, and add it as an X-XSRF-TOKEN header.
        'use_csrf' => false,
        // The URL to fetch the CSRF token from (if `use_csrf` is true).
        'csrf_url' => '/sanctum/csrf-cookie',
    ],
    // Text to place in the "Introduction" section, right after the `description`. Markdown and HTML are supported.
    'intro_text' => <<<'INTRO'
    This documentation aims to provide all the information you need to work with our API.

    <aside>As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
    You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).</aside>
    INTRO,
    // Generate a Postman collection (v2.1.0) in addition to HTML docs.
    // For 'static' docs, the collection will be generated to public/docs/collection.json.
    // For 'laravel' docs, it will be generated to storage/app/scribe/collection.json.
    // Setting `laravel.add_routes` to true (above) will also add a route for the collection.
    'postman' => ['enabled' => true, 'overrides' => []],
    // Generate an OpenAPI spec (v3.0.1) in addition to docs webpage.
    // For 'static' docs, the collection will be generated to public/docs/openapi.yaml.
    // For 'laravel' docs, it will be generated to storage/app/scribe/openapi.yaml.
    // Setting `laravel.add_routes` to true (above) will also add a route for the spec.
    'openapi' => ['enabled' => true, 'overrides' => []],
    // Custom logo path. This will be used as the value of the src attribute for the <img> tag,
    // so make sure it points to an accessible URL or path. Set to false to not use a logo.
    // For example, if your logo is in public/img:
    // - 'logo' => '../img/logo.png' // for `static` type (output folder is public/docs)
    // - 'logo' => 'img/logo.png' // for `laravel` type
    'logo' => false,
    // Customize the "Last updated" value displayed in the docs by specifying tokens and formats.
    // Examples:
    // - {date:F j Y} => March 28, 2022
    // - {git:short} => Short hash of the last Git commit
    // Available tokens are `{date:<format>}` and `{git:<format>}`.
    // The format you pass to `date` will be passed to PHP's `date()` function.
    // The format you pass to `git` can be either "short" or "long".
    'last_updated' => 'Last updated: {date:F j, Y}',
    'fractal' => [
        // If you are using a custom serializer with league/fractal, you can specify it here.
        'serializer' => null,
    ],
    'routeMatcher' => \Knuckles\Scribe\Matching\RouteMatcher::class,

    'groups' => [
        'default' => 'Other Endpoints',
        'order' => [
            'Auth',
            'Profile',
            'GitAssistant',
        ],
    ],
];
