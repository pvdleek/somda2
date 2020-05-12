<?php

namespace App\Form;

use App\Generics\ConstraintGenerics;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword as UserPasswordAssert;
use Symfony\Component\Validator\Constraints\Length;

class UserPassword extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                self::KEY_CONSTRAINTS => new UserPasswordAssert(
                    [ConstraintGenerics::MESSAGE => 'Jouw huidige wachtwoord is niet correct']
                ),
                self::KEY_LABEL => 'Jouw huidige wachtwoord',
                self::KEY_REQUIRED => true,
            ])
            ->add('newPassword', RepeatedType::class, [
                self::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MIN => 8,
                        ConstraintGenerics::MIN_MESSAGE => 'Het wachtwoord moet minimaal 8 karakters lang zijn',
                    ]),
                ],
                self::KEY_FIRST_OPTIONS => [self::KEY_LABEL => 'Kies een nieuw wachtwoord'],
                self::KEY_INVALID_MESSAGE => 'De wachtwoorden moeten overeen komen',
                self::KEY_REQUIRED => true,
                self::KEY_SECOND_OPTIONS => [self::KEY_LABEL => 'Typ nogmaals jouw nieuwe wachtwoord'],
                self::KEY_TYPE => PasswordType::class,
            ]);
    }
}
