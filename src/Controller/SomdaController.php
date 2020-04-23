<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Contact;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SomdaController
{
    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var FormHelper
     */
    private $formHelper;

    /**
     * @var TemplateHelper
     */
    private $templateHelper;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     * @param EmailHelper $emailHelper
     */
    public function __construct(
        UserHelper $userHelper,
        FormHelper $formHelper,
        TemplateHelper $templateHelper,
        EmailHelper $emailHelper
    ) {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @return Response
     */
    public function aboutAction(): Response
    {
        return $this->templateHelper->render('somda/about.html.twig');
    }

    /**
     * @return Response
     */
    public function advertiseAction(): Response
    {
        return $this->templateHelper->render('somda/advertise.html.twig');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function contactAction(Request $request)
    {
        $form = $this->formHelper->getFactory()->create(Contact::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailHelper->sendEmail(
                $this->userHelper->getAdministratorUser(),
                '[Somda-feedback] ' . $form->get('subject')->getData(),
                'contact',
                ['text' => $form->get('text')->getData(), 'user' => $this->userHelper->getUser()]
            );

            return $this->formHelper->finishFormHandling('Je bericht is naar de beheerder verzonden', 'home');
        }

        return $this->templateHelper->render('somda/contact.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param string|null $choice
     * @return RedirectResponse|Response
     */
    public function disclaimerAction(string $choice = null)
    {
        if (!is_null($choice) && in_array($choice, [User::COOKIE_OK, User::COOKIE_NOT_OK])) {
            $this->userHelper->getUser()->cookieOk = $choice;

            return $this->formHelper->finishFormHandling(
                'Dankjewel voor het doorgeven van je keuze met betrekking tot de Google Analytics cookie.',
                'disclaimer'
            );
        }
        return $this->templateHelper->render('somda/disclaimer.html.twig');
    }
}
