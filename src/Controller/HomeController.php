<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\News;
use App\Entity\RailNews;
use App\Entity\SpecialRoute;
use App\Entity\Spot;
use App\Entity\Statistic;
use App\Entity\User;
use App\Entity\UserPreference;
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
        $layoutLeft = explode(';', $layoutPart[0]);
        $layoutRight = explode(';', $layoutPart[1]);
        $layout = array_merge($layoutLeft, $layoutRight);

        $layoutData = [];
        $this->loadDataForDashboard($layout, $layoutData);
        $this->loadDataForSpecialRoutes($layout, $layoutData);
        $this->loadDataForForum($layout, $layoutData);
        $this->loadDataForNews($layout, $layoutData);

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
            $layoutData['dashboard']['spots'] = $this->doctrine->getRepository(Spot::class)->countAll();
            $layoutData['dashboard']['pageViews'] = $this->doctrine->getRepository(Statistic::class)->countPageViews();
            $layoutData['dashboard']['activeUsers'] = $this->doctrine->getRepository(User::class)->countActive();
            $layoutData['dashboard']['birthdayUsers'] = $this->doctrine->getRepository(User::class)->countBirthdays();
            $layoutData['dashboard']['statistics'] = $this->doctrine->getRepository(Statistic::class)->findLastDays(3);
        }
    }

    /**
     * @param array $layout
     * @param array $layoutData
     */
    private function loadDataForSpecialRoutes(array $layout, array &$layoutData): void
    {
        if (in_array('drgl', $layout)) {
            $layoutData['dashboard']['specialRoutes'] =
                $this->doctrine->getRepository(SpecialRoute::class)->findForDashboard(false);
        }
        if (in_array('werkzaamheden', $layout)) {
            $layoutData['dashboard']['specialRoutesConstruction'] =
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
        if (in_array('forum', $layout)) {
            $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_FORUM_POSTS);
            $layoutData['dashboard']['forum'] = $this->doctrine->getRepository(ForumDiscussion::class)->findForDashboard($limit, $this->getUser());
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
            $layoutData['dashboard']['news'] =
                $this->doctrine->getRepository(News::class)->findForDashboard($limit, $this->getUser());
        }
        if (in_array('spoornieuws', $layout)) {
            $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_NEWS);
            $layoutData['dashboard']['railNews'] = $this->doctrine->getRepository(RailNews::class)->findBy(
                ['active' => true, 'approved' => true],
                ['timestamp' => 'DESC'],
                $limit
            );
        }
    }
}
