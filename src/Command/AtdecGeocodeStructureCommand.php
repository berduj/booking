<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Structure;
use App\Service\Geocoder\GeocodeEntityEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'atdec:geocode:structure',
    description: 'Geocode les structure non geocodées',
)]
class AtdecGeocodeStructureCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nb = 0;

        $structures = $this->entityManager->getRepository(Structure::class)->findBy(
            [
                'enabled' => true,
                'scoreGeocode' => null,
            ],
            [
                'updatedAt' => 'ASC',
            ]
        );

        $progresBar = new ProgressBar($output, count($structures));
        $progresBar->start();

        foreach ($structures as $structure) {
            if ($structure->getCodePostal() && $structure->getCommune() && $structure->getAdresse()) {
                $this->eventDispatcher->dispatch(new GeocodeEntityEvent($structure));
                $this->entityManager->flush();
                sleep(2);
            }
            $progresBar->advance();
        }
        $progresBar->finish();

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
