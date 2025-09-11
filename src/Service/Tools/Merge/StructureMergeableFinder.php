<?php

declare(strict_types=1);

namespace App\Service\Tools\Merge;

use App\Entity\Structure;
use App\Repository\StructureRepository;

class StructureMergeableFinder
{
    public function __construct(private readonly StructureRepository $repository)
    {
    }

    /**
     * @return array<int, object> the objects
     */
    public function findKeepable(Structure $structureDeletable): array
    {
        return $this->repository->findBy([
            'raisonSociale' => $structureDeletable->getRaisonSociale(),
            'codePostal' => $structureDeletable->getCodePostal(),
            'enabled' => true]);
    }

    /**
     * @return array<int, object> the objects
     */
    public function findDeletable(): array
    {
        return $this->repository->findBy([
            'pays' => 'fusionner',
            'enabled' => false]);
    }
}
