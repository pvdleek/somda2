<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\Block;
use App\Entity\BlockHelp;
use App\Entity\RailNews;
use App\Entity\UserPreference;
use App\Form\RailNews as RailNewsForm;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Mobile_Detect;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TemplateHelper
{
    public const PARAMETER_DAY_NUMBER = 'dayNumber';
    public const PARAMETER_DISCUSSION = 'discussion';
    public const PARAMETER_FORM = 'form';
    public const PARAMETER_FORUM = 'forum';
    public const PARAMETER_PAGE_TITLE = 'pageTitle';
    public const PARAMETER_TRAIN_TABLE_INDICES = 'trainTableIndices';
    public const PARAMETER_TRAIN_TABLE_INDEX = 'trainTableIndex';
    public const PARAMETER_TRAIN_TABLE_INDEX_NUMBER = 'trainTableIndexNumber';

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var ManagerRegistry
     */
    protected ManagerRegistry $doctrine;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @var MenuHelper
     */
    private MenuHelper $menuHelper;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @param RequestStack $requestStack
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @param Environment $environment
     * @param MenuHelper $menuHelper
     * @param UserHelper $userHelper
     */
    public function __construct(
        RequestStack $requestStack,
        ManagerRegistry $registry,
        LoggerInterface $logger,
        Environment $environment,
        MenuHelper $menuHelper,
        UserHelper $userHelper
    ) {
        $this->requestStack = $requestStack;
        $this->doctrine = $registry;
        $this->logger = $logger;
        $this->twig = $environment;
        $this->menuHelper = $menuHelper;
        $this->userHelper = $userHelper;
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    public function render(string $view, array $parameters = [], Response $response = null): Response
    {
        try {
            $content = $this->twig->render($this->getCorrectView($view), $this->getParameters($parameters));
        } catch (Exception $exception) {
            $this->logger->critical('Error when rendering view "' . $view . '": "' . $exception->getMessage() . '"');
            $content = '';
        }

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);
        return $response;
    }

    /**
     * @param string $view
     * @return string
     * @throws Exception
     */
    private function getCorrectView(string $view): string
    {
        if ($this->userHelper->userIsLoggedIn()
            && $this->userHelper->getPreferenceByKey(UserPreference::KEY_FORCE_DESKTOP)->value === '1'
        ) {
            return $view;
        }

        $detect = new Mobile_Detect();
        if ($detect->isMobile() && file_exists(__DIR__ . '/../../templates/mobile/' . $view)) {
            $view = 'mobile/' . $view;
        }
        return $view;
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
            $headerContent = $banners[random_int(0, count($banners) - 1)];

            // Create a view for this banner
            $bannerView = new BannerView();
            $bannerView->banner = $headerContent;
            $bannerView->timestamp = new DateTime();
            $bannerView->ipAddress = ip2long($this->requestStack->getCurrentRequest()->getClientIp());
            $this->doctrine->getManager()->persist($bannerView);
            $this->doctrine->getManager()->flush();
        } else {
            $headerType = 'news';
            $headerContent = $this->doctrine->getRepository(RailNews::class)->findBy(
                ['active' => true, 'approved' => true],
                [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
                3
            )[random_int(0, 2)];
        }

        return array_merge($viewParameters, [
            'headerType' =>  $headerType,
            'headerContent' => $headerContent,
            'imageNumber' => random_int(1, 11),
            'menuStructure' => $this->menuHelper->getMenuStructure(),
            'nrOfOpenForumAlerts' => $this->menuHelper->getNumberOfOpenForumAlerts(),
            'blockHelp' => $this->getBlockHelp(),
        ]);
    }

    /**
     * @return BlockHelp|null
     */
    private function getBlockHelp(): ?BlockHelp
    {
        /**
         * @var Block $block
         */
        $block = $this->doctrine->getRepository(Block::class)->findOneBy(
            ['route' => $this->requestStack->getCurrentRequest()->get('_route')]
        );
        if (!is_null($block) && !is_null($block->blockHelp)) {
            return $block->blockHelp;
        }

        return null;
    }
}
