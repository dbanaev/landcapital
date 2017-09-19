<?php

/**
 * (c) Jack Davyd <lex9612007@rambler.ru>
 */

namespace SIP\ResourceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\CountWalker as DoctrineCountWalker;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\Query\Helper as QueryHelper;

class RoadRepository extends EntityRepository {

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager         $em    The EntityManager to use.
     * @param Mapping\ClassMetadata $class The class descriptor.
     */
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class) {
        parent::__construct($em, $class);
        $this->createQB();
    }

    /**
     * @return createQueryBuilde
     */
    public function createQB() {
        $this->listQB = $this->createQueryBuilder('r');
        return $this;
    }

    /** 
     * @param $limit integer Limit locality of objects on road
     * @param $columns integer Count Road in row
     * @return array Roads and Locality and count of objects
     */
    public function getRoadLocality($limit = 0, $columns = 0) {
        $qb = $this->listQB
                ->select('r.id, r.name r_name, l.id l_id, l.name l_name, l.count l_count')
                ->innerJoin('SIP\ResourceBundle\Entity\Lists\Locality', 'l', 'WITH', 'r.id = l.road')
                //->innerJoin('SIP\ResourceBundle\Entity\Lists\Village', 'v', 'WITH', 'v.locality = l.id')
                ->where('l.count > 0')
                ->orderBy('l.count', 'DESC')
        ;
       
        $result = [];        
        foreach ($qb->getQuery()->getResult() as $res) {
            $result[$res['r_name']][] = $res;
        }

        if ((int) $limit && $result) {
            $result = array_map(function($el) use ($limit) {
                return array_slice($el, 0, $limit);
            }, $result);
        }
        
        if((int)$columns && $result){
            $result = array_chunk($result, $columns, true);
        }
        
        return $result;
    }
}
