<?php

namespace App\Form;

use App\Entity\User as UserEntity;
use App\Generics\ConstraintGenerics;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserActivate extends BaseForm
{
    public const FIELD_KEY = 'key';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::FIELD_KEY, TextType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_MAX_LENGTH => 13],
                self::KEY_CONSTRAINTS => [
                    new Length([
                        ConstraintGenerics::MAX => 13,
                        ConstraintGenerics::MAX_MESSAGE => 'De activatie-sleutel moet exact 13 karakters lang zijn',
                        ConstraintGenerics::MIN => 13,
                        ConstraintGenerics::MIN_MESSAGE => 'De activatie-sleutel moet exact 13 karakters lang zijn',
                    ]),
                ],
                self::KEY_LABEL => 'Geef de activatie-sleutel die je per e-mail hebt ontvangen',
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
