<?php

namespace Jet\Modules\Post\Services;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;

trait LoadPostFixture
{
    /**
     * @param ObjectManager $manager
     */
    public function loadPost(ObjectManager $manager)
    {
        foreach ($this->data as $key => $data) {
            $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);
            $post = (Post::where('slug', $data['slug'])->where('website', $website)->count() == 0)
                ? new Post()
                : Post::findOneBy(['slug' => $data['slug'], 'website' => $website]);
            $post->setTitle($data['title']);
            $post->setSlug($data['slug']);
            $post->setDescription($data['short_description']);
            $post->setContent($data['content']);
            $post->setThumbnail($this->getReference($data['thumbnail']));
            $categories = new ArrayCollection();
            foreach ($data['categories'] as $category)
                $categories[] = ($this->hasReference($category)) ? $this->getReference($category) : PostCategory::findOneBySlug($category);
            $post->setPostCategories($categories);
            $post->setWebsite($website);
            $this->setReference($key, $post);
            $manager->persist($post);
        }
        $manager->flush();
    }

    /**
     * @param $data
     * @param Website $website
     * @return mixed
     */
    public function getPostContent($data, Website $website)
    {
        if(isset($data['data'])){
            if(isset($data['data']['db'])){
                foreach ($data['data']['db'] as $index => $db_item){
                    if($db_item['type'] == 'static'){
                        $new_db_item = [];
                        if(is_array($db_item['value'])) {
                            foreach ($db_item['value'] as $key => $val) {
                                /** @var PostCategory $cat */
                                if($this->hasReference($val))
                                    $cat = $this->getReference($val);
                                else
                                    $cat = ($db_item['alias'] == 'c')
                                        ? PostCategory::findOneBy(['slug' => $val, 'website' => $website]) : Post::findOneBy(['slug' => $val, 'website' => $website]);
                                $new_db_item[] = $cat->getId();
                            }
                        }
                        $data['data']['db'][$index]['value'] = $new_db_item;
                    }
                }
            }
            if(isset($data['data']['link'])){
                foreach ($data['data']['link'] as $key => $db_item){
                    if($db_item['type'] == 'static'){
                        /** @var PostCategory $cat */
                        if($this->hasReference($db_item['value']))
                            $item = $this->getReference($db_item['value']) ;
                        else
                            $item = ($db_item['alias'] == 'c')
                                ? PostCategory::findOneBy(['slug' => $db_item['value'], 'website' => $website]) : Post::findOneBy(['slug' => $db_item['value'], 'website' => $website]);
                        $data['data']['link'][$key]['value_id'] = $item->getId();
                        $data['data']['link'][$key]['value'] = $item[$db_item['column']];
                    }
                }
            }
        }
        return $data;
    }
    
    /**
     * @param $data
     * @return array
     */
    protected function getCustomFieldPost($data)
    {
        $new_content = $data;
        $website = (isset($new_content['website']) && $this->hasReference($new_content['website'])) ? $this->getReference($new_content['website']) : null;
        if (isset($new_content['data']['categories']))
            $new_content['data']['categories'] = $this->getCustomFieldPostCategories($new_content['data']['categories'], $website);
        foreach ($data['content'] as $key => $item) {
            if (is_array($item)) {
                $content = [];
                $this->recursiveSetPost($item, $content, $website);
                $new_content['content'][$key] = $content;
            } else {
                $post = ($this->hasReference($item)) ? $this->getReference($item) : Post::findOneBy(['slug' => $item, 'website' => $website]);
                $new_content['content'][$key] = $post->getId();
            }
        }
        return $new_content;
    }

    /**
     * @param $items
     * @param array $content
     * @param $website
     */
    private function recursiveSetPost($items, &$content = [], $website){
        foreach ($items as $index => $post) {
            if(is_array($post)){
                $content[$index] = [];
                $this->recursiveSetPost($post, $content[$index], $website);
            } else {
                $post = ($this->hasReference($post)) ? $this->getReference($post) : Post::findOneBy(['slug' => $post, 'website' => $website]);
                $content[] = $post->getId();
            }
        }
    }
    /**
     * @param $data
     * @param $website
     * @return array
     */
    protected function getCustomFieldPostCategories($data, $website)
    {
        $new_content = [];
        foreach ($data as $key => $cat) {
            $cat = ($this->hasReference($cat)) ? $this->getReference($cat) : PostCategory::findOneBy(['slug' => $cat, 'website' => $website]);
            $new_content[$key] = $cat->getId();
        }
        return $new_content;
    }

    /**
     * @param $title
     * @param $website
     * @return mixed
     */
    public function getPostTypeId($title, $website){
        $post = $this->hasReference($title) ? $this->getReference($title): Post::findOneBy(['slug' => $title, 'website' => $website]);
        return $post->getId();
    }

    /**
     * @param $title
     * @param $website
     * @return mixed
     */
    public function getPostCategoryTypeId($title, $website){
        $post = $this->hasReference($title) ? $this->getReference($title): PostCategory::findOneBy(['slug' => $title, 'website' => $website]);
        if(is_null($post)) $post = PostCategory::findOneBySlug($title);
        return $post->getId();
    }
}