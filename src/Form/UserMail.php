<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMail extends BaseForm
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
                self::KEY_CHOICES => $senderChoices,
                self::KEY_DATA => 'direct',
                self::KEY_LABEL => 'Kies de afzender',
                self::KEY_REQUIRED => true,
            ])
            ->add('subject', TextType::class, [
                self::KEY_LABEL => 'Geef het onderwerp van jouw bericht',
                self::KEY_REQUIRED => true,
            ])
            ->add('text', CKEditorType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 10, self::KEY_ATTRIBUTES_COLS => 80],
                self::KEY_LABEL => 'Jouw bericht',
                self::KEY_REQUIRED => true,
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
