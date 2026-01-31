<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserPreference;
use App\Form\UserPreferences;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController
{
    public function __construct(
        private readonly FormHelper $form_helper,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(Request $request): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var UserPreference[] $all_settings
         */
        $all_settings = $this->form_helper->getDoctrine()->getRepository(UserPreference::class)->findBy([], ['order' => 'ASC']);
        $form = $this->form_helper->getFactory()->create(
            UserPreferences::class,
            $this->user_helper->getUser(),
            ['user_helper' => $this->user_helper, 'all_settings' => $all_settings]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($all_settings as $setting) {
                if ($setting->order > 0) {
                    $user_preference = $this->user_helper->getPreferenceByKey($setting->key);
                    if (\is_object($form->get($setting->key)->getData())) {
                        $user_preference->value = (string) $form->get($setting->key)->getData()->name;
                    } else {
                        $user_preference->value = (string) $form->get($setting->key)->getData() ?? '';
                    }
                }
            }

            return $this->form_helper->finishFormHandling('Je instellingen zijn opgeslagen', 'settings');
        }

        return $this->template_helper->render('settings/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Instellingen',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
