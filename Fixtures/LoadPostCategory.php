<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Modules\Post\Models\PostCategory;

class LoadPostCategory extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        [
            'name' => 'Service',
            'slug' => 'service',
            'website' => 'Aster Website',
        ],
        [
            'name' => 'Actualité',
            'slug' => 'actualite',
            'website' => 'Aster Website',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $postCategory = new PostCategory();
            $postCategory->setName($data['name']);
            $postCategory->setSlug($data['slug']);
            $postCategory->setWebsite($this->getReference($data['website']));
            $this->addReference($data['slug'], $postCategory);
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
        return 24;
    }
}