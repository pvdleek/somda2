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
    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var ForumSearchHelper
     */
    private ForumSearchHelper $forumSearchHelper;

    /**
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     * @param ForumSearchHelper $forumSearchHelper
     */
    public function __construct(
        FormHelper $formHelper,
        TemplateHelper $templateHelper,
        ForumSearchHelper $forumSearchHelper
    ) {
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
        $this->forumSearchHelper = $forumSearchHelper;
    }

    /**
     * @param Request $request
     * @return Response
     */
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

    /**
     * @return RedirectResponse
     */
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
