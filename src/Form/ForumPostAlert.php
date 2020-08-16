<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumPostAlert extends BaseForm
{
    public const FIELD_COMMENT = 'comment';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::FIELD_COMMENT, TextType::class, [
            self::KEY_LABEL => 'Geef eventueel een toelichting bij je melding',
            self::KEY_REQUIRED => false,
        ]);
    }
}
