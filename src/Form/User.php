<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User as UserEntity;
use App\Generics\ConstraintGenerics;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class User extends AbstractType
{
    public const FIELD_EMAIL = 'email';
    public const FIELD_USERNAME = 'username';
    public const FIELD_PLAIN_PASSWORD = 'plainPassword';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::FIELD_EMAIL, TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 255,
                    FormGenerics::KEY_PLACEHOLDER => 'Geef jouw e-mailadres',
                ],
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add(self::FIELD_USERNAME, TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 20,
                    FormGenerics::KEY_PLACEHOLDER => 'Kies een gebruikersnaam',
                ],
                FormGenerics::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MAX => 20,
                        ConstraintGenerics::MAX_MESSAGE => 'De gebruikersnaam mag maximaal 20 karakters lang zijn',
                        ConstraintGenerics::MIN => 3,
                        ConstraintGenerics::MIN_MESSAGE => 'De gebruikersnaam moet minimaal 3 karakters lang zijn',
                    ]),
                    new Regex([
                        ConstraintGenerics::PATTERN => '/^[a-z0-9-]+$/i',
                        ConstraintGenerics::MESSAGE =>
                            'De gebruikersnaam mag alleen letters, cijfers of een liggend streepje bevatten',
                    ])
                ],
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add(self::FIELD_PLAIN_PASSWORD, PasswordType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_PLACEHOLDER => 'Kies een wachtwoord',
                ],
                FormGenerics::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MIN => 8,
                        ConstraintGenerics::MIN_MESSAGE => 'Het wachtwoord moet minimaal 8 karakters lang zijn',
                    ]),
                ],
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserEntity::class]);
    }
}
