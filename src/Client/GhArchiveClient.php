<?php

declare(strict_types=1);

namespace App\Client;

use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GhArchiveClient
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @throws HttpExceptionInterface
     */
    public function getEventsForDateAndHour(string $date, int $hour): array
    {
        $url = sprintf('https://data.gharchive.org/%s-%s.json.gz', $date, $hour);
        $response = $this->httpClient->request('GET', $url);

        try {
            $responseBody = $response->getContent();
        } catch (HttpExceptionInterface $e) {
            // TODO: Log error to have more details about what went wrong.
            throw $e;
        }

        try {
            return json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // TODO: Log error to have more details about what went wrong.
            throw $e;
        }
    }
}
