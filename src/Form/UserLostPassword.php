<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class UserLostPassword extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                self::KEY_ATTRIBUTES => ['maxlength' => 60],
                self::KEY_CONSTRAINTS=> [
                    new Email(['message' => 'Dit is geen geldig e-mailadres']),
                ],
                self::KEY_LABEL => 'Geef je e-mailadres om een nieuw wachtwoord te ontvangen',
                self::KEY_REQUIRED => true,
            ]);
    }
}
