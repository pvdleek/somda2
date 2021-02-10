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
            ->andWhere('r.approved = false OR r.active = true')
            ->addOrderBy('approved', 'DESC')
            ->addOrderBy(RailNewsForm::FIELD_TIMESTAMP, 'DESC')
            ->setMaxResults($limit);
        return $queryBuilder->getQuery()->getResult();
    }
}
