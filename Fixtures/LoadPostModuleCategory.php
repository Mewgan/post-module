<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;

class LoadPostModuleCategory extends AbstractFixture implements OrderedFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'name' => 'Post',
        'title' => 'Article',
        'slug' => 'post',
        'nav' => true,
        'description' => 'Module pour afficher des articles',
        'icon' => 'fa fa-newspaper-o',
        'author' => 'S.Sumugan',
        'version' => '0.1',
        'update_available' => false,
        'access_level' => 4
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModuleCategory($manager);
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 102;
    }
}