<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\DocumentableInterface;
use App\Entity\Traits\BlameableEntity;
use App\Entity\Traits\EnableableEntity;
use App\Entity\Traits\SortableEntity;
use App\Repository\DocumentRepository;
use App\Service\SortableEntity\SortableEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document implements SortableEntityInterface, \Stringable
{
    use BlameableEntity;
    use SortableEntity;
    use EnableableEntity;

    public ?UploadedFile $file = null;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foreignClass = null;

    #[ORM\Column(nullable: true)]
    private ?string $foreignId = null;

    public function __construct(?DocumentableInterface $object = null)
    {
        if ($object) {
            $this->setObject($object);
        }
    }

    public function setObject(DocumentableInterface $object): void
    {
        $this->setForeignClass($object::class);
        $this->setForeignId((string) $object->getId());
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getForeignId(): ?string
    {
        return $this->foreignId;
    }

    private function setForeignId(?string $foreignId): static
    {
        $this->foreignId = $foreignId;

        return $this;
    }

    public function __toString()
    {
        if ($this->getTitre()) {
            return (string) $this->getTitre();
        }

        return (string) $this->filename;
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

    public function getFilepath(): ?string
    {
        return $this->getDir().'/'.$this->filename;
    }

    public function getDir(): ?string
    {
        $foreignClass = basename(str_replace('\\', '/', (string) $this->foreignClass));

        return 'Document'.
            ($foreignClass ? '/'.$foreignClass : '').
            ($this->foreignId ? '/'.$this->foreignId : '');
    }

    public function getForeignClass(): ?string
    {
        return $this->foreignClass;
    }

    private function setForeignClass(?string $foreignClass): static
    {
        $this->foreignClass = $foreignClass;

        return $this;
    }
}
