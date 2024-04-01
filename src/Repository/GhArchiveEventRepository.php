<?php

declare(strict_types=1);

namespace App\Repository;

use App\Client\GhArchiveClient;
use App\Serializer\GhEventSerializer;

class GhArchiveEventRepository
{
    public function __construct(
        private GhArchiveClient $client,
        private GhEventSerializer $serializer
    ) {
    }

    public function findAllWithDateAndHour(string $date, int $hour)
    {
        $responseContent = $this->client->getEventsForDateAndHour($date, $hour);
        //TODO: Manage the entities -> flush
        $events = $this->serializer->deserializeEventsIntoEntities($responseContent);
        dd($events[0]);
    }
}
