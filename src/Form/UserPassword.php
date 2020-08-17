<?php

namespace App\Form;

use App\Generics\ConstraintGenerics;
use App\Generics\FormGenerics;
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
                FormGenerics::KEY_CONSTRAINTS => new UserPasswordAssert(
                    [ConstraintGenerics::MESSAGE => 'Jouw huidige wachtwoord is niet correct']
                ),
                FormGenerics::KEY_LABEL => 'Jouw huidige wachtwoord',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('newPassword', RepeatedType::class, [
                FormGenerics::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MIN => 8,
                        ConstraintGenerics::MIN_MESSAGE => 'Het wachtwoord moet minimaal 8 karakters lang zijn',
                    ]),
                ],
                FormGenerics::KEY_FIRST_OPTIONS => [FormGenerics::KEY_LABEL => 'Kies een nieuw wachtwoord'],
                FormGenerics::KEY_INVALID_MESSAGE => 'De wachtwoorden moeten overeen komen',
                FormGenerics::KEY_REQUIRED => true,
                FormGenerics::KEY_SECOND_OPTIONS => [FormGenerics::KEY_LABEL => 'Typ nogmaals jouw nieuwe wachtwoord'],
                FormGenerics::KEY_TYPE => PasswordType::class,
            ]);
    }
}
