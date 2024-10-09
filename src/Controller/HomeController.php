<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\News;
use App\Entity\RailNews;
use App\Entity\SpecialRoute;
use App\Entity\Spot;
use App\Entity\Statistic;
use App\Entity\User;
use App\Entity\UserPreference;
use App\Form\RailNews as RailNewsForm;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
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
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(): Response
    {
        $railNews = $this->doctrine
            ->getRepository(RailNews::class)
            ->findBy(['active' => true, 'approved' => true], [RailNewsForm::FIELD_TIMESTAMP => 'DESC'], 5);

        $layout = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT)->value;
        // SpecialRoutes-construction and wrong-spots no longer exist
        $layout = \str_replace(['werkzaamheden-min', 'werkzaamheden', 'foutespots'], '', $layout);
        $layout = \array_filter(\explode(';', $layout));

        $layoutData = [];
        $this->loadDataForDashboard($layout, $layoutData);
        $this->loadDataForSpecialRoutes($layout, $layoutData);
        $this->loadDataForForum($layout, $layoutData);
        $this->loadDataForNews($layout, $layoutData);
        $this->loadDataForSpots($layout, $layoutData);
        $this->loadDataForPassingRoutes($layout, $layoutData);

        return $this->templateHelper->render('home.html.twig', [
            'layout' => $layout,
            'layoutData' => $layoutData,
            'railNews' => $railNews,
        ]);
    }

    private function loadDataForDashboard(array $layout, array &$layoutData): void
    {
        if (\in_array(self::KEY_DASHBOARD, $layout) || \in_array(self::KEY_DASHBOARD_MINIMIZED, $layout)) {
            $layoutData[self::KEY_DASHBOARD]['activeUsers'] =
                $this->doctrine->getRepository(User::class)->countActive();
            $layoutData[self::KEY_DASHBOARD]['pageViews'] =
                $this->doctrine->getRepository(Statistic::class)->countPageViews();
            $layoutData[self::KEY_DASHBOARD]['spots'] = $this->doctrine->getRepository(Statistic::class)->countSpots();
            $layoutData[self::KEY_DASHBOARD]['statistics'] =
                $this->doctrine->getRepository(Statistic::class)->findLastDays(3);
            $layoutData[self::KEY_DASHBOARD]['birthdayUsers'] =
                $this->doctrine->getRepository(User::class)->countBirthdays();
        }
    }

    private function loadDataForSpecialRoutes(array $layout, array &$layoutData): void
    {
        if (\in_array('drgl', $layout) || \in_array('drgl-min', $layout)) {
            $layoutData['specialRoutes'] = $this->doctrine->getRepository(SpecialRoute::class)->findForDashboard();
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForForum(array $layout, array &$layoutData): void
    {
        $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_FORUM_POSTS)->value;

        if (\in_array(self::KEY_FORUM, $layout) || \in_array('forum-min', $layout)) {
            $layoutData[self::KEY_FORUM] = $this->doctrine
                ->getRepository(ForumDiscussion::class)
                ->findForDashboard($limit, $this->userHelper->getUser());
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForNews(array $layout, array &$layoutData): void
    {
        $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS)->value;

        if (\in_array('news', $layout) || \in_array('news-min', $layout)) {
            $layoutData['news'] =
                $this->doctrine->getRepository(News::class)->findForDashboard($limit, $this->userHelper->getUser());
        }
        if (\in_array('spoornieuws', $layout) || \in_array('spoornieuws-min', $layout)) {
            $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS)->value;
            $layoutData['railNews'] = $this->doctrine->getRepository(RailNews::class)->findBy(
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
            $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_SPOTS)->value;
            $layoutData[self::KEY_SPOTS] =
                $this->doctrine->getRepository(Spot::class)->findBy([], ['spotDate' => 'DESC'], $limit);
        }
    }

    /**
     * @throws \Exception
     */
    private function loadDataForPassingRoutes(array $layout, array &$layoutData): void
    {
        if (\in_array('doorkomst', $layout) || \in_array('doorkomst-min', $layout)) {
            $layoutData[self::KEY_PASSING_ROUTES]['location'] = $this->userHelper
                ->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION)->value;
            $layoutData[self::KEY_PASSING_ROUTES]['startTime'] = new \DateTime('-5 minutes');
            $layoutData[self::KEY_PASSING_ROUTES]['endTime'] = new \DateTime('+30 minutes');
        }
    }

    public function notImplementedAction(): Response
    {
        return $this->templateHelper->render('notImplemented.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Niet geimplementeerd'
        ]);
    }
}
