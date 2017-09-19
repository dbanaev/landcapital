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
/** @var
 * \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
$doctrine = $container->get('doctrine');

$em = $doctrine->getManager();

/** @var \Doctrine\ORM\EntityRepository $objectRepository */
$objectRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Object');

/** @var SIP\ResourceBundle\Entity\Object $object */
$objects = $objectRepository->findAll();
foreach ($objects as $object) {
    if (count($object->getGallery()) > 1) {
        foreach ($object->getGallery() as $gallery) {
            $object->setSecondImage($gallery->getImage());
            break;
        }
    } else {
        $object->setSecondImage($object->getImage());
    }

    $em->persist($object);
    $em->flush();

    echo "Update {$object->getId()}\n";
}