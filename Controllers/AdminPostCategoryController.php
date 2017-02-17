<?php

namespace Jet\Modules\Post\Controllers;

use Cocur\Slugify\Slugify;
use Jet\Modules\Post\Models\Post;
use Jet\Services\Auth;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Website;
use Jet\Modules\Post\Models\PostCategory;
use JetFire\Framework\Providers\EventProvider;
use JetFire\Framework\System\Request;

/**
 * Class AdminPostCategoryController
 * @package Jet\Modules\Post\Controllers
 */
class AdminPostCategoryController extends AdminController
{

    /**
     * @param Request $request
     * @param $website
     * @return array
     */
    public function all(Request $request, $website)
    {
        $max = ($request->exists('max')) ? (int)$request->query('max') : 10;
        $page = ($request->exists('page')) ? (int)$request->query('page') : 1;

        $website = Website::queryBuilder()->select('w')->from('Jet\Models\Website', 'w')->where('w.id = :id')->setParameter('id', $website)->getQuery()->getOneOrNullResult();
        $this->websites[] = $website;
        $this->getThemeWebsites($website);

        $params = [
            'websites' => $this->websites,
            'website_options' => $website->getData(),
            'search' => ($request->has('params') && isset($request->query('params')['search'])) ? $request->query('params')['search'] : '',
            'order' => ($request->has('params') && isset($request->query('params')['order'])) ? $request->query('params')['order'] : [],
            'filter' => ($request->has('params') && isset($request->query('params')['filter'])) ? $request->query('params')['filter'] : [],
        ];

        $response = PostCategory::repo()->listAll($page, $max, $params);
        $pages_count = ceil($response['total'] / $max);

        $themes = [
            'current_page' => $page,
            'count_pages' => $pages_count,
            'count_all' => $response['total'],
            'data' => $response['data']
        ];
        return ['status' => 'success', 'content' => $themes];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $website
     * @return array
     */
    public function create(Request $request, Auth $auth, Slugify $slugify, $website)
    {
        if ($request->method() == 'POST') {

            if(!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour mettre à jour une catégorie'];

            $category = $request->request->get('name');
            if (PostCategory::where('name', $category)->where('website', $website)->count() == 0) {
                if (PostCategory::create(['name' => $category, 'slug' => $slugify->slugify($category), 'website' => Website::findOneById($website)]))
                    return ['status' => 'success', 'message' => 'La catégorie a bien été créée'];
                return ['status' => 'error', 'message' => 'La catégorie n\'a pas été créée'];
            }
            return ['status' => 'error', 'message' => 'La catégorie existe déjà'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param EventProvider $event
     * @param $id
     * @param $website
     * @return array
     */
    public function update(Request $request, Auth $auth, Slugify $slugify, EventProvider $event, $id, $website)
    {
        if ($request->method() == 'PUT') {

            if(!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour mettre à jour une catégorie'];

            $name = $request->get('name');
            $replace = false;
            if (PostCategory::where('name', $name)->where('website', $website)->count() > 0) {
                return ['status' => 'error', 'message' => 'La catégorie existe déjà'];
            } else {

                /** @var PostCategory $category */
                $category = PostCategory::findOneById($id);
                if (is_null($category)) return ['status' => 'error', 'message' => 'Impossible de trouver la catégorie'];

                $old_category = $category;

                /** @var Website $website */
                $website = Website::findOneById($website);
                if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

                if (is_null($category->getWebsite()) || $category->getWebsite()->getId() != $website) {
                    $data = $this->excludeData($website->getData(), 'post_categories', $category->getId());
                    $website->setData($data);
                    Website::watch($website);

                    $category = new PostCategory();
                    $replace = true;
                }

                $category->setName($name);
                $category->setSlug($slugify->slugify($name));
                $category->setWebsite($website);
            }

            if(PostCategory::watchAndSave($category)){
                if($replace){
                    $event->emit('updatePostCategory', ['old_post_category' => $old_category->getId(), 'post_category' => $category->getId(), 'website' => $website->getId()]);
                    $this->createPosts($old_category, $category, $website);
                    $website = $category->getWebsite();
                    $data = $this->replaceData($website->getData(), 'post_categories', $id, $category->getId());
                    $website->setData($data);
                    Website::watchAndSave($website);
                }else
                    $event->emit('updatePostCategory', ['post_category' => $category->getId(), 'website' => $website->getId()]);
                return ['status' => 'success', 'message' => 'La catégorie a bien été mis à jour'];
            }else
                return ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param PostCategory $old_category
     * @param PostCategory $category
     * @param Website $website
     * @param EventProvider $event
     */
    private function createPosts(PostCategory $old_category, PostCategory $category, Website $website, EventProvider $event){
        $data = $website->getData();
        $this->getWebsite($website);
        $posts = $old_category->getPosts();
        foreach ($posts as $post){
            if(in_array($post->getWebsite()->getId(), $this->websites)) {
                /** @var Post $post */
                if ($post->getWebsite() != $website) {
                    /** @var Post $new_post */
                    $new_post = new Post;
                    $new_post->setTitle($post->getTitle());
                    $new_post->setSlug($post->getSlug());
                    $new_post->setThumbnail($post->getThumbnail());
                    $new_post->setDescription($post->getDescription());
                    $new_post->setContent($post->getContent());
                    $new_post->removePostCategory($old_category);
                    $new_post->addPostCategory($category);
                    $new_post->setWebsite($website);
                    if (Post::watchAndSave($new_post)) $event->emit('updatePost', ['old_post' => $post->getId(),'post' => $new_post->getId(), 'website' => $website->getId()]);

                    $data = $this->excludeData($data, 'posts', $post->getId());
                    $data = $this->replaceData($data, 'posts', $post->getId(), $new_post->getId());
                } else {
                    $post->removePostCategory($old_category);
                    $post->addPostCategory($category);
                    Post::watch($post);
                    $event->emit('updatePost', ['post' => $post->getId(), 'website' => $website->getId()]);
                }
            }
        }
        $website->setData($data);
        Website::watch($website);
    }


    /**
     * @param Request $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(Request $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
            
            if(!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];

            $data = $website->getData();
           
            $categories = PostCategory::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($categories as $category) {
                $data = $this->removeData($data, 'post_categories', $category['id']);
                if ($category['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'post_categories', $category['id']);
                } else
                    $ids[] = $category['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (PostCategory::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les catégories ont bien été supprimées']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les catégories n\'ont pas pu être supprimées'];
    }

    /**
     * @param $website
     * @return array
     */
    public function listRuleValue($website)
    {
        if (!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return PostCategory::repo()->getPostCategoryRules($this->websites, $this->website->getData());
    }

    /**
     * @param $website
     * @return array
     */
    public function listNames($website)
    {
        if (!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => PostCategory::repo()->getPostCategoryRules($this->websites, $this->website->getData())];
    }

    /**
     * @param $url
     * @param $id
     * @return array|mixed
     */
    public function getUrl($url, $id)
    {
        $cat = PostCategory::find($id);
        if (is_null($cat)) return ['status' => 'error', 'message' => 'Impossible de trouver la catégorie'];
        $replaces = ['id', 'slug'];
        foreach ($replaces as $replace)
            $url = str_replace(':' . $replace, $cat[$replace], $url);
        return $url;
    }
}