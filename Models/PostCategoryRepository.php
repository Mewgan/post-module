<?php

namespace Jet\Modules\Post\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class PostCategoryRepository
 * @package Jet\Modules\Post\Models
 */
class PostCategoryRepository extends EntityRepository{

    /**
     * @param $page
     * @param $max
     * @param array $params
     * @return array
     */
    public function listAll($page, $max, $params = []){
        
        $countSearch = false;
        $query = PostCategory::queryBuilder();
        
        $query->select('c')
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');

        if(isset($params['total_row']) && !empty($params['total_row'])) {
            $countSearch = true;
            $query->setMaxResults($params['total_row']);
        }else {
            $query->setFirstResult(($page - 1) * $max)
                ->setMaxResults($max);
        }

        $query = $this->getQueryWithParams($query,$params);

        if(isset($params['search']) && !empty($params['search'])) {
            $countSearch = true;
            $query->andWhere( $query->expr()->orX(
                $query->expr()->like('c.slug', ':search'),
                $query->expr()->like('c.name', ':search')
            ))->setParameter('search', '%'.$params['search'].'%');
        }
        
        if(isset($params['filter']) && !empty($params['filter']) && !empty($params['filter']['column'])){
            $countSearch = true;
            $query->andWhere($query->expr()->eq($params['filter']['column'], ':value'))
                ->setParameter('value', $params['filter']['value']);
        }

        (isset($params['order']) && !empty($params['order']) && !empty($params['order']['column']))
            ? $query->addOrderBy($params['order']['column'],strtoupper($params['order']['dir']))
            : $query->orderBy('c.id','DESC');

        $pg = new Paginator($query);
        $data = $pg->getQuery()->getResult();
        return ['data' => $data, 'total' => ($countSearch)?count($data):$this->countPostCategory($params)];
    }

    /**
     * @param $params
     * @return mixed
     */
    public function frontListAll($params){
        $query = PostCategory::queryBuilder();
        $query->select('partial c.{id,name,slug}')
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');

        $query = $this->getQueryWithParams($query,$params);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array $params
     * @return int
     */
    public function countPostCategory($params = []){
        $query = PostCategory::queryBuilder();

        $query->select('COUNT(c)')
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');

        $query = $this->getQueryWithParams($query,$params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function read($params = []){
        $query = PostCategory::em()->createQueryBuilder('c')
            ->select('c')
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');

        $query = $this->getQueryWithParams($query,$params);
        
        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids){
        $query = PostCategory::queryBuilder()
            ->select('partial c.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');
        return $query->where($query->expr()->in('c.id', ':ids'))
            ->setParameter('ids',$ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams($query, $params){

        if(isset($params['websites'])){
            $query->where(
                $query->expr()->orX(
                    $query->expr()->in('w.id',':websites'),
                    $query->expr()->isNull('w.id')
                )
            )->setParameter('websites',$params['websites']);
        }else{
            $query->where($query->expr()->isNull('w.id'));
        }

        if(isset($params['website_options']['parent_exclude']) && isset($params['website_options']['parent_exclude']['post_categories']) && !empty($params['website_options']['parent_exclude']['post_categories'])){
            $query->andWhere($query->expr()->notIn('c.id',':exclude_ids'))
                ->setParameter('exclude_ids',$params['website_options']['parent_exclude']['post_categories']);
        }

        return $query;
    }
    
    /**
     * @param $websites
     * @param $exclude
     * @param string $select
     * @return array
     */
    public function getPostCategoryRules($websites, $exclude, $select = 'partial c.{id,name,slug}'){
        $query = PostCategory::queryBuilder()
            ->select($select)
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');

        $query->where(
            $query->expr()->orX(
                $query->expr()->in('w.id',':websites'),
                $query->expr()->isNull('w.id')
            )
        )->setParameter('websites',$websites);

        if(isset($exclude['parent_exclude']) && isset($exclude['parent_exclude']['post_categories']) && !empty($exclude['parent_exclude']['post_categories'])){
            $query->andWhere($query->expr()->notIn('c.id',':exclude_ids'))
                ->setParameter('exclude_ids',$exclude['parent_exclude']['post_categories']);
        }

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $websites
     * @param $exclude
     * @return array
     */
    public function listTableValues($websites, $exclude){
        $query = PostCategory::queryBuilder()
            ->select(['c.id as id' ,'c.name as name', 'c.slug as slug'])
            ->from('Jet\Modules\Post\Models\PostCategory','c')
            ->leftJoin('c.website','w');

        $query->where(
            $query->expr()->orX(
                $query->expr()->in('w.id',':websites'),
                $query->expr()->isNull('w.id')
            )
        )->setParameter('websites',$websites);

        if(isset($exclude['parent_exclude']) && isset($exclude['parent_exclude']['post_categories']) && !empty($exclude['parent_exclude']['post_categories'])){
            $query->andWhere($query->expr()->notIn('c.id',':exclude_ids'))
                ->setParameter('exclude_ids',$exclude['parent_exclude']['post_categories']);
        }

        return $query->getQuery()
            ->getArrayResult();
    }
    
} 