<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMail extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $senderChoices = [
            'Verstuur de e-mail met mijn e-mailadres als afzender' => 'direct',
            'Verstuur de e-mail anoniem' => 'anonymous',
        ];
        if ($options['isModerator']) {
            $senderChoices['Verstuur de e-mail als moderator'] = 'moderator';
        }

        $builder
            ->add('senderOption', ChoiceType::class, [
                'choices' => $senderChoices,
                'data' => 'direct',
                'label' => 'Kies de afzender',
                'required' => true,
            ])
            ->add('subject', TextType::class, [
                'label' => 'Geef het onderwerp van jouw bericht',
                'required' => true,
            ])
            ->add('text', CKEditorType::class, [
                'attr' => ['rows' => 10, 'cols' => 80],
                'label' => 'Jouw bericht',
                'required' => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['isModerator' => false]);
    }
}
