<?php

namespace Jet\Modules\Post\Controllers;

use Cocur\Slugify\Slugify;
use Jet\Models\Media;
use Jet\Modules\Post\Requests\PostRequest;
use Jet\Services\Auth;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Account;
use Jet\Models\Route;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;

/**
 * Class AdminPostController
 * @package Jet\Modules\Post\Controllers
 */
class AdminPostController extends AdminController
{

    /**
     * @param PostRequest $request
     * @param $website
     * @return array
     */
    public function all(PostRequest $request, $website)
    {
        $max = ($request->exists('max')) ? (int)$request->query('max') : 10;
        $page = ($request->exists('page')) ? (int)$request->query('page') : 1;

        if(!$this->getWebsite($website)) return ['status' => 'error', 'Site non trouvé'];

        $params = [
            'websites' => $this->websites,
            'website_options' => $this->website->getData(),
            'search' => ($request->has('params') && isset($request->query('params')['search'])) ? $request->query('params')['search'] : '',
            'order' => ($request->has('params') && isset($request->query('params')['order'])) ? $request->query('params')['order'] : [],
            'filter' => ($request->has('params') && isset($request->query('params')['filter'])) ? $request->query('params')['filter'] : [],
        ];

        $response = Post::repo()->listAll($page, $max, $params);
        $post_count = ceil($response['total'] / $max);

        $posts = [
            'current_page' => $page,
            'count_pages' => $post_count,
            'count_all' => $response['total'],
            'data' => $response['data']
        ];
        return ['status' => 'success', 'content' => $posts];
    }

    /**
     * @param Auth $auth
     * @param $website
     * @param $id
     * @return array
     */
    public function read(Auth $auth, $website, $id)
    {

        if(!$this->getWebsite($website)) return ['status' => 'error', 'Site non trouvé'];

        if(!$this->isWebsiteOwner($auth, $website))
            return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];

        $route = Route::repo()->getRouteByName('module:post.type:dynamic.action:read', $this->websites, $this->website->getData());
        $post = Post::repo()->readAdmin($id);

