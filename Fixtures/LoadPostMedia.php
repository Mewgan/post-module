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
        [
            'title' => 'Drapeau de la France',
            'path' => '/france.png',
            'type' => 'image/png',
            'size' => 522,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Drapeau de la France'
        ],
        [
            'title' => 'Drapeau de l\'Angleterre',
            'path' => '/en.gif',
            'type' => 'image/gif',
            'size' => 2577,
            'access_level' => 4,
            'scope' => 'global',
            'alt' => 'Drapeau de l\'Angleterre'
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
        return 4;
    }
}