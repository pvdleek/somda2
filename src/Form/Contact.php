<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Contact extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Onderwerp van jouw bericht',
                'required' => true,
            ])
            ->add('text', TextareaType::class, [
                'attr' => ['rows' => 5, 'cols' => 60],
                'label' => 'Jouw bericht',
                'required' => true,
            ]);
    }
}
