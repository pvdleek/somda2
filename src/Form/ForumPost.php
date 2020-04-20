<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumPost extends AbstractType
{
    private const QUOTE_HTML = '<blockquote><strong>Quote</strong><hr />%s (%s): %s<hr /></blockquote><br />';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = '';
        if (!is_null($options['quotedPost'])) {
            $data = sprintf(
                self::QUOTE_HTML,
                $options['quotedPost']->author->username,
                $options['quotedPost']->timestamp->format('d-m-Y H:i:s'),
                $options['quotedPost']->text->text
            );
        } elseif (!is_null($options['editedPost'])) {
            $data = $options['editedPost']->text->text;

        }

        $builder
            ->add('text', CKEditorType::class, [
                'attr' => ['rows' => 10, 'cols' => 80],
                'data' => $data,
                'label' => 'Jouw reactie',
                'required' => true,
            ])
            ->add('signatureOn', CheckboxType::class, [
                'label' => 'Handtekening gebruiken',
            ]);

        if (!is_null($options['editedPost'])) {
            $builder->add('editReason', TextType::class, [
                'label' => 'Reden voor bewerking (optioneel)',
                'required' => false,
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['quotedPost' => null, 'editedPost' => null]);
    }
}
