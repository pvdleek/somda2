<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\BaseForm;
use App\Form\Contact;
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
    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var EmailHelper
     */
    private EmailHelper $emailHelper;

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
        return $this->templateHelper->render('somda/about.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Over Somda',
        ]);
    }

    /**
     * @return Response
     */
    public function advertiseAction(): Response
    {
        return $this->templateHelper->render('somda/advertise.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Adverteren op Somda',
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function contactAction(Request $request)
    {
        $form = $this->formHelper->getFactory()->create(Contact::class);
        if (!$this->userHelper->userIsLoggedIn()) {
            $form->add(Contact::FIELD_EMAIL, TextType::class, [
                BaseForm::KEY_LABEL => 'Jouw e-mailadres',
                BaseForm::KEY_REQUIRED => true,
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
        return $this->templateHelper->render('somda/disclaimer.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Disclaimer en cookies',
        ]);
    }
}
