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
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param RedirectHelper $redirectHelper
     */
    public function __construct(ManagerRegistry $doctrine, RedirectHelper $redirectHelper)
    {
        $this->doctrine = $doctrine;
        $this->redirectHelper = $redirectHelper;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
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
        $bannerHit->ipAddress = \ip2long($request->getClientIp());

        $this->doctrine->getManager()->persist($bannerHit);
        $this->doctrine->getManager()->flush();

        return $this->redirectHelper->redirect($banner->link);
    }
}
