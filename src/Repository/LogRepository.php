<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function removeByUser(User $user): void
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(Log::class, 'l')
            ->where('l.user = :user')
            ->setParameter('user', $user);
        $query_builder->getQuery()->execute();
    }
}
