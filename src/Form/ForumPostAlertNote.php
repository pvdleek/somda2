<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumPostAlertNote extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'attr' => ['rows' => 5, 'cols' => 60],
                'label' => 'Voeg commentaar toe',
                'required' => true,
            ])
            ->add('sentToReporter', CheckboxType::class, [
                'label' => 'Stuur dit commentaar naar de melder van het bericht',
                'required' => false,
            ]);
    }
}
