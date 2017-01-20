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
     * @param $data
     * @return mixed
     */
    public function getPostContent($data)
    {
        return $data;
    }

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
}