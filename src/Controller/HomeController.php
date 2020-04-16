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
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    /**
     * @return Response
     * @throws Exception
     */
    public function indexAction(): Response
    {
        $railNews = $this->doctrine
            ->getRepository(RailNews::class)
            ->findBy(['active' => true, 'approved' => true], ['timestamp' => 'DESC'], 5);

        $layout = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_LAYOUT);
        if (!$this->userIsLoggedIn()) {
            $layout = str_replace('foutespots', '', $layout);
        }
        $layoutPart = explode('$', $layout);
        $layoutLeft = array_filter(array_diff(explode(';', $layoutPart[0]), ['poll', 'shout']));
        $layoutRight = array_filter(array_diff(explode(';', $layoutPart[1]), ['poll', 'shout']));
        $layout = array_merge($layoutLeft, $layoutRight);

        $layoutData = [];
        $this->loadDataForDashboard($layout, $layoutData);
        $this->loadDataForSpecialRoutes($layout, $layoutData);
        $this->loadDataForForum($layout, $layoutData);
        $this->loadDataForNews($layout, $layoutData);
        $this->loadDataForSpots($layout, $layoutData);
        $this->loadDataForPassingRoutes($layout, $layoutData);

        return $this->render('home.html.twig', [
            'layoutLeft' => $layoutLeft,
            'layoutRight' => $layoutRight,
            'layoutData' => $layoutData,
            'railNews' => $railNews,
        ]);
    }

    /**
     * @param array $layout
     * @param array $layoutData
     */
    private function loadDataForDashboard(array $layout, array &$layoutData): void
    {
        if (in_array('dashboard', $layout)) {
            $layoutData['dashboard']['activeUsers'] = $this->doctrine->getRepository(User::class)->countActive();
            $layoutData['dashboard']['pageViews'] = $this->doctrine->getRepository(Statistic::class)->countPageViews();
            $layoutData['dashboard']['spots'] = $this->doctrine->getRepository(Spot::class)->countAll();
            $layoutData['dashboard']['statistics'] = $this->doctrine->getRepository(Statistic::class)->findLastDays(3);
            $layoutData['dashboard']['birthdayUsers'] = $this->doctrine->getRepository(User::class)->countBirthdays();
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     */
    private function loadDataForSpecialRoutes(array $layout, array &$layoutData): void
    {
        if (in_array('drgl', $layout)) {
            $layoutData['specialRoutes'] = $this->doctrine->getRepository(SpecialRoute::class)->findForDashboard(false);
        }
        if (in_array('werkzaamheden', $layout)) {
            $layoutData['specialRoutesConstruction'] =
                $this->doctrine->getRepository(SpecialRoute::class)->findForDashboard(true);
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     * @throws Exception
     */
    private function loadDataForForum(array $layout, array &$layoutData): void
    {
        $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_FORUM_POSTS);
        if (in_array('forum', $layout)) {
            $layoutData['forum'] =
                $this->doctrine->getRepository(ForumDiscussion::class)->findForDashboard($limit, $this->getUser());
        }
        if (in_array('foutespots', $layout)) {
            /**
             * @var ForumForum $forum
             */
            $forum = $this->doctrine->getRepository(ForumForum::class)->find($_ENV['WRONG_SPOTS_FORUM_ID']);
            $layoutData['forum-spots']['id'] = $forum->getId();
            $layoutData['forum-spots']['name'] = $forum->getName();
            $layoutData['forum-spots']['discussions'] =
                $this->doctrine->getRepository(ForumDiscussion::class)->findByForum($forum, $this->getUser());
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     * @throws Exception
     */
    private function loadDataForNews(array $layout, array &$layoutData): void
    {
        $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_NEWS);
        if (in_array('news', $layout)) {
            $layoutData['news'] =
                $this->doctrine->getRepository(News::class)->findForDashboard($limit, $this->getUser());
        }
        if (in_array('spoornieuws', $layout)) {
            $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_NEWS);
            $layoutData['railNews'] = $this->doctrine->getRepository(RailNews::class)->findBy(
                ['active' => true, 'approved' => true],
                ['timestamp' => 'DESC'],
                $limit
            );
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     * @throws Exception
     */
    private function loadDataForSpots(array $layout, array &$layoutData): void
    {
        if (in_array('spots', $layout)) {
            $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_SPOTS);
            $layoutData['spots'] = $this->doctrine->getRepository(Spot::class)->findBy([], ['date' => 'DESC'], $limit);
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     * @throws Exception
     */
    private function loadDataForPassingRoutes(array $layout, array &$layoutData): void
    {
        if (in_array('doorkomst', $layout)) {
            $layoutData['passingRoutes']['location'] =
                $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_DEFAULT_SPOT_LOCATION);
            $layoutData['passingRoutes']['startTime'] = new DateTime('-5 minutes');
            $layoutData['passingRoutes']['endTime'] = new DateTime('+30 minutes');
        }
    }
}
