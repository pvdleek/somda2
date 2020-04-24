<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumPostAlert extends AbstractType
{
    public const FIELD_COMMENT = 'comment';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::FIELD_COMMENT, TextType::class, [
            'label' => 'Geef eventueel een toelichting bij je melding',
            'required' => false,
        ]);
    }
}
