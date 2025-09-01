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
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly FlashHelper $flashHelper,
        private readonly FormFactoryInterface $formFactory,
        private readonly RedirectHelper $redirectHelper,
        private readonly EmailHelper $emailHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(Request $request, ?int $id = null): Response|RedirectResponse
    {
        if (null === $id) {
            if (!$this->userHelper->userIsLoggedIn()) {
                throw new AccessDeniedException('The user is not logged in');
            }
            $user = $this->userHelper->getUser();
        } else {
            $user = $this->doctrine->getRepository(User::class)->find($id);
        }

        $form = null;
        if ($user === $this->userHelper->getUser()) {
            $form = $this->formFactory->create(UserInfo::class, $this->userHelper->getUser()->info);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->doctrine->getManager()->flush();

                $this->flashHelper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Je profiel is aangepast');

                return $this->redirectHelper->redirectToRoute('profile');
            }
        }

        return $this->templateHelper->render('somda/profile.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Profiel van ' . $user->username,
            'user' => $user,
            TemplateHelper::PARAMETER_FORM => $form ? $form->createView() : null
        ]);
    }

    public function mailAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var User $user
         */
        $user = $this->doctrine->getRepository(User::class)->find($id);
        if (null === $user) {
            throw new AccessDeniedException('This user does not exist');
        }

        $form = $this->formFactory->create(
            UserMail::class,
            null,
            ['isModerator' => $this->userHelper->getUser()->hasRole(RoleGenerics::ROLE_ADMIN)]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('senderOption')->getData() === 'moderator') {
                if (!$this->userHelper->getUser()->hasRole(RoleGenerics::ROLE_ADMIN)) {
                    throw new AccessDeniedException('The user is not a moderator');
                }
                $from = ['mods@somda.nl', 'Somda moderator'];
                $template = 'user-mail-moderator';

                // Send a copy of the email to the moderator user
                $this->emailHelper->sendEmail(
                    $this->userHelper->getModeratorUser(),
                    'Somda - Door moderator verstuurde e-mail',
                    'user-mail-moderator-copy',
                    ['user' => $user, 'text' => $form->get('text')->getData()]
                );
            } elseif ($form->get('senderOption')->getData() === 'direct') {
                $from = [$this->userHelper->getUser()->email, $this->userHelper->getUser()->username];
                $template = 'user-mail-direct';
            } else {
                $from = ['noreply@somda.nl', $this->userHelper->getUser()->username];
                $template = 'user-mail-anonymous';
            }
            $this->emailHelper->sendEmail(
                $user,
                $form->get('subject')->getData(),
                $template,
                ['sender' => $this->userHelper->getUser(), 'from' => $from, 'text' => $form->get('text')->getData()]
            );

            $this->flashHelper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Je bericht is verzonden');

            return $this->redirectHelper->redirectToRoute('profile_view', ['id' => $user->id]);
        }

        return $this->templateHelper->render('somda/mail.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Mail sturen naar ' . $user->username,
            'user' => $user,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
