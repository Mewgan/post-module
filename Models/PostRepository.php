<?php

namespace Jet\Modules\Post\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class PostRepository
 * @package Jet\Modules\Post\Models
 */
class PostRepository extends EntityRepository
{

    /**
     * @param $page
     * @param $max
     * @param array $params
     * @return array
     */
    public function listAll($page, $max, $params = [])
    {

        $countSearch = false;
        /** @var QueryBuilder $query */
        $query = Post::queryBuilder();

        $query->select('p')
            ->addSelect('partial c.{id,name,slug}')
            ->addSelect('partial t.{id,path,alt}')
            ->addSelect('partial w.{id,domain}')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w')
            ->leftJoin('p.thumbnail', 't')
            ->leftJoin('p.categories', 'c');

        if (isset($params['total_row']) && !empty($params['total_row'])) {
            $countSearch = true;
            $query->setMaxResults($params['total_row']);
        } else {
            $query->setFirstResult(($page - 1) * $max)
                ->setMaxResults($max);
        }

        $query = $this->getQueryWithParams($query, $params);

        if (isset($params['search']) && !empty($params['search'])) {
            $countSearch = true;
            $query->andWhere($query->expr()->orX(
                $query->expr()->like('p.title', ':search'),
                $query->expr()->like('p.description', ':search'),
                $query->expr()->like('c.name', ':search')
            ))->setParameter('search', '%' . $params['search'] . '%');
        }

        if (isset($params['filter']) && !empty($params['filter']) && !empty($params['filter']['column'])) {
            $countSearch = true;
            $op = (isset($params['filter']['operator'])) ? $params['filter']['operator'] : 'eq';
            if($op == 'isNull')
                $query->andWhere($query->expr()->isNull($params['filter']['column']));
            elseif ($op == 'isNotNull')
                $query->andWhere($query->expr()->isNotNull($params['filter']['column']));
            else
                $query->andWhere($query->expr()->$op($params['filter']['column'], ':value'))
                    ->setParameter('value', $params['filter']['value']);
        }

        (isset($params['order']) && !empty($params['order']) && !empty($params['order']['column']))
            ? $query->addOrderBy($params['order']['column'], strtoupper($params['order']['dir']))
            : $query->orderBy('p.id', 'DESC');

        $pg = new Paginator($query);
        $data = $pg->getQuery()->getArrayResult();
        return ['data' => $data, 'total' => ($countSearch) ? count($data) : (int)$this->countPost($params)];
    }

