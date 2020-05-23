<?php

namespace App\Form;

use App\Entity\RailNews as RailNewsEntity;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RailNews extends BaseForm
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
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                self::KEY_LABEL => 'Titel van het bericht',
                self::KEY_REQUIRED => true,
            ])
            ->add('introduction', TextareaType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 5, self::KEY_ATTRIBUTES_COLS => 60],
                self::KEY_LABEL => 'Koptekst van het bericht',
                self::KEY_REQUIRED => true,
            ])
            ->add('approved', CheckboxType::class, [
                self::KEY_LABEL => 'Bericht goedgekeurd',
            ])
            ->add('automaticUpdates', CheckboxType::class, [
                self::KEY_LABEL => 'Bericht automatisch bijwerken',
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
