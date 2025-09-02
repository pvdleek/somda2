<?php

declare(strict_types=1);

namespace App\Form;

use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumPostAlertNote extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_ROWS => 5,
                    FormGenerics::KEY_ATTRIBUTES_COLS => 60,
                ],
                FormGenerics::KEY_LABEL => 'Voeg commentaar toe',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('sent_to_reporter', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Stuur dit commentaar naar de melder van het bericht',
                FormGenerics::KEY_REQUIRED => false,
            ]);
    }
}
