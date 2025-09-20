<?php

declare(strict_types=1);

namespace App\Service\Siret;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Il est nécessaire de créer un compte sur le site des API Insee
 * ensuite il faut declarer INSEE_CLE et INSEE_SECRET dans .env.local.
 */
class InseeApiSiret
{
    public const SIRET_LENGTH = 14;
    private string $siret_url = 'https://api.insee.fr/api-sirene/3.11/siret/';


    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(env: 'INSEE_CLE')]
        private readonly string $insee_cle,
    ) {
    }
    /**
     * @return array<mixed>
     *
     * @throws InvalidSiretFormatException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getEntreprise(string $numeroSiret): array
    {
        $numeroSiret = trim($numeroSiret);
        if (!is_numeric($numeroSiret) || \strlen($numeroSiret) !== self::SIRET_LENGTH) {
            throw new InvalidSiretFormatException();
        }

        try {
            // Faire la requête GET avec les en-têtes nécessaires
            $response = $this->client->request('GET', $this->siret_url . $numeroSiret, [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-INSEE-Api-Key-Integration' => $this->insee_cle,
                ],
            ]);

            return $response->toArray()['etablissement'];
        } catch (\Throwable $exception) {
            throw new InvalidSiretResponseException("Le Web Service n'a pas renvoyé de réponse pour le siret : " . $numeroSiret);
        }
    }


}
