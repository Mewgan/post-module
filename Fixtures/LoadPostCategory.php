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
    private $data = [
        [
            'name' => 'Service',
            'slug' => 'service',
            'website' => 'aster-society',
        ],
        [
            'name' => 'Actualité',
            'slug' => 'actualite',
            'website' => 'aster-society',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $website = Website::findOneByDomain($data['website']);
            $postCategory = (PostCategory::where('name',$data['name'])->where('website',$website)->count() == 0)
                ? new PostCategory()
                : PostCategory::findOneBy(['name' => $data['name'], 'website' => $website]);
            $postCategory->setName($data['name']);
            $postCategory->setSlug($data['slug']);
            $postCategory->setWebsite($website);
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
        return 5;
    }
}