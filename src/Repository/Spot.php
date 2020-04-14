<?php

namespace App\Repository;

use App\Entity\Spot as SpotEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class Spot extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(SpotEntity::class, 's');
        try {
            return $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        } catch (NoResultException $exception) {
            return null;
        }
    }
}
