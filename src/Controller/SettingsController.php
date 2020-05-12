<?php

namespace App\Controller;

use App\Entity\UserPreference;
use App\Form\UserPreferences;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController
{
    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param FormHelper $formHelper
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(FormHelper $formHelper, UserHelper $userHelper, TemplateHelper $templateHelper)
    {
        $this->formHelper = $formHelper;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        /**
         * @var UserPreference[] $allSettings
         */
        $allSettings = $this->formHelper
            ->getDoctrine()
            ->getRepository(UserPreference::class)
            ->findBy([], ['order' => 'ASC']);
        $form = $this->formHelper->getFactory()->create(
            UserPreferences::class,
            $this->userHelper->getUser(),
            ['userHelper' => $this->userHelper, 'allSettings' => $allSettings]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($allSettings as $setting) {
                if ($setting->order > 0) {
                    $userPreference = $this->userHelper->getPreferenceByKey($setting->key);
                    if (is_object($form->get($setting->key)->getData())) {
                        $userPreference->value = $form->get($setting->key)->getData()->name;
                    } else {
                        $userPreference->value = $form->get($setting->key)->getData();
                    }
                }
            }

            return $this->formHelper->finishFormHandling('Je instellingen zijn opgeslagen', 'settings');
        }

        return $this->templateHelper->render('settings/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Instellingen',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
