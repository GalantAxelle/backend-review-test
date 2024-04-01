<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Event;
use App\Repository\ActorRepository;
use App\Repository\RepoRepository;
use Doctrine\ORM\EntityManagerInterface;

class GhEventSerializer
{
    private array $localActors;
    private array $localRepos;
    private array $eventsToFlush;

    public function __construct(
        private ActorRepository $actorRepository,
        private RepoRepository $repoRepository,
        private EntityManagerInterface $entityManager
    ) {
        $this->localActors = [];
        $this->localRepos = [];
        $this->eventsToFlush = [];
    }

    /**
     * @param array $events Serialized events fetched from GH archive
     *
     * @return Event[]
     */
    public function deserializeEventsIntoEntities(array $events): array
    {
        foreach ($events as $event) {
            $this->processEvent($event);
        }

        //TODO: improve variable name
        return $this->eventsToFlush;
    }

    private function processEvent(array $event): void
    {
        $actor = null;
        $repo = null;

        if (!isset($this->localActors[$event['actor']['login']])) {
            $actor = $this->actorRepository->findOrCreate($event['actor']);
            $this->localActors[$event['actor']['login']] = $actor;
        }

        if (!isset($this->localRepos[$event['repo']['name']])) {
            $repo = $this->repoRepository->findOrCreate($event['repo']);
            $this->localRepos[$event['repo']['name']] = $repo;
        }

        $eventEntity = new Event(
            $event['id'],
            $event['type'],
            $actor ?? $this->localActors[$event['actor']['login']],
            $repo ?? $this->localRepos[$event['repo']['name']],
            $event['payload'],
            new \DateTimeImmutable($event['created_at']),
            $event['comment'] ?? null
        );

        $this->eventsToFlush[] = $eventEntity;
        $this->entityManager->persist($eventEntity);
    }
}
