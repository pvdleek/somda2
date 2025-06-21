<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Entity\ForumPost;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\RoleGenerics;
use App\Helpers\EmailHelper;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\UserHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumPostController extends AbstractFOSRestController
{
    public function __construct(
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
        private readonly EmailHelper $emailHelper,
    ) {
    }

    /**
     * @throws \Exception
     * @OA\Post(
     *     @OA\Parameter(in="formData", name="signatureOn", @OA\Schema(type="integer", enum={0,1})),
     *     @OA\Parameter(in="formData", name="text", @OA\Schema(type="string"))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the new forum-post",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="object", ref=@Model(type=ForumPost::class)),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function replyAction(Request $request, int $discussionId): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->find($discussionId);
        if (null === $discussion || !$this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser())) {
            throw new AccessDeniedException('This discussion does not exist or the user may not post');
        }

        $postInformation = (array) \json_decode($request->getContent(), true);
        $post = $this->formHelper->addPost(
            $discussion,
            $this->userHelper->getUser(),
            (bool)$postInformation['signatureOn'],
            $postInformation['text']
        );
        $this->handleFavoritesForAddedPost($discussion);

        $this->formHelper->getDoctrine()->getManager()->flush();

        return $this->handleView($this->view(['data' => $post], 200));
    }

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
