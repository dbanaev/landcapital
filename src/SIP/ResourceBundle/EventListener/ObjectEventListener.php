<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */

namespace SIP\ResourceBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SIP\ResourceBundle\Entity\Object;

class ObjectEventListener {

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args) {
        /* @var \SIP\ResourceBundle\Entity\Advert $entity */
        $entity = $args->getEntity();
        if ($entity instanceof Object) {
            $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
            foreach (['village', 'locality'] as $value) {
                if (isset($changes[$value])) {
                    /* @var \SIP\ResourceBundle\Entity\Lists\$value $oldObject */
                    $oldObject = $changes[$value][0];
                    /* @var \SIP\ResourceBundle\Entity\Lists\$value $newObject */
                    $newObject = $changes[$value][1];

                    if ($newObject) {
                        $newObject->setCount($newObject->getCount() + 1);
                        $args->getEntityManager()->persist($newObject);
                    }

                    if ($oldObject && $oldObject->getCount() > 0) {
                        $oldObject->setCount($oldObject->getCount() - 1);
                        $args->getEntityManager()->persist($oldObject);
                    }
                    if ($value === 'locality') {
                        if ($newObject) {
                            $newRoad = $newObject->getRoad();
                            $newRoad->setCount($newObject->getRoad()->getCount() + 1);
                            $args->getEntityManager()->persist($newRoad);
                        }

                        if ($oldObject && $oldObject->getRoad()->getCount() > 0) {
                            $oldRoad = $oldObject->getRoad();
                            $oldRoad->setCount($oldRoad->getCount() - 1);
                            $args->getEntityManager()->persist($oldRoad);
                        }
                    }

                    $args->getEntityManager()->flush();
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args) {
        /* @var \SIP\ResourceBundle\Entity\Object $entity */
        $entity = $args->getEntity();
        if ($entity instanceof Object) {
            $updates = [];
            if ($entity->getLocality())
                $updates[] = $entity->getLocality();
            if ($entity->getVillage())
                $updates[] = $entity->getVillage();
            if ($entity->getLocality() && $entity->getLocality()->getRoad())
                $updates[] = $entity->getLocality()->getRoad();

            foreach ($updates as $update) {
                if ($update->getCount() > 0) {
                    $update->setCount($update->getCount() - 1);
                    $args->getEntityManager()->persist($update);
                }
            }
            $args->getEntityManager()->flush();
        }        
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args) {
        /* @var \SIP\ResourceBundle\Entity\Advert $entity */
        $entity = $args->getEntity();
        if ($entity instanceof Object) {
            $updates = [
                $entity->getLocality(),
                $entity->getVillage(),
                $entity->getLocality()? $entity->getLocality()->getRoad(): null
            ];
            foreach ($updates as $update) {
                if ($update) {
                    $update->setCount($update->getCount() + 1);
                    $args->getEntityManager()->persist($update);
                }
            }
            $args->getEntityManager()->flush();
        }
    }

}
