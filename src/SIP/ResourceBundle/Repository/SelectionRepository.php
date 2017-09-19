<?php

/**
 * (c) Jack Davyd <lex9612007@rambler.ru>
 */

namespace SIP\ResourceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\CountWalker as DoctrineCountWalker;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\Query\Helper as QueryHelper;

class SelectionRepository extends EntityRepository
{
    /**
     * @param int $limit
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSelectObjectsQb()
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s, o, i, si, c, l, r')
            ->join('s.realty', 'o')
            ->leftjoin('o.image', 'i')
            ->leftjoin('o.secondImage', 'si')
            ->leftjoin('o.locality', 'l')
            ->leftjoin('l.road', 'r')
            ->leftjoin('o.currency', 'c')
            ->where('o.publish = :publish')->setParameter('publish', true)
            ->orderBy('s.position', 'ASC')
        ;

        return $qb;
    }

    /**
     * @param $id integer Road id
     * @param $limit integer Limit locality of objects on road
     * @return array SIP\ResourceBundle\Entity\Object
     */
    public function getSelectObjects($limit = 0)
    {
        return $this->getSelectObjectsQb($limit)->getQuery()->getResult();
    }
}
