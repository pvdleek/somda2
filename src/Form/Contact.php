<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Contact extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                self::KEY_LABEL => 'Onderwerp van jouw bericht',
                self::KEY_REQUIRED => true,
            ])
            ->add('text', TextareaType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 5, self::KEY_ATTRIBUTES_COLS => 60],
                self::KEY_LABEL => 'Jouw bericht',
                self::KEY_REQUIRED => true,
            ]);
    }
}
