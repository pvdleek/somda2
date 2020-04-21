<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\RailNews;
use App\Entity\User;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\Controller\TrainTableHelper;
use App\Helpers\MenuHelper;
use App\Helpers\UserHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

abstract class BaseController
{
    protected const REDIRECT_STATUS = 302;

    protected const FLASH_TYPE_INFORMATION = 'info';
    protected const FLASH_TYPE_WARNING = 'warn';
    protected const FLASH_TYPE_ERROR = 'alert';

    private const ADMINISTRATOR_UID = 1;
    private const MODERATOR_UID = 2;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var BreadcrumbHelper
     */
    protected $breadcrumbHelper;

    /**
     * @var MenuHelper
     */
    private $menuHelper;

    /**
     * @var UserHelper
     */
    protected $userHelper;

    /**
     * @var TrainTableHelper
     */
    protected $trainTableHelper;

    /**
     * @param RequestStack $requestStack
     * @param Security $security
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @param Environment $environment
     * @param RouterInterface $router
     * @param FormFactoryInterface $formFactory
     * @param MailerInterface $mailer
     * @param BreadcrumbHelper $breadcrumbHelper
     * @param MenuHelper $menuHelper
     * @param UserHelper $userHelper
     * @param TrainTableHelper $trainTableHelper
     */
    public function __construct(
        RequestStack $requestStack,
        Security $security,
        ManagerRegistry $registry,
        LoggerInterface $logger,
        Environment $environment,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        MailerInterface $mailer,
        BreadcrumbHelper $breadcrumbHelper,
        MenuHelper $menuHelper,
        UserHelper $userHelper,
        TrainTableHelper $trainTableHelper
    ) {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->doctrine = $registry;
        $this->logger = $logger;
        $this->twig = $environment;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->mailer = $mailer;
        $this->breadcrumbHelper = $breadcrumbHelper;
        $this->menuHelper = $menuHelper;
        $this->userHelper = $userHelper;
        $this->trainTableHelper = $trainTableHelper;
    }

    /**
     * @return User
     */
    protected function getUser(): ?User
    {
        if ($this->userIsLoggedIn()) {
            return $this->security->getUser();
        }
        return null;
    }

    /**
     * @return User
     */
    protected function getAdministratorUser(): User
    {
        return $this->doctrine->getRepository(User::class)->find(self::ADMINISTRATOR_UID);
    }

    /**
     * @return User
     */
    protected function getModeratorUser(): User
    {
        return $this->doctrine->getRepository(User::class)->find(self::MODERATOR_UID);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        try {
            $content = $this->twig->render($view, $this->getParameters($parameters));
        } catch (Exception $exception) {
            $this->logger->addRecord(
                Logger::CRITICAL,
                'Error when rendering view "' . $view . '": "' . $exception->getMessage() . '"'
            );
            $content = '';
        }

        if (null === $response) {
            $response = new Response();
        }

        $this->getUser()->lastVisit = new DateTime();
        $this->doctrine->getManager()->flush();

        $response->setContent($content);
        return $response;
    }

    /**
     * @param array $viewParameters
     * @return array
     * @throws Exception
     */
    private function getParameters(array $viewParameters): array
    {
        // Check if there is an active banner for the header
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_HEADER, 'active' => true]
        );
        if (count($banners) > 0) {
            $headerType = 'banner';
            $headerContent = $banners[rand(0, count($banners) - 1)];

            // Create a view for this banner
            $bannerView = new BannerView();
            $bannerView->banner = $headerContent;
            $bannerView->timestamp = new DateTime();
            $bannerView->ipAddress = inet_pton($this->requestStack->getCurrentRequest()->getClientIp());
            $this->doctrine->getManager()->persist($bannerView);
            $this->doctrine->getManager()->flush();
        } else {
            $headerType = 'news';
            $headerContent = $this->doctrine
                ->getRepository(RailNews::class)
                ->findBy(['active' => true, 'approved' => true], ['timestamp' => 'DESC'], 3)[rand(0, 2)];
        }

        return array_merge($viewParameters, [
            'headerType' =>  $headerType,
            'headerContent' => $headerContent,
            'imageNumber' => rand(1, 11),
            'breadcrumb' => $this->breadcrumbHelper->getBreadcrumb(),
            'menuStructure' => $this->menuHelper->getMenuStructure(),
            'defaultTrainTableYearId' => $this->trainTableHelper->getDefaultTrainTableYear()->getId(),
            'nrOfOpenForumAlerts' => $this->menuHelper->getNumberOfOpenForumAlerts(),
        ]);
    }

    /**
     * @param string $type
     * @param string $message
     */
    protected function addFlash(string $type, string $message): void
    {
        if (!in_array($type, [self::FLASH_TYPE_INFORMATION, self::FLASH_TYPE_WARNING, self::FLASH_TYPE_ERROR])) {
            $type = self::FLASH_TYPE_ERROR;
        }
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add($type, $message);
    }

    /**
     * @return bool
     */
    protected function userIsLoggedIn(): bool
    {
        return $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * @param string $url
     * @param int $status
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @param string $route
     * @param array $parameters
     * @return RedirectResponse
     */
    protected function redirectToRoute(string $route, array $parameters = []): RedirectResponse
    {
        return $this->redirect(
            $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH),
            self::REDIRECT_STATUS
        );
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $parameters
     * @return bool
     */
    protected function sendEmail(User $user, string $subject, string $template, array $parameters = []): bool
    {
        if (isset($parameters['from'])) {
            $from = new Address($parameters['from'][0], $parameters['from'][1]);
            unset($parameters['from']);
        } else {
            $from = new Address('webmaster@somda.nl', 'Somda');
        }

        $message = (new TemplatedEmail())
            ->from($from)
            ->to(new Address($user->email, $user->username))
            ->subject($subject)
            ->htmlTemplate('emails/' . $template . '.html.twig')
            ->textTemplate('emails/' . $template . '.text.twig')
            ->context($parameters);
        try {
            $this->mailer->send($message);
            return true;
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                'Failed to send email with subject "' . $subject . '" to user with id ' . $user->getId()
            );
            $this->logger->critical($exception->getMessage());
        }
        return false;
    }
}
