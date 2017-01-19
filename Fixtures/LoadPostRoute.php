<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;

class LoadPostRoute extends AbstractFixture implements OrderedFixtureInterface
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
            'argument' => ['slug' => '([a-z-_]+)'],
            'middleware' => null,
            'subdomain' => null,
        ],
        [
            'url' => '/article/:slug',
            'name' => 'module:post.type:dynamic.action:read',
            'method' => ['GET'],
            'argument' => ['slug' => '([a-z-_]+)'],
            'middleware' => null,
            'subdomain' => null,
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadRoute($manager);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 105;
    }
}