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
            ->findBy(['active' => true, 'approved' => true], ['dateTime' => 'DESC'], 5);

        $layout = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_LAYOUT);
        if (!$this->userIsLoggedIn()) {
            $layout = str_replace('foutespots', '', $layout);
        }
        $layoutPart = explode('$', $layout);
        $layoutLeft = explode(';', $layoutPart[0]);
        $layoutRight = explode(';', $layoutPart[1]);
        $layoutData = [];

        if (in_array('dashboard', $layoutLeft) || in_array('dashboard', $layoutRight)) {
            $layoutData['dashboard']['spots'] = $this->doctrine->getRepository(Spot::class)->countAll();
            $layoutData['dashboard']['pageViews'] = $this->doctrine->getRepository(Statistic::class)->countPageViews();
            $layoutData['dashboard']['activeUsers'] = $this->doctrine->getRepository(User::class)->countActive();
            $layoutData['dashboard']['birthdayUsers'] = $this->doctrine->getRepository(User::class)->countBirthdays();
            $layoutData['dashboard']['statistics'] = $this->doctrine->getRepository(Statistic::class)->findLastDays(3);
        }
        if (in_array('drgl', $layoutLeft) || in_array('drgl', $layoutRight)) {
            $layoutData['dashboard']['specialRoutes'] =
                $this->doctrine->getRepository(SpecialRoute::class)->findForDashboard();
        }
        if (in_array('forum', $layoutLeft) || in_array('forum', $layoutRight)) {
            $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_FORUM_POSTS);
            $layoutData['dashboard']['forum'] = $this->doctrine->getRepository(ForumDiscussion::class)->findForDashboard($limit, $this->getUser());
        }
        if (in_array('news', $layoutLeft) || in_array('news', $layoutRight)) {
            $limit = $this->userHelper->getPreferenceValueByKey($this->getUser(), UserPreference::KEY_HOME_MAX_NEWS);
            $layoutData['dashboard']['news'] = $this->doctrine->getRepository(News::class)->findForDashboard($limit, $this->getUser());
        }


        return $this->render('home.html.twig', [
            'layoutLeft' => $layoutLeft,
            'layoutRight' => $layoutRight,
            'layoutData' => $layoutData,
            'railNews' => $railNews,
        ]);
    }
}
