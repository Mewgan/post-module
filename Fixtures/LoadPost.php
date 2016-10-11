<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;

class LoadPost extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        /* Aster website posts */
        [
            'title' => 'Bienvenue',
            'slug' => 'bienvenue',
            'short_description' => 'Bienvenue sur le site Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'actualite'
            ],
            'website' => 'http://aster-society.in-salon.dev'
        ],
        [
            'title' => 'Service 1',
            'slug' => 'service-1',
            'short_description' => 'Service 1 de Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/en.gif',
            'categories' => [
                'service'
            ],
            'website' => 'http://aster-society.in-salon.dev'
        ],
        [
            'title' => 'Service 2',
            'slug' => 'service-2',
            'short_description' => 'Service 2 de Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/en.gif',
            'categories' => [
                'service'
            ],
            'website' => 'http://aster-society.in-salon.dev'
        ],
        [
            'title' => 'Service 3',
            'slug' => 'service-3',
            'short_description' => 'Service 3 de Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'service'
            ],
            'website' => 'http://aster-society.in-salon.dev'
        ],
        /* Balsamine website posts */
        [
            'title' => 'Balsamine Service 1',
            'slug' => 'service-1',
            'short_description' => 'Service 1 de Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/en.gif',
            'categories' => [
                'service'
            ],
            'website' => 'http://balsamine-society.in-salon.dev'
        ],
        [
            'title' => 'Balsamine Service 2',
            'slug' => 'service-2',
            'short_description' => 'Service 2 de Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/en.gif',
            'categories' => [
                'service'
            ],
            'website' => 'http://balsamine-society.in-salon.dev'
        ],
        [
            'title' => 'Balsamine Service 3',
            'slug' => 'service-3',
            'short_description' => 'Service 3 de Aster',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'service'
            ],
            'website' => 'http://balsamine-society.in-salon.dev'
        ],
        /* Luffy website posts */
        [
            'title' => 'Luffy Service 1',
            'slug' => 'luffy-service-1',
            'short_description' => 'Service de Luffy',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'service'
            ],
            'website' => 'http://luffy-society.in-salon.dev'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website =  Website::findOneByDomain($data['website']);
            $post = (Post::where('slug',$data['slug'])->where('website',$website)->count() == 0)
                ? new Post()
                : Post::findOneBy(['slug' => $data['slug'], 'website' => $website]);
            $post->setTitle($data['title']);
            $post->setSlug($data['slug']);
            $post->setDescription($data['short_description']);
            $post->setContent($data['content']);
            $post->setThumbnail($this->getReference($data['thumbnail']));
            $categories = new ArrayCollection();
            foreach ($data['categories'] as $category)
                $categories[] = $this->getReference($category);
            $post->setPostCategories($categories);
            $post->setWebsite($website);
            $manager->persist($post);
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
        return 6;
    }
}