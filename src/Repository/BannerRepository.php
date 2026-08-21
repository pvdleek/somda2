<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Banner;
use App\Entity\BannerHit;
use App\Entity\BannerView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Banner>
 */
class BannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    /**
     * @return int
     */
    public function getNumberOfHits(Banner $banner): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(bh.id) AS hits')
            ->from(BannerHit::class, 'bh')
            ->andWhere('bh.banner = :banner')
            ->setParameter('banner', $banner)
            ->setMaxResults(1);
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getNumberOfViews(Banner $banner): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(bv.id) AS hits')
            ->from(BannerView::class, 'bv')
            ->andWhere('bv.banner = :banner')
            ->setParameter('banner', $banner)
            ->setMaxResults(1);
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }
}
