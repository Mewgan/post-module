<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Services\LoadFixture;

class LoadPostTemplate extends AbstractFixture
{
    use LoadFixture;

    protected $data = [
        /* Post Module Templates */
        'post_whole_content' => [
            'name' => 'ModulePostPartialWholeContent',
            'title' => 'Article en entier',
            'content' => 'post',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
        'post_only_body' => [
            'name' => 'ModulePostPartialOnlyBody',
            'title' => 'Corps uniquement',
            'content' => 'post_body',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
        'post_basic_list' => [
            'name' => 'ModulePostPartialBasicList',
            'title' => 'Liste basique',
            'content' => 'post_basic_list',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadTemplate($manager);
    }
    
}