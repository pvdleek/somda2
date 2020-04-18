<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfo;
use App\Form\UserMail;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProfileController extends BaseController
{
    /**
     * @param Request $request
     * @param int|null $id
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function indexAction(Request $request, int $id = null)
    {
        if (is_null($id)) {
            if (!$this->userIsLoggedIn()) {
                throw new AccessDeniedHttpException();
            }
            $user = $this->getUser();
        } else {
            $user = $this->doctrine->getRepository(User::class)->find($id);
        }

        $form = null;
        if ($user === $this->getUser()) {
            $form = $this->formFactory->create(UserInfo::class, $this->getUser()->getInfo());

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->doctrine->getManager()->flush();

                $this->addFlash(self::FLASH_TYPE_INFORMATION, 'Je profiel is aangepast');

                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('somda/profile.html.twig', ['user' => $user, 'form' => $form ? $form->createView() : null]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response|RedirectResponse
     */
    public function mailAction(Request $request, int $id)
    {
        /**
         * @var User $user
         * @var User $moderator
         */
        $user = $this->doctrine->getRepository(User::class)->find($id);
        if (is_null($user)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(
            UserMail::class,
            null,
            ['isModerator' => $this->getUser()->hasRole('ROLE_ADMIN')]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('senderOption')->getData() === 'moderator') {
                if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
                    throw new AccessDeniedHttpException();
                }
                $from = ['mods@somda.nl', 'Somda moderator'];
                $template = 'user-mail-moderator';

                // Send a copy of the email to the moderator user
                $moderator = $this->doctrine->getRepository(User::class)->find(2);
                $this->sendEmail(
                    $moderator,
                    'Somda - Door moderator verstuurde e-mail',
                    'user-mail-moderator-copy',
                    ['user' => $user, 'text' => $form->get('text')->getData()]
                );
            } elseif ($form->get('senderOption')->getData() === 'direct') {
                $from = [$this->getUser()->getEmail(), $this->getUser()->getUsername()];
                $template = 'user-mail-direct';
            } else {
                $from = ['noreply@somda.nl', $this->getUser()->getUsername()];
                $template = 'user-mail-anonymous';
            }
            $this->sendEmail(
                $user,
                $form->get('subject')->getData(),
                $template,
                ['sender' => $this->getUser(), 'from' => $from, 'text' => $form->get('text')->getData()]);

            $this->addFlash(self::FLASH_TYPE_INFORMATION, 'Je bericht is verzonden');

            return $this->redirectToRoute('profile_view', ['id' => $user->getId()]);
        }

        return $this->render('somda/mail.html.twig', ['user' => $user, 'form' => $form->createView()]);
    }
}
