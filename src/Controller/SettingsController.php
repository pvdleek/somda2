<?php

namespace App\Controller;

use App\Entity\UserPreference;
use App\Form\UserPreferences;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends BaseController
{
    /**
     * @param Request $request
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        /**
         * @var UserPreference[] $allSettings
         */
        $allSettings = $this->doctrine->getRepository(UserPreference::class)->findBy([], ['order' => 'ASC']);
        $form = $this->formFactory->create(
            UserPreferences::class,
            $this->getUser(),
            ['userHelper' => $this->userHelper, 'allSettings' => $allSettings]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($allSettings as $setting) {
                if ($setting->getOrder() > 0) {
                    $userPreference = $this->userHelper->getPreferenceByKey($this->getUser(), $setting->getKey());
                    if (is_object($form->get($setting->getKey())->getData())) {
                        $userPreference->setValue($form->get($setting->getKey())->getData()->getName());
                    } else {
                        $userPreference->setValue($form->get($setting->getKey())->getData());
                    }
                }
            }
            $this->doctrine->getManager()->flush();

            $this->addFlash(self::FLASH_TYPE_INFORMATION, 'Je instellingen zijn opgeslagen');

            return $this->redirectToRoute('settings');
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
