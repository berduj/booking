<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactInterlocuteurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactInterlocuteurRepository::class)]
class ContactInterlocuteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'interlocuteurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    #[ORM\ManyToOne(inversedBy: 'contactInterlocuteurs')]
    private ?Personne $personne = null;

    #[ORM\ManyToOne(inversedBy: 'contactInterlocuteurs')]
    private ?Structure $structure = null;

    public function __construct(Contact $contact, ?Personne $personne, ?Structure $structure)
    {
        $this->contact = $contact;
        $this->personne = $personne;
        $this->structure = $structure;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
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

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): static
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @return Collection<int, Structure>|null
     */
    public function getAvailableStructures(): ?Collection
    {
        if ($this->structure !== null) {
            return null;
        }

        if ($this->personne === null) {
            return null;
        }

        return $this->personne->getStructures();
    }

    /**
     * @return Collection<int, Personne>|null
     */
    public function getAvailablePersonnes(): ?Collection
    {
        if ($this->personne !== null) {
            return null;
        }

        if ($this->structure === null) {
            return null;
        }

        return $this->structure->getPersonnes();
    }
}
