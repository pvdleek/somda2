<?php

namespace App\Form;

use App\Entity\User as UserEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserActivate extends AbstractType
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
                'attr' => ['maxlength' => 32],
                'constraints' => [
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
                'label' => 'Geef de activatie-sleutel die je per e-mail hebt ontvangen',
                'mapped' => false,
                'required' => true,
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
