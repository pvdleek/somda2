<?php

namespace App\Form;

use App\Entity\User as UserEntity;
use App\Generics\ConstraintGenerics;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class User extends BaseForm
{
    public const FIELD_EMAIL = 'email';
    public const FIELD_USERNAME = 'username';
    public const FIELD_PLAIN_PASSWORD = 'plainPassword';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::FIELD_EMAIL, TextType::class, [
                self::KEY_ATTRIBUTES => ['maxlength' => 255],
                self::KEY_LABEL => 'Geef jouw e-mailadres',
                self::KEY_REQUIRED => true,
            ])
            ->add(self::FIELD_USERNAME, TextType::class, [
                self::KEY_ATTRIBUTES => ['maxlength' => 10],
                self::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MAX => 10,
                        ConstraintGenerics::MAX_MESSAGE => 'De gebruikersnaam mag maximaal 10 karakters lang zijn',
                        ConstraintGenerics::MIN => 3,
                        ConstraintGenerics::MIN_MESSAGE => 'De gebruikersnaam moet minimaal 3 karakters lang zijn',
                    ]),
                    new Regex([
                        ConstraintGenerics::PATTERN => '/^[a-z0-9-]+$/i',
                        ConstraintGenerics::MESSAGE =>
                            'De gebruikersnaam mag alleen letters, cijfers of een liggend streepje bevatten',
                    ])
                ],
                self::KEY_LABEL => 'Kies een gebruikersnaam (maximaal 10 karakters)',
                self::KEY_REQUIRED => true,
            ])
            ->add(self::FIELD_PLAIN_PASSWORD, PasswordType::class, [
                self::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MIN => 8,
                        ConstraintGenerics::MIN_MESSAGE => 'Het wachtwoord moet minimaal 8 karakters lang zijn',
                    ]),
                ],
                self::KEY_LABEL => 'Kies een wachtwoord',
                self::KEY_MAPPED => false,
                self::KEY_REQUIRED => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserEntity::class]);
    }
}
