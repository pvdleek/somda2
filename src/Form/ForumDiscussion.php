<?php

namespace App\Form;

use App\Entity\ForumDiscussion as ForumDiscussionEntity;
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
                'label' => 'Onderwerp van de discussie',
                'required' => true,
            ])
            ->add('text', CKEditorType::class, [
                'attr' => ['rows' => 10, 'cols' => 80],
                'label' => 'Jouw bericht',
                'mapped' => false,
                'required' => true,
            ])
            ->add('signatureOn', CheckboxType::class, [
                'label' => 'Handtekening gebruiken',
                'mapped' => false,
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
