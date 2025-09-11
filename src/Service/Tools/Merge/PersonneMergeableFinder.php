<?php

declare(strict_types=1);

namespace App\Service\Tools\Merge;

use App\Entity\Personne;
use App\Repository\PersonneRepository;

class PersonneMergeableFinder
{
    public function __construct(private readonly PersonneRepository $repository)
    {
    }

    /**
     * @return array<int, object>
     */
    public function findKeepable(Personne $personneDeletable): array
    {
        return $this->repository->findBy([
            'nom' => $personneDeletable->getNom(),
            'prenom' => $personneDeletable->getPrenom(),
            'codePostal' => $personneDeletable->getCodePostal(),
            'enabled' => true]);
    }

    /**
     * @return array<int, object>
     */
    public function findDeletable(): array
    {
        return $this->repository->findBy([
            'fonction' => 'fusionner',
            'enabled' => false]);
    }
}
