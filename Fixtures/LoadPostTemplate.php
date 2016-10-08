<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\Template;

class LoadPostTemplate extends AbstractFixture implements OrderedFixtureInterface
{

    private $data = [
        /* Post Module Templates */
        'post_whole_content' => [
            'name' => 'ModulePostPartialWholeContent',
            'title' => 'Article en entier',
            'content' => 'post',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
        'post_only_body' => [
            'name' => 'ModulePostPartialOnlyBody',
            'title' => 'Corps uniquement',
            'content' => 'post_body',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
        'post_basic_list' => [
            'name' => 'ModulePostPartialBasicList',
            'title' => 'Liste basique',
            'content' => 'post_basic_list',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
        'post_basic_list_js' => [
            'name' => 'ModulePostPartialBasicListJs',
            'title' => 'Liste basique Js',
            'content' => 'post_basic_list_js',
            'category' => 'partial',
            'scope' => 'global',
            'type' => 'file'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data){
            $template = new Template();
            $template->setName($data['name']);
            $template->setTitle($data['title']);
            $template->setContent($data['content']);
            $template->setCategory($data['category']);
            $template->setScope($data['scope']);
            $template->setType($data['type']);
            $this->addReference($key,$template);
            $manager->persist($template);
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
        return 20;
    }
}