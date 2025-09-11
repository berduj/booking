<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnableableEntity
{
    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    public function getEnabledClass(): string
    {
        return $this->enabled ? 'on' : 'off';
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function toggleEnabled(): self
    {
        $this->enabled = !$this->enabled;

        return $this;
    }
}
