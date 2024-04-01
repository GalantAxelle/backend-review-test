<?php

declare(strict_types=1);

namespace App\Client;

use App\Decoder\JsonLinesDecoder;
use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GhArchiveClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private JsonLinesDecoder $decoder,
    )
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
            $unzippedContent = gzdecode($responseBody);
            return $this->decoder->decodeJsonLinesToArray($unzippedContent);
        } catch (JsonException $e) {
            // TODO: Log error to have more details about what went wrong.
            throw $e;
        }
    }
}
