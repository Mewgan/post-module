<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;


class LoadPostModule extends AbstractFixture implements OrderedFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'module_single_post' => [
            'name' => 'Article',
            'slug' => 'single-post',
            'callback' => 'Jet\Modules\Post\Controllers\FrontPostController@read',
            'description' => 'Affiche un seul article',
            'category' => 'post',
            'access_level' => 4,
            'templates' => [
                'post_whole_content',
                'post_only_body'
            ]
        ],
        'module_post_list' => [
            'name' => 'Liste d\'articles',
            'slug' => 'list-post',
            'callback' => 'Jet\Modules\Post\Controllers\FrontPostController@all',
            'description' => 'Liste d\'articles par catégorie',
            'category' => 'post',
            'access_level' => 4,
            'templates' => [
                'post_basic_list'
            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModule($manager);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 103;
    }
}