    /**
     * @param array $params
     * @return int
     */
    public function countPost($params = [])
    {
        $query = Post::queryBuilder();

        $query->select('COUNT(p)')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function read($params = [])
    {
        $query = Post::queryBuilder()
            ->select('p')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.website', 'w');

        $query = $this->getQueryWithParams($query, $params);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids){
        $query = Post::queryBuilder()
            ->select('partial p.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->leftJoin('p.website','w');
        return $query->where($query->expr()->in('p.id', ':ids'))
            ->setParameter('ids',$ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param $slug
     * @param $params
     * @return int
     */
    public function countBySlug($slug, $params){
        $query = Post::queryBuilder()
            ->select('COUNT(p)')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website','w');

        $query->where($query->expr()->eq('p.slug',':slug'))
            ->setParameter('slug', $slug);

        $query = $this->getRequiredParams($query, $params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function readAdmin($id)
    {
        $query = Post::queryBuilder()
            ->select('p')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.website', 'w')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCategories($id)
    {
        $query = Post::queryBuilder()
            ->select('partial p.{id}')
            ->addSelect('c')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.categories', 'c')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        return $query->getQuery()->getArrayResult()[0];
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, $params)
    {
        if (isset($params['published'])) {
            $query->where($query->expr()->eq('p.published', ':published'))
                ->setParameter('published', $params['published']);
        }

        if (isset($params['websites']) && !empty($params['websites'])) {
            $query->andWhere($query->expr()->in('w.id', ':websites'))
                ->setParameter('websites', $params['websites']);
        }

        if (isset($params['website_options']['parent_exclude'])) {
            if (isset($params['website_options']['parent_exclude']['posts']) && !empty($params['website_options']['parent_exclude']['posts'])) {
                $query->andWhere($query->expr()->notIn('p.id', ':exclude_post_ids'))
                    ->setParameter('exclude_post_ids', $params['website_options']['parent_exclude']['posts']);
            }

            if(isset($params['website_options']['parent_exclude']['post_categories']) && !empty($params['website_options']['parent_exclude']['post_categories'])){
                $query->andWhere($query->expr()->notIn('c.id',':exclude_category_ids'))
                    ->setParameter('exclude_category_ids',$params['website_options']['parent_exclude']['post_categories']);
            }
        }

        if (isset($params['db']) && !empty($params['db'])) {
            foreach ($params['db'] as $key => $db) {
                if (isset($db['type'])) {
                    if ($db['type'] == 'dynamic' && isset($db['route']) && !empty($db['route']) && isset($params['params'][$db['route']])) {
                        $query->andWhere($query->expr()->eq($db['alias'] . '.' . $db['column'], ':column_' . $key))
                            ->setParameter('column_' . $key, $params['params'][$db['route']]);
                    } elseif ($db['type'] == 'static' && isset($db['value']) && !empty($db['value'])) {
                        $replace_content = ($db['alias'] == 'p') ? 'posts' : 'post_categories';
                        if (is_array($db['value'])) {
                            if (isset($params['website_options']['parent_replace']) && isset($params['website_options']['parent_replace'][$replace_content])) {
                                foreach ($db['value'] as $k => $id) {
                                    if (isset($params['website_options']['parent_replace'][$replace_content][$id]))
                                        $db['value'][$k] = $params['website_options']['parent_replace'][$replace_content][$id];
                                }
                            }
                            $query->andWhere($query->expr()->in($db['alias'] . '.id', ':column_' . $key))
                                ->setParameter('column_' . $key, $db['value']);
                        } else {
                            if (isset($params['website_options']['parent_replace']) && isset($params['website_options']['parent_replace'][$replace_content]) && isset($params['website_options']['parent_replace'][$replace_content][$db['value']]))
                                $db['value'] = $params['website_options']['parent_replace'][$replace_content][$db['value']];
                            $query->andWhere($query->expr()->eq($db['alias'] . '.id', ':column_' . $key))
                                ->setParameter('column_' . $key, $db['value']);
                        }
                    }
                }
            }
        }

        return $query;
    }

    /**
     * @param $websites
     * @param $exclude
     * @param array $select
     * @return array
     */
    public function getPostRules($websites, $exclude, $select = ['p.id as id', 'p.title as name'])
    {
        $query = Post::queryBuilder()
            ->select($select)
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w');

        $query = $this->getRequiredParams($query, ['websites' => $websites, 'website_options' => $exclude]);

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $websites
     * @param $exclude
     * @param $categories
     * @return array
     */
    public function getPostByCategories($websites, $exclude, $categories = [])
    {

        $query = Post::queryBuilder()
            ->select(['p.id as id', 'p.title as name'])
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w')
            ->leftJoin('p.categories', 'c');

        if(is_array($categories) && !empty($categories)) {
            $query->where($query->expr()->in('c.id', ':categories'))
                ->setParameter('categories', $categories);
        }

        $query = $this->getRequiredParams($query, ['websites' => $websites, 'website_options' => $exclude]);

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $websites
     * @param $exclude
     * @return array
     */
    public function listTableValues($websites, $exclude)
    {
        $query = Post::queryBuilder()
            ->select(['p.id as id', 'p.title as name', 'p.slug as slug'])
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w')
            ->where('p.published = :published')
            ->setParameter('published', true);

        $query = $this->getRequiredParams($query, ['websites' => $websites, 'website_options' => $exclude]);

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $id
     * @param $keys
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function retrieveData($id, $keys){
        $query = Post::queryBuilder()
            ->select($keys)
            ->from('Jet\Modules\Post\Models\Post','p')
            ->where('p.id = :id')
            ->setParameter('id', $id);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    private function getRequiredParams($query, $params){
        if (isset($params['websites']) && !empty($params['websites'])) {
            $query->andWhere($query->expr()->in('w.id', ':websites'))
                ->setParameter('websites', $params['websites']);
        }

        if (isset($params['website_options']['parent_exclude'])) {
            if (isset($params['website_options']['parent_exclude']['posts']) && !empty($params['website_options']['parent_exclude']['posts'])) {
                $query->andWhere($query->expr()->notIn('p.id', ':exclude_post_ids'))
                    ->setParameter('exclude_post_ids', $params['website_options']['parent_exclude']['posts']);
            }
        }
        return $query;
    }
} 