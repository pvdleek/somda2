<?php

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Entity\TrainTableYear;
use App\Helpers\Controller\TrainTableHelper;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MobileController
{
    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var TrainTableHelper
     */
    private TrainTableHelper $trainTableHelper;

    /**
     * @param FormHelper $formHelper
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     * @param TrainTableHelper $trainTableHelper
     */
    public function __construct(
        FormHelper $formHelper,
        UserHelper $userHelper,
        TemplateHelper $templateHelper,
        TrainTableHelper $trainTableHelper
    ) {
        $this->formHelper = $formHelper;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
        $this->trainTableHelper = $trainTableHelper;
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param string|null $username
     * @return Response
     */
    public function indexAction(AuthenticationUtils $authenticationUtils, string $username = null): Response
    {
        return $this->templateHelper->render('mobile/home.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Somda mobiel',
            'lastUsername' => is_null($username) ? $authenticationUtils->getLastUsername() : $username,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @IsGranted("ROLE_TRAINTABLE")
     * @param string|null $routeNumber
     * @return Response
     */
    public function trainTableAction(string $routeNumber = null): Response
    {
        $trainTableLines = [];
        if (!is_null($routeNumber)) {
            $trainTableYearId = $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();
            $this->trainTableHelper->setTrainTableYear($trainTableYearId);
            $this->trainTableHelper->setRoute($routeNumber);

            $trainTableLines = $this->trainTableHelper->getTrainTableLines();
        }

        return $this->templateHelper->render('mobile/trainTable.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Somda mobiel',
            'routeNumber' => $routeNumber,
            'trainTableLines' => $trainTableLines,
        ]);
    }

    /**
     * @IsGranted("ROLE_PASSING_ROUTES")
     * @param string|null $locationName
     * @param int|null $dayNumber
     * @param string|null $startTime
     * @param string|null $endTime
     * @return Response
     * @throws Exception
     */
    public function passingRoutesAction(
        int $dayNumber = null,
        string $startTime = null,
        string $endTime = null,
        string $locationName = null
    ): Response {
        if (is_null($dayNumber)) {
            $dayNumber = date('N') - 1;
            $startTime = date('H:i', time() - (60 * 15));
            $endTime = date('H:i', time() + (60 * 45));

            $passingRoutes = [];
        } else {
            $trainTableYearId = $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();
            $this->trainTableHelper->setTrainTableYear($trainTableYearId);
            $this->trainTableHelper->setLocation($locationName);

            $passingRoutes = $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime);
        }

        return $this->templateHelper->render('mobile/passingRoutes.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Doorkomststaat',
            'trainTableIndex' => $this->trainTableHelper->getTrainTableYear(),
            'locationName' => $locationName,
            'dayNumber' => $dayNumber,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'passingRoutes' => $passingRoutes,
        ]);
    }

    /**
     * @param int|null $id
     * @return Response
     */
    public function specialRoutesAction(int $id = null): Response
    {
        $specialRoutes = [];
        $specialRoute = null;

        if (is_null($id)) {
            $specialRoutes = $this->formHelper
                ->getDoctrine()
                ->getRepository(SpecialRoute::class)
                ->findForFeed(10);
        } else {
            $specialRoute = $this->formHelper->getDoctrine()->getRepository(SpecialRoute::class)->find($id);
        }

        return $this->templateHelper->render('mobile/specialRoutes.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Somda mobiel',
            'specialRoutes' => $specialRoutes,
            'specialRoute' => $specialRoute,
        ]);
    }
}
