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
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $website
     * @return array
     */
    public function create(Request $request, Auth $auth, Slugify $slugify, $website)
    {
        if ($request->method() == 'POST') {

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour créer une catégorie'];

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

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour mettre à jour une catégorie'];

            $name = $request->get('name');
            $replace = false;

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];


            if (PostCategory::where('name', $name)->where('website', $website)->count() > 0) {
                return ['status' => 'error', 'message' => 'La catégorie existe déjà'];
            } else {

                /** @var PostCategory $category */
                $category = PostCategory::findOneById($id);
                if (is_null($category)) return ['status' => 'error', 'message' => 'Impossible de trouver la catégorie'];

                $old_category = $category;

                if (is_null($category->getWebsite()) || $category->getWebsite()->getId() != $website->getId()) {
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

            if (PostCategory::watchAndSave($category)) {
                if ($replace) {
                    $event->emit('updatePostCategory', ['old_post_category' => $old_category->getId(), 'post_category' => $category->getId(), 'website' => $website->getId()]);
                    $website = $category->getWebsite();
                    $data = $this->replaceData($website->getData(), 'post_categories', $id, $category->getId());
                    $website->setData($data);
                    Website::watchAndSave($website);
                } else
                    $event->emit('updatePostCategory', ['post_category' => $category->getId(), 'website' => $website->getId()]);
                return ['status' => 'success', 'message' => 'La catégorie a bien été mis à jour'];
            } else
                return ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
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

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

            if (!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour supprimer ces catégories'];

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
        return PostCategory::repo()->getPostCategoryRules($this->websites, $this->getWebsiteData($this->website));
    }

    /**
     * @param $website
     * @return array
     */
    public function listNames($website)
    {
        if (!$this->getWebsite($website)) return ['status' => 'error', 'Impossible de trouver le site web'];
        return ['resource' => PostCategory::repo()->getPostCategoryRules($this->websites, $this->getWebsiteData($this->website))];
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