<?php
declare(strict_types=1);

namespace App\Form;

use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumSearch extends AbstractType
{
    public const METHOD_ALL = 'all';
    public const METHOD_SOME = 'some';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('words', TextType::class, [
                FormGenerics::KEY_LABEL => 'Zoekterm(en)',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('method', ChoiceType::class, [
                FormGenerics::KEY_CHOICES => [
                    'Alle zoektermen moeten voorkomen' => self::METHOD_ALL,
                    '1 Of meer zoektermen moeten voorkomen' => self::METHOD_SOME,
                ],
                FormGenerics::KEY_DATA => self::METHOD_ALL,
                FormGenerics::KEY_EXPANDED => true,
                FormGenerics::KEY_LABEL => null,
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }
}
