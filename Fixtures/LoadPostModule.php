<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;


class LoadPostModule extends AbstractFixture implements DependentFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'module_single_post' => [
            'name' => 'Article',
            'slug' => 'single-post',
            'callback' => 'Jet\Modules\Post\Controllers\FrontPostController@read',
            'description' => 'Affiche un seul article',
            'category' => 'post',
            'access_level' => 4
        ],
        'module_post_list' => [
            'name' => 'Liste d\'articles',
            'slug' => 'list-post',
            'callback' => 'Jet\Modules\Post\Controllers\FrontPostController@all',
            'description' => 'Liste d\'articles par catégorie',
            'category' => 'post',
            'access_level' => 4
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModule($manager);
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            'Jet\Modules\Post\Fixtures\LoadPostModuleCategory'
        ];
    }
}