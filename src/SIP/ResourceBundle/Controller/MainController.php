<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */

namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller {

    /**
     * @Route("/", name="sip_resource_main")
     * @Template()
     * @return array
     */
    public function indexAction(Request $request) {
        $mkad_distance = (int) $request->get('mkad-distance');

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var array max and min distance value SIP\ResourceBundle\Entity\Object $distance */
        $distance = $em->getRepository('SIP\ResourceBundle\Entity\Object')
                        ->createQueryBuilder('l')
                        ->select('MAX(l.distance) AS max_distance, MIN(l.distance) AS min_distance')
                        ->getQuery()->getSingleResult();

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

        /** @var \SIP\ResourceBundle\Entity\Object[] $lastObjects */
        $lastObjects = $em->getRepository('SIP\ResourceBundle\Entity\Object')->lastObjects(20);
        /** @var \SIP\ResourceBundle\Entity\Lists\Road[] $roadObjects */
        $roadObjects = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Road')->getRoadLocality(5, 4);
        /** @var \SIP\ResourceBundle\Entity\Lists\Selection[] $selections */
        $selections = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Selection')->getSelectObjects();

        return [
            'villages' => $villageList,
            'roads' => $roadList,
            'distance' => $distance,
            'default_distance' => $mkad_distance,
            'lastObjects' => $lastObjects,
            'roadObjects' => $roadObjects,
            'selections' => $selections
        ];
    }

    /**
     * @Route("about/", name="sip_resource_main_about")
     * @Template()
     * @return array
     */
    public function aboutAction() {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Entity\Object $qb */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Setting');

        $content = $qb->findByCode('about');

        return ['content' => $content];
    }

    /**
     * @Route("contacts/", name="sip_resource_main_contacts")
     * @Template()
     * @return array
     */
    public function contactsAction() {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Entity\Object $qb */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Setting');

        $content = $qb->findByCode('contacts');

        return ['content' => $content];
    }

}
