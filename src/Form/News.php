<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\News as NewsEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class News extends AbstractType
{
    public const FIELD_TIMESTAMP = 'timestamp';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                FormGenerics::KEY_LABEL => 'Titel van het bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('text', HiddenType::class, [
                FormGenerics::KEY_LABEL => 'Bericht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('archived', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Bericht gearchiveerd',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => NewsEntity::class]);
    }
}
