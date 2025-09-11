<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Export;
use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Export>
 */
class ExportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Export::class);
    }

    /**
     * @return Export[]
     */
    public function findPublic(Personne $personne): array
    {
        $profil = $personne->getProfil();

        return $this->createQueryBuilder('e')
            ->andWhere('e.enabled = true')
            ->addOrderBy('e.sortable', 'ASC')
            ->andWhere(':profil MEMBER OF e.profils ')
            ->setParameter('profil', $profil)
            ->getQuery()
            ->getResult();
    }
}
