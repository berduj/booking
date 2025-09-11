<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
class Widget implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $class = null;

    /**
     * @var Collection<int, Profil>
     */
    #[ORM\ManyToMany(targetEntity: Profil::class, inversedBy: 'widgets')]
    private Collection $profils;

    /**
     * @var Collection<int, PersonneWidget>
     */
    #[ORM\OneToMany(targetEntity: PersonneWidget::class, mappedBy: 'widget')]
    private Collection $personneWidgets;

    public function __construct()
    {
        $this->profils = new ArrayCollection();
        $this->personneWidgets = new ArrayCollection();
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

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;

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

    /**
     * @return Collection<int, PersonneWidget>
     */
    public function getPersonneWidgets(): Collection
    {
        return $this->personneWidgets;
    }

    public function addPersonneWidget(PersonneWidget $personneWidget): static
    {
        if (!$this->personneWidgets->contains($personneWidget)) {
            $this->personneWidgets->add($personneWidget);
            $personneWidget->setWidget($this);
        }

        return $this;
    }

    public function removePersonneWidget(PersonneWidget $personneWidget): static
    {
        if ($this->personneWidgets->removeElement($personneWidget)) {
            // set the owning side to null (unless already changed)
            if ($personneWidget->getWidget() === $this) {
                $personneWidget->setWidget(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->libelle;
    }
}
