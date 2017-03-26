<?php

namespace Jet\Modules\Post\Models;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Jet\Models\AppRepository;
use JetFire\Db\IteratorResult;

/**
 * Class PostRepository
 * @package Jet\Modules\Post\Models
 */
class PostRepository extends AppRepository
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

        if ((isset($params['search']) && !empty($params['search'])) || isset($params['filter']['column'])) {
            $countSearch = true;
        }

        $pg = new Paginator($query);
        $data = $this->reassignCategories($pg->getQuery()->getArrayResult(), $params);

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
            ->addSelect('partial c.{id,name,slug}')
            ->addSelect('partial t.{id,path,alt}')
            ->addSelect('partial w.{id,domain}')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.thumbnail', 't')
            ->leftJoin('p.website', 'w');

        $query = $this->getRequiredParams($query, $params);
        $query = $this->frontQueryParams($query, $params);

        $result = $this->reassignCategories($query->getQuery()->getArrayResult(), $params);
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = Post::queryBuilder()
            ->select('partial p.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w');
        return $query->where($query->expr()->in('p.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param $slug
     * @param $params
     * @return int
     */
    public function countBySlug($slug, $params)
    {
        $query = Post::queryBuilder()
            ->select('COUNT(p)')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w');

        $query->where($query->expr()->eq('p.slug', ':slug'))
            ->setParameter('slug', $slug);

        $query = $this->getRequiredParams($query, $params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $params
     * @param array $select
     * @return array
     */
    public function getPostRules($params = [], $select = ['p.id as id', 'p.title as name'])
    {
        $query = Post::queryBuilder()
            ->select($select)
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w');

        $query = $this->getRequiredParams($query, $params);

        return $this->reassignCategories($query->getQuery()->getArrayResult(), $params);
    }

    /**
     * @param array $categories
     * @param array $params
     * @return array
     */
    public function getPostByCategories($categories = [], $params = [])
    {
        $query = Post::queryBuilder()
            ->select(['p.id as id', 'p.title as name'])
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w')
            ->leftJoin('p.categories', 'c');

        if (is_array($categories) && !empty($categories)) {
            $query->where($query->expr()->in('c.id', ':categories'))
                ->setParameter('categories', $categories);
        }

        $query = $this->getRequiredParams($query, $params);

        return $this->reassignCategories($query->getQuery()->getArrayResult(), $params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function listTableValues($params = [])
    {
        $query = Post::queryBuilder()
            ->select(['p.id as id', 'p.title as name', 'p.slug as slug'])
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w');

        $query = $this->getRequiredParams($query, $params);

        return $this->reassignCategories($query->getQuery()->getArrayResult(), $params);
    }

    /**
     * @param $id
     * @param $keys
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function retrieveData($id, $keys)
    {
        $query = Post::queryBuilder()
            ->select($keys)
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id);
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $column
     * @param $value
     * @param array $params
     * @return null
     */
    private function findOneByColumn($column, $value, $params = [])
    {
        $query = Post::queryBuilder()
            ->select('p.id')
            ->from('Jet\Modules\Post\Models\Post', 'p')
            ->leftJoin('p.website', 'w')
            ->orderBy('p.id', 'ASC');

        $query->where($query->expr()->eq('p.' . $column, ':value'))
            ->setParameter('value', $value);

        $query = $this->getRequiredParams($query, $params);

        $result = $query->getQuery()->getArrayResult();

        return isset($result[0]) ? $result[0] : null;
    }


    /**
     * @param array $data
     * @param array $params
     * @return array
     */
    private function reassignCategories($data = [], $params = [])
    {
        $categories = PostCategory::repo()->frontListAll($params);
        $exclude_cat_ids = isset($params['options']['parent_exclude']['post_categories']) ? array_flip($params['options']['parent_exclude']['post_categories']) : [];
        foreach ($data as $i => $post) {
            if (isset($post['categories']) && is_array($post['categories'])) {
                foreach ($post['categories'] as $y => $category) {
                    if (isset($exclude_cat_ids[$category['id']])) {
                        unset($data[$i]['categories'][$y]);
                    }
                    if (isset($params['options']['parent_replace']['post_categories'][$category['id']])) {
                        $index = findIndex($categories, 'id', $params['options']['parent_replace']['post_categories'][$category['id']]);
                        if ($index !== false) {
                            $data[$i]['categories'][$y] = $categories[$index];
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getRequiredParams(QueryBuilder $query, $params)
    {
        if (isset($params['websites']) && !empty($params['websites'])) {
            $query->andWhere($query->expr()->in('w.id', ':websites'))
                ->setParameter('websites', $params['websites']);
        }

        if (isset($params['options'])) {
            $query = $this->excludeData($query, $params['options'], 'posts', 'p');
        }

        return $query;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, $params)
    {

        $query = $this->getRequiredParams($query, $params);

        if (isset($params['search']) && !empty($params['search'])) {
            $query->andWhere($query->expr()->orX(
                $query->expr()->like('p.title', ':search'),
                $query->expr()->like('p.description', ':search'),
                $query->expr()->like('c.name', ':search')
            ))->setParameter('search', '%' . $params['search'] . '%');
        }

        if (isset($params['filter']) && !empty($params['filter']) && !empty($params['filter']['column'])) {
            $op = (isset($params['filter']['operator'])) ? $params['filter']['operator'] : 'eq';
            if ($op == 'isNull')
                $query->andWhere($query->expr()->isNull($params['filter']['column']));
            elseif ($op == 'isNotNull')
                $query->andWhere($query->expr()->isNotNull($params['filter']['column']));
            else {
                $alias = explode('.', $params['filter']['column']);
                $table = ($alias[0] == 'c') ? 'post_categories' : 'posts';
                $value = $params['filter']['value'];
                $flipped_array = array_flip($params['options']['parent_replace'][$table]);
                if (isset($params['options']['parent_replace'][$table]) && isset($flipped_array[$value])) {
                    $value = [$value, $flipped_array[$value]];
                    $op = 'in';
                }
                $query->andWhere($query->expr()->$op($params['filter']['column'], ':value'))
                    ->setParameter('value', $value);
            }
        }

        if (isset($params['published'])) {
            $query->andWhere($query->expr()->eq('p.published', ':published'))
                ->setParameter('published', $params['published']);
        }

        if (isset($params['no_category']) && $params['no_category']) {
            if (isset($params['options']['parent_exclude']['post_categories']) && !empty($params['options']['parent_exclude']['post_categories'])) {
                $orX = $query->expr()->orX();
                $orX->add($query->expr()->isNull('c.id'));
                if (isset($params['options']['parent_replace']['post_categories']) && !empty($params['options']['parent_replace']['post_categories'])) {
                    $orX->add($query->expr()->andX(
                        $query->expr()->in('c.id', ':exclude_cat'),
                        $query->expr()->notIn('c.id', ':replace_cat')
                    ));
                    $query->setParameter('replace_cat', array_keys($params['options']['parent_replace']['post_categories']));
                } else {
                    $orX->add($query->expr()->in('c.id', ':exclude_cat'));
                }
                $query->andWhere($orX)
                    ->setParameter('exclude_cat', $params['options']['parent_exclude']['post_categories']);
            } else {
                $query->andWhere($query->expr()->isNull('c.id'));
            }
        }

        $query = $this->frontQueryParams($query, $params);

        (isset($params['order']) && !empty($params['order']) && !empty($params['order']['column']))
            ? $query->addOrderBy($params['order']['column'], strtoupper($params['order']['dir']))
            : $query->orderBy('p.id', 'DESC');

        return $query;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return QueryBuilder
     */
    private function frontQueryParams(QueryBuilder $query, $params)
    {
        if (isset($params['db']) && !empty($params['db'])) {
            foreach ($params['db'] as $key => $db) {

                if (isset($db['type'])) {

                    $replace_content = ($db['alias'] == 'p') ? 'posts' : 'post_categories';
                    $exclude_ids = (isset($params['options']['parent_exclude'][$replace_content])) ? array_flip($params['options']['parent_exclude'][$replace_content]) : [];
                    $replace_ids = (isset($params['options']['parent_replace'][$replace_content])) ? array_flip($params['options']['parent_replace'][$replace_content]) : [];

                    if ($db['type'] == 'dynamic' && isset($db['route']) && !empty($db['route']) && isset($params['params'][$db['route']])) {

                        $item = null;
                        $value = [$params['params'][$db['route']]];
                        if ($db['column'] != 'id') {
                            $item = ($db['alias'] == 'c')
                                ? PostCategory::repo()->findOneByColumn($db['column'], $params['params'][$db['route']], $params)
                                : $this->findOneByColumn($db['column'], $params['params'][$db['route']], $params);
                        } else {
                            $item = ['id' => $params['params'][$db['route']]];
                        }

                        if (!is_null($item) && isset($item['id'])) {
                            $value = [$item['id']];
                            if (isset($exclude_ids[$item['id']]))
                                $value = [];
                            if (isset($params['options']['parent_replace'][$replace_content])) {
                                if (isset($params['options']['parent_replace'][$replace_content][$item['id']]))
                                    $value = [$item['id'], $params['options']['parent_replace'][$replace_content][$item['id']]];
                                elseif (isset($replace_ids[$item['id']]))
                                    $value = [$item['id'], $replace_ids[$item['id']]];
                            }
                        }

                        $query->andWhere($query->expr()->in($db['alias'] . '.id', ':column_' . $key))
                            ->setParameter('column_' . $key, $value);

                    } elseif ($db['type'] == 'static' && isset($db['value']) && !empty($db['value'])) {

                        if (!is_array($db['value'])) {
                            $db['value'] = [$db['value']];
                        }

                        if (isset($params['options']['parent_exclude'][$replace_content])) {
                            foreach ($db['value'] as $k => $id) {
                                if (isset($exclude_ids[$id]))
                                    unset($db['value'][$k]);
                                if (isset($params['options']['parent_replace'][$replace_content][$id]))
                                    $db['value'] = array_merge([$id, $params['options']['parent_replace'][$replace_content][$id]], $db['value']);
                                elseif (isset($replace_ids[$id])) {
                                    $db['value'] = array_merge([$id, $replace_ids[$id]], $db['value']);
                                }
                            }
                        }

                        $query->andWhere($query->expr()->in($db['alias'] . '.id', ':column_' . $key))
                            ->setParameter('column_' . $key, $db['value']);
                    }
                }
            }
        }

        return $query;
    }
} 