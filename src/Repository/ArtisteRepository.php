<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Artiste;
use App\Entity\Structure;
use App\ValueObject\EnabledDisabledAll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artiste>
 */
class ArtisteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artiste::class);
    }

    /**
     * @return Structure[]
     */
    public function findByLetterStatus(string $letter, EnabledDisabledAll $status): array
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.nom LIKE :letter')
            ->setParameter('letter', $letter.'%')
            ->addOrderBy('a.nom', 'ASC');

        if (is_bool($status->getSqlEnabledFilter())) {
            $query->andWhere('a.enabled = :enabled');
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
}
