<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Structure;

class StructureSiretUpdater
{
    public function __construct(private readonly StructureFactory $structureFactory)
    {
    }

    public function update(Structure $structure): void
    {
        if ($structure->getSiret() === null or $structure->getSiret() === '') {
            throw new \Exception("La structure n'a pas de Siret");
        }

        $newStructure = $this->structureFactory->createFromSiret($structure->getSiret());
        $structure
            ->setRaisonSociale((string) $newStructure->getRaisonSociale())
            ->setAdresse($newStructure->getAdresse())
            ->setCodePostal($newStructure->getCodePostal())
            ->setCommune($newStructure->getCommune())
            ->setPays($newStructure->getPays());

        if (!$structure->getNom()) {
            $structure->setNom($structure->getRaisonSociale());
        }
    }
}
