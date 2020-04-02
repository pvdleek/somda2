<?php

namespace App\Form;

use App\Entity\ForumPost as ForumPostEntity;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumPost extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', CKEditorType::class, [
                'attr' => ['rows' => 10, 'cols' => 80],
                'label' => 'Jouw reactie',
                'required' => true,
            ])
            ->add('signatureOn');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumPostEntity::class,
        ]);
    }
}
