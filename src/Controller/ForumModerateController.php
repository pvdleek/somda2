<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Form\ForumDiscussionMove;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumModerateController extends ForumBaseController
{
    public const ACTION_CLOSE = 'close';
    public const ACTION_OPEN = 'open';
    public const ACTION_MOVE = 'move';

    /**
     * @param Request $request
     * @param int $id
     * @param string $action
     * @return Response
     */
    public function indexAction(Request $request, int $id, string $action): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            return $this->redirectToRoute('forum');
        }
        if (!$this->userIsModerator($discussion)) {
            throw new AccessDeniedHttpException();
        }

        if ($action === self::ACTION_CLOSE && !$discussion->isLocked()) {
            $discussion->setLocked(true);
            $this->doctrine->getManager()->flush();
        } elseif ($action === self::ACTION_OPEN && $discussion->isLocked()) {
            $discussion->setLocked(false);
            $this->doctrine->getManager()->flush();
        } elseif ($action === self::ACTION_MOVE) {
            $form = $this->formFactory->create(ForumDiscussionMove::class, $discussion);
            $form->handleRequest($request);
            if (!$form->isSubmitted() || !$form->isValid()) {
                return $this->render('forum/discussionMove.html.twig', [
                    'discussion' => $discussion,
                    'form' => $form->createView()
                ]);
            }
            $this->doctrine->getManager()->flush();
        }

        return $this->redirectToRoute(
            'forum_discussion',
            ['id' => $discussion->getId(), 'name' => urlencode($discussion->getTitle())]
        );
    }
}
