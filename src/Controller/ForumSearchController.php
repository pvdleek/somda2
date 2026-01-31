<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumForum;
use App\Form\ForumSearch;
use App\Helpers\FormHelper;
use App\Helpers\ForumSearchHelper;
use App\Helpers\TemplateHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ForumSearchController
{
    public function __construct(
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
        private readonly ForumSearchHelper $forum_search_helper,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        $form = $this->form_helper->getFactory()->create(ForumSearch::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $this->forum_search_helper->getSearchResults(
                $form->get('method')->getData(),
                $this->forum_search_helper->getSearchWords($form->get('words')->getData())
            );

            return $this->template_helper->render('forum/search.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => 'Zoeken in het forum',
                TemplateHelper::PARAMETER_FORM => $form->createView(),
                'results' => \array_slice($results, 0, ForumSearchHelper::MAX_RESULTS),
                'moreResults' => \count($results) > ForumSearchHelper::MAX_RESULTS,
            ]);
        }

        return $this->template_helper->render('forum/search.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Zoeken in het forum',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function noteworthyStuffAction(): RedirectResponse
    {
        $forum = $this->form_helper->getDoctrine()->getRepository(ForumForum::class)->find(
            $_ENV['NOTEWORTHY_STUFF_FORUM_ID']
        );
        return $this->form_helper->getRedirectHelper()->redirectToRoute(
            'forum_forum',
            ['id' => $forum->id, 'name' => $forum->name]
        );
    }
}
