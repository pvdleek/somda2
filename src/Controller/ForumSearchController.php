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
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly ForumSearchHelper $forumSearchHelper,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        $form = $this->formHelper->getFactory()->create(ForumSearch::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $this->forumSearchHelper->getSearchResults(
                $form->get('method')->getData(),
                $this->forumSearchHelper->getSearchWords($form->get('words')->getData())
            );

            return $this->templateHelper->render('forum/search.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => 'Zoeken in het forum',
                TemplateHelper::PARAMETER_FORM => $form->createView(),
                'results' => \array_slice($results, 0, ForumSearchHelper::MAX_RESULTS),
                'moreResults' => \count($results) > ForumSearchHelper::MAX_RESULTS,
            ]);
        }

        return $this->templateHelper->render('forum/search.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Zoeken in het forum',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function noteworthyStuffAction(): RedirectResponse
    {
        $forum = $this->formHelper->getDoctrine()->getRepository(ForumForum::class)->find(
            $_ENV['NOTEWORTHY_STUFF_FORUM_ID']
        );
        return $this->formHelper->getRedirectHelper()->redirectToRoute(
            'forum_forum',
            ['id' => $forum->id, 'name' => $forum->name]
        );
    }
}
