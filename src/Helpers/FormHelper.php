<?php

namespace App\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumPostText;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FormHelper
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly FormFactoryInterface $factory,
        private readonly FlashHelper $flash_helper,
        private readonly RedirectHelper $redirect_helper,
    ) {
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
        return $this->flash_helper;
    }

    /**
     * @return RedirectHelper
     */
    public function getRedirectHelper(): RedirectHelper
    {
        return $this->redirect_helper;
    }

    public function finishFormHandling(string $flash_message, string $route, array $route_parameters = []): RedirectResponse
    {
        $this->doctrine->getManager()->flush();

        if (\strlen($flash_message) > 0) {
            $this->flash_helper->add(FlashHelper::FLASH_TYPE_INFORMATION, $flash_message);
        }

        return $this->redirect_helper->redirectToRoute($route, $route_parameters);
    }

    /**
     * @throws \Exception
     */
    public function addPost(ForumDiscussion $discussion, User $user, bool $signature_on, string $text): ForumPost
    {
        $post = new ForumPost();
        $post->author = $user;
        $post->timestamp = new \DateTime();
        $post->discussion = $discussion;
        $post->signature_on = $signature_on;
        $this->doctrine->getManager()->persist($post);

        $post_text = new ForumPostText();
        $post_text->post = $post;
        $post_text->text = $text;
        $this->doctrine->getManager()->persist($post_text);

        $post_log = new ForumPostLog();
        $post_log->action = ForumPostLog::ACTION_POST_NEW;
        $this->doctrine->getManager()->persist($post_log);

        $post->addLog($post_log);
        $post->text = $post_text;
        $discussion->addPost($post);

        return $post;
    }
}
