<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ModaliteContactRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModaliteContactRepository::class)]
class ModaliteContact implements SortableEntityInterface, \Stringable
{
    use Traits\BlameableEntity;
    use Traits\SortableEntity;
    use Traits\EnableableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->getLibelle();
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
