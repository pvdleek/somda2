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
        private readonly RedirectHelper $redirect_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function clickOutAction(Request $request, int $id): RedirectResponse
    {
        /** @var Banner|null $banner */
        $banner = $this->doctrine->getRepository(Banner::class)->find($id);
        if (null === $banner) {
            return $this->redirect_helper->redirectToRoute('home');
        }

        $banner_hit = new BannerHit();
        $banner_hit->banner = $banner;
        $banner_hit->timestamp = new \DateTime();
        $banner_hit->ip_address = \ip2long($request->getClientIp());

        $this->doctrine->getManager()->persist($banner_hit);
        $this->doctrine->getManager()->flush();

        return $this->redirect_helper->redirect($banner->link);
    }
}
