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
        'aster_welcome_content' => [
            'name' => 'Bienvenue',
            'block' => 'home_content',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'page' => '1',
            'data' => [
                'class' => 'col-md-6',
                'type' => 'static',
                'db' => [
                    [
                        'alias' => 'p',
                        'column' => 'id',
                        'value' => '1',
                        'route' => ''
                    ]
                ],
            ]
        ],
        'aster_list_static_home_post_content' => [
            'name' => 'Liste de services statique',
            'block' => 'list_post',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => null,
            'page' => '1',
            'data' => [
                'class' => 'col-md-6',
                'type' => 'static',
                'route_name' => 'module:post.type:dynamic.action:read',
                'total_row' => 3,
                'db' => [
                    [
                        'alias' => 'c',
                        'column' => 'slug',
                        'value' => ['service'],
                        'route' => ''
                    ]
                ],
                'link' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'aster_list_static_post_content' => [
            'name' => 'Liste de services statique',
            'block' => 'list_post',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => null,
            'page' => '2',
            'data' => [
                'class' => 'col-md-12',
                'type' => 'static',
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'aster_list_dynamic_post_content' => [
            'name' => 'Liste d\'article',
            'block' => 'list_post',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => 1,
            'page' => '3',
            'data' => [
                'class' => 'col-md-12',
                'type' => 'dynamic',
                'route_name' => 'module:post.type:dynamic.action:read',
                'db' => [
                    [
                        'alias' => 'c',
                        'column' => 'id',
                        'route' => 'id',
                        'value' => []
                    ],
                    [
                        'alias' => 'c',
                        'column' => 'slug',
                        'route' => 'category',
                        'value' => []
                    ],
                ],
                'link' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug',
                    ]
                ]
            ]
        ],
        'aster_single_post_content' => [
            'name' => 'Article',
            'block' => 'single_post',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'page' => '4',
            'data' => [
                'class' => 'col-md-12',
                'type' => 'dynamic',
                'db' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug',
                        'value' => ''
                    ]
                ]
            ]
        ],
        /* Balsamine website post module content */
        'balsamine_welcome_content' => [
            'name' => 'Bienvenue',
            'block' => 'home_content',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'page' => '5',
            'data' => [
                'class' => 'col-md-6',
                'type' => 'static',
                'db' => [
                    [
                        'alias' => 'p',
                        'column' => 'id',
                        'value' => '5',
                        'route' => ''
                    ]
                ],
            ]
        ],
        'balsamine_list_static_home_post_content' => [
            'name' => 'Liste de services statique',
            'block' => 'list_post',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list_js',
            'section' => null,
            'page' => '5',
            'data' => [
                'class' => 'col-md-6',
                'type' => 'static',
                'route_name' => 'module:post.type:dynamic.action:read',
                'total_row' => 3,
                'db' => [
                    [
                        'alias' => 'c',
                        'column' => 'slug',
                        'value' => ['service'],
                        'route' => ''
                    ]
                ],
                'link' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'balsamine_list_static_post_content' => [
            'name' => 'Liste de services statique',
            'block' => 'list_post',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list_js',
            'section' => null,
            'page' => '6',
            'data' => [
                'class' => 'col-md-12',
                'type' => 'static',
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'balsamine_list_dynamic_post_content' => [
            'name' => 'Liste d\'article',
            'block' => 'list_post',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list_js',
            'section' => 1,
            'page' => '7',
            'data' => [
                'class' => 'col-md-12',
                'type' => 'dynamic',
                'db' => [
                    [
                        'alias' => 'c',
                        'column' => 'id',
                        'route' => 'id',
                        'value' => []
                    ],
                    [
                        'alias' => 'c',
                        'column' => 'slug',
                        'route' => 'category',
                        'value' => []
                    ],
                ],
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug',
                    ]
                ]
            ]
        ],
        'balsamine_single_post_content' => [
            'name' => 'Article',
            'block' => 'single_post',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'page' => '8',
            'data' => [
                'class' => 'col-md-12',
                'type' => 'dynamic',
                'db' => [
                    [
                        'alias' => 'p',
                        'column' => 'slug',
                        'route' => 'slug',
                        'value' => []
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
            $this->addReference($key, $content);
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
        return 8;
    }
}