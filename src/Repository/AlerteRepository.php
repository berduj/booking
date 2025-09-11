<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Alerte;
use App\Entity\Personne;
use App\ValueObject\YearMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alerte>
 */
class AlerteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alerte::class);
    }

    /**
     * @return array<int,Alerte>
     */
    public function findByYearMonthFiltreUser(YearMonth $yearMonth, Personne $personne): array
    {
        /** @var Personne $personne */
        $uuid = $personne->getId() ? $personne->getId()->toBinary() : null;

        $query = $this->createQueryBuilder('alerte')
            ->andWhere('alerte.date LIKE  :yearMonth')
            ->setParameter('yearMonth', $yearMonth->getForSql().'%')
            ->leftJoin('alerte.personne', 'personne')
            ->andWhere('personne.id = :uuid')
            ->setParameter('uuid', $uuid)
            ->orderBy('alerte.date', 'ASC');

        return $query->getQuery()
            ->getResult();
    }

    /**
     * @return array<int,Alerte>
     */
    public function findForHomepageUser(Personne $personne): array
    {
        /** @var Personne $personne */
        $uuid = $personne->getId() ? $personne->getId()->toBinary() : null;
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        return $this->createQueryBuilder('alerte')
            ->andWhere('alerte.date <= :now')
            ->setParameter('now', $now)
            ->leftJoin('alerte.personne', 'personne')
            ->andWhere('personne.id = :uuid')
            ->setParameter('uuid', $uuid)
            ->andWhere('alerte.enabled = true')
            ->getQuery()
            ->getResult();
    }
}
