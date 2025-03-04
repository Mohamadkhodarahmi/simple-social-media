<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;
use App\Http\Responses\ApiResponses;
return [

    'responses'=>[
        'defaults'=> [
            200 => ApiResponses::success(['example' => 'data']),
            401 => ApiResponses::unauthenticated(),
            403 => ApiResponses::forbidden(),
            404 => ApiResponses::notFound(),
            422 => ApiResponses::validationError(['field' => ['Error message']]),
        ],
        'examples' => [
            'application/json' => [
                'POST /api/posts' => [
                    201 => ApiResponses::success([
                        'id' => 1,
                        'content' => 'This is my first post!',
                        'user' => ['id' => 1, 'name' => 'Ali'],
                    ], 201),
                    422 => ApiResponses::validationError(['content' => ['The content field is required.']]),
                ],
                'POST /api/comments' => [
                    201 => ApiResponses::success([
                        'id' => 1,
                        'content' => 'Great post!',
                        'user' => ['id' => 1, 'name' => 'Ali'],
                    ], 201),
                ],
            ],
        ],
        ],

    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     * If you need to change this behavior, you can add your custom routes resolver using `Scramble::routes()`.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    'info' => [

        'version' => env('API_VERSION', '0.0.1'),
        'title' => 'Social Network API',
        'description' => 'A simple social network API built with Laravel 11',
    ],

    /*
     * Customize Stoplight Elements UI
     */
    'ui' => [
        /*
         * Define the title of the documentation's website. App name is used when this config is `null`.
         */
        'title' => null,

        /*
         * Define the theme of the documentation. Available options are `light` and `dark`.
         */
        'theme' => 'light',

        /*
         * Hide the `Try It` feature. Enabled by default.
         */
        'hide_try_it' => false,

        /*
         * URL to an image that displays as a small square logo next to the title, above the table of contents.
         */
        'logo' => '',

        /*
         * Use to fetch the credential policy for the Try It feature. Options are: omit, include (default), and same-origin
         */
        'try_it_credentials_policy' => 'include',
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => null,

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    'extensions' => [],
];
