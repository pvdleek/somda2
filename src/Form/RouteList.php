<?php

namespace App\Form;

use App\Entity\Characteristic;
use App\Entity\RouteList as RouteListEntity;
use App\Entity\Transporter;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;

class RouteList extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstNumber', NumberType::class, [
                self::KEY_CONSTRAINTS => [
                    new GreaterThan(['message' => 'Het startnummer moet minimaal 1 zijn', 'value' => 0]),
                    new LessThan(['message' => 'Het startnummer mag maximaal 999999 zijn', 'value' => 1000000]),
                ],
                self::KEY_HTML5 => true,
                self::KEY_LABEL => 'Startnummer',
                self::KEY_REQUIRED => true,
                self::KEY_SCALE => 0,
            ])
            ->add('lastNumber', NumberType::class, [
                self::KEY_CONSTRAINTS => [
                    new GreaterThan(['message' => 'Het eindnummer moet minimaal 1 zijn', 'value' => 0]),
                    new LessThan(['message' => 'Het eindnummer mag maximaal 999999 zijn', 'value' => 1000000]),
                ],
                self::KEY_HTML5 => true,
                self::KEY_LABEL => 'Eindnummer',
                self::KEY_REQUIRED => true,
                self::KEY_SCALE => 0,
            ])
            ->add('transporter', EntityType::class, [
                self::KEY_CHOICE_LABEL => 'name',
                self::KEY_CHOICE_VALUE => 'id',
                self::KEY_CLASS => Transporter::class,
                self::KEY_LABEL => 'Vervoerder',
                self::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
                },
                self::KEY_REQUIRED => true,
            ])
            ->add('characteristic', EntityType::class, [
                self::KEY_CHOICE_LABEL => function (Characteristic $characteristic) {
                    return $characteristic->name . ' (' . $characteristic->description . ')';
                },
                self::KEY_CHOICE_VALUE => 'id',
                self::KEY_CLASS => Characteristic::class,
                self::KEY_LABEL => 'Karakteristiek',
                self::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                self::KEY_REQUIRED => true,
            ])
            ->add('section', TextType::class, [
                self::KEY_LABEL => 'Traject',
                self::KEY_REQUIRED => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => RouteListEntity::class ]);
    }
}
