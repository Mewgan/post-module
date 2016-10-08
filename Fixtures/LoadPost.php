<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
            'website' => 'Aster Website'
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
            'website' => 'Aster Website'
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
            'website' => 'Aster Website'
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
            'website' => 'Aster Website'
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
            'website' => 'Balsamine Website'
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
            'website' => 'Balsamine Website'
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
            'website' => 'Balsamine Website'
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
            'website' => 'Luffy Website'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $post = new Post();
            $post->setTitle($data['title']);
            $post->setSlug($data['slug']);
            $post->setDescription($data['short_description']);
            $post->setContent($data['content']);
            $post->setThumbnail($this->getReference($data['thumbnail']));
            foreach ($data['categories'] as $category)
                $post->addPostCategory($this->getReference($category));
            $post->setWebsite($this->getReference($data['website']));
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
        return 25;
    }
}