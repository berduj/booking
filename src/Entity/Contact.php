<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\ContactRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use App\Validator as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[Assert\Contact]
class Contact implements SortableEntityInterface, \Stringable
{
    use BlameableEntity;
    use SortableEntity;
    use EnableableEntity;

    public ?UploadedFile $file = null;
    public bool $removeFile = false;

    public ?Personne $mainPersonne = null;
    public ?Structure $mainStructure = null;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne()]
    private ?ModaliteContact $modaliteContact = null;

    #[ORM\ManyToOne(inversedBy: 'auteurContacts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $auteur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTimeInterface $date;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $compteRendu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $autresInterlocuteurs = null;

    /**
     * @var Collection<int, ContactInterlocuteur>
     */
    #[ORM\OneToMany(targetEntity: ContactInterlocuteur::class, mappedBy: 'contact', cascade: ['remove'])]
    private Collection $interlocuteurs;

    public function __construct(UserInterface $auteur)
    {
        if (!$auteur instanceof Personne) {
            throw new \InvalidArgumentException('vous devez passer une Personne');
        }

        $this->date = new \DateTime();

        $this->auteur = $auteur;
        $this->interlocuteurs = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getModaliteContact(): ?ModaliteContact
    {
        return $this->modaliteContact;
    }

    public function setModaliteContact(?ModaliteContact $modaliteContact): static
    {
        $this->modaliteContact = $modaliteContact;

        return $this;
    }

    public function getAuteur(): ?Personne
    {
        return $this->auteur;
    }

    public function setAuteur(?Personne $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCompteRendu(): ?string
    {
        return $this->compteRendu;
    }

    public function setCompteRendu(?string $compteRendu): static
    {
        $this->compteRendu = $compteRendu;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->getDir().'/'.$this->filename;
    }

    public function getDir(): ?string
    {
        return 'Contact';
    }

    public function __toString(): string
    {
        return 'Contact du '.$this->date->format('d/m/Y');
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAutresInterlocuteurs(): ?string
    {
        return $this->autresInterlocuteurs;
    }

    public function setAutresInterlocuteurs(?string $autresInterlocuteurs): static
    {
        $this->autresInterlocuteurs = $autresInterlocuteurs;

        return $this;
    }

    /**
     * @return Collection<int, ContactInterlocuteur>
     */
    public function getInterlocuteurs(): Collection
    {
        return $this->interlocuteurs;
    }

    public function addInterlocuteur(ContactInterlocuteur $interlocuteur): static
    {
        if (!$this->interlocuteurs->contains($interlocuteur)) {
            $this->interlocuteurs->add($interlocuteur);
            $interlocuteur->setContact($this);
        }

        return $this;
    }

    public function removeInterlocuteur(ContactInterlocuteur $interlocuteur): static
    {
        if ($this->interlocuteurs->removeElement($interlocuteur)) {
            // set the owning side to null (unless already changed)
            if ($interlocuteur->getContact() === $this) {
                $interlocuteur->setContact(null);
            }
        }

        return $this;
    }
}
