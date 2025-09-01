<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Train as TrainEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainEntity::class);
    }

    /**
     * @return array
     */
    public function findByTransporter(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t.number AS number')
            ->addSelect('t.name AS name')
            ->addSelect('tr.id AS transporter_id')
            ->addSelect('tr.name AS transporter_name')
            ->addSelect('np.name AS name_pattern_name')
            ->from(TrainEntity::class, 't')
            ->join('t.transporter', 'tr')
            ->leftJoin('t.namePattern', 'np')
            ->addOrderBy('tr.name', 'ASC')
            ->addOrderBy('t.number', 'ASC');
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
