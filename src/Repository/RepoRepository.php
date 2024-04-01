<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Repo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RepoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Repo::class);
    }

    public function findOrCreate(array $repoArray): Repo
    {
        $repo = $this->find($repoArray['id']);

        if (!$repo) {
            $repo = new Repo(
                $repoArray['id'],
                $repoArray['name'],
                $repoArray['url']
            );

            $this->_em->persist($repo);
        }

        return $repo;
    }
}
