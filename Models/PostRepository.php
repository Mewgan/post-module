<?php

namespace Jet\Modules\Post\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class PostRepository
 * @package Jet\Modules\Post\Models
 */
class PostRepository extends EntityRepository{

    /**
     * @param $page
     * @param $max
     * @param array $params
     * @return array
     */
    public function listAll($page, $max, $params = []){
        
        $countSearch = false;
        $query = Post::queryBuilder();
        
        $query->select('p')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->leftJoin('p.website','w')
            ->innerJoin('p.categories', 'c');

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
                $query->expr()->like('p.title', ':search'),
                $query->expr()->like('p.description', ':search'),
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
            : $query->orderBy('p.id','DESC');

        $pg = new Paginator($query);
        $data = $pg->getQuery()->getResult();
        return ['data' => $data, 'total' => ($countSearch)?count($data):(int)$this->countPost($params)];
    }

    /**
     * @param array $params
     * @return int
     */
    public function countPost($params = []){
        $query = Post::queryBuilder();

        $query->select('COUNT(p)')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->innerJoin('p.categories', 'c')
            ->leftJoin('p.website','w');

        $query = $this->getQueryWithParams($query,$params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function read($params = []){
        $query = Post::em()->createQueryBuilder()
            ->select('p')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->innerJoin('p.categories', 'c')
            ->leftJoin('p.website','w');

        $query = $this->getQueryWithParams($query,$params);
        
        return $query->getQuery()->getSingleResult();
    }

    public function readAdmin($id){
        $query = Post::em()->createQueryBuilder()
            ->select('p')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.website','w')
            ->where('p.id = :id')
            ->setParameter('id',$id);

        return $query->getQuery()->getSingleResult();
    }

    public function getCategories($id){
        $query = Post::em()->createQueryBuilder()
            ->select('partial p.{id}')
            ->addSelect('c')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->leftJoin('p.categories', 'c')
            ->where('p.id = :id')
            ->setParameter('id',$id);

        return $query->getQuery()->getArrayResult()[0];
    }

    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams($query, $params){
        if(isset($params['published'])){
            $query->where($query->expr()->eq('p.published',':published'))
                ->setParameter('published',$params['published']);
        }

        if(isset($params['websites'])){
            $query->andWhere($query->expr()->in('w.id',':websites'))
                ->setParameter('websites',$params['websites']);
        }

        if(isset($params['website_options']['parent_exclude'])){
            if(isset($params['website_options']['parent_exclude']['posts'])  && !empty($params['website_options']['parent_exclude']['posts'])){
                $query->andWhere($query->expr()->notIn('p.id',':exclude_post_ids'))
                    ->setParameter('exclude_post_ids',$params['website_options']['parent_exclude']['posts']);
            }

            /*if(isset($params['website_options']['parent_exclude']['post_categories']) && !empty($params['website_options']['parent_exclude']['post_categories'])){
                $query->andWhere($query->expr()->notIn('c.id',':exclude_category_ids'))
                    ->setParameter('exclude_category_ids',$params['website_options']['parent_exclude']['post_categories']);
            }*/
        }

        if(isset($params['db']) && !empty($params['db'])){
            foreach ($params['db'] as $key => $db) {
                if(isset($db['route']) && !empty($db['route']))
                    $query->andWhere($db['alias'] . '.' . $db['column'] . ' = :column_' . $key)
                        ->setParameter('column_' . $key, $params['params'][$db['route']]);
                elseif(isset($db['value']) && !empty($db['value'])) {
                    if (is_array($db['value']))
                        $query->andWhere($db['alias'] . '.' . $db['column'] . ' IN :column_' . $key)
                            ->setParameter('column_' . $key, $db['value']);
                    else
                        $query->andWhere($db['alias'] . '.' . $db['column'] . ' = :column_' . $key)
                            ->setParameter('column_' . $key, $db['value']);
                }
            }
        }

        return $query;
    }

    public function getPostRules($websites,$exclude){
        $query = Post::queryBuilder()
            ->select(['p.id as id','p.title as name'])
            ->from('Jet\Modules\Post\Models\Post','p')
            ->leftJoin('p.website','w');

        $query->where($query->expr()->in('w.id',':websites'))
            ->setParameter('websites',$websites);

        if(isset($exclude['parent_exclude']) && isset($exclude['parent_exclude']['posts'])){
            $query->andWhere($query->expr()->notIn('p.id',':exclude_ids'))
                ->setParameter('exclude_ids',$exclude['parent_exclude']['posts']);
        }
        return $query->getQuery()
            ->getArrayResult();
    }

    public function listTableValues($websites, $exclude){
        $query = Post::queryBuilder()
            ->select(['p.id as id' ,'p.title as title'])
            ->from('Jet\Modules\Post\Models\Post','p')
            ->leftJoin('c.website','w');

        $query->where($query->expr()->in('w.id',':websites'))
            ->setParameter('websites',$websites);

        if(isset($exclude['parent_exclude']) && isset($exclude['parent_exclude']['posts'])){
            $query->andWhere($query->expr()->notIn('p.id',':exclude_ids'))
                ->setParameter('exclude_ids',$exclude['parent_exclude']['posts']);
        }

        return $query->getQuery()
            ->getArrayResult();
    }

} 