<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

use Doctrine\ORM\Mapping as ORM;

trait GeocodableEntity
{
    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $scoreGeocode = null;

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getScoreGeocode(): ?float
    {
        return $this->scoreGeocode;
    }

    public function setScoreGeocode(?float $scoreGeocode): static
    {
        $this->scoreGeocode = $scoreGeocode;

        return $this;
    }
}
