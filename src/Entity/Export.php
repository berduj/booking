<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\ExportRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExportRepository::class)]
class Export implements SortableEntityInterface, \Stringable
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $requete = null;

    /**
     * @var Collection<int, Profil>
     */
    #[ORM\ManyToMany(targetEntity: Profil::class, inversedBy: 'exports')]
    private Collection $profils;

    public function __construct()
    {
        $this->profils = new ArrayCollection();
    }

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

    public function getRequete(): ?string
    {
        return $this->requete;
    }

    public function setRequete(?string $requete): static
    {
        $this->requete = $requete;

        return $this;
    }

    /**
     * @return Collection<int, Profil>
     */
    public function getProfils(): Collection
    {
        return $this->profils;
    }

    public function addProfil(Profil $profil): static
    {
        if (!$this->profils->contains($profil)) {
            $this->profils->add($profil);
        }

        return $this;
    }

    public function removeProfil(Profil $profil): static
    {
        $this->profils->removeElement($profil);

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->libelle;
    }
}
