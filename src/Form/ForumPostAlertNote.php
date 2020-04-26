<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumPostAlertNote extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 5, self::KEY_ATTRIBUTES_COLS => 60],
                self::KEY_LABEL => 'Voeg commentaar toe',
                self::KEY_REQUIRED => true,
            ])
            ->add('sentToReporter', CheckboxType::class, [
                self::KEY_LABEL => 'Stuur dit commentaar naar de melder van het bericht',
                self::KEY_REQUIRED => false,
            ]);
    }
}
