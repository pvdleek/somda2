<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Spot as SpotEntity;
use App\Generics\FormGenerics;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Spot extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('spot_date', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'datepicker'],
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Datum van de spot(s)',
                FormGenerics::KEY_REQUIRED => true,
                FormGenerics::KEY_WIDGET => 'single_text',
            ])
            ->add('train', TextType::class, [
                FormGenerics::KEY_DATA => $options['data']->train->number,
                FormGenerics::KEY_LABEL => 'Materieelnummer',
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('route', TextType::class, [
                FormGenerics::KEY_DATA => $options['data']->route->number,
                FormGenerics::KEY_LABEL => 'Treinnummer',
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('position', EntityType::class, [
                FormGenerics::KEY_CHOICE_LABEL => 'name',
                FormGenerics::KEY_CHOICE_VALUE => 'name',
                FormGenerics::KEY_CLASS => Position::class,
                FormGenerics::KEY_LABEL => 'Positie',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('location', EntityType::class, [
                FormGenerics::KEY_CHOICE_LABEL => function (Location $location) {
                    return $location->name.' - '.$location->description;
                },
                FormGenerics::KEY_CHOICE_VALUE => 'name',
                FormGenerics::KEY_CLASS => Location::class,
                FormGenerics::KEY_LABEL => 'Spot-locatie',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('extra', TextType::class, [
                FormGenerics::KEY_DATA => $options['data']->extra ? $options['data']->extra->extra : null,
                FormGenerics::KEY_LABEL => 'Extra',
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('user_extra', TextType::class, [
                FormGenerics::KEY_DATA => $options['data']->extra ? $options['data']->extra->user_extra : null,
                FormGenerics::KEY_LABEL => 'Verborgen informatie',
                FormGenerics::KEY_MAPPED => false,
                FormGenerics::KEY_REQUIRED => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SpotEntity::class]);
    }
}
