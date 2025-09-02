<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\News;
use App\Entity\RailNews;
use App\Form\News as NewsForm;
use App\Form\RailNews as RailNewsForm;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NewsController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    public function indexAction(?int $id = null): Response
    {
        if (null !== $id) {
            /**
             * @var News $news
             */
            $news = $this->doctrine->getRepository(News::class)->find($id);
            if (null === $news) {
                throw new AccessDeniedException('This news-item does not exist');
            }

            if ($this->user_helper->userIsLoggedIn() && !in_array($this->user_helper->getUser(), $news->getUserReads())) {
                $news->addUserRead($this->user_helper->getUser());
            }
            $this->doctrine->getManager()->flush();

            return $this->template_helper->render('news/item.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => $news->title,
                'news' => $news,
            ]);
        }

        /**
         * @var News[] $news
         */
        $news = $this->doctrine->getRepository(News::class)->findBy([], [NewsForm::FIELD_TIMESTAMP => 'DESC']);

        return $this->template_helper->render('news/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Nieuws',
            'news' => $news,
        ]);
    }

    public function railNewsAction(): Response
    {
        /**
         * @var RailNews[] $news
         */
        $news = $this->doctrine->getRepository(RailNews::class)->findBy(
            ['active' => true, 'approved' => true],
            [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
            250
        );
        
        return $this->template_helper->render('news/railNews.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spoornieuws',
            'news' => $news,
        ]);
    }
}
