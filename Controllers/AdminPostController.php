<?php

namespace Jet\Modules\Post\Controllers;

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

        $this->websites[] = $website;
        $website = Website::findOneById($website);
        $this->getThemeWebsites($website);

        $params = [
            'websites' => $this->websites,
            'website_options' => $website->getData(),
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
     * @param $website
     * @param $id
     * @return array
     */
    public function read($website, $id)
    {
        $this->websites[] = $website;
        $website = Website::findOneById($website);
        $this->getThemeWebsites($website);
        $route = Route::repo()->getRouteByName('module:post.type:dynamic.action:read', $this->websites, $website->getData());
        $post = Post::repo()->readAdmin($id);

        if (!is_null($post))
            return ['status' => 'success', 'resource' => $post, 'route' => (isset($route[0])) ? $route[0] : ''];
        return ['status' => 'error', 'message' => 'Article inexistant'];
    }

    /**
     * @param PostRequest $request
     * @param $website
     * @param $id
     * @return array|bool
     */
    public function updateOrCreate(PostRequest $request, $website, $id)
    {
        if ($request->method() == 'PUT' || $request->method() == 'POST') {
            $response = $request->validate();
            if ($response === true) {
                $post = ($id == 'create') ? new Post() : Post::findOneById($id);
                if (!is_null($post)) {
                    $website = Website::findOneById($website);
                    if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
                    $value = $request->getPost();
                    if ($id == 'create') {
                        $post->setWebsite($website);
                    } elseif ($post->getWebsite() != $website) {
                        $data = $website->getData();
                        $data['parent_exclude']['posts'] = (isset($data['parent_exclude']['posts']))
                            ? $data['parent_exclude']['posts'] : [];
                        if (!in_array($post->getId(), $data['parent_exclude']['posts']))
                            $data['parent_exclude']['posts'][] = $post->getId();
                        $website->setData($data);
                        Website::watch($website);
                        $post = new Post();
                        $post->setWebsite($website);
                    }
                    $post->setTitle($value->get('title'));
                    $post->setSlug($value->get('slug'));
                    $post->setDescription($value->get('description'));
                    $post->setContent($value->get('content'));
                    ($value->has('published') && $value->get('published') == 'true') ? $post->setPublished(1) : $post->setPublished(0);

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

                    return (Post::watchAndSave($post))
                        ? ['status' => 'success', 'message' => 'L\'article a bien été mis à jour', 'resource' => $post]
                        : ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
                }
                return ['status' => 'error', 'message' => 'Article non trouvé'];
            }
            return $response;
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param PostRequest $request
     * @param $website
     * @return array
     */
    public function delete(PostRequest $request, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
            $data = $website->getData();
            $data['parent_exclude']['posts'] = (isset($data['parent_exclude']['posts'])) ? $data['parent_exclude']['posts'] : [];

            $posts = Post::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($posts as $post) {
                if ($post->getWebsite() != $website) {
                    if (!in_array($post->getId(), $data['parent_exclude']['posts'])) $data['parent_exclude']['posts'][] = $post->getId();
                } else
                    $ids[] = $post->getId();
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
     * @param $website
     * @return array
     */
    public function changeState(PostRequest $request, $website)
    {
        if ($request->method() == 'PUT' && $request->exists(['ids', 'state'])) {
            $post_website = Website::findOneById($website);
            $data = $post_website->getData();
            $posts_exclude = (isset($data['parent_exclude']['posts'])) ? $data['parent_exclude']['posts'] : [];
            $posts_replace = (isset($data['parent_replace']['posts'])) ? $data['parent_replace']['posts'] : [];
            foreach ($request->get('ids') as $id) {
                $post = Post::findOneById($id);
                if ($post->getWebsite()->getId() != $website) {
                    $new_post = new Post();
                    $new_post->setTitle($post->getTitle());
                    $new_post->setSlug($post->getSlug());
                    $new_post->setDescription($post->getDescription());
                    $new_post->setContent($post->getContent());
                    $new_post->setPublished($request->get('state'));
                    $new_post->setWebsite($post_website);
                    $new_post->setThumbnail($post->getThumbnail());
                    $new_post->setPostCategories($post->getPostCategories());
                    Post::watchAndSave($new_post);
                    if (!in_array($post->getId(), $posts_exclude)) $posts_exclude[] = $post->getId();
                    if (!isset($posts_replace[$post->getId()])) $posts_replace[$post->getId()] = $new_post->getId();
                } else
                    Post::where('id', $id)->set(['published' => $request->get('state')]);
            }
            $data['parent_exclude']['posts'] = $posts_exclude;
            $data['parent_replace']['posts'] = $posts_replace;
            $post_website->setData($data);
            Website::watchAndSave($post_website);
            return ['status' => 'success', 'message' => 'Le(s) article(s) ont bien été mis à jour'];
        }
        return ['status' => 'error', 'message' => 'Le(s) article(s) n\'ont pas pu être mis à jour'];
    }

    /**
     * @param $website
     * @return array
     */
    public function listTableValues($website)
    {
        $this->websites[] = $website;
        $website = Website::findOneById($website);
        if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
        $this->getThemeWebsites($website);

        return [
            'c' => PostCategory::repo()->listTableValues($this->websites, $website->getData()),
            'p' => Post::repo()->listTableValues($this->websites, $website->getData())
        ];

    }

    /**
     * @param $website
     * @return array
     */
    public function listRuleValue($website)
    {
        $this->websites[] = $website;
        $website = Website::findOneById($website);
        if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
        $this->getThemeWebsites($website);
        return Post::repo()->getPostRules($this->websites, $website->getData());
    }
}