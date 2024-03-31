<?php

declare(strict_types=1);

namespace App\Client;


use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GhArchiveClient
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getEventsForDateAndHour(string $date, int $hour): ?string
    {
        //TODO: return array + exceptions?
        $url = sprintf('https://data.gharchive.org/%s-%s.json.gz', $date, $hour);
        $response = $this->httpClient->request('GET', $url);

        try {
            return $response->getContent();
        } catch (Exception $e) {
            return null;
        }
    }
}
