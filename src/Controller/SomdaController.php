<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Contact;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SomdaController extends BaseController
{
    /**
     * @return Response
     */
    public function aboutAction(): Response
    {
        return $this->render('somda/about.html.twig');
    }

    /**
     * @return Response
     */
    public function advertiseAction(): Response
    {
        return $this->render('somda/advertise.html.twig');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function contactAction(Request $request)
    {
        $form = $this->formFactory->create(Contact::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->sendEmail(
                $this->doctrine->getRepository(User::class)->find(1),
                '[Somda-feedback] ' . $form->get('subject')->getData(),
                'contact',
                ['text' => $form->get('text')->getData(), 'user' => $this->getUser()]
            );

            $this->addFlash(self::FLASH_TYPE_INFORMATION, 'Je bericht is naar de beheerder verzonden');

            return $this->redirectToRoute('home');
        }

        return $this->render('somda/contact.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param string|null $choice
     * @return RedirectResponse|Response
     */
    public function disclaimerAction(string $choice = null)
    {
        if (!is_null($choice) && in_array($choice, [User::COOKIE_OK, User::COOKIE_NOT_OK])) {
            $this->getUser()->setCookieOk($choice);
            $this->doctrine->getManager()->flush();

            $this->addFlash(
                'info',
                'Dankjewel voor het doorgeven van je keuze met betrekking tot de Google Analytics cookie.'
            );
            return $this->redirectToRoute('disclaimer');
        }
        return $this->render('somda/disclaimer.html.twig');
    }

    /**
     * @param int|null $id
     * @return Response
     */
    public function profileAction(int $id = null): Response
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
