<?php

namespace App\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumPostText;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FormHelper
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $factory;

    /**
     * @var FlashHelper
     */
    private FlashHelper $flashHelper;

    /**
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormFactoryInterface $formFactory
     * @param FlashHelper $flashHelper
     * @param RedirectHelper $redirectHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactoryInterface $formFactory,
        FlashHelper $flashHelper,
        RedirectHelper $redirectHelper
    ) {
        $this->doctrine = $doctrine;
        $this->factory = $formFactory;
        $this->flashHelper = $flashHelper;
        $this->redirectHelper = $redirectHelper;
    }

    /**
     * @return ManagerRegistry
     */
    public function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    /**
     * @return FormFactoryInterface
     */
    public function getFactory(): FormFactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return FlashHelper
     */
    public function getFlashHelper(): FlashHelper
    {
        return $this->flashHelper;
    }

    /**
     * @return RedirectHelper
     */
    public function getRedirectHelper(): RedirectHelper
    {
        return $this->redirectHelper;
    }

    /**
     * @param string $flashMessage
     * @param string $route
     * @param array $routeParameters
     * @return RedirectResponse
     */
    public function finishFormHandling(
        string $flashMessage,
        string $route,
        array $routeParameters = []
    ): RedirectResponse {
        $this->doctrine->getManager()->flush();

        if (strlen($flashMessage) > 0) {
            $this->flashHelper->add(FlashHelper::FLASH_TYPE_INFORMATION, $flashMessage);
        }

        return $this->redirectHelper->redirectToRoute($route, $routeParameters);
    }

    /**
     * @param ForumDiscussion $discussion
     * @param User $user
     * @param bool $signatureOn
     * @param string $text
     * @return ForumPost
     * @throws Exception
     */
    public function addPost(ForumDiscussion $discussion, User $user, bool $signatureOn, string $text): ForumPost
    {
        $post = new ForumPost();
        $post->author = $user;
        $post->timestamp = new DateTime();
        $post->discussion = $discussion;
        $post->signatureOn = $signatureOn;
        $this->doctrine->getManager()->persist($post);

        $postText = new ForumPostText();
        $postText->post = $post;
        $postText->text = $text;
        $this->doctrine->getManager()->persist($postText);

        $postLog = new ForumPostLog();
        $postLog->action = ForumPostLog::ACTION_POST_NEW;
        $this->doctrine->getManager()->persist($postLog);

        $post->addLog($postLog);
        $post->text = $postText;
        $discussion->addPost($post);

        return $post;
    }
}
