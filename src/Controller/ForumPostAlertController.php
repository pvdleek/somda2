<?php

namespace App\Controller;

use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\ForumPostAlert;
use App\Entity\ForumPostAlertNote;
use App\Form\ForumPostAlert as ForumPostAlertForm;
use App\Form\ForumPostAlertNote as ForumPostAlertNoteForm;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumPostAlertController
{
    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var FormHelper
     */
    private $formHelper;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var TemplateHelper
     */
    private $templateHelper;

    /**
     * @var ForumAuthorizationHelper
     */
    private $forumAuthHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param EmailHelper $emailHelper
     * @param TemplateHelper $templateHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     */
    public function __construct(
        UserHelper $userHelper,
        FormHelper $formHelper,
        EmailHelper $emailHelper,
        TemplateHelper $templateHelper,
        ForumAuthorizationHelper $forumAuthHelper
    ) {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->emailHelper = $emailHelper;
        $this->templateHelper = $templateHelper;
        $this->forumAuthHelper = $forumAuthHelper;
    }


    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function alertAction(Request $request, int $id)
    {
        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedHttpException();
        }

        /**
         * @var ForumPost $post
         */
        $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        $form = $this->formHelper->getFactory()->create(ForumPostAlertForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $forumPostAlert = new ForumPostAlert();
            $forumPostAlert->post = $post;
            $forumPostAlert->sender = $this->userHelper->getUser();
            $forumPostAlert->timestamp = new DateTime();
            $forumPostAlert->comment = $form->get('comment')->getData();
            $this->formHelper->getDoctrine()->getManager()->persist($forumPostAlert);
            $post->addAlert($forumPostAlert);

            // Send this alert to the forum-moderators
            foreach ($post->discussion->forum->getModerators() as $moderator) {
                $this->emailHelper->sendEmail(
                    $moderator,
                    '[Somda-Forum] Een gebruiker heeft een forumbericht gemeld!',
                    'forum-new-alert',
                    ['post' => $post, 'user' => $this->userHelper->getUser(), 'comment' => $form->get('comment')->getData()]
                );
            }

            return $this->formHelper->finishFormHandling('', 'forum_discussion_post', [
                'id' => $post->discussion->getId(),
                'postId' => $post->getId(),
                'name' => urlencode($post->discussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/alert.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function alertsAction(Request $request, int $id)
    {
        /**
         * @var ForumPost $post
         */
        $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        if (!$this->forumAuthHelper->userIsModerator($post->discussion, $this->userHelper->getUser())) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formHelper->getFactory()->create(ForumPostAlertNoteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $forumPostAlertNote = new ForumPostAlertNote();
            $forumPostAlertNote->alert = $post->getAlerts()[0];
            $forumPostAlertNote->author = $this->userHelper->getUser();
            $forumPostAlertNote->timestamp = new DateTime();
            $forumPostAlertNote->text = $form->get('text')->getData();
            $forumPostAlertNote->sentToReporter = $form->get('sentToReporter')->getData();

            $this->formHelper->getDoctrine()->getManager()->persist($forumPostAlertNote);
            $post->getAlerts()[0]->addNote($forumPostAlertNote);

            // Send this alert-note to the forum-moderators
            foreach ($post->discussion->forum->getModerators() as $moderator) {
                $this->emailHelper->sendEmail(
                    $moderator,
                    '[Somda-Forum] Notitie geplaatst bij gemeld forumbericht!',
                    'forum-new-alert-note',
                    ['post' => $post, 'note' => $forumPostAlertNote]
                );
            }

            if ($form->get('sentToReporter')->getData()) {
                // We need to inform the reporter(s)
                foreach ($post->getAlerts() as $alert) {
                    if (!$alert->closed) {
                        $template = $post->discussion->forum->type === ForumForum::TYPE_MODERATORS_ONLY ?
                            'forum-alert-follow-up-deleted' : 'forum-alert-follow-up';
                        $this->emailHelper->sendEmail(
                            $alert->sender,
                            '[Somda] Reactie op jouw melding van een forumbericht',
                            $template,
                            ['user' => $alert->sender, 'post' => $post, 'note' => $forumPostAlertNote]
                        );
                    }
                }
            }

            return $this->formHelper->finishFormHandling('', 'forum_discussion_post_alerts', ['id' => $post->getId()]);
        }

        return $this->templateHelper->render('forum/alerts.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function alertsCloseAction(int $id): RedirectResponse
    {
        /**
         * @var ForumPost $post
         */
        $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        if (!$this->forumAuthHelper->userIsModerator($post->discussion, $this->userHelper->getUser())) {
            throw new AccessDeniedHttpException();
        }

        foreach ($post->getAlerts() as $alert) {
            $alert->closed = true;
        }

        return $this->formHelper->finishFormHandling('', 'forum_discussion_post_alerts', ['id' => $post->getId()]);
    }
}
