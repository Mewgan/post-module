<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;

class LoadPostCategory extends AbstractFixture implements OrderedFixtureInterface
{
    protected $data = [
        [
            'name' => 'Service',
            'slug' => 'service'
        ],
        [
            'name' => 'Actualité',
            'slug' => 'actualite'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $postCategory = (PostCategory::where('name',$data['name'])->count() == 0)
                ? new PostCategory()
                : PostCategory::findOneByName($data['name']);
            $postCategory->setName($data['name']);
            $postCategory->setSlug($data['slug']);
            $this->setReference($data['slug'], $postCategory);
            $manager->persist($postCategory);
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
        return 104;
    }
}