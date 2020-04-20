<?php

namespace App\Controller;

use App\Entity\News;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NewsController extends BaseController
{
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

            if (!in_array($this->getUser(), $news->getUserReads())) {
                $news->addUserRead($this->getUser());
            }
            $this->doctrine->getManager()->flush();

            return $this->render('news/item.html.twig', ['news' => $news]);
        }

        /**
         * @var News[] $news
         */
        $news = $this->doctrine->getRepository(News::class)->findBy([], ['timestamp' => 'DESC']);
        return $this->render('news/index.html.twig', ['news' => $news]);
    }
}
