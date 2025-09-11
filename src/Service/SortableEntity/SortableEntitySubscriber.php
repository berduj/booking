<?php

declare(strict_types=1);

namespace App\Service\SortableEntity;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SortableEntitySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SortableEntityEvent::class => 'sort',
        ];
    }

    public function sort(SortableEntityEvent $event): void
    {
        $order = $event->getOrder();
        $className = $event->getEntity(); /** @var class-string<object> $className */
        $repository = $this->entityManager->getRepository($className);

        $sortable = 1;
        foreach ($order as $id) {
            if ($item = $repository->find($id)) {
                if ($item instanceof SortableEntityInterface) {
                    $item->setSortable($sortable++);
                }
            }
        }

        $this->entityManager->flush();
    }
}
