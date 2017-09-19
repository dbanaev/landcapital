<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller
{

    /**
     * @Route("/ajax/village", name="sip_ajax_village")
     * @return array
     */
    public function villageAction(Request $request)
    {
    	$value = $request->get('term');
        $localityId = $request->get('locality');
        $noName = $request->get('noname');

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Entity\Lists\Village[] $members */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Village')
            ->createQueryBuilder('c');
        if(!(int)$noName){
            $qb->where("c.name LIKE '%{$value}%'");
        }
        if ($localityId) {
            $qb->andWhere('c.locality = :localityId')->setParameter('localityId', $localityId);
        }
        $qb->orderBy('c.name', 'ASC');
        $members = $qb->getQuery()->getResult();

        $json = array();
        foreach ($members as $member) {
            $json[] = array(
                'label' => $member->getName(),
                'value' => $member->getId()
            );
        }

        return new JsonResponse($json);
    }
    
    /**
     * @Route("/ajax/village/road", name="sip_ajax_village_road")
     * @return array
     */
    public function villageRoadAction(Request $request)
    {
        $roadId = $request->get('id');        
        /** @var \Doctrine\ORM\EntityManager $em */
        
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Entity\Lists\Village[] $members */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Village')                
            ->createQueryBuilder('v')
            ->select('v.id, v.name')    
            ->join('v.locality','l')
            ->where("l.road = " . (int)$roadId)            
            ->orderBy('v.name')    
            ;
        
        return new JsonResponse($qb->getQuery()->getResult());
    }

    /**
     * @Route("/ajax/locality/road", name="sip_ajax_locality_road")
     * @return array
     */
    public function localityRoadAction(Request $request)
    {
        $roadId = $request->get('id');
        /** @var \Doctrine\ORM\EntityManager $em */

        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Entity\Lists\Locality[] $members */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Locality')
            ->createQueryBuilder('l')
            ->select('l.id, l.name')
            ->where("l.road = " . (int)$roadId)
            ->orderBy('l.name')
        ;

        return new JsonResponse($qb->getQuery()->getResult());
    }
    
    /**
     * @Route("/ajax/object/search", name="sip_ajax_object_search")
     * @return array
     */
    public function ajaxSearchAction(Request $request)
    {   
        $limit = $request->get('limit', 20);
        $page  = $request->get('page', 1);
        $route = $request->get('route', 'sip_resource_residential_estate');

        /** @var \Doctrine\ORM\EntityManager $em */        
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Entity\Object $members */
        $objects = $em->getRepository('SIP\ResourceBundle\Entity\Object')->search($request)->getQuery()->getResult();
        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($objects, $page, $limit);
        $pagination->setUsedRoute($route);

        $search = array_filter(array_merge($request->query->all(), $request->request->all()));
        foreach ($search as $index => $searchItem)
            $pagination->setParam($index, $searchItem);

        $page = $this->render('SIPResourceBundle:Realestate:list_objects.html.twig', ['objects'=>$pagination])->getContent();
        $map = [];
        foreach ($objects as $object) {
            if ($object->getCoordinates()) {
                if ($object->getDealType() == 'sell') {
                    switch ($object->getType()) {
                        case 'land':
                            $icon = 'land.png';
                            break;
                        default:
                            $icon = 'estate_gr.png';
                    }
                } else {
                    $icon = 'estate.png';
                }
                $latLngArr = explode(',', $object->getCoordinates());
                $latLng = array(
                    (float) $latLngArr[0],
                    (float) $latLngArr[1],
                );
                $map[] = [
                    'latLng' => $latLng,
                    'options' => array(
                        'icon' => "/bundles/sipresource/images/{$icon}",
                        'shadow' => '/bundles/sipresource/images/shadow.png'
                    ),
                    'data' => $this->render('SIPResourceBundle::mapMarker.hml.twig', array('data' => $object))->getContent()
                ];
            }
        }
        
        return new JsonResponse(['page'=>$page, 'map'=>$map]);
    }
}