<?php

namespace App\Form;

use App\Generics\FormGenerics;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Contact extends AbstractType
{
    public const FIELD_EMAIL = 'email';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                FormGenerics::KEY_LABEL => 'Onderwerp van jouw bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('text', TextareaType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_ROWS => 5,
                    FormGenerics::KEY_ATTRIBUTES_COLS => 60,
                ],
                FormGenerics::KEY_LABEL => 'Jouw bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('captcha', CaptchaType::class, [
                FormGenerics::KEY_LABEL => 'Neem de code in de afbeelding over',
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }
}
