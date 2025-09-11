<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

interface GeocodableInterface
{
    public function getAdresse(): ?string;

    public function getCodePostal(): ?string;

    public function getCommune(): ?string;

    public function getPays(): ?string;

    public function setLatitude(?float $longitude): static;

    public function setLongitude(?float $longitude): static;

    public function setScoreGeocode(?float $score): static;

    public function __toString(): string;
}
