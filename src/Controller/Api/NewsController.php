<?php

namespace App\Controller\Api;

use App\Entity\News;
use App\Entity\RailNews;
use App\Form\News as NewsForm;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
    ) {
    }

    /**
     * @OA\Parameter(
     *     description="ID of a specific news-item to request, all information about the items is in \
     *         the initial request. But requesting a specific news-item for a logged-in user will mark the \
     *         item as read",
     *     in="path",
     *     name="id",
     *     @OA\Schema(type="integer"),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns a single or all Somda news-items",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=News::class))),
     *     ),
     * )
     * @OA\Tag(name="News")
     */
    public function indexAction(int $id = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (null !== $id) {
            /**
             * @var News $news
             */
            $news = $this->doctrine->getRepository(News::class)->find($id);
            if (null === $news) {
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
     * @OA\Parameter(
     *     description="The maximum number of items to return (limited to 100)",
     *     in="path",
     *     name="limit",
     *     @OA\Schema(type="integer", default="25"),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all rail-news-items",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=RailNews::class))),
     *     ),
     * )
     * @OA\Tag(name="News")
     */
    public function railNewsAction(int $limit = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $limit = (null === $limit) ? 25 : \min($limit, 100);

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
