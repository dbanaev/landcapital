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
/** @var \Doctrine\ORM\EntityRepository $currencyRepository */
$currencyRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Currency');
/** @var \Doctrine\ORM\EntityRepository $villageRepository */
$villageRepository = $doctrine->getManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Village');

//details of the database connection
$host = 'localhost';
$dbname = '999_landcapital';
$user = 'root';
$passw = '';
$charset = 'utf8';
//table name
$tbl = 'estate';

// Соединяемся с БД
try {
    $dbh = new \PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $user, $passw);
} catch (\PDOException $e) {
    die($e->getMessage());
}

$em = $doctrine->getManager();

$sql = 'SELECT * FROM `' . $tbl . '`';
$date = $dbh->query($sql);
if (!$date) {
    echo 'Таблица пуста';
    exit;
}

while ($row = $date->fetch(\PDO::FETCH_OBJ)) {
    if (!($object = $objectRepository->find($row->id))) {
        $object = new Object();
        $object->setId($row->id);
        $object->setName($row->estate_name);
        $object->setPriceFrom((int) $row->price);
        $object->setPriceTo((int) $row->price2);
        if ($row->locality_id) {
            $object->setLocality($localityRepository->find($row->locality_id));
        }
        $object->setCurrency(getCyrrercy($row->currency, $currencyRepository, $em));
        If ($row->village_id) {
            $object->setVillage($villageRepository->find($row->village_id));
        }

        $coordArr = explode(',', $row->yamap);
        if (count($coordArr) > 1) {
            $object->setCoordinates("{$coordArr[1]},{$coordArr[0]}");

        }
        $object->setDistance((int) $row->distance);
        $object->setHouse((int) $row->size_house);
        $object->setArea((int) $row->size_land);
        $object->setAddInfo($row->info);
        $object->setLandInfo($row->info_land);
        $object->setLayout($row->info_plan);
        $object->setCommunication($row->info_communication);
        $object->setPrintInfo($row->info_print);
        $object->setTitle($row->title);
        $object->setDescription($row->description);
        $object->setKeywords($row->keywords);

        $object->setDealType($row->deal_type);
        $object->setType($row->type);
        $object->setEstateType($row->estate_type);

        $images = getImages((int) $row->id, $dbh);
        if ($images) {
            $em->persist($object);

            $metadata = $em->getClassMetaData(get_class($object));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $image = reset($images);
            $media = new Media;
            $file = uploadImage('http://9993999.ru/estates/' . $row->pic . '/' . $image->prefix . '_' . 'poster.jpg');
            if ($file) {
                $media->setName($file['filename']);
                $media->setBinaryContent($file['path']);
                $media->setProviderName('sonata.media.provider.image');
                $media->setProviderStatus(1);
                $media->setContext('object');
                $container->get('sonata.media.manager.media')->save($media);
                $object->setImage($media);
                @unlink($file['path']);
            }
            foreach ($images as $item) {
                $objectHasMedia = new ObjectHasMedia();
                $media = getMediaByImage($item, $container, $row);
                $objectHasMedia->setImage($media);
                $objectHasMedia->setObject($object);
                $em->persist($objectHasMedia);
            }
        }

        $em->persist($object);

        $em->flush();
        echo 'Сохранен обьект ' . $object->getId() . ", {$object->getName()}\n";
    } else {
        $object->setDealType($row->deal_type);
        $object->setType($row->type);
        $object->setEstateType($row->estate_type);

        $em->persist($object);
        $em->flush();
        echo 'Обьект уже существует' . $row->id . ", {$row->estate_name}\n";
    }
}

function uploadImage($url) {
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/tmp")) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . "/tmp", 0775, true);
    }
    $tmp = explode('/', $url);
    $filename = end($tmp);

    $file = @file_get_contents($url);
    if ($file) {
        $upload = file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/tmp/$filename", $file);
        if ($upload) {
            return ['filename' => $filename, 'path' => $_SERVER['DOCUMENT_ROOT'] . "/tmp/$filename"];
        }
    }
    return null;
}

function getImages($id, $dbh) {
    $sql = 'SELECT * FROM `estate_photo` WHERE `estate_id` = ' . $id;
    $res = $dbh->query($sql);
    $result = [];
    while ($row = $res->fetch(\PDO::FETCH_OBJ)) {
        $result[] = $row;
    }
    return $result ? $result : [];
}

function getMediaByImage($image, $container, $row) {
    $file = uploadImage('http://9993999.ru/estates/' . $row->pic . '/' . $image->prefix . '_' . 'poster.jpg');
    if ($file) {
        $media = new Media;
        $media->setName($file['filename']);
        $media->setBinaryContent($file['path']);
        $media->setProviderName('sonata.media.provider.image');
        $media->setProviderStatus(1);
        $media->setContext('object');
        $container->get('sonata.media.manager.media')->save($media);
        @unlink($file['path']);
        return $media;
    }
    return null;
}

function getCyrrercy($currency, $currencyRepository, $em) {
    $data = $currencyRepository->findOneByName($currency);
    if(!$data){
        $list = new Currency();
        $list->setName($currency);
        $list->setCode($currency);
        $list->setRatio(0);
        $em->persist($list);
        $em->flush();
        return $currencyRepository->findOneByName($currency);
    }else{
        return $data;
    }
}
