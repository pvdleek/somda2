<?php

declare(strict_types=1);

namespace App\Form;

use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumPostAlert extends AbstractType
{
    public const FIELD_COMMENT = 'comment';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::FIELD_COMMENT, TextType::class, [
            FormGenerics::KEY_LABEL => 'Geef eventueel een toelichting bij je melding',
            FormGenerics::KEY_REQUIRED => false,
        ]);
    }
}
