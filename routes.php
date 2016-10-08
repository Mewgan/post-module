<?php

return [
    
    '/module/post/create' => [
        'use' => 'AdminPostController@create',
        'name' => 'module.post.create',
        'method' => 'POST'
    ],

    '/module/post/update' => [
        'use' => 'AdminPostController@update',
        'name' => 'module.post.create',
        'method' => 'POST'
    ],
    
];