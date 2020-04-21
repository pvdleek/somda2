<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword as UserPasswordAssert;
use Symfony\Component\Validator\Constraints\Length;

class UserPassword extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'constraints' => new UserPasswordAssert(['message' => 'Jouw huidige wachtwoord is niet correct']),
                'label' => 'Jouw huidige wachtwoord',
                'required' => true,
            ])
            ->add('newPassword', RepeatedType::class, [
                'constraints' => [
                    new Length(['min' => 8, 'minMessage' => 'Het wachtwoord moet minimaal 8 karakters lang zijn']),
                ],
                'first_options' => ['label' => 'Kies een nieuw wachtwoord'],
                'invalid_message' => 'De wachtwoorden moeten overeen komen',
                'required' => true,
                'second_options' => ['label' => 'Typ nogmaals jouw nieuwe wachtwoord'],
                'type' => PasswordType::class,
            ]);
    }
}
