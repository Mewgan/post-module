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
            'short_description' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
            'content' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'actualite'
            ],
            'website' => 'aster-society'
        ],
        [
            'title' => 'Service 1',
            'slug' => 'service-1',
            'short_description' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
            'content' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/en.gif',
            'categories' => [
                'service'
            ],
            'website' => 'aster-society'
        ],
        [
            'title' => 'Service 2',
            'slug' => 'service-2',
            'short_description' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
            'content' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/en.gif',
            'categories' => [
                'service'
            ],
            'website' => 'aster-society'
        ],
        [
            'title' => 'Service 3',
            'slug' => 'service-3',
            'short_description' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
            'content' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'service'
            ],
            'website' => 'aster-society'
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
            'website' => 'balsamine-society'
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
            'website' => 'balsamine-society'
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
            'website' => 'balsamine-society'
        ],
        /* Luffy website posts */
        [
            'title' => 'Luffy Service 1',
            'slug' => 'luffy-service-1',
            'short_description' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.',
            'content' => 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, cupiditate, at! Minus rem aut, culpa aspernatur cumque enim blanditiis sunt!',
            'thumbnail' => '/france.png',
            'categories' => [
                'service'
            ],
            'website' => 'luffy-society'
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
        return 106;
    }
}