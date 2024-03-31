<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Event;
use App\Repository\ActorRepository;
use App\Repository\RepoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class GhEventSerializer
{
    private ActorRepository $actorRepository;
    private RepoRepository $repoRepository;
    private EntityManagerInterface $entityManager;
    private array $localActors;
    private array $localRepos;
    private array $eventsToFlush;

    public function __construct(
        ActorRepository $actorRepository,
        RepoRepository $repoRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->actorRepository = $actorRepository;
        $this->repoRepository = $repoRepository;
        $this->entityManager = $entityManager;
        $this->localActors = [];
        $this->localRepos = [];
        $this->eventsToFlush = [];
    }

    public function deserializeEventsIntoEntities(string $content): array
    {
        $events = json_decode($content, true);

        if (!is_array($events)) {
            throw new UnexpectedValueException('Invalid JSON response received from GHArchive');
        }

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
