<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerHit;
use App\Helpers\RedirectHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class BannerController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly RedirectHelper $redirectHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function clickOutAction(Request $request, int $id): RedirectResponse
    {
        /**
         * @var Banner $banner
         */
        $banner = $this->doctrine->getRepository(Banner::class)->find($id);
        if (null === $banner) {
            return $this->redirectHelper->redirectToRoute('home');
        }

        $bannerHit = new BannerHit();
        $bannerHit->banner = $banner;
        $bannerHit->timestamp = new \DateTime();
        $bannerHit->ip_address = \ip2long($request->getClientIp());

        $this->doctrine->getManager()->persist($bannerHit);
        $this->doctrine->getManager()->flush();

        return $this->redirectHelper->redirect($banner->link);
    }
}
