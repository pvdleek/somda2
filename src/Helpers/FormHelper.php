<?php

namespace App\Helpers;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FormHelper
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $factory;

    /**
     * @var FlashHelper
     */
    private FlashHelper $flashHelper;

    /**
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormFactoryInterface $formFactory
     * @param FlashHelper $flashHelper
     * @param RedirectHelper $redirectHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactoryInterface $formFactory,
        FlashHelper $flashHelper,
        RedirectHelper $redirectHelper
    ) {
        $this->doctrine = $doctrine;
        $this->factory = $formFactory;
        $this->flashHelper = $flashHelper;
        $this->redirectHelper = $redirectHelper;
    }

    /**
     * @return ManagerRegistry
     */
    public function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    /**
     * @return FormFactoryInterface
     */
    public function getFactory(): FormFactoryInterface
    {
        return $this->factory;
    }

    /**
     * @param string $flashMessage
     * @param string $route
     * @param array $routeParameters
     * @return RedirectResponse
     */
    public function finishFormHandling(
        string $flashMessage,
        string $route,
        array $routeParameters = []
    ): RedirectResponse {
        $this->doctrine->getManager()->flush();

        if (strlen($flashMessage) > 0) {
            $this->flashHelper->add(FlashHelper::FLASH_TYPE_INFORMATION, $flashMessage);
        }

        return $this->redirectHelper->redirectToRoute($route, $routeParameters);
    }
}
