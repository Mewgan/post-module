<?php

namespace Jet\Modules\Post\Models;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Jet\Models\AppRepository;

/**
 * Class PostCategoryRepository
 * @package Jet\Modules\Post\Models
 */
class PostCategoryRepository extends AppRepository
{

    /**
     * @param $params
     * @return mixed
     */
    public function frontListAll($params = [])
    {
        $query = PostCategory::queryBuilder();
        $query->select('partial c.{id,name,slug}')
            ->from('Jet\Modules\Post\Models\PostCategory', 'c')
            ->leftJoin('c.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function read($params = [])
    {
        $query = PostCategory::queryBuilder()
            ->select('c')
            ->from('Jet\Modules\Post\Models\PostCategory', 'c')
            ->leftJoin('c.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = PostCategory::queryBuilder()
            ->select('partial c.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Post\Models\PostCategory', 'c')
            ->leftJoin('c.website', 'w');
        return $query->where($query->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }


    /**
     * @param $column
     * @param $value
     * @param array $params
     * @return null
     */
    public function findOneByColumn($column, $value, $params = [])
    {
        $query = PostCategory::queryBuilder()
            ->select('c.id')
            ->from('Jet\Modules\Post\Models\PostCategory', 'c')
            ->leftJoin('c.website', 'w')
            ->orderBy('c.id', 'ASC');

        $query->where($query->expr()->eq('c.' . $column, ':value'))
            ->setParameter('value', $value);

        $query = $this->getQueryWithParams($query, $params);

        $result = $query->getQuery()->getArrayResult();

        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * @param $websites
     * @param $options
     * @param string $select
     * @return array
     */
    public function getPostCategoryRules($websites, $options, $select = 'partial c.{id,name,slug}')
    {
        $query = PostCategory::queryBuilder()
            ->select($select)
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Post\Models\PostCategory', 'c')
            ->leftJoin('c.website', 'w');

        $query = $this->getQueryWithParams($query, ['websites' => $websites, 'options' => $options]);

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * @param array $params
     * @return array
     */
    public function listTableValues($params = [])
    {
        $query = PostCategory::queryBuilder()
            ->select(['c.id as id', 'c.name as name', 'c.slug as slug'])
            ->from('Jet\Modules\Post\Models\PostCategory', 'c')
            ->leftJoin('c.website', 'w');

        $query = $this->getQueryWithParams($query, $params);

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, $params)
    {
        if (isset($params['websites'])) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->in('w.id', ':websites'),
                    $query->expr()->isNull('w.id')
                )
            )->setParameter('websites', $params['websites']);
        } else {
            $query->andWhere($query->expr()->isNull('w.id'));
        }

        if (isset($params['options'])) {
            $query = $this->excludeData($query, $params['options'], 'post_categories', 'c');
        }

        return $query;
    }
} 