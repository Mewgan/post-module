<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Modules\Post\Models\PostCategory;

class LoadPostCategory extends AbstractFixture
{
    protected $data = [
        [
            'name' => 'Service',
            'slug' => 'service'
        ],
        [
            'name' => 'Actualité',
            'slug' => 'actualite'
        ]
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

}