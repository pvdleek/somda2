<?php

declare(strict_types=1);

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
        private readonly EmailHelper $email_helper,
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    public function aboutAction(): Response
    {
        return $this->template_helper->render('somda/about.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Over Somda',
        ]);
    }

    public function advertiseAction(): Response
    {
        return $this->template_helper->render('somda/advertise.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Adverteren op Somda',
        ]);
    }

    public function disclaimerAction(): Response
    {
        return $this->template_helper->render('somda/disclaimer.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Somda disclaimer',
        ]);
    }

    public function contactAction(Request $request): Response|RedirectResponse
    {
        $form = $this->form_helper->getFactory()->create(Contact::class);
        if (!$this->user_helper->userIsLoggedIn()) {
            $form->add(Contact::FIELD_EMAIL, TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw e-mailadres',
                FormGenerics::KEY_REQUIRED => true,
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->email_helper->sendEmail(
                $this->user_helper->getAdministratorUser(),
                '[Somda-feedback] '.$form->get('subject')->getData(),
                'contact',
                [
                    'text' => $form->get('text')->getData(),
                    'user' => $this->user_helper->getUser(),
                    'emailAddress' => $form->has(Contact::FIELD_EMAIL) ?
                        $form->get(Contact::FIELD_EMAIL)->getData() : null,
                ]
            );

            return $this->form_helper->finishFormHandling('Je bericht is naar de beheerder verzonden', 'home');
        }

        return $this->template_helper->render('somda/contact.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Contact opnemen',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
