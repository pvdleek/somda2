<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class UserLostPassword extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['maxlength' => 60],
                'constraints' => [
                    new Email(['message' => 'Dit is geen geldig e-mailadres']),
                ],
                'label' => 'Geef je e-mailadres om een nieuw wachtwoord te ontvangen',
                'required' => true,
            ]);
    }
}
