<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\FormGenerics;
use App\Generics\RoleGenerics;
use App\Generics\RouteGenerics;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ForumPostController
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly EmailHelper $email_helper,
        private readonly FormHelper $form_helper,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly ForumHelper $forum_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function replyAction(Request $request, int $id, bool $quote = false): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var ForumPost $quotedPost
         */
        $quotedPost = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        if (!$this->forum_authorization_helper->mayPost($quotedPost->discussion->forum, $this->user_helper->getUser())
            || null === $quotedPost || $quotedPost->discussion->locked
        ) {
            throw new AccessDeniedException(
                'The quoted post does not exist, the discussion is locked or the user may not view the discussion'
            );
        }

        $user_is_moderator = $this->forum_authorization_helper->userIsModerator(
            $quotedPost->discussion->forum,
            $this->user_helper->getUser()
        );

        $form = $this->form_helper
            ->getFactory()
            ->create(ForumPostForm::class, null, [ForumPostForm::OPTION_QUOTED_POST => $quote ? $quotedPost : null]);
        if ($user_is_moderator) {
            $form->add('postAsModerator', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Plaatsen als moderator',
                FormGenerics::KEY_MAPPED => false,
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $user_is_moderator && $form->get('postAsModerator')->getData() ?
                $this->user_helper->getModeratorUser() : $this->user_helper->getUser();
            $this->form_helper->addPost(
                $quotedPost->discussion,
                $user,
                $form->get('signature_on')->getData(),
                $form->get('text')->getData()
            );
            $this->handleFavoritesForAddedPost($quotedPost->discussion);

            return $this->form_helper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $quotedPost->discussion->id,
                'name' => $this->slugger->slug($quotedPost->discussion->title),
            ]);
        }

        $lastPosts = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->findBy(
            [ForumPostForm::FIELD_DISCUSSION => $quotedPost->discussion],
            [ForumPostForm::FIELD_TIMESTAMP => 'DESC'],
            10
        );

        return $this->template_helper->render('forum/reply.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - '.$quotedPost->discussion->title,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'post' => $quotedPost,
            'lastPosts' => $lastPosts,
        ]);
    }

    private function handleFavoritesForAddedPost(ForumDiscussion $discussion): void
    {
        foreach ($discussion->getFavorites() as $favorite) {
            if ($favorite->alerting === ForumFavorite::ALERTING_ON) {
                $this->email_helper->sendEmail(
                    $favorite->user,
                    'Somda - Nieuwe forumreactie op "'.$discussion->title.'"',
                    'forum-new-reply',
                    [ForumPostForm::FIELD_DISCUSSION => $discussion]
                );
                $favorite->alerting = ForumFavorite::ALERTING_SENT;
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var ForumPost $post
         */
        $post = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        $user_is_moderator = $this->forum_authorization_helper->userIsModerator(
            $post->discussion->forum,
            $this->user_helper->getUser()
        );
        if (!$this->forum_authorization_helper->mayPost($post->discussion->forum, $this->user_helper->getUser())
            || $post->discussion->locked
            || ($post->author !== $this->user_helper->getUser() && !$user_is_moderator)
        ) {
            throw new AccessDeniedException(
                'The user may not post, the discussion is locked or the author is not the user'
            );
        }

        $form = $this->form_helper
            ->getFactory()
            ->create(ForumPostForm::class, null, [ForumPostForm::OPTION_EDITED_POST => $post]);
        if ($user_is_moderator) {
            $form->add(
                ForumPostForm::FIELD_EDIT_AS_MODERATOR,
                CheckboxType::class,
                [FormGenerics::KEY_LABEL => 'Bewerken als moderator']
            );
            $postNrInDiscussion = $this->forum_discussion_repository->getPostNumberInDiscussion($post->discussion, $post->id);
            if ($postNrInDiscussion === 0) {
                $form->add(ForumPostForm::FIELD_TITLE, TextType::class, [
                    FormGenerics::KEY_DATA => $post->discussion->title,
                    FormGenerics::KEY_LABEL => 'Onderwerp van de discussie',
                    FormGenerics::KEY_REQUIRED => true,
                ]);
            }
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->editPost($form, $post);

            return $this->form_helper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $post->discussion->id,
                'name' => $this->slugger->slug($post->discussion->title)
            ]);
        }

        return $this->template_helper->render('forum/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - '.$post->discussion->title,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @throws \Exception
     */
    private function editPost(FormInterface $form, ForumPost $post): void
    {
        if ($form->has(ForumPostForm::FIELD_EDIT_AS_MODERATOR)
            && $form->get(ForumPostForm::FIELD_EDIT_AS_MODERATOR)->getData()
        ) {
            $editor = $this->user_helper->getModeratorUser();
        } else {
            $editor = $this->user_helper->getUser();
        }

        if ($form->has(ForumPostForm::FIELD_TITLE)) {
            $post->discussion->title = $form->get(ForumPostForm::FIELD_TITLE)->getData();
        }

        $post->edit_timestamp = new \DateTime();
        $post->editor = $editor;
        $post->edit_reason = $form->get('edit_reason')->getData();
        $post->signature_on = $form->get('signature_on')->getData();
        $post->text->text = $form->get('text')->getData();

        $postLog = new ForumPostLog();
        $postLog->action = ForumPostLog::ACTION_POST_EDIT;
        $this->form_helper->getDoctrine()->getManager()->persist($postLog);

        $post->addLog($postLog);
    }
}
