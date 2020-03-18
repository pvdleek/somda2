<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class SomdaController extends BaseController
{
    /**
     * @param int|null $id
     * @return Response
     */
    public function profileAction(int $id = null) : Response
    {
        $this->breadcrumbHelper->addPart('general.navigation.somda.home', 'somda_home');
        $this->breadcrumbHelper->addPart('general.navigation.somda.profile', 'profile_view', ['id' => $id], true);

        if (is_null($id)) {
            $user = $this->getUser();
        } else {
            $user = $this->doctrine->getRepository(User::class)->find($id);
        }

        return $this->render('somda/profile.html.twig', ['user' => $user]);
    }
}
