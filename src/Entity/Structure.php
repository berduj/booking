<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Repository\StructureRepository;
use App\Service\Geocoder\GeocodableEntity;
use App\Service\Geocoder\GeocodableInterface;
use App\Validator as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StructureRepository::class)]
#[UniqueEntity(fields: ['siret'])]
#[Assert\Delete\DeleteStructure(['delete'])]
class Structure implements \Stringable, GeocodableInterface
{
    use BlameableEntity;
    use GeocodableEntity;
    use EnableableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commune = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    /**
     * @var Collection<int, Personne>
     */
    #[ORM\ManyToMany(targetEntity: Personne::class, inversedBy: 'structures')]
    #[ORM\OrderBy(['nom' => 'ASC', 'prenom' => 'ASC'])]
    private Collection $personnes;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $infosDiverses = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    /**
     * @var Collection<int, TypeStructure>
     */
    #[ORM\ManyToMany(targetEntity: TypeStructure::class, inversedBy: 'structures')]
    #[ORM\OrderBy(['sortable' => 'ASC'])]
    private Collection $typeStructures;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'structures')]
    private Collection $tags;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Personne>
     */
    #[ORM\ManyToMany(targetEntity: Personne::class, inversedBy: 'referentStructures')]
    #[ORM\JoinTable(name: 'structure_referent')]
    private Collection $referents;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $complementAdresse = null;

    #[ORM\ManyToOne]
    private ?Personne $referentPrincipal = null;

    /**
     * @var Collection<int, ContactInterlocuteur>
     */
    #[ORM\OneToMany(targetEntity: ContactInterlocuteur::class, mappedBy: 'structure')]
    private Collection $contactInterlocuteurs;

    #[ORM\Column]
    private ?bool $adminOnly = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePremierContact = null;

    public function __construct()
    {
        $this->personnes = new ArrayCollection();
        $this->typeStructures = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->referents = new ArrayCollection();
        $this->contactInterlocuteurs = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = preg_replace('/\s+/', '', $siret);

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function __toString(): string
    {
        if (is_string($this->nom) && $this->nom > '') {
            return $this->nom;
        }

        return (string)$this->raisonSociale;
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
        }

        return $this;
    }

    public function removePersonne(Personne $personne): static
    {
        $this->personnes->removeElement($personne);

        return $this;
    }

    public function getInfosDiverses(): ?string
    {
        return $this->infosDiverses;
    }

    public function setInfosDiverses(?string $infosDiverses): static
    {
        $this->infosDiverses = $infosDiverses;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, TypeStructure>
     */
    public function getTypeStructures(): Collection
    {
        return $this->typeStructures;
    }

    public function addTypeStructure(TypeStructure $typeStructure): static
    {
        if (!$this->typeStructures->contains($typeStructure)) {
            $this->typeStructures->add($typeStructure);
        }

        return $this;
    }

    public function removeTypeStructure(TypeStructure $typeStructure): static
    {
        $this->typeStructures->removeElement($typeStructure);

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getReferents(): Collection
    {
        return $this->referents;
    }

    public function addReferent(Personne $referent): static
    {
        if (!$this->referents->contains($referent)) {
            $this->referents->add($referent);
        }

        return $this;
    }

    public function removeReferent(Personne $referent): static
    {
        $this->referents->removeElement($referent);
        if ($this->getReferentPrincipal() === $referent) {
            $this->setReferentPrincipal(null);
        }

        return $this;
    }

    public function getReferentPrincipal(): ?Personne
    {
        return $this->referentPrincipal;
    }

    public function setReferentPrincipal(?Personne $referentPrincipal): static
    {
        $this->referentPrincipal = $referentPrincipal;

        return $this;
    }

    public function getComplementAdresse(): ?string
    {
        return $this->complementAdresse;
    }

    public function setComplementAdresse(?string $complementAdresse): static
    {
        $this->complementAdresse = $complementAdresse;

        return $this;
    }

    /**
     * @return Collection<int, ContactInterlocuteur>
     */
    public function getContactInterlocuteurs(): Collection
    {
        $array = $this->contactInterlocuteurs->toArray();
        usort($array, function (ContactInterlocuteur $a, ContactInterlocuteur $b): int {
            if ($a->getContact() and $b->getContact()) {
                return (int)($a->getContact()->getDate() < $b->getContact()->getDate());
            }

            return 1;
        });

        return new ArrayCollection($array);
    }

    public function addContactInterlocuteur(ContactInterlocuteur $contactInterlocuteur): static
    {
        if (!$this->contactInterlocuteurs->contains($contactInterlocuteur)) {
            $this->contactInterlocuteurs->add($contactInterlocuteur);
            $contactInterlocuteur->setStructure($this);
        }

        return $this;
    }

    public function removeContactInterlocuteur(ContactInterlocuteur $contactInterlocuteur): static
    {
        if ($this->contactInterlocuteurs->removeElement($contactInterlocuteur)) {
            // set the owning side to null (unless already changed)
            if ($contactInterlocuteur->getStructure() === $this) {
                $contactInterlocuteur->setStructure(null);
            }
        }

        return $this;
    }

    public function isAdminOnly(): ?bool
    {
        return $this->adminOnly;
    }

    public function setAdminOnly(bool $adminOnly): static
    {
        $this->adminOnly = $adminOnly;

        return $this;
    }

    public function getDatePremierContact(): ?\DateTimeInterface
    {
        return $this->datePremierContact;
    }

    public function setDatePremierContact(?\DateTimeInterface $datePremierContact): static
    {
        $this->datePremierContact = $datePremierContact;

        return $this;
    }
}
