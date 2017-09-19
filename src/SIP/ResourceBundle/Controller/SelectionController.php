<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class SelectionController extends Controller
{
    /**
     * @Route("/{slug}", name="sip_resource_selection")
     * @Template()
     * @return array
     */
    public function indexAction(Request $request, $slug)
    {
        $limit = $request->get('limit', 20);
        $page = $request->get('page' ,1);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \SIP\ResourceBundle\Repository\SelectionRepository $repository */
        $repository = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Selection');
        /** @var \SIP\ResourceBundle\Repository\ObjectRepository $objectRepository */
        $objectRepository = $em->getRepository('SIP\ResourceBundle\Entity\Object');

        /** @var \SIP\ResourceBundle\Entity\Lists\Selection $selection */
        $selection = $repository->findOneByAlias($slug);
        if (!$selection) {
            throw $this->createNotFoundException('The product does not exist');
        }

        $query = $objectRepository->getObjectsQb($limit)->join('lo.selection', 's')->andWhere('s.id = :selection')->setParameter('selection', $selection->getId())->getQuery();
        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query->getResult(), $page, $limit);

        return ['objects' => $pagination, 'selection' => $selection];
    }
}
