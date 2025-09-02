<?php

declare(strict_types=1);

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
use App\Repository\ForumPostAlertRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class ForumPostAlertController
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly UserHelper $user_helper,
        private readonly FormHelper $form_helper,
        private readonly EmailHelper $email_helper,
        private readonly TemplateHelper $template_helper,
        private readonly ForumPostAlertRepository $forum_post_alert_repository,
    ) {
    }

    public function alertsAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN);

        return $this->template_helper->render('forum/alerts.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - Overzicht van meldingen',
            'alerts' => $this->forum_post_alert_repository->findForOverview(),
        ]);
    }

    /**
     * User can create a new alert for a post
     *
     * @throws \Exception
     */
    public function alertAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var ForumPost $post
         */
        $post = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        $form = $this->form_helper->getFactory()->create(ForumPostAlertForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $forum_post_alert = new ForumPostAlert();
            $forum_post_alert->post = $post;
            $forum_post_alert->sender = $this->user_helper->getUser();
            $forum_post_alert->timestamp = new \DateTime();
            $forum_post_alert->comment = $form->get(ForumPostAlertForm::FIELD_COMMENT)->getData();
            $this->form_helper->getDoctrine()->getManager()->persist($forum_post_alert);
            $post->addAlert($forum_post_alert);

            // Send this alert to the forum-moderators
            foreach ($post->discussion->forum->getModerators() as $moderator) {
                if ($moderator !== $this->user_helper->getModeratorUser()) {
                    $this->email_helper->sendEmail(
                        $moderator,
                        '[Somda-Forum] Een gebruiker heeft een forumbericht gemeld!',
                        'forum-new-alert',
                        [
                            'post' => $post,
                            'user' => $this->user_helper->getUser(),
                            'comment' => $form->get(ForumPostAlertForm::FIELD_COMMENT)->getData()
                        ]
                    );
                }
            }

            return $this->form_helper->finishFormHandling('', 'forum_discussion_post', [
                'id' => $post->discussion->id,
                'post_id' => $post->id,
                'name' => $this->slugger->slug($post->discussion->title)
            ]);
        }

        return $this->template_helper->render('forum/alert.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - '.$post->discussion->title,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * View alerts for a specific post and add a note
     *
     * @throws \Exception
     */
    public function postAlertsAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN);

        /**
         * @var ForumPost $post
         */
        $post = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($id);

        $form = $this->form_helper->getFactory()->create(ForumPostAlertNoteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $forum_post_alert_note = $this->getNewAlertNote($post, $form);

            // Send this alert-note to the forum-moderators
            foreach ($post->discussion->forum->getModerators() as $moderator) {
                if ($moderator !== $this->user_helper->getModeratorUser()) {
                    $this->email_helper->sendEmail(
                        $moderator,
                        '[Somda-Forum] Notitie geplaatst bij gemeld forumbericht!',
                        'forum-new-alert-note',
                        ['post' => $post, 'note' => $forum_post_alert_note]
                    );
                }
            }

            if ($form->get('sent_to_reporter')->getData()) {
                // We need to inform the reporter(s)
                $this->sendNoteToReporters($post, $forum_post_alert_note);
            }

            return $this->form_helper->finishFormHandling('', 'forum_discussion_post_alerts', ['id' => $post->id]);
        }

        return $this->template_helper->render('forum/postAlerts.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - '.$post->discussion->title,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'post' => $post,
        ]);
    }

    private function getNewAlertNote(ForumPost $post, FormInterface $form): ForumPostAlertNote
    {
        $forum_post_alert_note = new ForumPostAlertNote();
        $forum_post_alert_note->alert = $post->getAlerts()[0];
        $forum_post_alert_note->author = $this->user_helper->getUser();
        $forum_post_alert_note->timestamp = new \DateTime();
        $forum_post_alert_note->text = $form->get('text')->getData();
        $forum_post_alert_note->sent_to_reporter = $form->get('sent_to_reporter')->getData();

        $this->form_helper->getDoctrine()->getManager()->persist($forum_post_alert_note);
        $post->getAlerts()[0]->addNote($forum_post_alert_note);

        return $forum_post_alert_note;
    }

    private function sendNoteToReporters(ForumPost $post, ForumPostAlertNote $forum_post_alert_note): void
    {
        foreach ($post->getAlerts() as $alert) {
            if (!$alert->closed) {
                $template = $post->discussion->forum->type === ForumForum::TYPE_MODERATORS_ONLY ?
                    'forum-alert-follow-up-deleted' : 'forum-alert-follow-up';
                $this->email_helper->sendEmail(
                    $alert->sender,
                    '[Somda] Reactie op jouw melding van een forumbericht',
                    $template,
                    ['user' => $alert->sender, 'post' => $post, 'note' => $forum_post_alert_note]
                );
            }
        }
    }

    public function alertsCloseAction(int $id): RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN);

        /**
         * @var ForumPost $post
         */
        $post = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($id);
        foreach ($post->getAlerts() as $alert) {
            $alert->closed = true;
        }

        return $this->form_helper->finishFormHandling('', 'forum_discussion_post_alerts', ['id' => $post->id]);
    }
}
