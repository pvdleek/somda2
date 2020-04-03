<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumPostText;
use App\Form\ForumPost as ForumPostForm;
use DateTime;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumPostController extends ForumBaseController
{
    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function replyAction(Request $request, int $id)
    {
        /**
         * @var ForumPost $quotedPost
         */
        $quotedPost = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (!$this->mayPost($quotedPost->getDiscussion()->getForum()) || $quotedPost->getDiscussion()->isLocked()) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(ForumPostForm::class, null, ['quotedPost' => $quotedPost]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addPost($form, $quotedPost);
            $this->handleFavoritesForAddedPost($quotedPost->getDiscussion());

            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('forum_discussion', [
                'id' => $quotedPost->getDiscussion()->getId(),
                'name' => urlencode($quotedPost->getDiscussion()->getTitle())
            ]);
        }

        $lastPosts = $this->doctrine->getRepository(ForumPost::class)->findBy(
            ['discussion' => $quotedPost->getDiscussion()],
            ['timestamp' => 'DESC'],
            10
        );

        return $this->render('forum/reply.html.twig', [
            'form' => $form->createView(),
            'post' => $quotedPost,
            'lastPosts' => $lastPosts,
        ]);
    }

    /**
     * @param FormInterface $form
     * @param ForumPost $quotedPost
     * @throws Exception
     */
    private function addPost(FormInterface $form, ForumPost $quotedPost): void
    {
        $post = new ForumPost();
        $post
            ->setAuthor($this->getUser())
            ->setTimestamp(new DateTime())
            ->setDiscussion($quotedPost->getDiscussion())
            ->setSignatureOn($form->get('signatureOn')->getData());
        $this->doctrine->getManager()->persist($post);

        $postText = new ForumPostText();
        $postText->setPost($post)->setText($form->get('text')->getData());
        $this->doctrine->getManager()->persist($postText);

        $postLog = new ForumPostLog();
        $postLog->setAction(ForumPostLog::ACTION_POST_NEW);
        $this->doctrine->getManager()->persist($postLog);

        $post->addLog($postLog)->setText($postText);
    }

    /**
     * @param ForumDiscussion $discussion
     */
    private function handleFavoritesForAddedPost(ForumDiscussion $discussion): void
    {
        foreach ($discussion->getFavorites() as $favorite) {
            if ($favorite->getAlerting() === ForumFavorite::ALERTING_ON) {
                $this->sendEmail(
                    $favorite->getUser(),
                    'Somda - Nieuwe forumreactie op "' . $discussion->getTitle() . '"',
                    'forum-new-reply',
                    ['discussion' => $discussion]
                );
                $favorite->setAlerting(ForumFavorite::ALERTING_SENT);
            }
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function editAction(Request $request, int $id)
    {
        /**
         * @var ForumPost $post
         */
        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (!$this->mayPost($post->getDiscussion()->getForum()) || $post->getDiscussion()->isLocked()
            || ($post->getAuthor() !== $this->getUser() && !$this->userIsModerator($post->getDiscussion()))
        ) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(ForumPostForm::class, null, ['editedPost' => $post]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editPost($form, $post);
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('forum_discussion', [
                'id' => $post->getDiscussion()->getId(),
                'name' => urlencode($post->getDiscussion()->getTitle())
            ]);
        }

        return $this->render('forum/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @param FormInterface $form
     * @param ForumPost $post
     * @throws Exception
     */
    private function editPost(FormInterface $form, ForumPost $post): void
    {
        $post
            ->setEditTimestamp(new DateTime())
            ->setEditor($this->getUser())
            ->setEditReason($form->get('editReason')->getData())
            ->setSignatureOn($form->get('signatureOn')->getData())
            ->getText()->setText($form->get('text')->getData());

        $postLog = new ForumPostLog();
        $postLog->setAction(ForumPostLog::ACTION_POST_EDIT);
        $this->doctrine->getManager()->persist($postLog);

        $post->addLog($postLog);
    }
}
