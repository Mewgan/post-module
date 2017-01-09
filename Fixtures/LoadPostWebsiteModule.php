<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;

class LoadPostWebsiteModule extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        'aster-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
            'medias' => [
                '/sites/1/master.jpg',
            ]
        ],
        'balsamine-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'heliotrope-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'pivoine-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'rose-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'luffy-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
            'exclude' => [4],
        ],
        'zoro-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'sanji-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'chopper-society' => [
            'modules' => [
                'module_post_list',
                'module_single_post',
            ],
        ],
        'robin-society' => [
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
            if(isset($data['medias'])) {
                foreach ($data['medias'] as $media) {
                    $md = $this->getReference($media);
                    $md->setWebsite($website);
                    $manager->persist($md);
                }
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
        return 107;
    }
}