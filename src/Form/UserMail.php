<?php

namespace App\Form;

use App\Generics\FormGenerics;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMail extends AbstractType
{
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
                FormGenerics::KEY_CHOICES => $senderChoices,
                FormGenerics::KEY_DATA => 'direct',
                FormGenerics::KEY_LABEL => 'Kies de afzender',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('subject', TextType::class, [
                FormGenerics::KEY_LABEL => 'Geef het onderwerp van jouw bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('text', CKEditorType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_ROWS => 10,
                    FormGenerics::KEY_ATTRIBUTES_COLS => 80,
                ],
                FormGenerics::KEY_LABEL => 'Jouw bericht',
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['isModerator' => false]);
    }
}
