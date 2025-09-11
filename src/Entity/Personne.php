<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Repository\PersonneRepository;
use App\Security\UserPersonneInterface;
use App\Service\Geocoder\GeocodableEntity;
use App\Service\Geocoder\GeocodableInterface;
use App\Validator as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[Assert\Personne]
#[Assert\Delete\DeletePersonne(['delete'])]
class Personne implements \Stringable, GeocodableInterface, UserPersonneInterface, PasswordAuthenticatedUserInterface
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
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone_mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone_fixe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $civilite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonction = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commune = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    /**
     * @var Collection<int, Structure>
     */
    #[ORM\ManyToMany(targetEntity: Structure::class, mappedBy: 'personnes')]
    private Collection $structures;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?Profil $profil = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastLogin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    /**
     * @var Collection<int, Structure>
     */
    #[ORM\ManyToMany(targetEntity: Structure::class, mappedBy: 'referents')]
    private Collection $referentStructures;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $complementAdresse = null;

    /**
     * @var Collection<int, ContactInterlocuteur>
     */
    #[ORM\OneToMany(targetEntity: ContactInterlocuteur::class, mappedBy: 'personne')]
    private Collection $contactInterlocuteurs;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'auteur')]
    private Collection $auteurContacts;

    /**
     * @var Collection<int, PersonneWidget>
     */
    #[ORM\OneToMany(targetEntity: PersonneWidget::class, mappedBy: 'personne', cascade: ['remove'])]
    #[ORM\OrderBy(['sortable' => 'ASC'])]
    private Collection $personneWidgets;

    #[ORM\Column]
    private ?bool $vip = false;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?DepartementDomaine $departementDomaine = null;

    /**
     * @var Collection<int, Alerte>
     */
    #[ORM\OneToMany(targetEntity: Alerte::class, mappedBy: 'personne', cascade: ['remove'])]
    private Collection $auteur;

    /**
     * @var Collection<int, Alerte>
     */
    #[ORM\OneToMany(targetEntity: Alerte::class, mappedBy: 'auteur')]
    private Collection $auteurAlertes;

    public function __construct()
    {
        $this->structures = new ArrayCollection();
        $this->referentStructures = new ArrayCollection();
        $this->contactInterlocuteurs = new ArrayCollection();
        $this->auteurContacts = new ArrayCollection();
        $this->personneWidgets = new ArrayCollection();
        $this->auteur = new ArrayCollection();
        $this->auteurAlertes = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = strtoupper($nom);

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

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

    public function getTelephoneMobile(): ?string
    {
        return $this->telephone_mobile;
    }

    public function setTelephoneMobile(?string $telephone_mobile): static
    {
        $this->telephone_mobile = $telephone_mobile;

        return $this;
    }

    public function getTelephoneFixe(): ?string
    {
        return $this->telephone_fixe;
    }

    public function setTelephoneFixe(?string $telephone_fixe): static
    {
        $this->telephone_fixe = $telephone_fixe;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): static
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): static
    {
        $this->fonction = $fonction;

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

    public function __toString()
    {
        return (string) trim($this->prenom.' '.$this->nom);
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
            $structure->addPersonne($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): static
    {
        if ($this->structures->removeElement($structure)) {
            $structure->removePersonne($this);
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        if ($this->username === null || $this->username === '') {
            return '-';
        }

        return $this->username;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        if ($this->profil === null) {
            return [];
        }
        $roles = ['ROLE_USER'];
        $roles = array_merge($roles, $this->profil->getRoles());

        return array_unique($roles);
    }

    /**
     * @return Collection<int, Structure>
     */
    public function getReferentStructures(): Collection
    {
        return $this->referentStructures;
    }

    public function addReferentStructures(Structure $referentStructure): static
    {
        if (!$this->referentStructures->contains($referentStructure)) {
            $this->referentStructures->add($referentStructure);
            $referentStructure->addReferent($this);
        }

        return $this;
    }

    public function removeReferentStructure(Structure $referentStructure): static
    {
        if ($this->referentStructures->removeElement($referentStructure)) {
            $referentStructure->removeReferent($this);
        }

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
        usort($array, function (ContactInterlocuteur $a, ContactInterlocuteur $b) {
            if ($a->getContact() and $b->getContact()) {
                return (int) ($a->getContact()->getDate() < $b->getContact()->getDate());
            }

            return 0;
        });

        return new ArrayCollection($array);
    }

    public function addContactInterlocuteurs(ContactInterlocuteur $contactInterlocuteurs): static
    {
        if (!$this->contactInterlocuteurs->contains($contactInterlocuteurs)) {
            $this->contactInterlocuteurs->add($contactInterlocuteurs);
            $contactInterlocuteurs->setPersonne($this);
        }

        return $this;
    }

    public function removeContactInterlocuteurs(ContactInterlocuteur $contactInterlocuteurs): static
    {
        if ($this->contactInterlocuteurs->removeElement($contactInterlocuteurs)) {
            // set the owning side to null (unless already changed)
            if ($contactInterlocuteurs->getPersonne() === $this) {
                $contactInterlocuteurs->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getAuteurContacts(): Collection
    {
        return $this->auteurContacts;
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
            $personneWidget->setPersonne($this);
        }

        return $this;
    }

    public function removePersonneWidget(PersonneWidget $personneWidget): static
    {
        if ($this->personneWidgets->removeElement($personneWidget)) {
            // set the owning side to null (unless already changed)
            if ($personneWidget->getPersonne() === $this) {
                $personneWidget->setPersonne(null);
            }
        }

        return $this;
    }

    public function hasWidget(Widget $widget): bool
    {
        return $this->getWidgets()->contains($widget);
    }

    /**
     * @return Collection<int,Widget>
     */
    public function getWidgets(): Collection
    {
        $widgets = new ArrayCollection();
        foreach ($this->personneWidgets as $personneWidget) {
            $widgets->add($personneWidget->getWidget());
        }

        return $widgets;
    }

    public function isVip(): ?bool
    {
        return $this->vip;
    }

    public function setVip(bool $vip): static
    {
        $this->vip = $vip;

        return $this;
    }

    public function getDepartementDomaine(): ?DepartementDomaine
    {
        return $this->departementDomaine;
    }

    public function setDepartementDomaine(?DepartementDomaine $departementDomaine): static
    {
        $this->departementDomaine = $departementDomaine;

        return $this;
    }

    public function addAuteur(Alerte $auteur): static
    {
        if (!$this->auteur->contains($auteur)) {
            $this->auteur->add($auteur);
            $auteur->setPersonne($this);
        }

        return $this;
    }

    public function removeAuteur(Alerte $auteur): static
    {
        if ($this->auteur->removeElement($auteur)) {
            // set the owning side to null (unless already changed)
            if ($auteur->getPersonne() === $this) {
                $auteur->setPersonne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Alerte>
     */
    public function getAuteurAlertes(): Collection
    {
        return $this->auteurAlertes;
    }

    public function addAuteurAlerte(Alerte $auteurAlerte): static
    {
        if (!$this->auteurAlertes->contains($auteurAlerte)) {
            $this->auteurAlertes->add($auteurAlerte);
            $auteurAlerte->setAuteur($this);
        }

        return $this;
    }

    public function removeAuteurAlerte(Alerte $auteurAlerte): static
    {
        if ($this->auteurAlertes->removeElement($auteurAlerte)) {
            // set the owning side to null (unless already changed)
            if ($auteurAlerte->getAuteur() === $this) {
                $auteurAlerte->setAuteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Alerte>
     */
    public function getAuteur(): Collection
    {
        return $this->auteur;
    }

    public function getBinaryId(): ?string
    {
        if ($this->id instanceof Uuid) {
            return $this->id->toBinary();
        }

        return null;
    }
}
