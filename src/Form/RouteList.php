<?php

namespace App\Form;

use App\Entity\Characteristic;
use App\Entity\RouteList as RouteListEntity;
use App\Entity\Transporter;
use App\Generics\ConstraintGenerics;
use App\Generics\FormGenerics;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;

class RouteList extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstNumber', NumberType::class, [
                FormGenerics::KEY_CONSTRAINTS => [
                    new GreaterThan([
                        ConstraintGenerics::MESSAGE => 'Het startnummer moet minimaal 1 zijn',
                        ConstraintGenerics::VALUE => 0,
                    ]),
                    new LessThan([
                        ConstraintGenerics::MESSAGE => 'Het startnummer mag maximaal 999999 zijn',
                        ConstraintGenerics::VALUE => 1000000,
                    ]),
                ],
                FormGenerics::KEY_HTML5 => true,
                FormGenerics::KEY_LABEL => 'Startnummer',
                FormGenerics::KEY_REQUIRED => true,
                FormGenerics::KEY_SCALE => 0,
            ])
            ->add('lastNumber', NumberType::class, [
                FormGenerics::KEY_CONSTRAINTS => [
                    new GreaterThan([
                        ConstraintGenerics::MESSAGE => 'Het eindnummer moet minimaal 1 zijn',
                        ConstraintGenerics::VALUE => 0,
                    ]),
                    new LessThan([
                        ConstraintGenerics::MESSAGE => 'Het eindnummer mag maximaal 999999 zijn',
                        ConstraintGenerics::VALUE => 1000000,
                    ]),
                ],
                FormGenerics::KEY_HTML5 => true,
                FormGenerics::KEY_LABEL => 'Eindnummer',
                FormGenerics::KEY_REQUIRED => true,
                FormGenerics::KEY_SCALE => 0,
            ])
            ->add('transporter', EntityType::class, [
                FormGenerics::KEY_CHOICE_LABEL => 'name',
                FormGenerics::KEY_CHOICE_VALUE => 'id',
                FormGenerics::KEY_CLASS => Transporter::class,
                FormGenerics::KEY_LABEL => 'Vervoerder',
                FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
                },
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('characteristic', EntityType::class, [
                FormGenerics::KEY_CHOICE_LABEL => function (Characteristic $characteristic) {
                    return $characteristic->name . ' (' . $characteristic->description . ')';
                },
                FormGenerics::KEY_CHOICE_VALUE => 'id',
                FormGenerics::KEY_CLASS => Characteristic::class,
                FormGenerics::KEY_LABEL => 'Karakteristiek',
                FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                },
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('section', TextType::class, [
                FormGenerics::KEY_LABEL => 'Traject',
                FormGenerics::KEY_REQUIRED => false,
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
