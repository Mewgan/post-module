<?php

namespace Jet\Modules\Post\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PostRepository extends EntityRepository{

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

    public function countPost($params = []){
        $query = Post::queryBuilder();

        $query->select('COUNT(p)')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->innerJoin('p.categories', 'c')
            ->leftJoin('p.website','w');

        $query = $this->getQueryWithParams($query,$params);

        return (int)$query->getQuery()->getSingleScalarResult();
    }
    
    public function read($params = []){
        $query = Post::em()->createQueryBuilder('p')
            ->select('p')
            ->from('Jet\Modules\Post\Models\Post','p')
            ->innerJoin('p.categories', 'c')
            ->leftJoin('p.website','w');

        $query = $this->getQueryWithParams($query,$params,'p');
        
        return $query->getQuery()->getSingleResult();
    }

    private function getQueryWithParams($query, $params , $alias = 'c'){

        if(isset($params['websites'])){
            $query->where($query->expr()->in('w.id',':websites'))
                ->setParameter('websites',$params['websites']);
        }

        if(isset($params['website_options']['parent_exclude']) && isset($params['website_options']['parent_exclude']['posts'])){
            $query->andWhere($query->expr()->notIn('p.id',':exclude_ids'))
                ->setParameter('exclude_ids',$params['website_options']['parent_exclude']['posts']);
        }

        if(isset($params['db']) && !empty($params['db'])){
            foreach ($params['db'] as $key => $db) {
                if(isset($db['route']))
                    $query->andWhere('p.' . $db['column'] . ' = :column_' . $key)
                        ->setParameter('column_' . $key, $params['params'][$db['route']]);
                elseif(isset($db['value']))
                    if (is_array($db['value']))
                        $query->andWhere($alias.'.' . $db['column'] . ' IN :column_' . $key)
                            ->setParameter('column_' . $key, $db['value']);
                    else
                        $query->andWhere($alias.'.' . $db['column'] . ' = :column_' . $key)
                            ->setParameter('column_' . $key, $db['value']);
            }
        }

        return $query;
    }

} 