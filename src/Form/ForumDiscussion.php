<?php

namespace App\Form;

use App\Entity\ForumDiscussion as ForumDiscussionEntity;
use App\Generics\FormGenerics;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumDiscussion extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                FormGenerics::KEY_LABEL => 'Onderwerp van de discussie',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('text', CKEditorType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_ROWS => 10,
                    FormGenerics::KEY_ATTRIBUTES_COLS => 80,
                ],
                FormGenerics::KEY_LABEL => 'Jouw bericht',
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('signatureOn', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Handtekening gebruiken',
                FormGenerics::KEY_MAPPED => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ForumDiscussionEntity::class]);
    }
}
