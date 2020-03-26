<?php

namespace App\Form;

use App\Entity\User as UserEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class User extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['maxlength' => 255],
                'label' => 'Geef jouw e-mailadres',
                'required' => true,
            ])
            ->add('username', TextType::class, [
                'attr' => ['maxlength' => 10],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 10,
                        'minMessage' => 'De gebruikersnaam moet minimaal 3 karakters lang zijn',
                        'maxMessage' => 'De gebruikersnaam mag maximaal 10 karakters lang zijn',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-z0-9-]+$/i',
                        'message' => 'De gebruikersnaam mag alleen letters, cijfers of een liggend streepje bevatten',
                    ])
                ],
                'label' => 'Kies een gebruikersnaam (maximaal 10 karakters)',
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                'constraints' => [
                    new Length(['min' => 8, 'minMessage' => 'Het wachtwoord moet minimaal 8 karakters lang zijn']),
                ],
                'label' => 'Kies een wachtwoord',
                'mapped' => false,
                'required' => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEntity::class,
        ]);
    }
}
