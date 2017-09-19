<?php

/*
 * (c) Jack Davyd <lex9612007@rambler.ru>
 */

namespace SIP\ResourceBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SIP\ResourceBundle\Lib\CurrencyMethods;
use \SIP\ResourceBundle\Entity\Lists\Currency;

class CurrencyAdminController extends Controller {

    public function currupdateAction() {
        /** @var \SIP\ResourceBundle\Entity\Lists\Currency $currencies */
        $repository = $this->get('Doctrine')->getRepository('SIP\ResourceBundle\Entity\Lists\Currency');
        $em = $this->getDoctrine()->getManager();
        $currencies = $repository->findAll();
        /** @var array $codes Currencies codes */
        $codes = array_map(function($el) {
            return $el->getCode();
        }, $currencies);

        /** @var SIP\ResourceBundle\Lib\CurrencyMethods $metods */
        $metods = new CurrencyMethods();
        /** @var SIP\ResourceBundle\Lib\CurrencyMethods\cursOnDateCbr($date, $currencies) $curss */
        $curss = $metods->cursOnDateCbr(null, $codes);

        foreach ($curss as $curs) {
            $currency = $repository->findOneByCode($curs->VchCode);
            $currency->setRatio($curs->RealCurs);
            $date = new \DateTime($curs->RealLastDate);
            $currency->setUpdated($date);
            $em->persist($currency);
            $em->flush();
        }

        return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

}
