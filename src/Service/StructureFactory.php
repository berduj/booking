<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Structure;
use App\Repository\NafRepository;
use App\Service\Siret\InseeApiSiret;

class StructureFactory
{
    public function __construct(private readonly InseeApiSiret $apiSiret)
    {
    }

    /**
     * @throws Siret\InvalidSiretFormatException
     */
    public function createFromSiret(string $numeroSiret): Structure
    {
        $structureInsee = $this->apiSiret->getEntreprise($numeroSiret);

        $structure = new Structure();
        $structure->setSiret($numeroSiret);

        $this->setUniteLegale($structure, $structureInsee);
        $this->setAdresseEtablissement($structure, $structureInsee);

        return $structure;
    }

    /**
     * @param array<mixed> $structureInsee
     */
    private function setUniteLegale(Structure $structure, array $structureInsee): void
    {
        if (!array_key_exists('uniteLegale', $structureInsee)) {
            return;
        }

        $uniteLegale = $structureInsee['uniteLegale'];
        if (!$uniteLegale) {
            return;
        }

        try {
            $structure->setRaisonSociale((string) $uniteLegale['denominationUniteLegale']);
            $structure->setNom($structure->getRaisonSociale());
        } catch (\Exception $e) {
            return;
        }

    }

    /**
     * @param array<mixed> $structureInsee
     */
    private function setAdresseEtablissement(Structure $structure, array $structureInsee): void
    {
        if (!array_key_exists('adresseEtablissement', $structureInsee)) {
            return;
        }

        $adresseEtablissement = $structureInsee['adresseEtablissement'];

        $adresse =
            $adresseEtablissement['numeroVoieEtablissement'].
            $adresseEtablissement['indiceRepetitionEtablissement'].' '.
            $adresseEtablissement['typeVoieEtablissement'].' '.
            $adresseEtablissement['libelleVoieEtablissement'];

        $adresse = trim((string) preg_replace('/\s+/', ' ', $adresse));

        if ($adresseEtablissement['complementAdresseEtablissement']) {
            $adresse .= "\n".$adresseEtablissement['complementAdresseEtablissement'];
        }

        $structure->setAdresse(trim($adresse));
        $structure->setCodePostal($adresseEtablissement['codePostalEtablissement'] ?? null);
        $structure->setCommune($adresseEtablissement['libelleCommuneEtablissement'] ?? null);
        $structure->setPays('France');
    }
}
