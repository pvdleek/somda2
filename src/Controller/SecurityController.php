<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Form\User as UserForm;
use App\Form\UserActivate;
use DateTime;
use Exception;
use LogicException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param string|null $username
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, string $username = null): Response
    {
         if ($this->userIsLoggedIn()) {
             return $this->redirectToRoute('home');
         }

        return $this->render('security/login.html.twig', [
            'lastUsername' => is_null($username) ? $authenticationUtils->getLastUsername() : $username,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function logoutAction(): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->formFactory->create(UserForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->validateUsername($form);
            $this->validateEmail($form);
            $this->validatePassword($form);

            if ($form->isValid()) {
                $user->active = false;
                $user->password = md5(md5(md5($form->get('plainPassword')->getData())));
                $user->activationKey = md5(md5(rand()));
                $user->registrationTimestamp = new DateTime();
                $this->doctrine->getManager()->persist($user);

                $userInfo = new UserInfo();
                $userInfo->user = $user;
                $this->doctrine->getManager()->persist($userInfo);

                $user->info = $userInfo;

                $this->doctrine->getManager()->flush();

                if ($this->sendEmail(
                    $user,
                    'Jouw registratie bij Somda',
                    'register',
                    ['userId' => $user->getId(), 'activationKey' => $user->activationKey]
                )) {
                    $this->addFlash(
                        self::FLASH_TYPE_INFORMATION,
                        'Je registratie is geslaagd! Er is een e-mail gestuurd met daarin een link en een ' .
                        'activatiecode. Je kunt op de link klikken of de code op onderstaand scherm invoeren ' .
                        'om jouw account direct actief te maken.'
                    );

                    return $this->redirectToRoute('activate', ['id' => $user->getId()]);
                } else {
                    $this->doctrine->getManager()->remove($user);
                    $this->doctrine->getManager()->flush();

                    $this->addFlash(
                        self::FLASH_TYPE_ERROR,
                        'Het is niet gelukt een e-mail naar het door jou opgegeven e-mailadres te sturen, ' .
                        'controleer het e-mailadres.'
                    );
                }
            }
        }

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param FormInterface $form
     */
    private function validateUsername(FormInterface $form): void
    {
        $existingUsername = $this->doctrine->getRepository(User::class)->findOneBy(
            ['username' => $form->get('username')->getData()]
        );
        if (!is_null($existingUsername)) {
            $form->get('username')->addError(new FormError(
                'De gebruikersnaam die je hebt gekozen is al in gebruik, kies een andere gebruikersnaam'
            ));
        }
    }

    /**
     * @param FormInterface $form
     */
    public function validateEmail(FormInterface $form): void
    {
        if (substr($form->get('email')->getData(), -16) === 'ikbenspamvrij.nl') {
            $form->get('email')->addError(
                new FormError('E-mailadressen van ikbenspamvrij.nl zijn niet toegestaan')
            );
        }

        $existingEmail = $this->doctrine->getRepository(User::class)->findOneBy(
            ['email' => $form->get('email')->getData()]
        );
        if (!is_null($existingEmail)) {
            $form->get('email')->addError(
                new FormError('Het e-mailadres dat je hebt gekozen is al in gebruik, probeer het opnieuw')
            );
        }
    }

    /**
     * @param FormInterface $form
     */
    public function validatePassword(FormInterface $form): void
    {
        if (stristr($form->get('plainPassword')->getData(), $form->get('username')->getData())
            || stristr($form->get('username')->getData(), $form->get('plainPassword')->getData())
            || stristr(strrev($form->get('username')->getData()), $form->get('plainPassword')->getData())
            || stristr($form->get('plainPassword')->getData(), strrev($form->get('username')->getData()))
        ) {
            $form->get('plainPassword')->addError(
                new FormError('Het wachtwoord dat je hebt gekozen vertoont teveel overeenkomsten ' .
                    'met jouw gebruikersnaam, probeer het opnieuw'
                )
            );
        }
        if (stristr($form->get('plainPassword')->getData(), $form->get('email')->getData())
            || stristr($form->get('email')->getData(), $form->get('plainPassword')->getData())
            || stristr(strrev($form->get('email')->getData()), $form->get('plainPassword')->getData())
            || stristr($form->get('plainPassword')->getData(), strrev($form->get('email')->getData()))
        ) {
            $form->get('plainPassword')->addError(
                new FormError('Het wachtwoord dat je hebt gekozen vertoont teveel overeenkomsten ' .
                    'met jouw e-mailadres, probeer het opnieuw'
                )
            );
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @param string|null $key
     * @return RedirectResponse|Response
     */
    public function activateAction(Request $request, int $id, string $key = null)
    {
        /**
         * @var User $user
         */
        $user = $this->doctrine->getRepository(User::class)->find($id);
        if (is_null($user)) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(UserActivate::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('key')->getData() === $user->activationKey) {
                $user->active = true;
                $user->activationKey = '0';
                $user->addRole('ROLE_USER');
                $this->doctrine->getManager()->flush();

                $this->sendEmail($user, 'Welkom op Somda -- Belangrijke informatie', 'new-account');

                // Send the email to the admin account
                $samePasswordUsers = $this->doctrine->getRepository(User::class)->findBy(
                    ['password' => $user->getPassword()]
                );
                $this->sendEmail(
                    $this->doctrine->getRepository(User::class)->find(1),
                    'Nieuwe registratie bij Somda',
                    'new-account-admin',
                    ['user' => $user, 'samePasswordUsers' => $samePasswordUsers]
                );

                $this->addFlash(
                    self::FLASH_TYPE_INFORMATION,
                    'Jouw account is geactiveerd, je kunt hieronder inloggen'
                );
                return $this->redirectToRoute('login_with_username', ['username' => $user->getUsername()]);
            }
            $form->get('key')->addError(new FormError('De activatie-sleutel is niet correct, probeer het opnieuw'));
        }

        return $this->render('security/activate.html.twig', ['form' => $form->createView()]);
    }
}
