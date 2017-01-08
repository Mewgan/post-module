<?php

namespace Jet\Modules\Post\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Models\Template;
use Jet\Models\Website;

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
        /* Aster template */
        'aster_post_list_partial' => [
            'name' => 'ThemeAsterPostListFilePartial',
            'title' => 'Theme Aster Post List Template',
            'content' => 'Themes/Aster/post_list',
            'website' => 'aster-society',
            'category' => 'partial',
            'scope' => 'specified',
            'type' => 'file'
        ],
        'aster_single_post_partial' => [
            'name' => 'ThemeAsterPostFilePartial',
            'title' => 'Theme Aster Post Template',
            'content' => 'Themes/Aster/post',
            'website' => 'aster-society',
            'category' => 'partial',
            'scope' => 'specified',
            'type' => 'file'
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach($this->data as $key => $data){
            $template = (Template::where('name',$data['name'])->count() == 0)
                ? new Template()
                : Template::findOneByName($data['name']);
            $template->setName($data['name']);
            $template->setTitle($data['title']);
            $template->setContent($data['content']);
            $template->setCategory($data['category']);
            $template->setScope($data['scope']);
            $template->setType($data['type']);
            if(isset($data['website'])){
                $website = Website::findOneByDomain($data['website']);
                if(!is_null($website)) $template->setWebsite($website);
            }
            $this->setReference($key, $template);
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
        return 101;
    }
}