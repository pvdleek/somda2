<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\RailNews as RailNewsEntity;
use App\Form\RailNews as RailNewsForm;
use Doctrine\ORM\EntityRepository;

class RailNews extends EntityRepository
{
    /**
     * @param int $limit
     * @return RailNewsEntity[]
     */
    public function findForManagement(int $limit): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RailNewsEntity::class, 'r')
            ->andWhere('r.approved = 0 OR r.active = 1')
            ->addOrderBy('r.approved', 'ASC')
            ->addOrderBy('r.' . RailNewsForm::FIELD_TIMESTAMP, 'DESC')
            ->setMaxResults($limit);
        return $queryBuilder->getQuery()->getResult();
    }
}
