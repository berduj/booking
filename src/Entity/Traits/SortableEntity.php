<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SortableEntity
{
    #[ORM\Column(type: 'integer')]
    private int $sortable = 9999;

    public function getSortable(): int
    {
        return $this->sortable;
    }

    public function setSortable(int $sortable): self
    {
        $this->sortable = $sortable;

        return $this;
    }
}
