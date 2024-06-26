<?php

namespace App\Repository;

use App\Entity\Log as LogEntity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Log extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntity::class);
    }

    public function removeByUser(User $user): void
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(LogEntity::class, 'l')
            ->where('l.user = :user')
            ->setParameter('user', $user);
        $queryBuilder->getQuery()->execute();
    }
}
