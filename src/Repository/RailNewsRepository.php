<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RailNews;
use App\Form\RailNews as RailNewsForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RailNews>
 */
class RailNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RailNews::class);
    }

    /**
     * @param int $limit
     * @return RailNews[]
     */
    public function findForManagement(int $limit): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RailNews::class, 'r')
            ->andWhere('r.approved = 0 OR r.active = 1')
            ->addOrderBy('r.approved', 'ASC')
            ->addOrderBy('r.'.RailNewsForm::FIELD_TIMESTAMP, 'DESC')
            ->setMaxResults($limit);
        return $query_builder->getQuery()->getResult();
    }
}
