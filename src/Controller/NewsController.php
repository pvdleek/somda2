<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\News as NewsForm;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NewsController
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
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper, TemplateHelper $templateHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @param int|null $id
     * @return Response
     */
    public function indexAction(int $id = null): Response
    {
        if (!is_null($id)) {
            /**
             * @var News $news
             */
            $news = $this->doctrine->getRepository(News::class)->find($id);
            if (is_null($news)) {
                throw new AccessDeniedHttpException();
            }

            if (!in_array($this->userHelper->getUser(), $news->getUserReads())) {
                $news->addUserRead($this->userHelper->getUser());
            }
            $this->doctrine->getManager()->flush();

            return $this->templateHelper->render('news/item.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => $news->title,
                'news' => $news,
            ]);
        }

        /**
         * @var News[] $news
         */
        $news = $this->doctrine->getRepository(News::class)->findBy([], [NewsForm::FIELD_TIMESTAMP => 'DESC']);
        return $this->templateHelper->render('news/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Nieuws',
            'news' => $news,
        ]);
    }
}
