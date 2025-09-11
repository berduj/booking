<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GeocodeEntitySubscriber implements EventSubscriberInterface
{
    public function __construct(protected readonly Geocoder $geocoder, private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GeocodeEntityEvent::class => 'onGeocode',
        ];
    }

    public function onGeocode(GeocodeEntityEvent $event): void
    {
        $entity = $event->getEntity();
        try {
            $geocodedAdresse = $this->geocoder->geoCode($entity);
            $entity->setScoreGeocode($geocodedAdresse->score);
            $context = [
                'structure' => (string) $entity,
                'adresse' => $entity->getAdresse().'-'.$entity->getCodePostal().' '.$entity->getCommune(),
                'score' => $geocodedAdresse->score,
                'latitude' => $geocodedAdresse->latitude,
                'longitude' => $geocodedAdresse->longitude,
            ];

            if ($geocodedAdresse->score > 0.70) {
                $entity
                    ->setLatitude($geocodedAdresse->latitude)
                    ->setLongitude($geocodedAdresse->longitude);
                $this->logger->info('Geocodage '.$entity, $context);
            } else {
                $this->logger->notice('Geocodage '.$entity, $context);
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Erreur geocodage '.$entity,
                [
                    'structure' => (string) $entity,
                    'adresse' => $entity->getAdresse().'-'.$entity->getCodePostal().' '.$entity->getCommune(),
                    'error' => $e->getMessage(),
                ]
            );
            sleep(3);
        }
    }
}
