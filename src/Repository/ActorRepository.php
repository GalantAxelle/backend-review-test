<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Actor;
use Doctrine\ORM\EntityRepository;

class ActorRepository extends EntityRepository
{
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
