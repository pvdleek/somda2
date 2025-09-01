<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserPreference;
use App\Form\RailNews as RailNewsForm;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use App\Repository\NewsRepository;
use App\Repository\RailNewsRepository;
use App\Repository\SpecialRouteRepository;
use App\Repository\SpotRepository;
use App\Repository\StatisticRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public const KEY_DASHBOARD = 'dashboard';
    public const KEY_DASHBOARD_MINIMIZED = 'dashboard-min';
    public const KEY_FORUM = 'forum';
    public const KEY_SPOTS = 'spots';
    public const KEY_SPOTS_MINIMIZED = 'spots-min';
    public const KEY_FORUM_SPOTS = 'forumSpots';
    public const KEY_PASSING_ROUTES = 'passing-routes';

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
        private readonly NewsRepository $news_repository,
        private readonly RailNewsRepository $rail_news_repository,
        private readonly SpecialRouteRepository $special_route_repository,
        private readonly SpotRepository $spot_repository,
        private readonly StatisticRepository $statistic_repository,
        private readonly UserRepository $user_repository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(): Response
    {
        $railNews = $this->rail_news_repository->findBy(['active' => true, 'approved' => true], [RailNewsForm::FIELD_TIMESTAMP => 'DESC'], 5);

        $layout = $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT)->value;
        $layout = \array_filter(\explode(';', $layout));

        $layoutData = [];
        $this->loadDataForDashboard($layout, $layoutData);
        $this->loadDataForSpecialRoutes($layout, $layoutData);
        $this->loadDataForForum($layout, $layoutData);
        $this->loadDataForNews($layout, $layoutData);
        $this->loadDataForSpots($layout, $layoutData);
        $this->loadDataForPassingRoutes($layout, $layoutData);

        return $this->template_helper->render('home.html.twig', [
            'layout' => $layout,
            'layoutData' => $layoutData,
            'railNews' => $railNews,
        ]);
    }

    private function loadDataForDashboard(array $layout, array &$layoutData): void
    {
        if (\in_array(self::KEY_DASHBOARD, $layout) || \in_array(self::KEY_DASHBOARD_MINIMIZED, $layout)) {
            $layoutData[self::KEY_DASHBOARD]['activeUsers'] = $this->user_repository->countActive();
            $layoutData[self::KEY_DASHBOARD]['pageViews'] = $this->statistic_repository->countPageViews();
            $layoutData[self::KEY_DASHBOARD]['spots'] = $this->statistic_repository->countSpots();
            $layoutData[self::KEY_DASHBOARD]['statistics'] = $this->statistic_repository->findLastDays(3);
            $layoutData[self::KEY_DASHBOARD]['birthdayUsers'] = $this->user_repository->countBirthdays();
        }
    }

    private function loadDataForSpecialRoutes(array $layout, array &$layoutData): void
    {
        if (\in_array('drgl', $layout) || \in_array('drgl-min', $layout)) {
            $layoutData['specialRoutes'] = $this->special_route_repository->findForDashboard();
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForForum(array $layout, array &$layoutData): void
    {
        $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_FORUM_POSTS)->value;

        if (\in_array(self::KEY_FORUM, $layout) || \in_array('forum-min', $layout)) {
            $layoutData[self::KEY_FORUM] = $this->forum_discussion_repository->findForDashboard($limit, $this->user_helper->getUser());
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForNews(array $layout, array &$layoutData): void
    {
        $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS)->value;

        if (\in_array('news', $layout) || \in_array('news-min', $layout)) {
            $layoutData['news'] = $this->news_repository->findForDashboard($limit, $this->user_helper->getUser());
        }
        if (\in_array('spoornieuws', $layout) || \in_array('spoornieuws-min', $layout)) {
            $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS)->value;
            $layoutData['railNews'] = $this->rail_news_repository->findBy(
                ['active' => true, 'approved' => true],
                [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
                $limit
            );
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForSpots(array $layout, array &$layoutData): void
    {
        if (\in_array(self::KEY_SPOTS, $layout) || \in_array(self::KEY_SPOTS_MINIMIZED, $layout)) {
            $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_SPOTS)->value;
            $layoutData[self::KEY_SPOTS] =
                $this->spot_repository->findBy([], ['spot_date' => 'DESC'], $limit);
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForPassingRoutes(array $layout, array &$layoutData): void
    {
        if (\in_array('doorkomst', $layout) || \in_array('doorkomst-min', $layout)) {
            $layoutData[self::KEY_PASSING_ROUTES]['location'] = $this->user_helper
                ->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION)->value;
            $layoutData[self::KEY_PASSING_ROUTES]['start_time'] = new \DateTime('-5 minutes');
            $layoutData[self::KEY_PASSING_ROUTES]['end_time'] = new \DateTime('+30 minutes');
        }
    }

    public function notImplementedAction(): Response
    {
        return $this->template_helper->render('notImplemented.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Niet geimplementeerd'
        ]);
    }
}
