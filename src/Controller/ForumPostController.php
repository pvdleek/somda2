<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\User;
use App\Form\ForumPost as ForumPostForm;
use DateTime;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
        if (!$this->mayPost($quotedPost->discussion->forum) || $quotedPost->discussion->locked) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(ForumPostForm::class, null, ['quotedPost' => $quotedPost]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addPost($form, $quotedPost->discussion);
            $this->handleFavoritesForAddedPost($quotedPost->discussion);

            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('forum_discussion', [
                'id' => $quotedPost->discussion->getId(),
                'name' => urlencode($quotedPost->discussion->title)
            ]);
        }

        $lastPosts = $this->doctrine->getRepository(ForumPost::class)->findBy(
            ['discussion' => $quotedPost->discussion],
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
     * @param ForumDiscussion $discussion
     */
    private function handleFavoritesForAddedPost(ForumDiscussion $discussion): void
    {
        foreach ($discussion->getFavorites() as $favorite) {
            if ($favorite->alerting === ForumFavorite::ALERTING_ON) {
                $this->sendEmail(
                    $favorite->user,
                    'Somda - Nieuwe forumreactie op "' . $discussion->title . '"',
                    'forum-new-reply',
                    ['discussion' => $discussion]
                );
                $favorite->alerting = ForumFavorite::ALERTING_SENT;
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
        if (!$this->mayPost($post->discussion->forum) || $post->discussion->locked
            || ($post->author !== $this->getUser() && !$this->userIsModerator($post->discussion))
        ) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(ForumPostForm::class, null, ['editedPost' => $post]);
        if ($this->userIsModerator($post->discussion)) {
            $form->add('editAsModerator', CheckboxType::class, ['label' => 'Bewerken als moderator']);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editPost($form, $post);
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('forum_discussion', [
                'id' => $post->discussion->getId(),
                'name' => urlencode($post->discussion->title)
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
        if ($form->has('editAsModerator') && $form->get('editAsModerator')->getData()) {
            $editor = $this->getModeratorUser();
        } else {
            $editor = $this->getUser();
        }

        $post->editTimestamp = new DateTime();
        $post->editor = $editor;
        $post->editReason = $form->get('editReason')->getData();
        $post->signatureOn = $form->get('signatureOn')->getData();
        $post->text->text = $form->get('text')->getData();

        $postLog = new ForumPostLog();
        $postLog->action = ForumPostLog::ACTION_POST_EDIT;
        $this->doctrine->getManager()->persist($postLog);

        $post->addLog($postLog);
    }
}
