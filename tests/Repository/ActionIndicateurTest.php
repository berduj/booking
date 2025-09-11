<?php

declare(strict_types=1);

namespace Repository;

use App\Entity\ActionIndicateur;

class ActionIndicateurTest extends AbstractRepository
{
    public function testRepo(): void
    {
        $kernel = self::bootKernel();

        $repo = $this->entityManager->getRepository(ActionIndicateur::class);
        $this->assertIsArray($repo->findAll());
    }
}
