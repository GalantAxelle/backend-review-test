<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Repo;
use Doctrine\ORM\EntityRepository;

class RepoRepository extends EntityRepository
{
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
