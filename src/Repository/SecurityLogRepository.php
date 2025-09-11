<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SecurityLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SecurityLog>
 */
class SecurityLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecurityLog::class);
    }

    /**
     * @return array<int, SecurityLog>
     */
    public function findByPage(int $page, string $action): array
    {
        return $this->createQueryBuilder('log')
            ->andWhere('log.action = :action')
            ->setParameter('action', $action)
            ->orderBy('log.date', 'DESC')
            ->setFirstResult(($page - 1) * SecurityLog::NB_PER_PAGE_LOGIN)
            ->setMaxResults(SecurityLog::NB_PER_PAGE_LOGIN)
            ->getQuery()
            ->getResult();
    }

    public function getNbPages(string $action): float
    {
        return ceil($this->getNbEnregistrements($action) / SecurityLog::NB_PER_PAGE_LOGIN);
    }

    public function getNbEnregistrements(string $action): int
    {
        $qb = $this->createQueryBuilder('log');

        return intval(
            $qb->select($qb->expr()->count('log.id'))
                ->andWhere('log.action = :action')
                ->setParameter('action', $action)
                ->getQuery()
                ->getSingleScalarResult()
        );
    }
}
