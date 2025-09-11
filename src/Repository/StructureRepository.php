<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Structure;
use App\ValueObject\EnabledDisabledAll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Structure>
 */
class StructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Structure::class);
    }

    /**
     * @return Structure[]
     */
    public function findByLetterStatus(string $letter, EnabledDisabledAll $status): array
    {
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.raisonSociale LIKE :letter')
            ->setParameter('letter', $letter.'%')
            ->orderBy('e.raisonSociale', 'ASC');

        if (is_bool($status->getSqlEnabledFilter())) {
            $query->andWhere('e.enabled = :enabled');
            $query->setParameter('enabled', $status->getSqlEnabledFilter());
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @return array<int, mixed>
     */
    public function findAutocomplete(string $query, int $nbMax): array
    {
        $structures = $this->createQueryBuilder('e')
            ->where('e.raisonSociale LIKE :query OR e.nom LIKE :query')
            ->andWhere('e.enabled = true')
            ->setParameter('query', '%'.$query.'%')
            ->addOrderBy('e.raisonSociale', 'ASC')
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult();

        $ret = [];
        foreach ($structures as $structure) {
            $ret[] = ['label' => $structure->getRaisonSociale().' ('.$structure->getCommune().') ', 'id' => $structure->getId(), 'type' => 'structure-picto'];
        }

        return $ret;
    }
}
