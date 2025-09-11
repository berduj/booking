<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Personne;
use App\Entity\Structure;
use App\ValueObject\EnabledDisabledAll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personne>
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    /**
     * @return Structure[]
     */
    public function findByLetterStatus(string $letter, EnabledDisabledAll $status): array
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.nom LIKE :letter')
            ->setParameter('letter', $letter.'%')
            ->addOrderBy('p.nom', 'ASC')
            ->addOrderBy('p.prenom', 'ASC');

        if (is_bool($status->getSqlEnabledFilter())) {
            $query->andWhere('p.enabled = :enabled');
            $query->setParameter('enabled', $status->getSqlEnabledFilter());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @return array<int, mixed>
     */
    public function findAutocomplete(string $query, int $nbMax): array
    {
        $personnes = $this->createQueryBuilder('p')
            ->where('p.nom LIKE :query or p.prenom LIKE :query')
            ->andWhere('p.enabled = true')
            ->setParameter('query', '%'.$query.'%')
            ->addOrderBy('p.nom', 'ASC')
            ->addOrderBy('p.prenom', 'ASC')
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult();
        $ret = [];

        foreach ($personnes as $personne) {
            $ret[] = ['label' => $personne->getNom().' '.$personne->getPrenom(), 'id' => $personne->getId(), 'type' => 'personne-picto'];
        }

        return $ret;
    }

    /**
     * @return array<int, mixed>
     */
    public function findAutocompleteReferent(string $query): array
    {
        $personnes = $this->createQueryBuilder('p')
            ->join('p.services', 'service')
            ->where('p.nom LIKE :query or p.prenom LIKE :query')
            ->andWhere('p.enabled = true')
            ->setParameter('query', '%'.$query.'%')
            ->addOrderBy('p.nom', 'ASC')
            ->addOrderBy('p.prenom', 'ASC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
        $ret = [];

        foreach ($personnes as $personne) {
            $ret[] = ['label' => $personne->getNom().' '.$personne->getPrenom(), 'id' => $personne->getId(), 'type' => 'personne-picto'];
        }

        return $ret;
    }
}
