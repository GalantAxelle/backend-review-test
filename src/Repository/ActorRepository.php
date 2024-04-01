<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Actor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Actor::class);
    }

    public function findOrCreate(array $actorArray): Actor
    {
        $actor = $this->find($actorArray['id']);

        if (!$actor) {
            $actor = new Actor(
                $actorArray['id'],
                $actorArray['login'],
                $actorArray['url'],
                $actorArray['avatar_url']
            );

            $this->_em->persist($actor);
        }

        return $actor;
    }
}
