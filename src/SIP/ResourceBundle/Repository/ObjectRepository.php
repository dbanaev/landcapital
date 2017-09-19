<?php

/**
 * (c) Jack Davyd <lex9612007@rambler.ru>
 */

namespace SIP\ResourceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;

use SIP\ResourceBundle\Entity\Object;

class ObjectRepository extends EntityRepository {

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function mapFilter(Request $request, $limit = 0) {
        $road = (int) $request->get('road');
        $object_name = $request->get('object_name');
        $village = (int) $request->get('village');
        $locality = (int) $request->get('locality');
        $mkad_distance = (int) $request->get('mkad-distance');
        $dealType = $request->get('dealType');

        $qb = $this->createQueryBuilder('o')
                ->select('o, i, c, l, r, si')
                ->innerJoin('o.locality', 'l')
                ->leftjoin('l.road', 'r')
                ->leftjoin('o.secondImage', 'si')
                ->join('o.currency', 'c')
                ->leftjoin('o.image', 'i')
                ->where('o.publish = :publish')->setParameter('publish', true)
        ;

        if ((int) $limit)
            $qb->setMaxResults($limit);
        if ($road)
            $qb->andWhere('l.road = :road')->setParameter('road', $road);
        if ($locality)
            $qb->andWhere('o.locality = :locality')->setParameter('locality', $locality);
        //будем надеяться что фремворк сам экранирует и иньекцию не получим
        if ($object_name)
            $qb->andWhere("o.name LIKE '%{$object_name}%'");
        if ($mkad_distance)
            $qb->andWhere("o.distance <= " . $mkad_distance);
        if ($village)
            $qb->andWhere("o.village = :village")->setParameter('village', $village);
        if ($dealType)
            $qb->andWhere("o.dealType = :dealType")->setParameter('dealType', $dealType);

        return $qb;
    }

    /**
     * @param $limit
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getObjectsQb($limit)
    {
        $qb = $this->createQueryBuilder('lo')
            ->select('lo, i, si, c, l, r')
            ->leftjoin('lo.image', 'i')
            ->leftjoin('lo.secondImage', 'si')
            ->leftjoin('lo.locality', 'l')
            ->leftjoin('l.road', 'r')
            ->join('lo.currency', 'c')
            ->where('lo.publish = :publish')->setParameter('publish', true)
            ->orderBy('lo.id', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb;
    }

    /**
     * @param integer $limit
     * @return array SIP\ResourceBundle\Entity\Object
     */
    public function lastObjects($limit)
    {
        return $this->getObjectsQb($limit)->getQuery()->getResult();
    }

    /**
     * @param $id integer Id of object
     * @return object SIP\ResourceBundle\Entity\Object
     */
    public function getСardObject($id) {
        $object = $this->findOneById((int) $id);
        if ($object->getCurrency()->getCode() !== 'RUR') {
            $object->rurValFrom = (float) $object->getPriceFrom() * $object->getCurrency()->getRatio();
        }
        return $object;
    }

