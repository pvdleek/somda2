<?php

namespace App\Form;

use App\Entity\ForumDiscussion as ForumDiscussionEntity;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumDiscussion extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                self::KEY_LABEL => 'Onderwerp van de discussie',
                self::KEY_REQUIRED => true,
            ])
            ->add('text', CKEditorType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 10, self::KEY_ATTRIBUTES_COLS => 80],
                self::KEY_LABEL => 'Jouw bericht',
                self::KEY_MAPPED => false,
                self::KEY_REQUIRED => true,
            ])
            ->add('signatureOn', CheckboxType::class, [
                self::KEY_LABEL => 'Handtekening gebruiken',
                self::KEY_MAPPED => false,
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
