<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SecurityLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecurityLogRepository::class)]
class SecurityLog
{
    public const LOGIN = 'login';
    public const CHANGE_PASSWORD = 'change-password';

    public const NB_PER_PAGE_LOGIN = 500;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Personne $personne = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personneNom = null;

    public function __construct(Personne $personne, \DateTimeInterface $date, string $action)
    {
        $this->personne = $personne;
        $this->date = $date;
        $this->action = $action;
        $this->personneNom = $personne->getPrenom().' '.$personne->getNom();
    }

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getPersonneNom(): ?string
    {
        return $this->personneNom;
    }

    public function setPersonneNom(?string $personneNom): static
    {
        $this->personneNom = $personneNom;

        return $this;
    }
}
