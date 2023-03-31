<?php

namespace App\Controller;

use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\ForumPostAlert;
use App\Entity\ForumPostAlertNote;
use App\Form\ForumPostAlert as ForumPostAlertForm;
use App\Form\ForumPostAlertNote as ForumPostAlertNoteForm;
use App\Generics\RoleGenerics;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumPostAlertController
{
    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var EmailHelper
     */
    private EmailHelper $emailHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param EmailHelper $emailHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(
        UserHelper $userHelper,
        FormHelper $formHelper,
        EmailHelper $emailHelper,
        TemplateHelper $templateHelper
    ) {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->emailHelper = $emailHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @return Response
     */
    public function alertsAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN);

        $alerts = $this->formHelper->getDoctrine()->getRepository(ForumPostAlert::class)->findForOverview();
        return $this->templateHelper->render('forum/alerts.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - Overzicht van meldingen',
            'alerts' => $alerts,
        ]);
    }

    /**
     * User can create a new alert for a post
     *
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     * @throws \Exception
     */
    public function alertAction(Request $request, int $id)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

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
            $forumPostAlert->timestamp = new \DateTime();
            $forumPostAlert->comment = $form->get(ForumPostAlertForm::FIELD_COMMENT)->getData();
            $this->formHelper->getDoctrine()->getManager()->persist($forumPostAlert);
            $post->addAlert($forumPostAlert);

            // Send this alert to the forum-moderators
            foreach ($post->discussion->forum->getModerators() as $moderator) {
                if ($moderator !== $this->userHelper->getModeratorUser()) {
                    $this->emailHelper->sendEmail(
                        $moderator,
                        '[Somda-Forum] Een gebruiker heeft een forumbericht gemeld!',
                        'forum-new-alert',
                        [
                            'post' => $post,
                            'user' => $this->userHelper->getUser(),
                            'comment' => $form->get(ForumPostAlertForm::FIELD_COMMENT)->getData()
                        ]
                    );
                }
            }

            return $this->formHelper->finishFormHandling('', 'forum_discussion_post', [
                'id' => $post->discussion->id,
                'postId' => $post->id,
                'name' => \urlencode($post->discussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/alert.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $post->discussion->title,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * View alerts for a specific post and add a note
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function postAlertsAction(Request $request, int $id)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN);

        /**
         * @var ForumPost $post
         */
        $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($id);

        $form = $this->formHelper->getFactory()->create(ForumPostAlertNoteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $forumPostAlertNote = $this->getNewAlertNote($post, $form);

            // Send this alert-note to the forum-moderators
            foreach ($post->discussion->forum->getModerators() as $moderator) {
                if ($moderator !== $this->userHelper->getModeratorUser()) {
                    $this->emailHelper->sendEmail(
                        $moderator,
                        '[Somda-Forum] Notitie geplaatst bij gemeld forumbericht!',
                        'forum-new-alert-note',
                        ['post' => $post, 'note' => $forumPostAlertNote]
                    );
                }
            }

            if ($form->get('sentToReporter')->getData()) {
                // We need to inform the reporter(s)
                $this->sendNoteToReporters($post, $forumPostAlertNote);
            }

            return $this->formHelper->finishFormHandling('', 'forum_discussion_post_alerts', ['id' => $post->id]);
        }

        return $this->templateHelper->render('forum/postAlerts.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $post->discussion->title,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @param ForumPost $post
     * @param FormInterface $form
     * @return ForumPostAlertNote
     */
    private function getNewAlertNote(ForumPost $post, FormInterface $form): ForumPostAlertNote
    {
        $forumPostAlertNote = new ForumPostAlertNote();
        $forumPostAlertNote->alert = $post->getAlerts()[0];
        $forumPostAlertNote->author = $this->userHelper->getUser();
        $forumPostAlertNote->timestamp = new \DateTime();
        $forumPostAlertNote->text = $form->get('text')->getData();
        $forumPostAlertNote->sentToReporter = $form->get('sentToReporter')->getData();

        $this->formHelper->getDoctrine()->getManager()->persist($forumPostAlertNote);
        $post->getAlerts()[0]->addNote($forumPostAlertNote);

        return $forumPostAlertNote;
    }

    /**
     * @param ForumPost $post
     * @param ForumPostAlertNote $forumPostAlertNote
     */
    private function sendNoteToReporters(ForumPost $post, ForumPostAlertNote $forumPostAlertNote): void
    {
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

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function alertsCloseAction(int $id): RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN);

        /**
         * @var ForumPost $post
         */
        $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        foreach ($post->getAlerts() as $alert) {
            $alert->closed = true;
        }

        return $this->formHelper->finishFormHandling('', 'forum_discussion_post_alerts', ['id' => $post->id]);
    }
}
