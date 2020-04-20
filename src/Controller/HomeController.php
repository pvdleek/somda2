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
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $layout = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT, $this->getUser())->value;
        if (!$this->userIsLoggedIn()) {
            $layout = str_replace('foutespots', '', $layout);
        }
        $layout = array_filter(array_diff(explode(';', str_replace('$', ';', $layout)), ['poll', 'shout']));

        $layoutData = [];
        $this->loadDataForDashboard($layout, $layoutData);
        $this->loadDataForSpecialRoutes($layout, $layoutData);
        $this->loadDataForForum($layout, $layoutData);
        $this->loadDataForNews($layout, $layoutData);
        $this->loadDataForSpots($layout, $layoutData);
        $this->loadDataForPassingRoutes($layout, $layoutData);

        return $this->render('home.html.twig', [
            'layout' => $layout,
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
        if (in_array('dashboard', $layout) || in_array('dashboard-min', $layout)) {
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
        if (in_array('drgl', $layout) || in_array('drgl-min', $layout)) {
            $layoutData['specialRoutes'] = $this->doctrine->getRepository(SpecialRoute::class)->findForDashboard(false);
        }
        if (in_array('werkzaamheden', $layout) || in_array('werkzaamheden-min', $layout)) {
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
        $limit = $this->userHelper
            ->getPreferenceByKey(UserPreference::KEY_HOME_MAX_FORUM_POSTS, $this->getUser())->value;

        if (in_array('forum', $layout) || in_array('forum-min', $layout)) {
            $layoutData['forum'] =
                $this->doctrine->getRepository(ForumDiscussion::class)->findForDashboard($limit, $this->getUser());
        }
        if (in_array('foutespots', $layout) || in_array('foutespots-min', $layout)) {
            /**
             * @var ForumForum $forum
             */
            $forum = $this->doctrine->getRepository(ForumForum::class)->find($_ENV['WRONG_SPOTS_FORUM_ID']);
            $layoutData['forum-spots']['id'] = $forum->getId();
            $layoutData['forum-spots']['name'] = $forum->name;
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
        $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS, $this->getUser())->value;

        if (in_array('news', $layout) || in_array('news-min', $layout)) {
            $layoutData['news'] =
                $this->doctrine->getRepository(News::class)->findForDashboard($limit, $this->getUser());
        }
        if (in_array('spoornieuws', $layout) || in_array('spoornieuws-min', $layout)) {
            $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_NEWS, $this->getUser())->value;
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
        if (in_array('spots', $layout) || in_array('spots-min', $layout)) {
            $limit = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_MAX_SPOTS, $this->getUser())->value;
            $layoutData['spots'] =
                $this->doctrine->getRepository(Spot::class)->findBy([], ['timestamp' => 'DESC'], $limit);
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     * @throws Exception
     */
    private function loadDataForPassingRoutes(array $layout, array &$layoutData): void
    {
        if (in_array('doorkomst', $layout) || in_array('doorkomst-min', $layout)) {
            $layoutData['passingRoutes']['location'] = $this->userHelper
                ->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION, $this->getUser())->value;
            $layoutData['passingRoutes']['startTime'] = new DateTime('-5 minutes');
            $layoutData['passingRoutes']['endTime'] = new DateTime('+30 minutes');
        }
    }

    /**
     * @param string $layout
     * @return JsonResponse
     * @throws Exception
     */
    public function updateLayoutAction(string $layout): JsonResponse
    {
        if (!$this->userIsLoggedIn()) {
            return new JsonResponse();
        }

        $userPreference = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT, $this->getUser());
        $userPreference->value = $layout;
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
