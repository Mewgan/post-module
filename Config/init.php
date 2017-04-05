<?php

return [

    'app' => [
        'blocks' => [
            'PostModule' => [
                'path' => 'src/Modules/Post/',
                'namespace' => '\\Jet\\Modules\\Post',
                'view_dir' => 'src/Modules/Post/Views/',
                'prefix' => 'admin',
            ],
        ],
        'fixtures' => [
            'src/Modules/Post/Fixtures/'
        ],
        'settings' => [
            'custom_field_type' => [
                'content' => [
                    'values' => [
                        ['post', 'Article', 'Post@postCustomField', 'Post@postRenderCustomField'],
                    ]
                ]
            ],
            'custom_field_callback' => [
                'post' => '\\Jet\\Modules\\Post\\Controllers\\FrontPostController@renderField'
            ],
            'publication_type' => [
                'post' => [
                    'id' => 'post',
                    'name' => 'Article',
                    'renderCallback' => '\\Jet\\Modules\\Post\\Controllers\\FrontPostController@listCustomFields'
                ],
            ],
            'navigation' => [
                'post' => [
                    'id' => 'post',
                    'name' => 'Article',
                    'plural' => 'Articles',
                    'all' => '\\Jet\\Modules\\Post\\Controllers\\AdminPostController@listNames',
                    'get_url' => '\\Jet\\Modules\\Post\\Controllers\\AdminPostController@getUrl'
                ],
                'post_category' => [
                    'id' => 'post_category',
                    'name' => 'Catégorie d\'article',
                    'plural' => 'Catégories d\'article',
                    'all' => '\\Jet\\Modules\\Post\\Controllers\\AdminPostCategoryController@listNames',
                    'get_url' => '\\Jet\\Modules\\Post\\Controllers\\AdminPostCategoryController@getUrl'
                ],
            ],
        ],
        'events' => [
            'updatePost' => [
                [
                    'type' => 'async', // linear or async
                    'method' => 'POST',
                    'route' => 'api.navigation.update_url',
                ]
            ],
            'updatePostCategory' => [
                [
                    'type' => 'async', // linear or async
                    'method' => 'POST',
                    'route' => 'api.navigation.update_url',
                ]
            ]
        ]
    ],

    'extensions' => [
        'Jet\Modules\Post\Extensions\PostTwigExtension'
    ]
];