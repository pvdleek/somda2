<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Train as TrainEntity;
use Doctrine\ORM\EntityRepository;

class Train extends EntityRepository
{
    /**
     * @return array
     */
    public function findByTransporter(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t.number AS number')
            ->addSelect('t.name AS name')
            ->addSelect('tr.id AS transporterId')
            ->addSelect('tr.name AS transporterName')
            ->addSelect('np.name AS namePatternName')
            ->from(TrainEntity::class, 't')
            ->join('t.transporter', 'tr')
            ->leftJoin('t.namePattern', 'np')
            ->addOrderBy('tr.name', 'ASC')
            ->addOrderBy('t.number', 'ASC');
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
