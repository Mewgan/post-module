<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;

class LoadPostRoute extends AbstractFixture
{
    use LoadFixture;

    protected $data = [
        /* Post Module */
        [
            'url' => '/articles',
            'name' => 'module:post.type:static.action:all',
            'method' => ['GET'],
            'argument' => null,
            'middleware' => null,
            'subdomain' => null,
        ],
        [
            'url' => '/articles/:slug',
            'name' => 'module:post.type:dynamic.action:all',
            'method' => ['GET'],
            'argument' => ['slug' => '([a-zA-Z0-9-_]+)'],
            'middleware' => null,
            'subdomain' => null,
        ],
        [
            'url' => '/article/:slug',
            'name' => 'module:post.type:dynamic.action:read',
            'method' => ['GET'],
            'argument' => ['slug' => '([a-zA-Z0-9-_]+)'],
            'middleware' => null,
            'subdomain' => null,
        ],
        [
            'url' => '/services',
            'name' => 'module:post.type:static.action:list.name:service',
            'method' => ['GET'],
            'argument' => ['slug' => '([a-zA-Z0-9-_]+)'],
            'middleware' => null,
            'subdomain' => null,
        ],
        [
            'url' => '/actualites',
            'name' => 'module:post.type:static.action:list.name:actualite',
            'method' => ['GET'],
            'argument' => ['slug' => '([a-zA-Z0-9-_]+)'],
            'middleware' => null,
            'subdomain' => null,
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadRoute($manager);
    }

}