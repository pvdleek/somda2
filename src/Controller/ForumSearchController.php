<?php

namespace App\Controller;

use App\Entity\ForumSearchWord;
use App\Form\ForumSearch;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Model\ForumSearchResult;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ForumSearchController
{
    public const MAX_RESULTS = 100;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(UserHelper $userHelper, FormHelper $formHelper, TemplateHelper $templateHelper)
    {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
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
            $results = $this->getSearchResults($form->get('method')->getData(), $this->getSearchWords($form));

            return $this->templateHelper->render('forum/search.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => 'Zoeken in het forum',
                'form' => $form->createView(),
                'results' => array_slice($results, 0, self::MAX_RESULTS),
                'moreResults' => count($results) > self::MAX_RESULTS,
            ]);
        }

        return $this->templateHelper->render('forum/search.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Zoeken in het forum',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param FormInterface $form
     * @return ForumSearchWord[]
     */
    private function getSearchWords(FormInterface $form): array
    {
        $words = array_filter(explode(' ', $form->get('words')->getData()));
        foreach ($words as $key => $word) {
            $words[$key] = $this->formHelper->getDoctrine()->getRepository(ForumSearchWord::class)->findOneBy(
                ['word' => $word]
            );
        }
        return $words;
    }

    /**
     * @param string $searchMethod
     * @param ForumSearchWord[] $searchWords
     * @return ForumSearchResult[]
     */
    private function getSearchResults(string $searchMethod, array $searchWords): array
    {
        if ($searchMethod === ForumSearch::METHOD_SOME) {
            return $this->formHelper->getDoctrine()->getRepository(ForumSearchWord::class)->searchByWords(
                $searchWords
            );
        }

        $results = null;
        foreach ($searchWords as $word) {
            $result = $this->formHelper->getDoctrine()->getRepository(ForumSearchWord::class)->searchByWords(
                [$word]
            );
            if (is_null($results)) {
                $results = $result;
            } else {
                $results = array_intersect($results, $result);
            }
        }

        return $results;
    }
}
