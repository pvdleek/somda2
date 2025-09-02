<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserInfo;
use App\Form\User as UserForm;
use App\Form\UserActivate;
use App\Form\UserLostPassword;
use App\Form\UserPassword;
use App\Generics\RoleGenerics;
use App\Helpers\EmailHelper;
use App\Helpers\FlashHelper;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController
{
    public function __construct(
        private readonly EmailHelper $email_helper,
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, ?string $username = null): Response
    {
        if ($this->user_helper->userIsLoggedIn()) {
            return $this->form_helper->getRedirectHelper()->redirectToRoute('home');
        }

        return $this->template_helper->render('security/login.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Inloggen of account maken bij Somda',
            'lastUsername' => null === $username ? $authenticationUtils->getLastUsername() : $username,
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'register_form' => $this->form_helper->getFactory()->create(UserForm::class, new User())->createView(),
            'view' => 'login',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function registerAction(Request $request): Response|RedirectResponse
    {
        $user = new User();
        $form = $this->form_helper->getFactory()->create(UserForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->validateUsername($form);
            $this->validateEmail($form);
            $this->validatePassword($form);

            if ($form->isValid()) {
                $user->active = false;
                $user->password = (string)password_hash(
                    $form->get(UserForm::FIELD_PLAIN_PASSWORD)->getData(),
                    PASSWORD_DEFAULT
                );
                $user->activation_key = \uniqid();
                $user->register_timestamp = new \DateTime();
                $this->form_helper->getDoctrine()->getManager()->persist($user);

                $userInfo = new UserInfo();
                $userInfo->user = $user;
                $this->form_helper->getDoctrine()->getManager()->persist($userInfo);

                $user->info = $userInfo;

                $this->form_helper->getDoctrine()->getManager()->flush();

                if ($this->email_helper->sendEmail(
                    $user,
                    'Jouw registratie bij Somda',
                    'register',
                    ['user_id' => $user->id, 'activationKey' => $user->activation_key]
                )) {
                    $this->form_helper->getFlashHelper()->add(
                        FlashHelper::FLASH_TYPE_INFORMATION,
                        'Je registratie is geslaagd! Er is een e-mail gestuurd met daarin een link en een ' .
                        'activatiecode. Je kunt op de link klikken of de code op onderstaand scherm invoeren ' .
                        'om jouw account direct actief te maken.'
                    );

                    return $this->form_helper->getRedirectHelper()->redirectToRoute(
                        'activate',
                        ['id' => $user->id]
                    );
                } else {
                    $this->form_helper->getDoctrine()->getManager()->remove($user);
                    $this->form_helper->getDoctrine()->getManager()->flush();

                    $this->form_helper->getFlashHelper()->add(
                        FlashHelper::FLASH_TYPE_ERROR,
                        'Het is niet gelukt een e-mail naar het door jou opgegeven e-mailadres te sturen, ' .
                        'controleer het e-mailadres.'
                    );
                }
            }
        }

        return $this->template_helper->render('security/login.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Inloggen of account maken bij Somda',
            'lastUsername' => '',
            'error' => null,
            'register_form' => $form->createView(),
            'view' => 'register',
        ]);
    }

    private function validateUsername(FormInterface $form): void
    {
        $existingUsername = $this->form_helper->getDoctrine()->getRepository(User::class)->findOneBy(
            [UserForm::FIELD_USERNAME => $form->get(UserForm::FIELD_USERNAME)->getData()]
        );
        if (null !== $existingUsername) {
            $form->get(UserForm::FIELD_USERNAME)->addError(
                new FormError('De gebruikersnaam die je hebt gekozen is al in gebruik, kies een andere gebruikersnaam')
            );
        }
    }

    public function validateEmail(FormInterface $form): void
    {
        if (\substr($form->get(UserForm::FIELD_EMAIL)->getData(), -16) === 'ikbenspamvrij.nl') {
            $form->get(UserForm::FIELD_EMAIL)->addError(
                new FormError('E-mailadressen van ikbenspamvrij.nl zijn niet toegestaan')
            );
        }

        $existingEmail = $this->form_helper->getDoctrine()->getRepository(User::class)->findOneBy(
            [UserForm::FIELD_EMAIL => $form->get(UserForm::FIELD_EMAIL)->getData()]
        );
        if (null !== $existingEmail) {
            $form->get(UserForm::FIELD_EMAIL)->addError(
                new FormError('Het e-mailadres dat je hebt gekozen is al in gebruik, probeer het opnieuw')
            );
        }
    }

    public function validatePassword(FormInterface $form): void
    {
        $plainPassword = (string) $form->get(UserForm::FIELD_PLAIN_PASSWORD)->getData();

        $username = (string) $form->get(UserForm::FIELD_USERNAME)->getData();
        if (\stristr($plainPassword, $username) || \stristr($username, $plainPassword)
            || \stristr(strrev($username), $plainPassword) || \stristr($plainPassword, \strrev($username))
        ) {
            $form->get(UserForm::FIELD_PLAIN_PASSWORD)->addError(
                new FormError('Het wachtwoord dat je hebt gekozen vertoont teveel overeenkomsten ' .
                    'met jouw gebruikersnaam, probeer het opnieuw')
            );
        }

        $email = (string) $form->get(UserForm::FIELD_EMAIL)->getData();
        if (\stristr($plainPassword, $email) || \stristr($email, $plainPassword)
            || \stristr(strrev($email), $plainPassword) || \stristr($plainPassword, \strrev($email))
        ) {
            $form->get(UserForm::FIELD_PLAIN_PASSWORD)->addError(
                new FormError('Het wachtwoord dat je hebt gekozen vertoont teveel overeenkomsten ' .
                    'met jouw e-mailadres, probeer het opnieuw')
            );
        }
    }

    public function activateAction(Request $request, int $id, ?string $key = null): Response|RedirectResponse
    {
        /**
         * @var User $user
         */
        $user = $this->form_helper->getDoctrine()->getRepository(User::class)->find($id);
        if (null === $user) {
            throw new AccessDeniedException('This user does not exist');
        }

        $form = $this->form_helper->getFactory()->create(UserActivate::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get(UserActivate::FIELD_KEY)->getData() === $user->activation_key) {
                /**
                 * @var Group $userGroup
                 */
                $userGroup = $this->form_helper->getDoctrine()->getRepository(Group::class)->find(4);
                $userGroup->addUser($user);
                
                $user->activation_key = null;
                $user->addRole('ROLE_USER')->addGroup($userGroup);
                $this->form_helper->getDoctrine()->getManager()->flush();

                $this->email_helper->sendEmail($user, 'Welkom op Somda -- Belangrijke informatie', 'new-account');

                // Send the email to the admin account
                $samePasswordUsers = $this->form_helper->getDoctrine()->getRepository(User::class)->findBy(
                    ['password' => $user->getPassword()]
                );
                $this->email_helper->sendEmail(
                    $this->user_helper->getAdministratorUser(),
                    'Nieuwe registratie bij Somda',
                    'new-account-admin',
                    ['user' => $user, 'samePasswordUsers' => $samePasswordUsers]
                );

                $this->form_helper->getFlashHelper()->add(
                    FlashHelper::FLASH_TYPE_INFORMATION,
                    'Jouw account is geactiveerd, je kunt na controle door een beheerder inloggen'
                );
                return $this->form_helper->getRedirectHelper()->redirectToRoute(
                    'login_with_username',
                    ['username' => $user->getUserIdentifier()]
                );
            }
            $form->get(UserActivate::FIELD_KEY)->addError(
                new FormError('De activatie-sleutel is niet correct, probeer het opnieuw')
            );
        } elseif (null !== $key) {
            $form->get(UserActivate::FIELD_KEY)->setData($key);
        }

        return $this->template_helper->render('security/activate.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Account activeren',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function lostPasswordAction(Request $request): Response|RedirectResponse
    {
        $form = $this->form_helper->getFactory()->create(UserLostPassword::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $this->form_helper->getDoctrine()->getRepository(User::class)->findOneBy(
                [UserForm::FIELD_EMAIL => $form->get(UserForm::FIELD_EMAIL)->getData()]
            );
            if (null !== $user) {
                $newPassword = $this->getRandomPassword(12);
                $user->password = (string)password_hash($newPassword, PASSWORD_DEFAULT);
                $this->form_helper->getDoctrine()->getManager()->flush();

                $this->email_helper->sendEmail(
                    $user,
                    'Jouw nieuwe wachtwoord voor Somda',
                    'lost-password',
                    ['newPassword' => $newPassword]
                );
            }

            $this->form_helper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_INFORMATION,
                'Er is een e-mail gestuurd met een nieuw wachtwoord'
            );

            return $this->form_helper->getRedirectHelper()->redirectToRoute('lost_password');
        }

        return $this->template_helper->render('security/lostPassword.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Wachtwoord vergeten',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function getRandomPassword(int $length): string
    {
        $keySpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keySpace[\random_int(0, \mb_strlen($keySpace, '8bit') - 1)];
        }
        return \implode('', $pieces);
    }

    public function changePasswordAction(Request $request): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $form = $this->form_helper->getFactory()->create(UserPassword::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->user_helper->getUser()->password = \password_hash($form->get('newPassword')->getData(), PASSWORD_DEFAULT);
            $this->form_helper->getDoctrine()->getManager()->flush();

            $this->form_helper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_INFORMATION,
                'Jouw wachtwoord is gewijzigd'
            );

            return $this->form_helper->getRedirectHelper()->redirectToRoute('home');
        }

        return $this->template_helper->render('security/changePassword.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Wachtwoord wijzigen',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
