<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\Block;
use App\Entity\BlockRight;
use App\Entity\Group;
use App\Entity\RailNews;
use App\Entity\User;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\Controller\TrainTableHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

abstract class BaseController
{
    protected const REDIRECT_STATUS = 302;

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
     * @var BreadcrumbHelper
     */
    protected $breadcrumbHelper;

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
     * @param BreadcrumbHelper $breadcrumbHelper
     * @param TrainTableHelper $trainTableHelper
     */
    public function __construct(
        RequestStack $requestStack,
        Security $security,
        ManagerRegistry $registry,
        LoggerInterface $logger,
        Environment $environment,
        RouterInterface $router,
        BreadcrumbHelper $breadcrumbHelper,
        TrainTableHelper $trainTableHelper
    ) {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->doctrine = $registry;
        $this->logger = $logger;
        $this->twig = $environment;
        $this->router = $router;
        $this->breadcrumbHelper = $breadcrumbHelper;
        $this->trainTableHelper = $trainTableHelper;
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        if ($this->userIsLoggedIn()) {
            return $this->security->getUser();
        }
        return new User();
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

        $response->setContent($content);
        return $response;
    }

    /**
     * @param array $viewParameters
     * @return array
     */
    private function getParameters(array $viewParameters): array
    {
        // Check if there is an active banner for the header
        $activeBanners = $this->doctrine->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_HEADER, 'active' => true]
        );
        if (count($activeBanners) > 0) {
            $headerType = 'banner';
            $headerContent = $activeBanners[rand(0, count($activeBanners) - 1)];

            // Create a view for this banner
            $bannerView = new BannerView();
            $bannerView
                ->setBanner($headerContent)
                ->setTimestamp(time())
                ->setIp(inet_pton($this->requestStack->getCurrentRequest()->getClientIp()));
            $this->doctrine->getManager()->persist($bannerView);
            $this->doctrine->getManager()->flush();
        } else {
            $headerType = 'news';
            $headerContent = $this->doctrine
                ->getRepository(RailNews::class)
                ->findBy(['active' => true, 'approved' => true], ['dateTime' => 'DESC'], 3)[rand(0, 2)];
        }

//        if ($session->logged_in) {
//            $query = 'SELECT u.value
//    FROM '.DB_PREFIX.'_users_prefs u
//    JOIN '.DB_PREFIX.'_prefs p ON p.prefid=u.prefid
//    WHERE u.uid='.$session->uid.' AND p.sleutel=\'tdr_tabel\'';
//            $dbset_pref = $db->query($query);
//            list($tdrtabel) = $db->fetchRow($dbset_pref);
//            $db->freeResult($dbset_pref);
//        } else {
//            $tdrtabel = '1';
//        }

        return array_merge($viewParameters, [
            'cookieChoice' => $this->getUser()->getCookieOk(),
            'headerType' =>  $headerType,
            'headerContent' => $headerContent,
            'imageNumber' => rand(1, 11),
            'menuStructure' => $this->getMenuStructure(),
        ]);
    }

    /**
     * @return array
     */
    private function getMenuStructure(): array
    {
        $blocks = $this->doctrine->getRepository(Block::class)->getMenuStructure();
        $allowedBlocks = [];

        foreach ($blocks as $block) {
            if (is_null($block['role'])
                || ($this->getUser()->hasRole($block['role']) || $this->getUser()->hasRole('ROLE_SUPER_ADMIN'))
            ) {
                $allowedBlocks[] = $block;
            }
        }

        return $allowedBlocks;


        // Haal eventueel alvast het aantal openstaande alerts op
//        if ($session->allowed(70)) {
//            $query = 'SELECT COUNT(*) FROM ' . DB_PREFIX . '_forum_alerts WHERE closed=\'0\'';
//            $dbset_alerts = $db->query($query);
//            list($nr_of_alerts) = $db->fetchRow($dbset_alerts);
//            $db->freeResult($dbset_alerts);
//        } else {
//            $nr_of_alerts = 0;
//        }

        $menuStructure = [];

        $menuParents = $this->doctrine->getRepository(Block::class)->findBy(
            ['parentBlock' => null, 'type' => Block::BLOCK_TYPE_PUBLIC]
        );
        foreach ($menuParents as $menuParent) {
            $menuStructure[$menuParent->getId()] = [
                'id' => $menuParent->getId(),
                'volgorde' => $menuParent->getMenuOrder(),
                'name' => $menuParent->getName(),
                //TODO
                'path' => strlen($menuParent->getShortUrl()) > 0 ? $menuParent->getShortUrl() : 'home',
            ];
            // TODO
//            if ($menuParent->getShortUrl() == 'forum_home' && $session->allowed(70)) {
//                $menuStructure[$menuParent->getId()]['alerts'] = $nr_of_alerts;
//            }
            if (strlen($menuParent->getShortUrl()) > 0) {
                $menuStructure[$menuParent->getId()]['children'][0] = [
                    'id' => 0,
                    'volgorde' => 0,
                    'name' => 'Homepagina ' . strtolower($menuParent->getName()),
                    'path' => $menuParent->getShortUrl(),
                    'followed_by_separator' => true
                ];
            }

            $menuChildren = $this->doctrine->getRepository(Block::class)->findBy(['parentBlock' => $menuParent]);
            $childFound = false;
            foreach ($menuChildren as $menuChild) {
                if ($this->shouldDoBlock($menuChild)) {
                    $childFound = true;

                    if ($menuChild->getId() === 54) {
                        $path = 'logout';
                    } elseif (strlen($menuChild->getShortUrl()) < 1) {
                        //TODO
                        $path = 'home';
                    } else {
                        $path = $menuChild->getShortUrl();
                    }

                    $menuStructure[$menuParent->getId()]['children'][$menuChild->getId()] = [
                        'id' => $menuChild->getId(),
                        'volgorde' => $menuChild->getMenuOrder(),
                        'name' => $menuChild->getName(),
                        'path' => $path,
                        'followed_by_separator' => $menuChild->getDoSeparator() === '1',
                    ];
                    // TODO
//                    if ($url_short == 'forum/meldingen' && $session->allowed(70)) {
//                        $menuStructure[$parent_id]['children'][$menuChild->getId()]['alerts'] = $nr_of_alerts;
//                    }
                }
            }
            if (!$childFound) {
                unset($menuStructure[$menuParent->getId()]);
            }
        }

        return $menuStructure;
    }

    /**
     * @param Block $block
     * @return bool
     */
    protected function shouldDoBlock(Block $block): bool
    {
        return is_null($block->getRole())
            || ($this->getUser()->hasRole($block->getRole()) || $this->getUser()->hasRole('ROLE_SUPER_ADMIN'));
    }

    /**
     * @return bool
     */
    protected function userIsLoggedIn(): bool
    {
        return $this->security->isGranted('IS_AUTHENTICATED_FULLY');
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
}
