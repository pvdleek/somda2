<?php

namespace App\Controller;

use App\Entity\RailNews;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $railNews = $this->doctrine
            ->getRepository(RailNews::class)
            ->findBy(['active' => true, 'approved' => true], ['dateTime' => 'DESC'], 5);

        return $this->render('home.html.twig', ['railNews' => $railNews]);
    }
}
