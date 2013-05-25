<?php

namespace Kendoctor\Bundle\DocumentorBundle\Versionable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Kendoctor\Bundle\DocumentorBundle\Entity\Document;
use Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DocumentVersionableSubscriber
 *
 * @author bobo
 */
class DocumentVersionableSubscriber implements EventSubscriber {

    public function getSubscribedEvents() {
        return array(
            'onFlush'
        );
    }

    public function onFlush(OnFlushEventArgs $eventArgs) {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {

            $this->logVersion($entity, $em, $uow, true);
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {

            $this->logVersion($entity, $em, $uow, false);
        }
    }

    public function logVersion($entity, $em, $uow, $isNew) {

        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof Document) {
            if ($isNew) {
                $versionLog = new DocumentVersion();
                $versionLog->setContent($entity->getBody());
                $versionLog->setTitle($entity->getTitle());
                $versionLog->setIsReleased(true);
                $versionLog->setVersion($entity->getVersion());
                $versionLog->setLang($entity->getLocale());
                $versionLog->setCreatedAt(new \DateTime());
                $versionLog->setDocument($entity);
                $versionLog->setDocumentHash($entity->getCurrentVersionHash());
                $em->persist($versionLog);

                $metaVersionLog = $em->getClassMetadata(get_class($versionLog));
                $uow->computeChangeSet($metaVersionLog, $versionLog);
            } else {
                $query = $em->createQuery("
                    SELECT v FROM KendoctorDocumentorBundle:DocumentVersion v
                    JOIN v.document d
                    WHERE v.documentHash = :hash AND d.id = :documentId
                ")
                        ->setParameter("hash", $entity->getCurrentVersionHash())
                        ->setParameter("documentId", $entity->getId());
                $version = $query->getSingleResult();
                $isReleased = $version->getIsReleased();
                if($isReleased)  $version->setIsReleased(false);
                $oldVersion = $version->getVersion();
                $version->setDocumentHash(md5(uniqid(rand(), true)));
                $version->setVersion(md5(uniqid(rand(), true)));

                $em->persist($version);
                $metaVersion = $em->getClassMetadata(get_class($version));
                $uow->computeChangeSet($metaVersion, $version);


                $versionLog = new DocumentVersion();
                $versionLog->setContent($entity->getBody());
                $versionLog->setTitle($entity->getTitle());
                $versionLog->setIsReleased($isReleased);
                $versionLog->setVersion($oldVersion);
                $versionLog->setLang($entity->getLocale());
                $versionLog->setCreatedAt(new \DateTime());
                $versionLog->setDocument($entity);
                $versionLog->setDocumentHash($entity->getCurrentVersionHash());
                $em->persist($versionLog);

                $metaVersionLog = $em->getClassMetadata(get_class($versionLog));
                $uow->computeChangeSet($metaVersionLog, $versionLog);
            }
        }
    }

}