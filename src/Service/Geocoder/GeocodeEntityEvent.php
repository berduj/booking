<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

class GeocodeEntityEvent
{
    public function __construct(private readonly GeocodableInterface $entity)
    {
    }

    public function getEntity(): GeocodableInterface
    {
        return $this->entity;
    }
}
