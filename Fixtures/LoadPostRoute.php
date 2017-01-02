<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Route;

class LoadPostRoute extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
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
            'argument' => null,
            'middleware' => null,
            'subdomain' => null,
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $route = (Route::where('name',$data['name'])->count() == 0)
                ? new Route()
                : Route::findOneByName($data['name']);
            $route->setUrl($data['url']);
            $route->setName($data['name']);
            $route->setArgument($data['argument']);
            $route->setMiddleware($data['middleware']);
            $route->setSubdomain($data['subdomain']);
            $this->setReference($data['name'], $route);
            $manager->persist($route);
        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 110;
    }
}