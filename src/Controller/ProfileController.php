<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfo;
use App\Form\UserMail;
use App\Generics\RoleGenerics;
use App\Helpers\EmailHelper;
use App\Helpers\FlashHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController
{
    public function __construct(
        private readonly FormFactoryInterface $form_factory,
        private readonly ManagerRegistry $doctrine,
        private readonly EmailHelper $email_helper,
        private readonly FlashHelper $flash_helper,
        private readonly RedirectHelper $redirect_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(Request $request, ?int $id = null): Response|RedirectResponse
    {
        if (null === $id) {
            if (!$this->user_helper->userIsLoggedIn()) {
                throw new AccessDeniedException('The user is not logged in');
            }
            $user = $this->user_helper->getUser();
        } else {
            $user = $this->doctrine->getRepository(User::class)->find($id);
        }

        $form = null;
        if ($user === $this->user_helper->getUser()) {
            $form = $this->form_factory->create(UserInfo::class, $this->user_helper->getUser()->info);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->doctrine->getManager()->flush();

                $this->flash_helper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Je profiel is aangepast');

                return $this->redirect_helper->redirectToRoute('profile');
            }
        }

        return $this->template_helper->render('somda/profile.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Profiel van ' . $user->username,
            'user' => $user,
            TemplateHelper::PARAMETER_FORM => $form ? $form->createView() : null
        ]);
    }

    public function mailAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var User $user
         */
        $user = $this->doctrine->getRepository(User::class)->find($id);
        if (null === $user) {
            throw new AccessDeniedException('This user does not exist');
        }

        $form = $this->form_factory->create(
            UserMail::class,
            null,
            ['isModerator' => $this->user_helper->getUser()->hasRole(RoleGenerics::ROLE_ADMIN)]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('senderOption')->getData() === 'moderator') {
                if (!$this->user_helper->getUser()->hasRole(RoleGenerics::ROLE_ADMIN)) {
                    throw new AccessDeniedException('The user is not a moderator');
                }
                $from = ['mods@somda.nl', 'Somda moderator'];
                $template = 'user-mail-moderator';

                // Send a copy of the email to the moderator user
                $this->email_helper->sendEmail(
                    $this->user_helper->getModeratorUser(),
                    'Somda - Door moderator verstuurde e-mail',
                    'user-mail-moderator-copy',
                    ['user' => $user, 'text' => $form->get('text')->getData()]
                );
            } elseif ($form->get('senderOption')->getData() === 'direct') {
                $from = [$this->user_helper->getUser()->email, $this->user_helper->getUser()->username];
                $template = 'user-mail-direct';
            } else {
                $from = ['noreply@somda.nl', $this->user_helper->getUser()->username];
                $template = 'user-mail-anonymous';
            }
            $this->email_helper->sendEmail(
                $user,
                $form->get('subject')->getData(),
                $template,
                ['sender' => $this->user_helper->getUser(), 'from' => $from, 'text' => $form->get('text')->getData()]
            );

            $this->flash_helper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Je bericht is verzonden');

            return $this->redirect_helper->redirectToRoute('profile_view', ['id' => $user->id]);
        }

        return $this->template_helper->render('somda/mail.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Mail sturen naar ' . $user->username,
            'user' => $user,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
