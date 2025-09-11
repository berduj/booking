<?php

declare(strict_types=1);

namespace App\Service\SortableEntity;

interface SortableEntityInterface
{
    public function setSortable(int $sortable): self;

    public function getSortable(): int;
}
