<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Module;


class LoadPostModule extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
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
        foreach($this->data as $key => $data){
            $module = (Module::where('callback',$data['callback'])->count() == 0)
                ? new Module()
                : Module::findOneByCallback($data['callback']);
            $module->setName($data['name']);
            $module->setCallback($data['callback']);
            $module->setDescription($data['description']);
            $module->setCategory($this->getReference($data['category']));
            $module->setAccessLevel($data['access_level']);
            $templates = new ArrayCollection();
            foreach ($data['templates'] as $template)
                $templates[] = $this->getReference($template);
            $module->setTemplates($templates);
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
        return 3;
    }
}