        if (!is_null($post))
            return ['status' => 'success', 'resource' => $post, 'route' => is_null($route) ? '' : $route];
        return ['status' => 'error', 'message' => 'Article inexistant'];
    }

    /**
     * @param PostRequest $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $website
     * @param $id
     * @return array|bool
     */
    public function updateOrCreate(PostRequest $request, Auth $auth, Slugify $slugify, $website, $id)
    {
        if ($request->method() == 'PUT' || $request->method() == 'POST') {
            $response = $request->validate();
            $replace = false;
            if ($response === true) {
                $post = ($id == 'create') ? new Post() : Post::findOneById($id);
                if (!is_null($post)) {

                    if($this->getWebsite($website) == false)
                        return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
                  /*  $website = Website::findOneById($website);
                    if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web']; */

                    if(!$this->isWebsiteOwner($auth, $website))
                        return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour mettre à jour l\'article'];

                    $value = $request->getPost();
                    $slug = ($value->has('slug') && !empty($value->get('slug')) && $auth->get('status')->level < 4)
                        ? $value->get('slug') : $slugify->slugify($value->get('title'));

                    /* Check for douplon */
                    $countPost = Post::repo()->countBySlug($slug, ['websites' => $this->websites, 'website_options' => $this->website->getData()]);
                    if($id == 'create' && $countPost > 0 || $id != 'create' && $countPost > 1)
                        return ['status' => 'error', 'message' => 'Un article existe déjà avec ce titre'];

                    if ($post->getWebsite()->getId() != $website && $id != 'create') {
                        $data = $this->excludeData($this->website->getData(), 'posts', $post->getId());
                        $this->website->setData($data);
                        Website::watch($this->website);
                        $post = new Post();
                        $replace = true;
                    }

                    $post->setWebsite($this->website);
                    $post->setTitle($value->get('title'));
                    $post->setSlug($slug);
                    $post->setDescription($value->get('description'));
                    $post->setContent($value->get('content'));

                    ($value->has('published') && ($value->get('published') == 'true' || $value->get('published') == 1))
                        ? $post->setPublished(1) : $post->setPublished(0);

                    if ($value->has('thumbnail')) {
                        if (isset($value->get('thumbnail')['id']) && !empty($value->get('thumbnail')['id'])) {
                            $thumbnail = Media::findOneById($value->get('thumbnail')['id']);
                            if (!is_null($thumbnail)) $post->setThumbnail($thumbnail);
                        }
                    }

                    if ($value->has('new_categories') && !empty($value->get('new_categories'))) {
                        $categories = PostCategory::findBy(['id' => $value->get('new_categories')]);
                        if (!is_null($categories)) $post->setPostCategories($categories);
                    }

                    if (Post::watchAndSave($post)){
                        $this->app->emit('updatePost', ['old_post' => $id, 'post' => $post->getId(), 'website' => $this->website->getId()]);
                        if($replace && $id != 'create'){
                            $website = $post->getWebsite();
                            $data = $this->replaceData($website->getData(), 'posts', $id, $post->getId());
                            $website->setData($data);
                            Website::watchAndSave($website);
                        }
                        return ['status' => 'success', 'message' => 'L\'article a bien été mis à jour', 'resource' => $post];
                    }
                    return ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
                }
                return ['status' => 'error', 'message' => 'Article non trouvé'];
            }
            return $response;
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param PostRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(PostRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {

            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
            
            if(!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];

            $data = $website->getData();

            $posts = Post::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($posts as $post) {
                $data = $this->removeData($data, 'posts', $post['id']);
                if ($post['website']['id'] != $website->getId())
                    $data = $this->excludeData($data, 'posts', $post['id']);
                else
                    $ids[] = $post['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (Post::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les articles ont bien été supprimées']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les articles n\'ont pas pu être supprimées'];
    }

    /**
     * @param PostRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function changeState(PostRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'PUT' && $request->exists(['ids', 'state'])) {
            $website = Website::findOneById($website);
            $state = $request->get('state');

            if(is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site'];

            if(!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];

            $data = $website->getData();
            foreach ($request->get('ids') as $id) {
                $post = Post::findOneById($id);
                if(is_null($post)) return ['status' => 'error', 'message' => 'Impossible de trouver l\'article aves l\'identifiant : '. $id];
                if (is_null($post->getWebsite()) || $post->getWebsite() != $website) {
                    $post = new Post();
                    $post->setTitle($post->getTitle());
                    $post->setSlug($post->getSlug());
                    $post->setDescription($post->getDescription());
                    $post->setContent($post->getContent());
                    $post->setWebsite($website);
                    $post->setThumbnail($post->getThumbnail());
                    $post->setPostCategories($post->getPostCategories());
                    $data = $this->excludeData($data, 'posts', $post->getId());
                    $data = $this->replaceData($data, 'posts', $id, $post->getId());
                }
                $post->setPublished($state);
                Post::watch($post);
            }
            $website->setData($data);
            return (Website::watchAndSave($website))
                ? ['status' => 'success', 'message' => 'Le(s) article(s) ont bien été mis à jour']
                : ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
        }
        return ['status' => 'error', 'message' => 'Le(s) article(s) n\'ont pas pu être mis à jour'];
    }

    /**
     * @param $website
     * @return array
     */
    public function listTableValues($website)
    {
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return [
            'c' => PostCategory::repo()->listTableValues($this->websites, $this->website->getData()),
            'p' => Post::repo()->listTableValues($this->websites, $this->website->getData())
        ];

    }

    /**
     * @param $website
     * @return array
     */
    public function listRuleValue($website)
    {
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return Post::repo()->getPostRules($this->websites, $this->website->getData());
    }

    /**
     * @param PostRequest $request
     * @param $website
     * @return array
     */
    public function listNames(PostRequest $request, $website)
    {
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        if($request->exists('categories'))
            return ['resource' => Post::repo()->getPostByCategories($this->websites, $this->website->getData(), $request->get('categories'))];
        return ['resource' => Post::repo()->getPostRules($this->websites, $this->website->getData())];
    }

    /**
     * @param $url
     * @param $id
     * @return array|mixed
     */
    public function getUrl($url, $id)
    {
        $post = Post::find($id);
        if (is_null($post)) return ['status' => 'error', 'message' => 'Impossible de trouver l\'article'];
        $replaces = ['id', 'slug'];
        foreach ($replaces as $replace)
            $url = str_replace(':' . $replace, $post[$replace], $url);
        return $url;
    }
}