<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class SomdaController extends BaseController
{
    /**
     * @param int|null $id
     * @return Response
     */
    public function profileAction(int $id = null) : Response
    {
        $this->get('app.breadcrumb_helper')->addPart('general.navigation.somda.home', 'somda_home');
        $this->get('app.breadcrumb_helper')->addPart('general.navigation.somda.profile', 'profile_view', ['id' => $id], true);

        if (is_null($id)) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        }

        return $this->render('@App/somda/profile.html.twig', [
            'user' => $user,
        ]);
    }
}
