<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait BlameableEntity
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $createdBy = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $updatedBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function createdByAt(): string
    {
        $created = 'Le '.$this->getCreatedAt()?->format('d/m/Y H:i:s').' par '.$this->getCreatedBy();

        return $created;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function updatedByAt(): bool|string
    {
        if (!$this->getUpdatedAt()) {
            return false;
        }
        $updated = 'Le '.$this->getUpdatedAt()->format('d/m/Y H:i:s').' par '.$this->getUpdatedBy();

        return $updated;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(string $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
