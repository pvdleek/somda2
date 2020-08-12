<?php

namespace App\Controller\Api;

use App\Entity\News;
use App\Entity\RailNews;
use App\Form\News as NewsForm;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends AbstractFOSRestController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int|null $id
     * @return Response
     * @SWG\Parameter(
     *     description="ID of a specific news-item to request, all information about the items is in \
     *         the initial request. But requesting a specific news-item for a logged-in user will mark the \
     *         item as read",
     *     in="path",
     *     name="id",
     *     type="integer",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single or all Somda news-items",
     *     @SWG\Schema(
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=News::class))),
     *     ),
     * )
     * @SWG\Tag(name="News")
     */
    public function indexAction(int $id = null): Response
    {
        if (!is_null($id)) {
            /**
             * @var News $news
             */
            $news = $this->doctrine->getRepository(News::class)->find($id);
            if (is_null($news)) {
                return $this->handleView($this->view(['error' => 'This news-item does not exist'], 404));
            }

            if ($this->userHelper->userIsLoggedIn() && !in_array($this->userHelper->getUser(), $news->getUserReads())) {
                $news->addUserRead($this->userHelper->getUser());
            }
            $this->doctrine->getManager()->flush();

            return $this->handleView($this->view(['data' => $news], 200));
        }

        /**
         * @var News[] $news
         */
        $news = $this->doctrine->getRepository(News::class)->findBy([], [NewsForm::FIELD_TIMESTAMP => 'DESC']);
        return $this->handleView($this->view(['data' => $news], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int|null $limit
     * @return Response
     * @SWG\Parameter(
     *     default="25",
     *     description="The maximum number of items to return (limited to 100)",
     *     in="path",
     *     name="limit",
     *     type="integer",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns all rail-news-items",
     *     @SWG\Schema(
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=RailNews::class))),
     *     ),
     * )
     * @SWG\Tag(name="News")
     */
    public function railNewsAction(int $limit = null): Response
    {
        $limit = is_null($limit) ? 25 : min($limit, 100);

        /**
         * @var RailNews[] $news
         */
        $news = $this->doctrine->getRepository(RailNews::class)->findBy(
            ['active' => true, 'approved' => true],
            ['timestamp' => 'DESC'],
            $limit
        );
        return $this->handleView($this->view(['data' => $news], 200));
    }
}
