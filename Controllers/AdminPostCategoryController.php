<?php

namespace Jet\Modules\Post\Controllers;

use Jet\Services\Auth;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Account;
use Jet\Models\Website;
use Jet\Modules\Post\Models\PostCategory;
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
    public function all(Request $request, $website){
        $max = ($request->exists('max')) ? (int)$request->query('max') : 10;
        $page = ($request->exists('page')) ? (int)$request->query('page') : 1;

        $website = Website::queryBuilder()->select('w')->from('Jet\Models\Website','w')->where('w.id = :id')->setParameter('id',$website)->getQuery()->getSingleResult();
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
     * @param $website
     * @return mixed
     */
    public function listByName($website){
        $this->websites[] = (int)$website;
        $website = Website::findOneById($website);
        $this->getThemeWebsites($website);
        return PostCategory::repo()->getNames($this->websites, $website->getData());
    }

    /**
     * @param Request $request
     * @param $website
     * @return array
     */
    public function create(Request $request, $website){
        if ($request->method() == 'POST') {
            $category = $request->request->get('name');
            if(PostCategory::where('name',$category)->where('website',$website)->count() == 0){
                if(PostCategory::create(['name' => $category, 'slug' => slugify($category), 'website' => Website::findOneById($website)]))
                    return ['status' => 'success', 'message' => 'La catégorie a bien été créée'];
                return ['status' => 'error', 'message' => 'La catégorie n\'a pas été créée'];
            }
            return ['status' => 'error', 'message' => 'La catégorie existe déjà'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param $id
     */
    public function read($id){

    }

    /**
     * @param Request $request
     * @param $id
     * @param $website
     * @return array
     */
    public function update(Request $request, $id, $website){
        if ($request->method() == 'PUT') {
            $category = $request->request->get('name');
            if(PostCategory::where('name',$category)->where('website',$website)->count() > 1) {
                return ['status' => 'error', 'message' => 'La catégorie existe déjà'];
            }else{
                $post_category_website = Website::findOneById($website);
                $data = $post_category_website->getData();
                $post_categories_exclude = (isset($data['parent_exclude']['post_categories']))?$data['parent_exclude']['post_categories']:[];
                $post_category = PostCategory::findOneById($id);
                if($post_category->getWebsite()->getId() != $website) {
                    $new_category = new PostCategory();
                    $new_category->setName($category);
                    $new_category->setSlug(slugify($category));
                    $new_category->setWebsite($post_category_website);
                    $new_category->setPosts($post_category->getPosts());
                    if(!PostCategory::watchAndSave($new_category))
                        return ['status' => 'error', 'message' => 'La catégorie n\'a pas été mis à jour'];
                    if(!isset($post_categories_exclude[$id]))$post_categories_exclude[] = $id;
                }else
                    if(!PostCategory::where('id', $id)->set(['name' => $category, 'slug' => slugify($category)]))
                        return ['status' => 'error', 'message' => 'La catégorie n\'a pas été mis à jour'];
            }
            $data['parent_exclude']['post_categories'] = $post_categories_exclude;
            $post_category_website->setData($data);
            Website::watchAndSave($post_category_website);
            return ['status' => 'success', 'message' => 'La catégorie a bien été mis à jour'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
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
            $post_category_website = Website::findOneById($website);
            $data = $post_category_website->getData();
            $post_categories_exclude = (isset($data['parent_exclude']['post_categories']))?$data['parent_exclude']['post_categories']:[];
            $account = Account::repo()->getWebsiteAccount($website);
            if($auth->get('id') == $account->getId() || $auth->get('status')->level <= 2) {
                foreach ($request->get('ids') as $id){
                    $category = PostCategory::orm('pdo')->select('id','website_id')->where('id',$id)->get(true);
                    if($category['website_id'] != $website) {
                        if (!isset($post_categories_exclude[$id])) $post_categories_exclude[] = $id;
                    }else
                        PostCategory::destroy($id);
                }
                $data['parent_exclude']['post_categories'] = $post_categories_exclude;
                $post_category_website->setData($data);
                Website::watchAndSave($post_category_website);
                return ['status' => 'success', 'message' => 'Les catégories ont bien été supprimées'];
            }
            return ['status' => 'error', 'message' => 'Vous n\'avez pas les permission pour supprimer ces catégories'];
        }
        return ['status' => 'error', 'message' => 'Les catégories n\'ont pas pu être supprimées'];
    }

    /**
     * @param $website
     * @return array
     */
    public function listRuleValue($website)
    {
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return PostCategory::repo()->getPostCategoryRules($this->websites, $this->website->getData());
    }

    /**
     * @param $website
     * @return array
     */
    public function listNames($website)
    {
        if(!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => PostCategory::repo()->getPostCategoryRules($this->websites, $this->website->getData(), ['c.id as id','c.name as title'])];
    }

    /**
     * @param $url
     * @param $cat
     * @return array|mixed
     */
    public function getUrl($url, $cat)
    {
        $cat = PostCategory::find($cat);
        if (is_null($cat)) return ['status' => 'error', 'message' => 'Impossible de trouver la catégorie'];
        $replaces = ['id', 'slug'];
        foreach ($replaces as $replace)
            $url = str_replace(':' . $replace, $cat[$replace], $url);
        return $url;
    }
}