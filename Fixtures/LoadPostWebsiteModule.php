<?php

namespace Jet\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;

class LoadPostWebsiteModule extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        'http://aster-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://balsamine-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://heliotrope-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://pivoine-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://rose-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://luffy-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
            'exclude' => [4],
        ],
        'http://zoro-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://sanji-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://chopper-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'http://robin-society.in-salon.dev' => [
            'modules' => [
                'module_post_list',
                'module_single_post'
            ],
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website = Website::findOneByDomain($key);
            foreach ($data['modules'] as $module) {
                $mod = $this->getReference($module);
                $modules = is_null($website->getModules())?[]:$website->getModules();
                if(!in_array($mod->getId(),$modules)) 
                    $website->addModule($mod->getId());
            }
            if(isset($data['exclude'])){
                $d = $website->getData();
                $d['parent_exclude']['posts'] = (isset($d['parent_exclude']['posts']))
                    ? array_merge($data['exclude'],$d['parent_exclude']['posts'])
                    : $data['exclude'];
                $website->setData($d);
            }
            $manager->persist($website);
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
        return 7;
    }
}