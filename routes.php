<?php

return [

    '/module/post/*' => [
        'use' => 'AdminPostController@{method}',
        'ajax' => true
    ],

    '/module/post-category/*' => [
        'use' => 'AdminPostCategoryController@{method}',
        'ajax' => true
    ],
    
];