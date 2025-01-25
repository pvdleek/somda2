<?php

namespace App\Controller;

use App\Form\Contact;
use App\Generics\FormGenerics;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SomdaController
{
    public function __construct(
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly EmailHelper $emailHelper,
    ) {
    }

    public function aboutAction(): Response
    {
        return $this->templateHelper->render('somda/about.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Over Somda',
        ]);
    }

    public function advertiseAction(): Response
    {
        return $this->templateHelper->render('somda/advertise.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Adverteren op Somda',
        ]);
    }

    public function contactAction(Request $request): Response|RedirectResponse
    {
        $form = $this->formHelper->getFactory()->create(Contact::class);
        if (!$this->userHelper->userIsLoggedIn()) {
            $form->add(Contact::FIELD_EMAIL, TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw e-mailadres',
                FormGenerics::KEY_REQUIRED => true,
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailHelper->sendEmail(
                $this->userHelper->getAdministratorUser(),
                '[Somda-feedback] ' . $form->get('subject')->getData(),
                'contact',
                [
                    'text' => $form->get('text')->getData(),
                    'user' => $this->userHelper->getUser(),
                    'emailAddress' => $form->has(Contact::FIELD_EMAIL) ?
                        $form->get(Contact::FIELD_EMAIL)->getData() : null,
                ]
            );

            return $this->formHelper->finishFormHandling('Je bericht is naar de beheerder verzonden', 'home');
        }

        return $this->templateHelper->render('somda/contact.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Contact opnemen',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
