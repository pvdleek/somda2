<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User as UserEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserActivate extends AbstractType
{
    public const FIELD_KEY = 'key';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::FIELD_KEY, TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 13],
                FormGenerics::KEY_CONSTRAINTS => [
                    new Length(max: 13, min: 13, maxMessage: 'De activatie-sleutel moet exact 13 karakters lang zijn', minMessage: 'De activatie-sleutel moet exact 13 karakters lang zijn'),
                ],
                FormGenerics::KEY_LABEL => 'Geef de activatie-sleutel die je per e-mail hebt ontvangen',
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserEntity::class]);
    }
}
