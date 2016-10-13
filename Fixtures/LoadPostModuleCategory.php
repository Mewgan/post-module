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
        'icon' => 'fa fa-newspaper-o',
        'author' => 'S.Sumugan',
        'version' => '0.1',
        'update_available' => false
    ];

    public function load(ObjectManager $manager)
    {
        $cat = (ModuleCategory::where('name',$this->data['name'])->count() == 0)
            ? new ModuleCategory()
            : ModuleCategory::findOneByName($this->data['name']);
        $cat->setName($this->data['name']);
        $cat->setTitle($this->data['title']);
        $cat->setSlug($this->data['slug']);
        $cat->setIcon($this->data['icon']);
        $cat->setAuthor($this->data['author']);
        $cat->setVersion($this->data['version']);
        $cat->setUpdateAvailable($this->data['update_available']);
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
        return 2;
    }
}