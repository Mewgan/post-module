<?php

namespace Jet\Modules\Post\Controllers;

use Jet\FrontBlock\Controllers\MainController;
use Jet\Models\Content;
use Jet\Models\CustomField;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;
use JetFire\Framework\Factories\View;
use JetFire\Framework\System\Request;

/**
 * Class FrontPostController
 * @package Jet\Modules\Post
 */
class FrontPostController extends MainController
{

    /**
     * @var Post
     */
    private $post;
    
    /**
     * @param Request $request
     * @param Website $website
     * @param Content $content
     * @return mixed
     */
    public function all(Request $request, Website $website, $content)
    {
        $data = $content->getData();
        $max = (isset($data['max']) && !empty($data['max'])) ? (int)$data['max'] : 10;
        $page = ($request->exists('page')) ? (int)$request->query('page') : 1;

        if (!empty($data)) {
            $response = Post::repo()->listAll($page, $max, $this->getParams($website, $data));
            $pages_count = ceil($response['total'] / $max);
            $posts = $response['data'];
            $pagination = [
                'page' => $page,
                'pages_count' => $pages_count,
                'count_all' => $response['total'],
            ];
            $args = (isset($data['link']) && !empty($data['link'])) ? $data['link'] : [];
            $route = isset($data['route_name']) ? $data['route_name'] : '';
            $categories = PostCategory::repo()->frontListAll($this->getParams($website, $data));

            return (empty($posts))
                ? null
                : $this->_renderContent($content->getTemplate(), 'src/Modules/Post/Views/', compact('categories', 'posts', 'pagination', 'args', 'route'));
        }
        return $this->notFound();
    }

    /**
     * @param Website $website
     * @param Content $content
     * @return mixed|null
     */
    public function read(Website $website, $content)
    {
        $data = $content->getData();
        if (!empty($data)) {
            $this->post = Post::repo()->read($this->getParams($website, $data));
            if(!is_null($this->post)){
                return $this->_renderContent($content->getTemplate(), 'src/Modules/Post/Views/', ['post' => $this->post]);
            }
        }
        return $this->notFound();
    }

    /**
     * @param Website $website
     * @param $data
     * @return array
     */
    private function getParams(Website $website, $data)
    {
        $data['params'] = (View::hasData('data') && isset(View::getData('data')['route_params'])) ? View::getData('data')['route_params'] : [];
        $data['published'] = true;
        $this->refreshWebsite($website);
        $data['options'] = $this->getWebsiteData($website);
        $data['websites'] = $this->websites;
        return $data;
    }

    /**
     * @param Website $website
     * @param $websites
     * @param $key
     * @return
     */
    public function listCustomFields(Website $website, $websites, $key){
        /** @var Post $post */
        $post = null;
        $key = explode('@', $key);
        if(isset($key[1]) && $key[0] == 'post' && is_numeric($key[1])) {
            $post = Post::findOneById($key[1]);
        }
        if(!is_null($post)) {
            $categories = [];
            foreach ($post->getPostCategories() as $category)$categories[] = $category->getId();
            $rules = [
                'everywhere' => null,
                'publication_type' => 'post',
                'post' => $post->getId(),
                'post_category' => $categories
            ];
            return CustomField::repo()->frontRender($websites, $this->getWebsiteData($website), $rules);
        }
        return null;
    }

    /**
     * @param Website $website
     * @param $value
     * @return null
     */
    public function renderField(Website $website, $value){
        if (!empty($value) && !is_null($value) && is_numeric($value)){
            /** @var Post $post */
            $post = Post::findOneById($value);
            if(!is_null($post)){
                $this->refreshWebsite($website);
                $data = $this->getWebsiteData($website);
                if(isset($data['parent_replace']['posts'][$post->getId()])){
                    $post = Post::findOneById($data['parent_replace']['posts'][$post->getId()]);
                    if($post->isPublished()) {
                        return $post;
                    }
                } else if($post->isPublished() && (!isset($data['parent_exclude']['posts']) || !in_array($post->getId(), $data['parent_exclude']['posts']))) {
                    return $post;
                }
            }
        }
        return null;
    }
}