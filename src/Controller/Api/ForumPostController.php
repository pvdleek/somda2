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
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
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
     * @param Request $request
     * @param int $discussionId
     * @return Response
     * @throws Exception
     * @OA\Post(
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/NewForumPost")
     *             @OA\Schema(
     *                 @OA\Parameter(in="query", name="signatureOn", type="integer", enum={0,1}),
     *                 @OA\Parameter(in="query", name="text", type="string")
     *             ),
     *         ),
     *     ),
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

//required:
//        - name
//      properties:
//        name:
//          type: string
//        tag:
//          type: string
//      type: object
    /**
     * @OA\Post(
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/NewPet")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="pet response",
     *         @OA\JsonContent(ref="#/components/schemas/Pet")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
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
        if (is_null($discussion)
            || !$this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser())
        ) {
            throw new AccessDeniedException('This discussion does not exist or the user may not post');
        }

        $postInformation = (array)json_decode($request->getContent(), true);
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
