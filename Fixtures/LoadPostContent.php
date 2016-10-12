<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Content;
use Jet\Models\Section;
use Jet\Models\Website;

class LoadPostContent extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        /* Aster website post module content */
        'aster_welcome_content' => [
            'name' => 'Bienvenue',
            'block' => 'home_content',
            'scope' => 'specified',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'data' => [
                'class' => 'col-md-6',
                'db' => [
                    [
                        'table' => 'post',
                        'column' => 'id',
                        'value' => 1
                    ]  
                ],
            ]
        ],
        'aster_list_static_home_post_content' => [
            'name' => 'Liste de services',
            'block' => 'list_post',
            'scope' => 'specified',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => null,
            'data' => [
                'class' => 'col-md-6',
                'route_name' => 'module:post.type:dynamic.action:read',
                'total_row' => 3,
                'db' => [
                    [
                        'column' => 'slug',
                        'value' => 'service',
                    ]
                ],
                'link' => [
                    [
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'aster_list_static_post_content' => [
            'name' => 'Liste de services',
            'block' => 'list_post',
            'scope' => 'specified',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => null,
            'data' => [
                'class' => 'col-md-12',
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'aster_list_dynamic_post_content' => [
            'name' => 'Liste d\'article',
            'block' => 'list_post',
            'scope' => 'specified',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list',
            'section' => 1,
            'data' => [
                'class' => 'col-md-12',
                'db' => [
                    [
                        'table' => 'post_category',
                        'column' => 'id',
                        'route' => 'id',
                    ],
                    [
                        'table' => 'post_category',
                        'column' => 'slug',
                        'route' => 'category',
                    ],
                ],
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'column' => 'slug',
                        'route' => 'slug',
                    ]
                ]
            ]
        ],
        'aster_single_post_content' => [
            'name' => 'Article',
            'block' => 'single_post',
            'scope' => 'specified',
            'website' => 'http://aster-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'data' => [
                'class' => 'col-md-12',
                'db' => [
                    [
                        'table' => 'post',
                        'column' => 'slug',
                        'route' => 'slug',
                    ]
                ]
            ]
        ],
        /* Balsamine website post module content */
        'balsamine_welcome_content' => [
            'name' => 'Bienvenue',
            'block' => 'home_content',
            'scope' => 'specified',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'data' => [
                'class' => 'col-md-6',
                'db' => [
                    [
                        'table' => 'post',
                        'column' => 'id',
                        'value' => 5
                    ]
                ],
            ]
        ],
        'balsamine_list_static_home_post_content' => [
            'name' => 'Liste de services',
            'block' => 'list_post',
            'scope' => 'specified',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list_js',
            'section' => null,
            'data' => [
                'class' => 'col-md-6',
                'route_name' => 'module:post.type:dynamic.action:read',
                'total_row' => 3,
                'db' => [
                    [
                        'column' => 'slug',
                        'value' => 'service',
                    ]
                ],
                'link' => [
                    [
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'balsamine_list_static_post_content' => [
            'name' => 'Liste de services',
            'block' => 'list_post',
            'scope' => 'specified',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list_js',
            'section' => null,
            'data' => [
                'class' => 'col-md-12',
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'column' => 'slug',
                        'route' => 'slug'
                    ]
                ],
            ]
        ],
        'balsamine_list_dynamic_post_content' => [
            'name' => 'Liste d\'article',
            'block' => 'list_post',
            'scope' => 'specified',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_post_list',
            'template' => 'post_basic_list_js',
            'section' => 1,
            'data' => [
                'class' => 'col-md-12',
                'db' => [
                    [
                        'table' => 'post_category',
                        'column' => 'id',
                        'route' => 'id',
                    ],
                    [
                        'table' => 'post_category',
                        'column' => 'slug',
                        'route' => 'category',
                    ],
                ],
                'route_name' => 'module:post.type:dynamic.action:read',
                'link' => [
                    [
                        'column' => 'slug',
                        'route' => 'slug',
                    ]
                ]
            ]
        ],
        'balsamine_single_post_content' => [
            'name' => 'Article',
            'block' => 'single_post',
            'scope' => 'specified',
            'website' => 'http://balsamine-society.in-salon.dev',
            'module' => 'module_single_post',
            'template' => 'post_whole_content',
            'section' => 1,
            'data' => [
                'class' => 'col-md-12',
                'db' => [
                    [
                        'table' => 'post',
                        'column' => 'slug',
                        'route' => 'slug',
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
            $content->setScope($data['scope']);
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