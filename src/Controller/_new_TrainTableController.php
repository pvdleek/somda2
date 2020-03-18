<?php

namespace App\Controller;

use App\Form\TrainTable as TrainTableForm;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class _new_TrainTableController extends BaseController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param RequestStack $requestStack
     * @param Security $security
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @param Environment $environment
     * @param RouterInterface $router
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        RequestStack $requestStack,
        Security $security,
        ManagerRegistry $registry,
        LoggerInterface $logger,
        Environment $environment,
        RouterInterface $router,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($requestStack, $security, $registry, $logger, $environment, $router);

        $this->formFactory = $formFactory;
    }

    public function indexAction(int $trainTable = null, int $routeNumber = null) : Response
    {
        $form = $this->formFactory->create(TrainTableForm::class);

        return $this->render('TrainTable/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function postAction(Request $request): RedirectResponse
    {
        $form = $this->formFactory->create(TrainTableForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('dienstregeling_with_data', [
                'trainTable' => $form->get('trainTableYear')->getData()->getId(),
                'routeNumber' => $form->get('routeNumber')->getData()
            ]);
        }
        return $this->redirectToRoute('dienstregeling');
    }
}
