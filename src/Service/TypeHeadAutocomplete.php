<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Personne;
use App\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;

class TypeHeadAutocomplete
{
    public const NB_CAR_RECHERCHE_SIRET = 3;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @return array<int, mixed>
     */
    public function get(string $query): array
    {
        if ($query === '') {
            return [];
        }
        $ret = [];

        if ($this->security->isGranted('ROLE_PERSONNE_VIEW')) {
            $ret = array_merge($ret, $this->getPersonnes($query));
        }

        if ($this->security->isGranted('ROLE_STRUCTURE_VIEW')) {
            $ret = array_merge($ret, $this->getStructures($query));
        }

        return $ret;
    }

    /**
     * @return array<int, mixed>
     */
    private function getPersonnes(string $query): array
    {
        $personnes = $this->entityManager->getRepository(Personne::class)->createQueryBuilder('p')
            ->where('p.nom LIKE :query OR p.prenom LIKE :query')
            ->andWhere('p.enabled = true')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults(10)
            ->addOrderBy('p.nom')
            ->addOrderBy('p.prenom')
            ->getQuery()->getResult();

        $ret = [];

        if (count($personnes)) {
            foreach ($personnes as $personne) {
                $ret[] = [
                    'label' => $personne->__toString(),
                    'id' => $this->router->generate('app_personne_show', ['id' => $personne->getId()]),
                    'type' => 'personne-picto',
                ];
            }
        }

        return $ret;
    }

    /**
     * @return array<int, mixed>
     */
    private function getStructures(string $query): array
    {
        $filtre = 'e.raisonSociale LIKE :query OR e.nom LIKE :query';

        if (strlen($query) > self::NB_CAR_RECHERCHE_SIRET) {
            $filtre = 'e.raisonSociale LIKE :query OR e.nom LIKE :query OR e.siret LIKE :query';
        }

        $structures = $this->entityManager->getRepository(Structure::class)->createQueryBuilder('e')
            ->where($filtre)
            ->andWhere('e.enabled = true')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults(10)
            ->addOrderBy('e.raisonSociale')
            ->getQuery()->getResult();
        $ret = [];

        if (count($structures)) {
            /** @var Structure $structure */
            foreach ($structures as $structure) {
                $ret[] = [
                    'label' => $this->getLibelle($structure, $query),
                    'id' => $this->router->generate('app_structure_show', ['id' => $structure->getId()]),
                    'type' => 'structure-picto',
                ];
            }
        }

        return $ret;
    }

    private function getLibelle(Structure $structure, string $query): string
    {
        if (strlen($query) > self::NB_CAR_RECHERCHE_SIRET && str_contains((string) $structure->getSiret(), $query)) {
            return (string) $structure.' ('.$structure->getSiret().')';
        }

        if ($structure->getNom() === null) {
            return (string) $structure;
        }

        if (str_contains($structure->getNom(), $query)) {
            $libelleStructure = $structure->getNom();
            if ($structure->getNom() !== $structure->getRaisonSociale()) {
                $libelleStructure .= ' ('.$structure->getRaisonSociale().')';
            }

            return $libelleStructure;
        }

        $libelleStructure = $structure->getRaisonSociale();
        if ($structure->getNom() !== $structure->getRaisonSociale()) {
            $libelleStructure .= ' ('.$structure->getNom().')';
        }

        return (string) $libelleStructure;
    }
}
