<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Content;
use Jet\Models\Page;
use Jet\Models\Section;
use Jet\Models\Website;

class LoadPostContent extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        /* Aster website post module content */
        'aster_list_home_post_content' => [
            'name' => 'Articles',
            'block' => 'list_home_post',
            'website' => 'aster-society',
            'module' => 'module_post_list',
            'template' => 'aster_post_list_partial',
            'section' => null,
            'page' => '1',
            'data' => [
                'class' => '',
                'route_name' => 'module:post.type:dynamic.action:read',
                'total_row' => 100,
                'db' => [],
                'link' => [
                    [
                        'alias' => 'p',
                        'type' => 'dynamic',
                        'route' => 'slug',
                        'column' => 'slug',
                        'value' => '',
                        'value_id' => ''
                    ]
                ],
            ]
        ],
        'aster_single_post_content' => [
            'name' => 'Article',
            'block' => 'single_post',
            'website' => 'aster-society',
            'module' => 'module_single_post',
            'template' => 'aster_single_post_partial',
            'section' => null,
            'page' => '2',
            'data' => [
                'class' => '',
                'db' => [
                    [
                        'alias' => 'p',
                        'type' => 'dynamic',
                        'column' => 'slug',
                        'route' => 'slug',
                        'value' => '',
                        'value_id' => ''
                    ]
                ]
            ]
        ],
        /* Balsamine website post module content */
        'balsamine_welcome_content' => [
            'name' => 'Bienvenue',
            'block' => 'home_content',
            'website' => 'balsamine-society',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'page' => '5',
            'data' => [
                'class' => 'col-md-6',
                'db' => [
                    [
                        'alias' => 'p',
                        'type' => 'static',
                        'column' => 'id',
                        'value' => '5',
                        'route' => '',
                        'value_id' => ''
                    ]
                ],
            ]
        ],
        'balsamine_list_static_home_post_content' => [
            'name' => 'Services',
            'block' => 'list_home_post',
            'website' => 'balsamine-society',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => null,
            'page' => '5',
            'data' => [
                'class' => 'col-md-6',
                'route_name' => 'module:post.type:dynamic.action:read',
                'total_row' => 3,
                'db' => [
                    [
                        'alias' => 'c',
                        'type' => 'static',
                        'column' => 'slug',
                        'value' => ['service'],
                        'route' => '',
                        'value_id' => []
                    ]
                ],
                'link' => [
                    [
                        'alias' => 'p',
                        'type' => 'dynamic',
                        'route' => 'slug',
                        'column' => 'slug',
                        'value' => '',
                        'value_id' => ''
                    ]
                ],
            ]
        ],
        'balsamine_list_static_post_content' => [
            'name' => 'Articles statique',
            'block' => 'list_post',
            'website' => 'balsamine-society',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => null,
            'page' => '6',
            'data' => [
                'class' => 'col-md-12',
                'route_name' => 'module:post.type:dynamic.action:read',
                'db' => [],
                'link' => [
                    [
                        'alias' => 'p',
                        'type' => 'static',
                        'route' => 'slug',
                        'column' => 'slug',
                        'value' => '',
                        'value_id' => ''
                    ]
                ],
            ]
        ],
        'balsamine_list_dynamic_post_content' => [
            'name' => 'Articles',
            'block' => 'list_post',
            'website' => 'balsamine-society',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => 1,
            'page' => '7',
            'data' => [
                'class' => 'col-md-12',
                'db' => [
                   /* [
                        'alias' => 'c',
                        'type' => 'dynamic',
                        'column' => 'id',
                        'route' => 'id',
                        'value' => [],
                        'value_id' => []
                    ],*/
                    [
                        'alias' => 'c',
                        'type' => 'dynamic',
                        'column' => 'slug',
                        'route' => 'slug',
                        'value' => [],
                        'value_id' => []
                    ],
                ],
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'alias' => 'p',
                        'type' => 'dynamic',
                        'route' => 'slug',
                        'column' => 'slug',
                        'value' => '',
                        'value_id' => ''
                    ]
                ]
            ]
        ],
        'balsamine_single_post_content' => [
            'name' => 'Article',
            'block' => 'single_post',
            'website' => 'balsamine-society',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'page' => '8',
            'data' => [
                'class' => 'col-md-12',
                'db' => [
                    [
                        'alias' => 'p',
                        'type' => 'dynamic',
                        'column' => 'slug',
                        'route' => 'slug',
                        'value' => [],
                        'value_id' => []
                    ]
                ]
            ]
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website = Website::findOneByDomain($data['website']);
            $content = (Content::where('website',$website)->where('block',$data['block'])->where('name',$data['name'])->count() == 0)
                ?  new Content()
                : Content::findOneBy(['website' => $website, 'block' => $data['block'], 'name' => $data['name']]);
            $content->setName($data['name']);
            $content->setBlock($data['block']);
            if(!is_null($data['page']))$content->setPage(Page::findOneById($data['page']));
            $content->setWebsite($website);
            $content->setModule($this->getReference($data['module']));
            $content->setTemplate($this->getReference($data['template']));
            if (!is_null($data['section']))
                $content->setSection(Section::findOneById($data['section']));
            $content->setData($data['data']);
            $this->setReference($key, $content);
            $manager->persist($content);
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
        return 108;
    }
}