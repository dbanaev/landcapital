<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */
$loader = require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../app/AppKernel.php';

use SIP\ResourceBundle\Entity\Object;
use SIP\ResourceBundle\Entity\Media\ObjectHasMedia;
use SIP\ResourceBundle\Entity\Media\Media;
use SIP\ResourceBundle\Entity\Lists\Currency;

$kernel = new AppKernel('dev', true);

$kernel->loadClassCache();
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();
/** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
$doctrine = $container->get('doctrine');

/** @var \Doctrine\ORM\EntityRepository $objectRepository */
$objectRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Object');
/** @var \Doctrine\ORM\EntityRepository $localityRepository */
$localityRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Locality');
/** @var \Doctrine\ORM\EntityRepository $villageRepository */
$villageRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Village');
/** @var \Doctrine\ORM\EntityRepository $villageRepository */
$roadRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Road');

$em = $doctrine->getManager();

//locality
$localities = $localityRepository->findAll();

foreach ($localities as $locality) {
    $qb = $objectRepository->createQueryBuilder('o');
    $qb->select('COUNT(o)');
    $qb->where('o.locality = ' . $locality->getId());    
    $locality->setCount($qb->getQuery()->getSingleScalarResult());
    $em->persist($locality);
}
$em->flush();
echo 'Обработаны locality' . "\n";

//village
$villages = $villageRepository->findAll();
foreach ($villages as $village) {
    $qb = $objectRepository->createQueryBuilder('o');
    $qb->select('COUNT(o)');
    $qb->where('o.village = ' . $village->getId());
    $qb->getQuery()->getSingleScalarResult();
    $village->setCount($qb->getQuery()->getSingleScalarResult());
    $em->persist($village);
}
$em->flush();
echo 'Обработаны village' . "\n";

//road
$roads = $roadRepository->findAll();
foreach ($roads as $road) {
    $qb = $objectRepository->createQueryBuilder('o');
    $qb->select('COUNT(o)');
    $qb->join('o.locality', 'l');
    $qb->join('l.road', 'r');        
    $qb->where('r.id = ' . $road->getId());
    $road->setCount((int)$qb->getQuery()->getSingleScalarResult());
    $em->persist($road);
}
$em->flush();
echo 'Обработаны road' . "\n";
echo 'Все' . "\n";

