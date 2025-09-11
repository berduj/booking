<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\Personne;
use App\Entity\Structure;
use App\ValueObject\YearMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @return array<int, Contact>
     */
    public function findByYearMonth(YearMonth $yearMonth): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date LIKE  :yearMonth  ')
            ->setParameter('yearMonth', $yearMonth->getForSql().'%')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, Contact>
     */
    public function findByStructure(Structure $structure): array
    {
        $uuid = $structure->getId() ? $structure->getId()->toBinary() : null;

        return $this->createQueryBuilder('c')
            ->join('c.interlocuteurs', 'interlocuteur')
            ->join('interlocuteur.structure', 'structure')
            ->andWhere('structure.id = :uuid')
            ->setParameter('uuid', $uuid)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, Contact>
     */
    public function findByPersonne(Personne $personne): array
    {
        $uuid = $personne->getId() ? $personne->getId()->toBinary() : null;

        return $this->createQueryBuilder('c')
            ->join('c.interlocuteurs', 'interlocuteur')
            ->join('interlocuteur.personne', 'personne')
            ->andWhere('personne.id = :uuid')
            ->setParameter('uuid', $uuid)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
