<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\TagRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements SortableEntityInterface, \Stringable
{
    use BlameableEntity;
    use SortableEntity;
    use EnableableEntity;

    public const TYPE_STRUCTURE = 'Structure';
    public const TYPE_PERSONNE = 'Personne';
    public const TYPES = [self::TYPE_STRUCTURE, self::TYPE_PERSONNE];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Structure>
     */
    #[ORM\ManyToMany(targetEntity: Structure::class, mappedBy: 'tags')]
    private Collection $structures;

    #[ORM\Column(length: 255)]
    private string $type = self::TYPE_STRUCTURE;


    public function __construct(string $type)
    {
        $this->setType($type);
        $this->structures = new ArrayCollection();
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
     * @return Collection<int, Structure>
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): static
    {
        if (!$this->structures->contains($structure)) {
            $this->structures->add($structure);
            $structure->addTag($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): static
    {
        if ($this->structures->removeElement($structure)) {
            $structure->removeTag($this);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException(sprintf('Le type "%s" n\'est pas valide.', $type));
        }
        $this->type = $type;

        return $this;
    }
}
