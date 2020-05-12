<?php

namespace App\Form;

use App\Entity\News as NewsEntity;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class News extends BaseForm
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
                self::KEY_ATTRIBUTES => ['maxlength' => 255],
                self::KEY_LABEL => 'Titel van het bericht',
                self::KEY_REQUIRED => true,
            ])
            ->add('text', CKEditorType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 10, self::KEY_ATTRIBUTES_COLS => 80],
                self::KEY_LABEL => 'Bericht',
                self::KEY_REQUIRED => true,
            ])
            ->add('archived', CheckboxType::class, [
                self::KEY_LABEL => 'Bericht gearchiveerd',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => NewsEntity::class]);
    }
}
