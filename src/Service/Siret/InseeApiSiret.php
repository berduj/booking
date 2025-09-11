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
    private string $token_url = 'https://api.insee.fr/token';
    private string $siret_url = 'https://api.insee.fr/entreprises/sirene/V3.11/siret/';

    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $client,
        #[Autowire(env: 'INSEE_CLE')]
        private readonly string $insee_cle,
        #[Autowire(env: 'INSEE_SECRET')]
        private readonly string $insee_secret,
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
            $response = $this->client->request('GET', $this->siret_url.$numeroSiret, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->getToken(),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
            ]);

            return $response->toArray()['etablissement'];
        } catch (\Throwable $exception) {
            throw new InvalidSiretResponseException("Le Web Service n'a pas renvoyé de réponse pour le siret : ".$numeroSiret);
        }
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getToken(): string
    {
        $bearer = $this->cache->get('insee_bearer', function (ItemInterface $item) {
            $item->expiresAfter(86400);
            $response = $this->client->request('POST', $this->token_url, [
                'headers' => [
                    'Authorization' => 'Basic '.base64_encode($this->insee_cle.':'.$this->insee_secret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => 'grant_type=client_credentials',
            ]);

            $responseArray = $response->toArray();
            if (!array_key_exists('access_token', $responseArray)) {
                throw new NoInseeTokenException();
            }

            return $responseArray['access_token'];
        });

        return $bearer;
    }
}
