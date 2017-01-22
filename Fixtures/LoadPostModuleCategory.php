<?php
namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;

class LoadPostModuleCategory extends AbstractFixture
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

}