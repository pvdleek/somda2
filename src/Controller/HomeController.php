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
        $rail_news = $this->rail_news_repository->findBy(['active' => true, 'approved' => true], [RailNewsForm::FIELD_TIMESTAMP => 'DESC'], 5);

        $layout = $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT)->value;
        $layout = \array_filter(\explode(';', $layout));

        $layout_data = [];
        $this->loadDataForDashboard($layout, $layout_data);
        $this->loadDataForSpecialRoutes($layout, $layout_data);
        $this->loadDataForForum($layout, $layout_data);
        $this->loadDataForNews($layout, $layout_data);
        $this->loadDataForSpots($layout, $layout_data);
        $this->loadDataForPassingRoutes($layout, $layout_data);

        return $this->template_helper->render('home.html.twig', [
            'layout' => $layout,
            'layoutData' => $layout_data,
            'railNews' => $rail_news,
        ]);
    }

    private function loadDataForDashboard(array $layout, array &$layout_data): void
    {
        if (\in_array(self::KEY_DASHBOARD, $layout) || \in_array(self::KEY_DASHBOARD_MINIMIZED, $layout)) {
            $layout_data[self::KEY_DASHBOARD]['activeUsers'] = $this->user_repository->countActive();
            $layout_data[self::KEY_DASHBOARD]['pageViews'] = $this->statistic_repository->countPageViews();
            $layout_data[self::KEY_DASHBOARD]['spots'] = $this->statistic_repository->countSpots();
            $layout_data[self::KEY_DASHBOARD]['statistics'] = $this->statistic_repository->findLastDays(3);
            $layout_data[self::KEY_DASHBOARD]['birthdayUsers'] = $this->user_repository->countBirthdays();
        }
    }

    private function loadDataForSpecialRoutes(array $layout, array &$layout_data): void
    {
        if (\in_array('drgl', $layout) || \in_array('drgl-min', $layout)) {
            $layout_data['specialRoutes'] = $this->special_route_repository->findForDashboard();
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForForum(array $layout, array &$layout_data): void
    {
        $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_FORUM_POSTS)->value;

        if (\in_array(self::KEY_FORUM, $layout) || \in_array('forum-min', $layout)) {
            $layout_data[self::KEY_FORUM] = $this->forum_discussion_repository->findForDashboard($limit, $this->user_helper->getUser());
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForNews(array $layout, array &$layout_data): void
    {
        $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS)->value;

        if (\in_array('news', $layout) || \in_array('news-min', $layout)) {
            $layout_data['news'] = $this->news_repository->findForDashboard($limit, $this->user_helper->getUser());
        }
        if (\in_array('spoornieuws', $layout) || \in_array('spoornieuws-min', $layout)) {
            $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS)->value;
            $layout_data['railNews'] = $this->rail_news_repository->findBy(
                ['active' => true, 'approved' => true],
                [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
                $limit
            );
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForSpots(array $layout, array &$layout_data): void
    {
        if (\in_array(self::KEY_SPOTS, $layout) || \in_array(self::KEY_SPOTS_MINIMIZED, $layout)) {
            $limit = (int) $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_SPOTS)->value;
            $layout_data[self::KEY_SPOTS] =
                $this->spot_repository->findBy([], ['spot_date' => 'DESC'], $limit);
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForPassingRoutes(array $layout, array &$layout_data): void
    {
        if (\in_array('doorkomst', $layout) || \in_array('doorkomst-min', $layout)) {
            $layout_data[self::KEY_PASSING_ROUTES]['location'] = $this->user_helper
                ->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION)->value;
            $layout_data[self::KEY_PASSING_ROUTES]['start_time'] = new \DateTime('-5 minutes');
            $layout_data[self::KEY_PASSING_ROUTES]['end_time'] = new \DateTime('+30 minutes');
        }
    }

    public function notImplementedAction(): Response
    {
        return $this->template_helper->render('notImplemented.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Niet geimplementeerd'
        ]);
    }
}
