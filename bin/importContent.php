<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

use SIP\ResourceBundle\Entity\Lists\Road;
use SIP\ResourceBundle\Entity\Lists\Locality;
use SIP\ResourceBundle\Entity\Lists\Village;

$kernel = new AppKernel('dev', true);

$kernel->loadClassCache();
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();
/** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
$doctrine = $container->get('doctrine');

/** @var \Doctrine\ORM\EntityRepository $roadRepository */
$roadRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Road');
/** @var \Doctrine\ORM\EntityRepository $localityRepository */
$localityRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Locality');

// Соединяемся, выбираем базу данных
$link = mysql_connect('127.0.0.1', 'root')
or die('Не удалось соединиться: ' . mysql_error());
echo 'Соединение успешно установлено';
mysql_select_db('999_landcapital') or die('Не удалось выбрать базу данных');

$em = $doctrine->getManager();

/*
$query = 'SELECT * FROM highways';
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
$lines =array();
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $lines[] = $line;
    $road = new Road();
    $road->setId($line['id']);
    $road->setName($line['label']);
    $road->setAlias($line['alias']);
    $road->setTitle($line['title']);
    $road->setKeywords($line['keywords']);
    $road->setDescription($line['description']);
    $road->setText($line['text']);
    $em->persist($road);

    $metadata = $em->getClassMetaData(get_class($road));
    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

    $em->flush();
}
*/
/*
$query = 'SELECT * FROM locality';
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
$lines =array();
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $lines[] = $line;
    $locality = new Locality();
    $locality->setId($line['id']);
    $locality->setName($line['name']);
    $locality->setAlias($line['alias']);
    $coordArr = explode(',', $line['yamap']);
    $locality->setCoordinates("{$coordArr[1]},{$coordArr[0]}");
    $locality->setText($line['info']);
    $locality->setTitle($line['info']);
    $locality->setKeywords($line['keywords']);
    $locality->setDescription($line['description']);
    $locality->setRoad($roadRepository->find($line['id_highway']));
    $locality->setDistance($line['distance']);
    $em->persist($locality);

    $metadata = $em->getClassMetaData(get_class($locality));
    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

    $em->flush();
}
*/

$query = 'SELECT * FROM village';
$result = mysql_query($query) or die('Запрос не удался: ' . mysql_error());
$lines =array();
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $lines[] = $line;
    $village = new Village();
    $village->setId($line['id']);
    $village->setName($line['name']);
    $coordArr = explode(',', $line['yamap']);
    if (count($coordArr) > 1) {
        $village->setCoordinates("{$coordArr[1]},{$coordArr[0]}");
    }
    $village->setTitle($line['info']);
    $village->setKeywords($line['keywords']);
    $village->setDescription($line['description']);
    $locality = $localityRepository->find($line['locality_id']);
    if ($locality) {
        $village->setLocality($localityRepository->find($line['locality_id']));
    }

    $em->persist($village);

    $metadata = $em->getClassMetaData(get_class($village));
    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

    $em->flush();
}