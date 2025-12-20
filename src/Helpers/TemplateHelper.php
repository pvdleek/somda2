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
use Detection\MobileDetect;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TemplateHelper
{
    public const PARAMETER_DAY_NUMBER = 'day_number';
    public const PARAMETER_DISCUSSION = 'discussion';
    public const PARAMETER_FORM = 'form';
    public const PARAMETER_FORUM = 'forum';
    public const PARAMETER_PAGE_TITLE = 'pageTitle';
    public const PARAMETER_TRAIN_TABLE_INDICES = 'trainTableIndices';
    public const PARAMETER_TRAIN_TABLE_INDEX = 'trainTableIndex';
    public const PARAMETER_TRAIN_TABLE_INDEX_NUMBER = 'trainTableIndexNumber';

    public function __construct(
        private readonly RequestStack $request_stack,
        private readonly ManagerRegistry $doctrine,
        private readonly LoggerInterface $logger,
        private readonly Environment $twig,
        private readonly MenuHelper $menu_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    public function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        try {
            $content = $this->twig->render($this->getCorrectView($view), $this->getParameters($parameters));
        } catch (\Exception $exception) {
            $this->logger->critical('Error when rendering view "'.$view.'": "'.$exception->getMessage().'"');
            $content = '';
        }

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);
        return $response;
    }

    /**
     * @throws \Exception
     */
    private function getCorrectView(string $view): string
    {
        if ($this->user_helper->userIsLoggedIn()
            && '1' === $this->user_helper->getPreferenceByKey(UserPreference::KEY_FORCE_DESKTOP)->value
        ) {
            return $view;
        }

        $detect = new MobileDetect();
        if ($detect->isMobile() && \file_exists(__DIR__.'/../../templates/mobile/'.$view)) {
            $view = 'mobile/'.$view;
        }

        return $view;
    }

    /**
     * @throws \Exception
     */
    private function getParameters(array $viewParameters): array
    {
        // Check if there is an active banner for the header
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_HEADER, 'active' => true]
        );
        if (\count($banners) > 0) {
            $header_type = 'banner';
            $header_content = $banners[\random_int(0, \count($banners) - 1)];

            // Create a view for this banner
            $banner_view = new BannerView();
            $banner_view->banner = $header_content;
            $banner_view->timestamp = new \DateTime();
            $banner_view->ip_address = \ip2long($this->request_stack->getCurrentRequest()->getClientIp());
            $this->doctrine->getManager()->persist($banner_view);
            $this->doctrine->getManager()->flush();
        } else {
            $header_type = 'news';
            $header_content = $this->doctrine->getRepository(RailNews::class)->findBy(
                ['active' => true, 'approved' => true],
                [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
                3
            )[\random_int(0, 2)];
        }

        return \array_merge($viewParameters, [
            'design_number' => $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_DESIGN, true)?->value,
            'headerType' => $header_type,
            'headerContent' => $header_content,
            'menuStructure' => $this->menu_helper->getMenuStructure(),
            'nrOfOpenForumAlerts' => $this->menu_helper->getNumberOfOpenForumAlerts(),
            'block_help' => $this->getBlockHelp(),
        ]);
    }

    private function getBlockHelp(): ?BlockHelp
    {
        /**
         * @var Block $block
         */
        $block = $this->doctrine->getRepository(Block::class)->findOneBy(
            ['route' => $this->request_stack->getCurrentRequest()->attributes->get('_route')]
        );
        if (null !== $block && null !== $block->block_help) {
            return $block->block_help;
        }

        return null;
    }
}
