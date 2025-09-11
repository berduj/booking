<?php

declare(strict_types=1);

namespace App\Service\Geocoder\Dto;

class GeocodedAdresse implements \Stringable
{
    public string $adresse;
    public string $code_postal;
    public string $commune;
    public string $pays;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?float $score = null;

    public function __toString(): string
    {
        return $this->adresse.' '.$this->code_postal.' '.$this->commune.' '.$this->pays;
    }
}
