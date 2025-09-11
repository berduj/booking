<?php

declare(strict_types=1);

namespace App\Service\SortableEntity;

use Symfony\Contracts\EventDispatcher\Event;

class SortableEntityEvent extends Event
{
    /**
     * @param array <int, mixed> $order
     */
    public function __construct(private readonly array $order, private readonly string $entity)
    {
        if (!class_exists($entity)) {
            throw new \InvalidArgumentException('Entity "'.$entity.'" does not exist');
        }

        $object = new $this->entity();

        if (!$object instanceof SortableEntityInterface) {
            throw new \InvalidArgumentException('Class "'.$entity.'" does not implement SortableEntityInterface');
        }
    }

    /**
     * @return array<int, mixed>
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }
}
