<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\MetierRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MetierRepository::class)]
class Metier implements SortableEntityInterface, \Stringable
{
    use BlameableEntity;
    use SortableEntity;
    use EnableableEntity;

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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->libelle;
    }
}
