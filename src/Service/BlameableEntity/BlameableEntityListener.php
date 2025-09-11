<?php

declare(strict_types=1);

namespace App\Service\BlameableEntity;

use App\Security\LoggdedInUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush)]
class BlameableEntityListener
{
    public function __construct(
        private readonly LoggdedInUser $loggdedInUser,
    ) {
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $uow = $eventArgs->getObjectManager()->getUnitOfWork();
        $insertions = $uow->getScheduledEntityInsertions();

        foreach ($insertions as $entity) {
            $this->handleCreatedAt($entity);
            $this->handleCreatedBy($entity);

            $classMetaData = $eventArgs->getObjectManager()->getClassMetadata(get_class($entity));
            $uow->recomputeSingleEntityChangeSet($classMetaData, $entity);
        }

        $updates = $uow->getScheduledEntityUpdates();

        foreach ($updates as $entity) {
            $this->handleUpdatedAt($entity);
            $this->handleUpdatedBy($entity);

            $classMetaData = $eventArgs->getObjectManager()->getClassMetadata(get_class($entity));
            $uow->recomputeSingleEntityChangeSet($classMetaData, $entity);
        }
    }

    private function handleCreatedAt(object $entity): void
    {
        if (method_exists($entity, 'setCreatedAt') && method_exists($entity, 'getCreatedAt')) {
            if (!$entity->getCreatedAt()) {
                $entity->setCreatedAt(new \DateTime());
            }
        }
    }

    private function handleUpdatedAt(object $entity): void
    {
        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    private function handleCreatedBy(object $entity): void
    {
        try {
            $user = $this->loggdedInUser->getUser()->getUserIdentifier();
            if (method_exists($entity, 'setCreatedBy')) {
                $entity->setCreatedBy($user);
            }
        } catch (\Exception $e) {
            $user = '';
        }
    }

    private function handleUpdatedBy(object $entity): void
    {
        try {
            $user = $this->loggdedInUser->getUser()->getUserIdentifier();
            if (method_exists($entity, 'setUpdatedBy')) {
                $entity->setUpdatedBy($user);
            }
        } catch (\Exception $e) {
            $user = null;
        }
    }
}
