<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */
$loader = require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../app/AppKernel.php';

use SIP\ResourceBundle\Entity\Object;
use SIP\ResourceBundle\Entity\Media\ObjectHasMedia;
use SIP\ResourceBundle\Entity\Media\Media;
use SIP\ResourceBundle\Lib\CurrencyMethods;

$kernel = new AppKernel('dev', true);

$kernel->loadClassCache();
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();
/** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
$doctrine = $container->get('doctrine');

/** @var \Doctrine\ORM\EntityRepository $currencyRepository */
$currencyRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Currency');

$em = $doctrine->getManager();

/** @var SIP\ResourceBundle\Lib\CurrencyMethods $metods */
$metods = new CurrencyMethods();

$currencies = $currencyRepository->findAll();

/** @var array $codes Currencies codes */
$codes = array_map(function($el) {
    return $el->getCode();
}, $currencies);

/** @var SIP\ResourceBundle\Lib\CurrencyMethods\cursOnDateCbr($date, $currencies) $curss */
$curss = $metods->cursOnDateCbr(null, $codes);

foreach ($curss as $curs) {
    $currency = $currencyRepository->findOneByCode($curs->VchCode);
    $currency->setRatio($curs->RealCurs);
    $date = new \DateTime($curs->RealLastDate);
    $currency->setUpdated($date);
    $em->persist($currency);
    $em->flush();
    echo 'Обработана ' . $curs->VchCode . "\n";
}
echo 'Все' . "\n";