    /**
     * @param $object \SIP\ResourceBundle\Entity\Object
     * @param $limit integer Limit limit of similar objects
     * @return array SIP\ResourceBundle\Entity\Object
     */
    public function similarObjects(\SIP\ResourceBundle\Entity\Object $object, $limit = 0) {

        $objectId = $object->getId();

        $dealType = $object->getDealType();
        $road = $object->getLocality()->getRoad();
        $locality = $object->getLocality();

        $qb = $this->createQueryBuilder('o');
        $qb
            ->leftJoin('o.locality', 'l')
            ->leftJoin('l.road', 'r')
            ->orderBy('o.position', 'ASC')
            ->orderBy('o.created', 'DESC')
            ->select('o, l, r')
        ;
        
        $qb->orWhere('o.locality = :locality')->setParameter('locality', $locality);
        $qb->orWhere('l.road = :road')->setParameter('road', $road);

        $qb->andWhere('o.publish = :publish')->setParameter('publish', true);
        $qb->andWhere('o.id <> :id')->setParameter('id', $objectId);
        $qb->andWhere('o.dealType = :dealType')->setParameter('dealType', $dealType);


        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $object \SIP\ResourceBundle\Entity\Object
     * @param $limit integer Limit limit of similar objects
     * @return array SIP\ResourceBundle\Entity\Object
     */
    public function favoritesObjects($limit = 0) {
        $favorites = json_decode($_COOKIE['feature']);
        if (!$favorites) {
            return [];
        }
        $qb = $this->createQueryBuilder('o')
                ->select('o, i')
                ->leftjoin('o.image', 'i')
                ->groupBy('o.id')
                ->where('o.publish = :publish')->setParameter('publish', true)
                ->orderBy('o.position', 'ASC')
                ->orderBy('o.created', 'DESC')
        ;
        $qb->andWhere($qb->expr()->in('o.id', $favorites));
        if ((int) $limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @return array SIP\ResourceBundle\Entity\Object
     */
    public function search(Request $request, $limit = 0) {
        $search = array_filter(array_merge($request->query->all(), $request->request->all()));

        $qb = $this->createQueryBuilder('o')
                ->select('o, i, c, l, r, si')
                ->innerJoin('o.locality', 'l')
                ->leftJoin('l.road', 'r')
                ->leftJoin('o.secondImage', 'si')
                ->innerJoin('o.currency', 'c')
                ->leftjoin('o.image', 'i')
                ->where('o.publish = :publish')->setParameter('publish', true)
                ->orderBy('o.position', 'ASC')
//                ->groupBy('o.coordinates')
        ;

        if ($limit)
            $qb->setMaxResults($limit);
        
        if(!$search){
            $qb->andWhere('l.road = 1');
        }

        $minMaxValues = Object::getMinMaxValues();

        foreach ($search as $index => $searchItem) {

            switch ($index) {
                case 'road':
                    $qb->andWhere('l.road = :road')->setParameter('road', $searchItem);
                    break;
                case 'object_name':
                    if (trim($searchItem))
                        $qb->andWhere("o.name LIKE '%" . $searchItem . "%'");
                    break;
                case 'mkad_distance':
                    if ($minMaxValues['min_distance'] < (int)$searchItem)
                        $qb->andWhere("o.distance <= " . (int) $searchItem);
                    break;
                case 'locality':
                    $qb->andWhere("o.locality = :locality")->setParameter('locality', $searchItem);
                    break;
                case 'village':
                    $qb->andWhere("o.village = :village")->setParameter('village', $searchItem);
                    break;
                case 'dealType':
                    $qb->andWhere("o.dealType = :dealType")->setParameter('dealType', $searchItem);
                    break;
                case 'price_min':
                    $value = (int)$searchItem;
                    if (isset($search['dealType']) and $search['dealType'] == Object::DEAL_TYPE_SELL)
                        $value = (float)$searchItem * 1000000;

                    if ($minMaxValues['min_price'] < $value)
                        $qb->andWhere("o.priceFrom >= :priceFrom")->setParameter('priceFrom', $value);
                    break;
                case 'price_max':
                    $value = (int)$searchItem;
                    if (isset($search['dealType']) and $search['dealType'] == Object::DEAL_TYPE_SELL)
                        $value = (float)$searchItem * 1000000;

                    if ($minMaxValues['max_price'] > $value)
                        $qb->andWhere("o.priceFrom <= :priceTo")->setParameter('priceTo', $value);
                    break;
                case 'type':
                    $qb->andWhere('o.type = :type')->setParameter('type', $searchItem);
                    break;
                case 'min_house':
                    if ($minMaxValues['min_house'] < $searchItem)
                        $qb->andWhere('o.house >= :min_house')->setParameter('min_house', $searchItem);
                    break;
                case 'max_house':
                    if ($minMaxValues['max_house'] > $searchItem)
                        $qb->andWhere('o.house <= :max_house')->setParameter('max_house', $searchItem);
                    break;
                case 'min_area':
                    if ($minMaxValues['min_area'] < $searchItem)
                        $qb->andWhere('o.area >= :min_area')->setParameter('min_area', $searchItem);
                    break;
                case 'max_area':
                    if ($minMaxValues['max_area'] > $searchItem)
                        $qb->andWhere('o.area <= :max_area')->setParameter('max_area', $searchItem);
                    break;
                case 'interval':                                       
                    $qb->andWhere($qb->expr()->between('o.created', ':start', ':end'))
                            ->setParameter('start', new \DateTime('-'.$searchItem))
                            ->setParameter('end', new \DateTime);
                    break;
                case 'isSecurity':
                    if ($searchItem)
                        $qb->andWhere("o.isSecurity = :isSecurity")->setParameter('isSecurity', true);
                    break;
                case 'isHasPool':
                    if ($searchItem)
                        $qb->andWhere("o.isHasPool = :isHasPool")->setParameter('isHasPool', true);
                    break;
                case 'isFull':
                    if ($searchItem)
                        $qb->andWhere("o.isFull = :isFull")->setParameter('isFull', true);
                    break;
                case 'isUnderFinish':
                    if ($searchItem)
                        $qb->andWhere("o.isUnderFinish = :isUnderFinish")->setParameter('isUnderFinish', true);
                    break;
                case 'isFurnished':
                    if ($searchItem)
                        $qb->andWhere("o.isFurnished = :isFurnished")->setParameter('isFurnished', true);
                    break;
                case 'isUndeveloped':
                    if ($searchItem)
                        $qb->andWhere("o.isUndeveloped = :isUndeveloped")->setParameter('isUndeveloped', true);
                    break;
            }
        }

        if (!isset($search['road']))
            $qb->andWhere('l.road IN (1,4)');

        return $qb;
    }

}
