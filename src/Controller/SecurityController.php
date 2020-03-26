<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User as UserForm;
use DateTime;
use Exception;
use LogicException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->userIsLoggedIn()) {
             return $this->redirectToRoute('home');
         }

        return $this->render('security/login.html.twig', [
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     *
     */
    public function logoutAction(): void
    {
        throw new LogicException('This method can be blank, it will be intercepted by the logout key on your firewall');
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
                $user
                    ->setActive(false)
                    ->setPassword(md5(md5(md5($form->get('plainPassword')->getData()))))
                    ->setActivationKey(md5(md5(rand())))
                    ->setRegistrationDate(new DateTime());

                $this->doctrine->getManager()->persist($user);
                $this->doctrine->getManager()->flush();

                if ($this->sendEmail(
                    $user,
                    'Jouw registratie bij Somda',
                    'register',
                    ['userId' => $user->getId(), 'activationKey' => $user->getActivationKey()]
                )) {
                    $this->addFlash(
                        'success',
                        'Je registratie is geslaagd! Er is een e-mail gestuurd met daarin een link en een ' .
                        'activatiecode. Je kunt op de link klikken of de code op onderstaand scherm invoeren ' .
                        'om jouw account direct actief te maken.'
                    );

                    return $this->redirectToRoute('activate');
                } else {
                    $this->doctrine->getManager()->remove($user);
                    $this->doctrine->getManager()->flush();

                    $this->addFlash(
                        'danger',
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
}
