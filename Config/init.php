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
        ]
    ],

    'extensions' => [
        'Jet\Modules\Post\Extensions\PostTwigExtension'
    ]
];