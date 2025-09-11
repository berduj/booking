<?php

declare(strict_types=1);

namespace App\Service\Charts;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

abstract class AbstractSqlChart
{
    private Connection $connection;

    public function __construct(protected readonly ChartBuilderInterface $chartbuilder, ManagerRegistry $managerRegistry)
    {
        $connections = $managerRegistry->getConnections();
        if (!isset($connections['readonly']) || !$connections['readonly'] instanceof Connection) {
            throw new \RuntimeException('La connexion "readonly" n\'existe pas ou n\'est pas valide.');
        }
        $this->connection = $connections['readonly'];
    }

    /**
     * @return array<int, mixed>
     */
    protected function getData(string $sql): array
    {
        $statement = $this->connection->executeQuery($sql);

        return $statement->fetchAllAssociative();
    }
}
