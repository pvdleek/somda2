<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ForumSearch extends BaseForm
{
    public const METHOD_ALL = 'all';
    public const METHOD_SOME = 'some';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('words', TextType::class, [
                self::KEY_LABEL => 'Zoekterm(en)',
                self::KEY_REQUIRED => true,
            ])
            ->add('method', ChoiceType::class, [
                self::KEY_CHOICES => [
                    'Alle zoektermen moeten voorkomen' => self::METHOD_ALL,
                    '1 Of meer zoektermen moeten voorkomen' => self::METHOD_SOME,
                ],
                self::KEY_DATA => self::METHOD_ALL,
                self::KEY_EXPANDED => true,
                self::KEY_LABEL => null,
                self::KEY_REQUIRED => true,
            ]);
    }
}
