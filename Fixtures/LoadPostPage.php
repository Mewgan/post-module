<?php

namespace Jet\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Page;

class LoadPostPage extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        /* Aster pages */
        '1' =>  [
            'contents' => [
               'aster_welcome_content',
               'aster_list_static_home_post_content'
            ], 
        ],
        '2' =>  [
            'contents' => [
                'aster_list_static_post_content'
            ],
        ],
        '3' =>  [
            'contents' => [
                'aster_list_dynamic_post_content'
            ],
        ],
        '4' =>  [
            'contents' => [
                'aster_single_post_content'
            ],
        ],
        /* Balsamine pages */
        '5' =>  [
            'contents' => [
                'balsamine_welcome_content',
                'balsamine_list_static_home_post_content'
            ],
        ],
        '6' =>  [
            'contents' => [
                'balsamine_list_static_post_content'
            ],
        ],
        '7' =>  [
            'contents' => [
                'balsamine_list_dynamic_post_content'
            ],
        ],
        '8' =>  [
            'contents' => [
                'balsamine_single_post_content'
            ],
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $page = Page::findOneById($key);
            $contents = $page->getContents();
            foreach ($data['contents'] as $content){
                $c = $this->getReference($content);
                if(!$contents->contains($c))
                    $page->addContent($c);
            }
            $manager->persist($page);
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
        return 9;
    }
}