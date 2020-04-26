<?php

namespace App\Form;

use App\Entity\User as UserEntity;
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
                self::KEY_ATTRIBUTES => ['maxlength' => 32],
                self::KEY_CONSTRAINTS=> [
                    new Length([
                        'max' => 32,
                        'maxMessage' => 'De activatie-sleutel moet exact 32 karakters lang zijn',
                        'min' => 32,
                        'minMessage' => 'De activatie-sleutel moet exact 32 karakters lang zijn',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z0-9]+$/i',
                        'message' => 'De gebruikersnaam mag alleen letters en cijfers bevatten',
                    ])
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
