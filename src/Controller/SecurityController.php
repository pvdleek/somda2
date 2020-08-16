<?php

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
use DateTime;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController
{
    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var EmailHelper
     */
    private EmailHelper $emailHelper;

    /**
     * @param FormHelper $formHelper
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     * @param EmailHelper $emailHelper
     */
    public function __construct(
        FormHelper $formHelper,
        UserHelper $userHelper,
        TemplateHelper $templateHelper,
        EmailHelper $emailHelper
    ) {
        $this->formHelper = $formHelper;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param string|null $username
     * @return Response
     * @throws Exception
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, string $username = null): Response
    {
        if ($this->userHelper->userIsLoggedIn()) {
            return $this->formHelper->getRedirectHelper()->redirectToRoute('home');
        }

        return $this->templateHelper->render('security/login.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Inloggen',
            'lastUsername' => is_null($username) ? $authenticationUtils->getLastUsername() : $username,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->formHelper->getFactory()->create(UserForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->validateUsername($form);
            $this->validateEmail($form);
            $this->validatePassword($form);

            if ($form->isValid()) {
                $user->active = false;
                $user->password = password_hash(
                    $form->get(UserForm::FIELD_PLAIN_PASSWORD)->getData(),
                    PASSWORD_DEFAULT
                );
                $user->activationKey = uniqid();
                $user->registerTimestamp = new DateTime();
                $this->formHelper->getDoctrine()->getManager()->persist($user);

                $userInfo = new UserInfo();
                $userInfo->user = $user;
                $this->formHelper->getDoctrine()->getManager()->persist($userInfo);

                $user->info = $userInfo;

                $this->formHelper->getDoctrine()->getManager()->flush();

                if ($this->emailHelper->sendEmail(
                    $user,
                    'Jouw registratie bij Somda',
                    'register',
                    ['userId' => $user->id, 'activationKey' => $user->activationKey]
                )) {
                    $this->formHelper->getFlashHelper()->add(
                        FlashHelper::FLASH_TYPE_INFORMATION,
                        'Je registratie is geslaagd! Er is een e-mail gestuurd met daarin een link en een ' .
                        'activatiecode. Je kunt op de link klikken of de code op onderstaand scherm invoeren ' .
                        'om jouw account direct actief te maken.'
                    );

                    return $this->formHelper->getRedirectHelper()->redirectToRoute(
                        'activate',
                        ['id' => $user->id]
                    );
                } else {
                    $this->formHelper->getDoctrine()->getManager()->remove($user);
                    $this->formHelper->getDoctrine()->getManager()->flush();

                    $this->formHelper->getFlashHelper()->add(
                        FlashHelper::FLASH_TYPE_ERROR,
                        'Het is niet gelukt een e-mail naar het door jou opgegeven e-mailadres te sturen, ' .
                        'controleer het e-mailadres.'
                    );
                }
            }
        }

        return $this->templateHelper->render('security/register.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Nieuw account aanmaken',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param FormInterface $form
     */
    private function validateUsername(FormInterface $form): void
    {
        $existingUsername = $this->formHelper->getDoctrine()->getRepository(User::class)->findOneBy(
            [UserForm::FIELD_USERNAME => $form->get(UserForm::FIELD_USERNAME)->getData()]
        );
        if (!is_null($existingUsername)) {
            $form->get(UserForm::FIELD_USERNAME)->addError(
                new FormError('De gebruikersnaam die je hebt gekozen is al in gebruik, kies een andere gebruikersnaam')
            );
        }
    }

    /**
     * @param FormInterface $form
     */
    public function validateEmail(FormInterface $form): void
    {
        if (substr($form->get(UserForm::FIELD_EMAIL)->getData(), -16) === 'ikbenspamvrij.nl') {
            $form->get(UserForm::FIELD_EMAIL)->addError(
                new FormError('E-mailadressen van ikbenspamvrij.nl zijn niet toegestaan')
            );
        }

        $existingEmail = $this->formHelper->getDoctrine()->getRepository(User::class)->findOneBy(
            [UserForm::FIELD_EMAIL => $form->get(UserForm::FIELD_EMAIL)->getData()]
        );
        if (!is_null($existingEmail)) {
            $form->get(UserForm::FIELD_EMAIL)->addError(
                new FormError('Het e-mailadres dat je hebt gekozen is al in gebruik, probeer het opnieuw')
            );
        }
    }

    /**
     * @param FormInterface $form
     */
    public function validatePassword(FormInterface $form): void
    {
        $plainPassword = (string)$form->get(UserForm::FIELD_PLAIN_PASSWORD)->getData();

        $username = (string)$form->get(UserForm::FIELD_USERNAME)->getData();
        if (stristr($plainPassword, $username) || stristr($username, $plainPassword)
            || stristr(strrev($username), $plainPassword) || stristr($plainPassword, strrev($username))
        ) {
            $form->get(UserForm::FIELD_PLAIN_PASSWORD)->addError(
                new FormError('Het wachtwoord dat je hebt gekozen vertoont teveel overeenkomsten ' .
                    'met jouw gebruikersnaam, probeer het opnieuw')
            );
        }

        $email = (string)$form->get(UserForm::FIELD_EMAIL)->getData();
        if (stristr($plainPassword, $email) || stristr($email, $plainPassword)
            || stristr(strrev($email), $plainPassword) || stristr($plainPassword, strrev($email))
        ) {
            $form->get(UserForm::FIELD_PLAIN_PASSWORD)->addError(
                new FormError('Het wachtwoord dat je hebt gekozen vertoont teveel overeenkomsten ' .
                    'met jouw e-mailadres, probeer het opnieuw')
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
        $user = $this->formHelper->getDoctrine()->getRepository(User::class)->find($id);
        if (is_null($user)) {
            throw new AccessDeniedException('This user does not exist');
        }

        $form = $this->formHelper->getFactory()->create(UserActivate::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get(UserActivate::FIELD_KEY)->getData() === $user->activationKey) {
                /**
                 * @var Group $userGroup
                 */
                $userGroup = $this->formHelper->getDoctrine()->getRepository(Group::class)->find(4);
                $userGroup->addUser($user);
                
                $user->active = true;
                $user->activationKey = null;
                $user->addRole('ROLE_USER')->addGroup($userGroup);
                $this->formHelper->getDoctrine()->getManager()->flush();

                $this->emailHelper->sendEmail($user, 'Welkom op Somda -- Belangrijke informatie', 'new-account');

                // Send the email to the admin account
                $samePasswordUsers = $this->formHelper->getDoctrine()->getRepository(User::class)->findBy(
                    ['password' => $user->getPassword()]
                );
                $this->emailHelper->sendEmail(
                    $this->userHelper->getAdministratorUser(),
                    'Nieuwe registratie bij Somda',
                    'new-account-admin',
                    ['user' => $user, 'samePasswordUsers' => $samePasswordUsers]
                );

                $this->formHelper->getFlashHelper()->add(
                    FlashHelper::FLASH_TYPE_INFORMATION,
                    'Jouw account is geactiveerd, je kunt nu inloggen'
                );
                return $this->formHelper->getRedirectHelper()->redirectToRoute(
                    'login_with_username',
                    ['username' => $user->getUsername()]
                );
            }
            $form->get(UserActivate::FIELD_KEY)->addError(
                new FormError('De activatie-sleutel is niet correct, probeer het opnieuw')
            );
        } elseif (!is_null($key)) {
            $form->get(UserActivate::FIELD_KEY)->setData($key);
        }

        return $this->templateHelper->render('security/activate.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Account activeren',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function lostPasswordAction(Request $request)
    {
        $form = $this->formHelper->getFactory()->create(UserLostPassword::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $this->formHelper->getDoctrine()->getRepository(User::class)->findOneBy(
                [UserForm::FIELD_EMAIL => $form->get(UserForm::FIELD_EMAIL)->getData()]
            );
            if (!is_null($user)) {
                $newPassword = $this->getRandomPassword(12);
                $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->formHelper->getDoctrine()->getManager()->flush();

                $this->emailHelper->sendEmail(
                    $user,
                    'Jouw nieuwe wachtwoord voor Somda',
                    'lost-password',
                    ['newPassword' => $newPassword]
                );
            }

            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_INFORMATION,
                'Er is een e-mail gestuurd met een nieuw wachtwoord'
            );

            return $this->formHelper->getRedirectHelper()->redirectToRoute('lost_password');
        }

        return $this->templateHelper->render('security/lostPassword.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Wachtwoord vergeten',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param int $length
     * @return string
     * @throws Exception
     */
    private function getRandomPassword(int $length): string
    {
        $keySpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keySpace[random_int(0, mb_strlen($keySpace, '8bit') - 1)];
        }
        return implode('', $pieces);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function changePasswordAction(Request $request)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $form = $this->formHelper->getFactory()->create(UserPassword::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userHelper->getUser()->password =
                password_hash($form->get('newPassword')->getData(), PASSWORD_DEFAULT);
            $this->formHelper->getDoctrine()->getManager()->flush();

            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_INFORMATION,
                'Jouw wachtwoord is gewijzigd'
            );

            return $this->formHelper->getRedirectHelper()->redirectToRoute('home');
        }

        return $this->templateHelper->render('security/changePassword.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Wachtwoord wijzigen',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
