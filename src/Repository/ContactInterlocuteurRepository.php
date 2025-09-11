<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContactInterlocuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactInterlocuteur>
 */
class ContactInterlocuteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactInterlocuteur::class);
    }
}
