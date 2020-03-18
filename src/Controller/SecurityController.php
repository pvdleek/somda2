<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController
{
    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function loginAction(Request $request)
    {
        if (is_null($this->getUser())) {
            return parent::loginAction($request);
        } else {
            // Don't show the login page to already logged in users
            return new RedirectResponse($this->get('router')->generate('home'));
        }
    }
}
