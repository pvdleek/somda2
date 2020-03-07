<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    public function HomeAction(Request $request)
    {
        // Check if there is an active banner for the header
        $activeBanners = $this->getDoctrine()->getRepository('App:Banner')->findBy(
            ['location' => Banner::LOCATION_HEADER, 'active' => true]
        );
        if (count($activeBanners) > 0) {
            $headerType = 'banner';
            $headerContent = $activeBanners[rand(0, count($activeBanners) - 1)];

            // Create a view for this banner
            $bannerView = new BannerView();
            $bannerView->setBanner($headerContent)->setTimestamp(time())->setIp(inet_pton($request->getClientIp()));
            $this->getDoctrine()->getManager()->persist($bannerView);
            $this->getDoctrine()->getManager()->flush();
        } else {
            $headerType = 'news';
            $headerContent = $this->getDoctrine()
                ->getRepository('App:RailNews')
                ->findBy(['active' => true, 'approved' => true], ['timestamp' => 'DESC'], 3)[rand(0, 2)];
        }

        return $this->render('home.html.twig', [
            'cookieChoice' => 1,
            'headerType' =>  $headerType,
            'headerContent' => $headerContent,
            'imageNumber' => rand(1, 11),
        ]);
    }
}
