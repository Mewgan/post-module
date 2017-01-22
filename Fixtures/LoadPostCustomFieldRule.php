<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\CustomFieldRule;

class LoadCustomFieldRule extends AbstractFixture
{
    protected $data = [
        'post_rule' => [
            'title' => 'Article',
            'name' => 'post',
            'type' => 'single',
            'table' => 'posts',
            'callback' => '/module/post/list-rule-value',
        ],
        'post_category_rule' => [
            'title' => 'Catégorie d\'article',
            'name' => 'post_category',
            'type' => 'single',
            'table' => 'post_categories',
            'callback' => '/module/post-category/list-rule-value',
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data) {
            $cf = (CustomFieldRule::where('name',$data['name'])->count() == 0)
                ? new CustomFieldRule()
                : CustomFieldRule::findOneByName($data['name']);
            $cf->setTitle($data['title']);
            $cf->setName($data['name']);
            $cf->setCallback($data['callback']);
            $cf->setType($data['type']);
            $cf->setReplaceTable($data['table']);
            $this->setReference($key, $cf);
            $manager->persist($cf);
        }
        $manager->flush();
    }
}