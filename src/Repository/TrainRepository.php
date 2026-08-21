<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Train;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Train>
 */
class TrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Train::class);
    }

    /**
     * @return array
     */
    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByTransporter(): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t.number AS number')
            ->addSelect('t.name AS name')
            ->addSelect('tr.id AS transporter_id')
            ->addSelect('tr.name AS transporter_name')
            ->addSelect('np.name AS name_pattern_name')
            ->from(Train::class, 't')
            ->join('t.transporter', 'tr')
            ->leftJoin('t.name_pattern', 'np')
            ->addOrderBy('tr.name', 'ASC')
            ->addOrderBy('t.number', 'ASC');
        return $query_builder->getQuery()->getArrayResult();
    }
}
