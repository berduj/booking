<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

use App\Service\Geocoder\Dto\GeocodedAdresse;
use App\Service\Geocoder\Exception\GeocodageErrorException;
use App\Service\Geocoder\Exception\InvalidPaysException;
use App\Service\Geocoder\Exception\UnknownAdresseException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Geocoder
{
    private string $url = 'https://api-adresse.data.gouv.fr/search/';

    public function __construct(protected readonly HttpClientInterface $httpClient)
    {
    }

    public function geoCode(GeocodableInterface $adresse): GeocodedAdresse
    {
        $this->checkPays($adresse);
        $texte = $adresse->getAdresse().' '.$adresse->getCodePostal().' '.$adresse->getCommune();

        $response = $this->httpClient->request('GET', $this->url, [
            'query' => [
                'q' => $texte,
                'limit' => 1,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new GeocodageErrorException();
        }

        $responseArray = $response->toArray();

        if (count($responseArray['features']) === 0) {
            throw new UnknownAdresseException();
        }

        $properties = $responseArray['features'][0]['properties'];
        $geometry = $responseArray['features'][0]['geometry'];

        $geocodedAdresse = new GeocodedAdresse();
        $geocodedAdresse->pays = 'France';

        $this->copyProperty($properties, $geocodedAdresse, 'name', 'adresse');
        $this->copyProperty($properties, $geocodedAdresse, 'postcode', 'code_postal');
        $this->copyProperty($properties, $geocodedAdresse, 'city', 'commune');
        $this->copyProperty($properties, $geocodedAdresse, 'score', 'score');

        $this->copyProperty($geometry['coordinates'], $geocodedAdresse, '0', 'latitude');
        $this->copyProperty($geometry['coordinates'], $geocodedAdresse, '1', 'longitude');

        return $geocodedAdresse;
    }

    /**
     * @param array<string, string > $properties
     */
    private function copyProperty(array $properties, GeocodedAdresse $adresse, string $propertyIndex, string $adresseAttribute): void
    {
        if (!property_exists($adresse, $adresseAttribute)) {
            throw new \Exception("l'attribut ".$adresseAttribute." n'existe pas sur l'adresse ".get_class($adresse));
        }

        if (array_key_exists($propertyIndex, $properties)) {
            $adresse->$adresseAttribute = $properties[$propertyIndex];
        }
    }

    private function checkPays(GeocodableInterface $adresse): void
    {
        if ($adresse->getPays() === null) {
            return;
        }

        $pays = trim(strtolower($adresse->getPays()));
        if ($pays === '' or $pays === 'france') {
            return;
        }

        throw new InvalidPaysException("Les adresses dans le pays '".$adresse->getPays()."' ne peuvent pas être géocodées avec ce service");
    }
}
