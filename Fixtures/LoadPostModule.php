<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Module;


class LoadPostModule extends AbstractFixture implements OrderedFixtureInterface
{
    private $modules = [
        'module_single_post' => [
            'name' => 'Article',
            'callback' => 'Jet\Modules\Post\Controllers\FrontPostController@read',
            'description' => 'Affiche un seul article',
            'category' => 'post',
            'access_level' => 2,
            'templates' => [
                'post_whole_content',
                'post_only_body'
            ]
        ],
        'module_post_list' => [
            'name' => 'Liste d\'articles',
            'callback' => 'Jet\Modules\Post\Controllers\FrontPostController@all',
            'description' => 'Liste d\'articles par catégorie',
            'category' => 'post',
            'access_level' => 2,
            'templates' => [
                'post_basic_list'
            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->modules as $key => $data){
            $module = new Module();
            $module->setName($data['name']);
            $module->setCallback($data['callback']);
            $module->setDescription($data['description']);
            $module->setCategory($this->getReference($data['category']));
            $module->setAccessLevel($data['access_level']);
            foreach ($data['templates'] as $template)
                $module->addTemplate($this->getReference($template));
            $this->addReference($key, $module);
            $manager->persist($module);
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
        return 22;
    }
}