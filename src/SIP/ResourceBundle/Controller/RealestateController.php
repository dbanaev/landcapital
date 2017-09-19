<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Controller;

use SIP\ResourceBundle\Entity\Object;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

class RealestateController extends Controller
{
    /**
     * @Route("/residential-estate", name="sip_resource_residential_estate")
     * @Template()
     * @return array
     */
    public function residentialAction(Request $request)
    {
        $mkad_distance = (int) $request->get('mkad-distance');        
        $limit = $request->get('limit', 20);
        $page = $request->get('page' ,1);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Repository\ObjectRepository $repository */
        $repository = $em->getRepository('SIP\ResourceBundle\Entity\Object');

        $roads = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Road')->findAll();
        $villages = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Village')->findAll();
        $roadList = [];
        foreach ($roads as $road) {
            $roadList[] = [
                'id' => $road->getId(),
                'name' => $road->getName(),
            ];
        }

        $villageList = [];
        foreach ($villages as $village) {
            $villageList[] = [
                'id' => $village->getId(),
                'name' => $village->getName(),
            ];
        }

        $qb = $repository->search($request);
        $qb->andWhere('o.dealType = :dealType')->setParameter('dealType', Object::DEAL_TYPE_SELL);

        $query = $qb->getQuery();
        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getResult(), $page, $limit);

        $metadata = null;
        if ($request->get('road')) {
            /** @var \SIP\ResourceBundle\Entity\Lists\Road $road */
            $road = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Road')->find($request->get('road'));
            if ($road) {
                $metadata = array(
                    'title'       => $road->getTitle(),
                    'description' => $road->getDescription(),
                    'keywords'    => $road->getKeywords(),
                );
            }
        }
        if ($request->get('locality')) {
            /** @var \SIP\ResourceBundle\Entity\Lists\Locality $locality */
            $locality = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Locality')->find($request->get('locality'));
            if ($locality) {
                $metadata = array(
                    'title'       => $locality->getTitle(),
                    'description' => $locality->getDescription(),
                    'keywords'    => $locality->getKeywords(),
                );
            }
        }
        
        return array(
            'villages'  => $villageList,
            'roads'     => $roadList,
            'MaxMinVal' => Object::getMinMaxValues(),
            'objects'   => $pagination,
            'distance'  => $mkad_distance,
            'params'    => array(
                'limit'         => $limit,
                'selectVillage' => $request->get('village'),
                'selectRoad'    => $request->get('road'),
                'selectedDealType' => Object::DEAL_TYPE_SELL,
                'metadata'         => $metadata
            ),
            'request'=> $request->query->all(),
            'types'=>Object::getTypes(),
            'dealtypes'=>Object::getDealTypes(),
        );
    }

    /**
     * @Route("/residential-estate/{road}/{locality}", name="sip_resource_residential_estate_forward")
     * @Template()
     * @return array
     */
    public function forwardAction($road, $locality){
        $em = $this->getDoctrine()->getManager();

        $road = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Road')->findOneBy(array('alias' => $road));
        $loc = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Locality')->findOneBy(array('alias' => $locality));

        return $this->forward(
            '\SIP\\ResourceBundle\\Controller\\RealestateController::residentialAction',
            array(),
            array('road' => $road->getId(), 'locality' => $loc->getId())
        );
    }

    /**
     * @Route("/list/rent", name="sip_resource_rent")
     * @Template()
     * @return array
     */
    public function rentAction(Request $request)
    {
        $mkad_distance = (int) $request->get('mkad-distance');
        $limit = $request->get('limit', 20);
        $page = $request->get('page' ,1);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Repository\ObjectRepository $repository */
        $repository = $em->getRepository('SIP\ResourceBundle\Entity\Object');

        $roads = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Road')->findAll();
        $roadList = [];
        foreach ($roads as $road) {
            $roadList[] = [
                'id' => $road->getId(),
                'name' => $road->getName(),
            ];
        }

        $qb = $repository->search($request);
        $qb->andWhere('o.dealType = :dealType')->setParameter('dealType', Object::DEAL_TYPE_RENT);

        $query = $qb->getQuery();
        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getResult(), $page, $limit);

        $metadata = null;
        if ($request->get('road')) {
            /** @var \SIP\ResourceBundle\Entity\Lists\Road $road */
            $road = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Road')->find($request->get('road'));
            if ($road) {
                $metadata = array(
                    'title'       => $road->getTitle(),
                    'description' => $road->getDescription(),
                    'keywords'    => $road->getKeywords(),
                );
            }
        }
        if ($request->get('locality')) {
            /** @var \SIP\ResourceBundle\Entity\Lists\Locality $locality */
            $locality = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Locality')->find($request->get('locality'));
            if ($locality) {
                $metadata = array(
                    'title'       => $locality->getTitle(),
                    'description' => $locality->getDescription(),
                    'keywords'    => $locality->getKeywords(),
                );
            }
        }

        return array(
            'roads'     => $roadList,
            'MaxMinVal' => Object::getMinMaxValues(),
            'objects'   => $pagination,
            'distance'  => $mkad_distance,
            'params'    => array(
                'limit'            => $limit,
                'selectVillage'    => $request->get('village'),
                'selectRoad'       => $request->get('road'),
                'selectedDealType' => Object::DEAL_TYPE_RENT,
                'metadata'         => $metadata
            ),
            'types'=>Object::getTypes(),
            'request'=> $request->query->all(),
            'dealtypes'=>Object::getDealTypes(),
        );
    }
}