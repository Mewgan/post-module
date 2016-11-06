<?php

namespace Jet\Modules\Post\Controllers;


use Jet\AdminBlock\Classes\Auth;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Account;
use Jet\Models\Route;
use Jet\Models\Website;
use Jet\Modules\Post\Models\Post;
use Jet\Modules\Post\Models\PostCategory;
use JetFire\Framework\System\Request;

/**
 * Class AdminPostController
 * @package Jet\Modules\Post\Controllers
 */
class AdminPostController extends AdminController
{

    /**
     * @param Request $request
     * @param $website
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function all(Request $request, $website){
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
        $pages_count = ceil($response['total'] / $max);

        $themes = [
            'current_page' => $page,
            'count_pages' => $pages_count,
            'count_all' => $response['total'],
            'data' => $response['data']
        ];
        return $this->json(['status' => 'success', 'content' => $themes]);
    }

    /**
     *
     */
    public function create(){

    }

    /**
     * @param $website
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function read($website, $id){
        $this->websites[] = $website;
        $website = Website::findOneById($website);
        $this->getThemeWebsites($website);
        $route = Route::repo()->getRouteByName('module:post.type:dynamic.action:read', $this->websites, $website->getData());
        $post = Post::repo()->readAdmin($id);

        if (!is_null($post))
            return $this->json(['status' => 'success', 'resource' => $post, 'route' => (isset($route[0]))?$route[0]:'']);
        return $this->json(['status' => 'error', 'message' => 'Article inexistant']);
    }

    /**
     *
     */
    public function update(){
        
    }

    /**
     * @param Request $request
     * @param $website
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {
            $post_website = Website::findOneById($website);
            $data = $post_website->getData();
            $posts_exclude = (isset($data['parent_exclude']['posts']))?$data['parent_exclude']['posts']:[];
            $account = Account::repo()->getWebsiteAccount($website);
            if(Auth::get('id') == $account->getId() || Auth::get('status')->level <= 2) {
                foreach ($request->get('ids') as $id){
                    $post = Post::orm('pdo')->select('id','website_id')->where('id',$id)->get(true);
                    if($post['website_id'] != $website) {
                        if (!isset($posts_exclude[$id])) $posts_exclude[] = $id;
                    }else
                        Post::delete($id);
                }
                $data['parent_exclude']['posts'] = $posts_exclude;
                $post_website->setData($data);
                Website::watchAndSave($post_website);
                return $this->json(['status' => 'success', 'message' => 'Le(s) article(s) ont bien été supprimé(s)']);
            }
            return $this->json(['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces articles']);
        }
        return $this->json(['status' => 'error', 'message' => 'Le(s) article(s) n\'ont pas pu être supprimé(s)']);
    }

    /**
     * @param Request $request
     * @param $website
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeState(Request $request, $website)
    {
        if ($request->method() == 'PUT' && $request->exists(['ids', 'state'])) {
            $post_website = Website::findOneById($website);
            $data = $post_website->getData();
            $posts_exclude = (isset($data['parent_exclude']['posts']))?$data['parent_exclude']['posts']:[];
            foreach ($request->get('ids') as $id) {
                $post = Post::findOneById($id);
                if($post->getWebsite()->getId() != $website) {
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
                    if(!isset($posts_exclude[$id]))$posts_exclude[] = $id;
                }else
                    Post::where('id', $id)->set(['published' => $request->get('state')]);
            }
            $data['parent_exclude']['posts'] = $posts_exclude;
            $post_website->setData($data);
            Website::watchAndSave($post_website);
            return $this->json(['status' => 'success', 'message' => 'Le(s) article(s) ont bien été mis à jour']);
        }
        return $this->json(['status' => 'error', 'message' => 'Le(s) article(s) n\'ont pas pu être mis à jour']);
    }

    /**
     *
     */
    public function createContent(){
        
    }

    /**
     *
     */
    public function updateContent(){

    }

    public function listTableValues($website){
        $this->websites[] = $website;
        $website = Website::findOneById($website);
        $this->getThemeWebsites($website);

        return $this->json([
            'c' => PostCategory::repo()->listTableValues($this->websites, $website->getData()),
            'p' => Post::repo()->listTableValues($this->websites, $website->getData())
        ]);
        
    }
}