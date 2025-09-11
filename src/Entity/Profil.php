<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\ProfilRepository;
use App\Security\Role;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil implements \Stringable, SortableEntityInterface
{
    use BlameableEntity;
    use SortableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: false)]
    private array $roles = [];

    /**
     * @var Collection<int, Personne>
     */
    #[ORM\OneToMany(mappedBy: 'profil', targetEntity: Personne::class)]
    private Collection $personnes;

    /**
     * @var Collection<int, Export>
     */
    #[ORM\ManyToMany(targetEntity: Export::class, mappedBy: 'profils')]
    private Collection $exports;

    /**
     * @var Collection<int, Widget>
     */
    #[ORM\ManyToMany(targetEntity: Widget::class, mappedBy: 'profils')]
    private Collection $widgets;

    public function __construct()
    {
        $this->personnes = new ArrayCollection();
        $this->exports = new ArrayCollection();
        $this->widgets = new ArrayCollection();
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

    public function __toString(): string
    {
        return (string) $this->libelle;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getRolesText(): array
    {
        $ret = [];
        foreach ($this->roles as $role) {
            if (array_key_exists($role, Role::ROLES)) {
                $ret[] = Role::ROLES[$role];
            }
        }

        return array_unique($ret);
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): static
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes->add($personne);
            $personne->setProfil($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): static
    {
        if ($this->personnes->removeElement($personne)) {
            // set the owning side to null (unless already changed)
            if ($personne->getProfil() === $this) {
                $personne->setProfil(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Export>
     */
    public function getExports(): Collection
    {
        return $this->exports;
    }

    public function addExport(Export $export): static
    {
        if (!$this->exports->contains($export)) {
            $this->exports->add($export);
            $export->addProfil($this);
        }

        return $this;
    }

    public function removeExport(Export $export): static
    {
        if ($this->exports->removeElement($export)) {
            $export->removeProfil($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Widget>
     */
    public function getWidgets(): Collection
    {
        return $this->widgets;
    }

    public function addWidget(Widget $widget): static
    {
        if (!$this->widgets->contains($widget)) {
            $this->widgets->add($widget);
            $widget->addProfil($this);
        }

        return $this;
    }

    public function removeWidget(Widget $widget): static
    {
        if ($this->widgets->removeElement($widget)) {
            $widget->removeProfil($this);
        }

        return $this;
    }
}
