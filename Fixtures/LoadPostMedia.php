<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Media;
use Jet\Models\Website;

class LoadPostMedia extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        /* Aster theme media */
        [
            'title' => 'Article 1',
            'path' => '/article-1.jpg',
            'type' => 'image/jpg',
            'size' => 10.86,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Article 1'
        ],
        [
            'title' => 'Article 2',
            'path' => '/article-2.jpg',
            'type' => 'image/jpg',
            'size' => 2577,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Article 2'
        ],
        [
            'title' => 'Article 3',
            'path' => '/article-3.jpg',
            'type' => 'image/jpg',
            'size' => 2577,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Article 3'
        ],
        [
            'title' => 'Article 4',
            'path' => '/article-4.jpg',
            'type' => 'image/jpg',
            'size' => 2577,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Article 4'
        ],
        [
            'title' => 'Article 5',
            'path' => '/article-5.jpg',
            'type' => 'image/jpg',
            'size' => 2577,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Article 5'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $data){
            $media = (Media::where('path',$data['path'])->count() == 0)
                ? new Media()
                : Media::findOneByPath($data['path']);
            $media->setTitle($data['title']);
            $media->setPath($data['path']);
            $media->setType($data['type']);
            $media->setSize($data['size']);
            if(isset($data['website']))$media->setWebsite(Website::findOneByDomain($data['website']));
            $media->setAccessLevel($data['access_level']);
            $media->setAlt($data['alt']);
            $manager->persist($media);
            $this->addReference($data['path'], $media);
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