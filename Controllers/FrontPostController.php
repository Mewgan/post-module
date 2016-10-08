<?php

namespace Jet\Modules\Post\Controllers;

use Jet\InSalonBlock\Controllers\InSalonController;
use Jet\Models\Content;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;
use JetFire\Framework\Factories\View;
use JetFire\Framework\System\Request;

/**
 * Class FrontPostController
 * @package Jet\Modules\Post
 */
class FrontPostController extends InSalonController
{
    /**
     * @param Request $request
     * @param Website $website
     * @param Content $content
     * @return mixed
     */
    public function all(Request $request, Website $website, $content){

        $data = $content->getData();
        $max = (isset($data['pagination_max'])) ? (int)$data['pagination_max'] : 2;
        $page = ($request->exists('page')) ? (int)$request->query('page') : 1;
        
        if(!empty($data)) {
            $response = Post::repo()->listAll($page, $max, $this->getParams($website,$data));
            $pages_count = ceil($response['total'] / $max);
            $posts = $response['data'];
            $pagination = [
                'page' => $page,
                'pages_count' => $pages_count,
                'count_all' => $response['total'],
            ];
            $args = isset($data['link']) ? $data['link'] : ['column' => 'id', 'route' => 'id'];
            $route = isset($data['route_name']) ? $data['route_name'] : '';

            return (empty($posts))
                ? null
                : $this->_renderContent($content->getTemplate(), 'src/Modules/Post/Views/', compact('posts','pagination','args','route'));
        }
        return null;
    }

    /**
     * @param Website $website
     * @param Content $content
     * @return mixed|null
     */
    public function read(Website $website, $content){
        $data = $content->getData();
        if(!empty($data)) {
            $post = Post::repo()->read($this->getParams($website,$data));
            return (empty($post))
                ? null
                : $this->_renderContent($content->getTemplate(), 'src/Modules/Post/Views/', compact('post'));
        }
        return null;
    }

    /**
     * @param Website $website
     * @param $data
     * @return array
     */
    private function getParams(Website $website, $data){
        $data['website_options'] = $website->getData();
        $data['params'] = (View::hasData('data') && isset(View::getData('data')['route_params'])) ? View::getData('data')['route_params'] : [];
        $theme_website = $website->getTheme()->getWebsite();
        $data['websites'] = ($theme_website->getId() != $website->getId())
            ? [$website->getId(),$theme_website->getId()]
            : [$website->getId()];
        return $data;
    }
}