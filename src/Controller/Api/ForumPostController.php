<?php

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Form\ForumPost as ForumPostForm;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\UserHelper;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumPostController extends AbstractFOSRestController
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
     * @var ForumAuthorizationHelper
     */
    private ForumAuthorizationHelper $forumAuthHelper;

    /**
     * @var EmailHelper
     */
    private EmailHelper $emailHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param EmailHelper $emailHelper
     */
    public function __construct(
        UserHelper $userHelper,
        FormHelper $formHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        EmailHelper $emailHelper
    ) {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param int $discussionId
     * @return Response
     * @throws Exception
     */
    public function replyAction(Request $request, int $discussionId): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->find($discussionId);
        if (is_null($discussion)
            || !$this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser())
        ) {
            throw new AccessDeniedException();
        }

        $post = $this->formHelper->addPost(
            $discussion,
            $this->userHelper->getUser(),
            $request->get('signatureOn'),
            $request->get('text')
        );
        $this->handleFavoritesForAddedPost($discussion);

        return $this->handleView($this->view(['data' => $post], 200));
    }

    /**
     * @param ForumDiscussion $discussion
     */
    private function handleFavoritesForAddedPost(ForumDiscussion $discussion): void
    {
        foreach ($discussion->getFavorites() as $favorite) {
            if ($favorite->alerting === ForumFavorite::ALERTING_ON) {
                $this->emailHelper->sendEmail(
                    $favorite->user,
                    'Somda - Nieuwe forumreactie op "' . $discussion->title . '"',
                    'forum-new-reply',
                    [ForumPostForm::FIELD_DISCUSSION => $discussion]
                );
                $favorite->alerting = ForumFavorite::ALERTING_SENT;
            }
        }
    }
}
