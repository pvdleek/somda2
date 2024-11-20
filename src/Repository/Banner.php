<?php

namespace App\Repository;

use App\Entity\Banner as BannerEntity;
use App\Entity\BannerHit;
use App\Entity\BannerView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Banner extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BannerEntity::class);
    }

    /**
     * @return int
     */
    public function getNumberOfHits(BannerEntity $banner): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(bh.id) AS hits')
            ->from(BannerHit::class, 'bh')
            ->andWhere('bh.banner = :banner')
            ->setParameter('banner', $banner)
            ->setMaxResults(1);
        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getNumberOfViews(BannerEntity $banner): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(bv.id) AS hits')
            ->from(BannerView::class, 'bv')
            ->andWhere('bv.banner = :banner')
            ->setParameter('banner', $banner)
            ->setMaxResults(1);
        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }
}
