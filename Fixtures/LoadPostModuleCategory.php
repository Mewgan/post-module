<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\ModuleCategory;


class LoadPostModuleCategory extends AbstractFixture implements OrderedFixtureInterface
{
    private $data = [
        'name' => 'Post',
        'title' => 'Article',
        'slug' => 'post',
        'description' => 'Module pour afficher des articles',
        'icon' => 'fa fa-newspaper-o fa-4x',
        'author' => 'S.Sumugan'
    ];

    public function load(ObjectManager $manager)
    {
        $cat = new ModuleCategory();
        $cat->setName($this->data['name']);
        $cat->setTitle($this->data['title']);
        $cat->setSlug($this->data['slug']);
        $cat->setIcon($this->data['icon']);
        $cat->setAuthor($this->data['author']);
        $cat->setDescription($this->data['description']);
        $manager->persist($cat);
        $this->addReference($this->data['slug'], $cat);
        $manager->flush();
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 21;
    }
}