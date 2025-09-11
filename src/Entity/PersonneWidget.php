<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\PersonneWidgetRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonneWidgetRepository::class)]
class PersonneWidget implements SortableEntityInterface
{
    use BlameableEntity;
    use SortableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'personneWidgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $personne = null;

    #[ORM\ManyToOne(inversedBy: 'personneWidgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Widget $widget = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): static
    {
        $this->personne = $personne;

        return $this;
    }

    public function getWidget(): ?Widget
    {
        return $this->widget;
    }

    public function setWidget(?Widget $widget): static
    {
        $this->widget = $widget;

        return $this;
    }

    public function __construct(Personne $personne, Widget $widget)
    {
        $this->personne = $personne;
        $this->widget = $widget;
    }
}
