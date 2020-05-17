<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumPost extends BaseForm
{
    private const QUOTE_HTML = '<blockquote><strong>Quote</strong><hr />%s (%s): %s<hr /></blockquote><br />';

    public const FIELD_EDIT_AS_MODERATOR = 'editAsModerator';
    public const FIELD_TITLE = 'title';
    public const FIELD_DISCUSSION = 'discussion';
    public const FIELD_TIMESTAMP = 'timestamp';

    public const OPTION_QUOTED_POST = 'quotedPost';
    public const OPTION_EDITED_POST = 'editedPost';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = '';
        if (!is_null($options[self::OPTION_QUOTED_POST])) {
            $data = sprintf(
                self::QUOTE_HTML,
                $options[self::OPTION_QUOTED_POST]->author->username,
                $options[self::OPTION_QUOTED_POST]->timestamp->format('d-m-Y H:i:s'),
                $options[self::OPTION_QUOTED_POST]->text->text
            );
        } elseif (!is_null($options[self::OPTION_EDITED_POST])) {
            $data = $options[self::OPTION_EDITED_POST]->text->text;

        }

        $builder
            ->add('text', CKEditorType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 10, self::KEY_ATTRIBUTES_COLS => 80],
                self::KEY_DATA => $data,
                self::KEY_LABEL => 'Jouw reactie',
                self::KEY_REQUIRED => true,
            ])
            ->add('signatureOn', CheckboxType::class, [
                self::KEY_LABEL => 'Handtekening gebruiken',
            ]);

        if (!is_null($options[self::OPTION_EDITED_POST])) {
            $builder->add('editReason', TextType::class, [
                self::KEY_LABEL => 'Reden voor bewerking (optioneel)',
                self::KEY_REQUIRED => false,
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([self::OPTION_QUOTED_POST => null, self::OPTION_EDITED_POST => null]);
    }
}
