<?php

namespace App\Form;

use App\Entity\ForumDiscussion as ForumDiscussionEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumDiscussion extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                FormGenerics::KEY_LABEL => 'Onderwerp van de discussie',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('text', HiddenType::class, [
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('signatureOn', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Handtekening gebruiken',
                FormGenerics::KEY_MAPPED => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ForumDiscussionEntity::class]);
    }
}
