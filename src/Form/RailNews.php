<?php

namespace App\Form;

use App\Entity\RailNews as RailNewsEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RailNews extends AbstractType
{
    public const FIELD_TIMESTAMP = 'timestamp';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                FormGenerics::KEY_LABEL => 'Titel van het bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('introduction', TextareaType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_ROWS => 5,
                    FormGenerics::KEY_ATTRIBUTES_COLS => 60,
                ],
                FormGenerics::KEY_LABEL => 'Koptekst van het bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('active', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Bericht goedgekeurd',
            ])
            ->add('automaticUpdates', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Bericht automatisch bijwerken',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => RailNewsEntity::class]);
    }
}
