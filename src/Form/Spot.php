<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Spot as SpotEntity;
use DateTime;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Spot extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('spotDate', DateType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_CLASS=> 'datepicker'],
                self::KEY_DATA => new DateTime(),
                self::KEY_FORMAT=> 'dd-MM-yyyy',
                self::KEY_HTML5 => false,
                self::KEY_LABEL => 'Datum van de spot(s)',
                self::KEY_REQUIRED => true,
                self::KEY_WIDGET => 'single_text',
            ])
            ->add('train', TextType::class, [
                self::KEY_DATA => $options['data']->train->number,
                self::KEY_LABEL => 'Materieelnummer',
                self::KEY_MAPPED => false,
                self::KEY_REQUIRED => true,
            ])
            ->add('route', TextType::class, [
                self::KEY_DATA => $options['data']->route->number,
                self::KEY_LABEL => 'Treinnummer',
                self::KEY_MAPPED => false,
                self::KEY_REQUIRED => true,
            ])
            ->add('position', EntityType::class, [
                self::KEY_CHOICE_LABEL => 'name',
                self::KEY_CHOICE_VALUE => 'name',
                self::KEY_CLASS => Position::class,
                self::KEY_LABEL => 'Positie',
                self::KEY_REQUIRED => true,
            ])
            ->add('location', EntityType::class, [
                self::KEY_CHOICE_LABEL => function (Location $location) {
                    return $location->name . ' - ' . $location->description;
                },
                self::KEY_CHOICE_VALUE => 'name',
                self::KEY_CLASS => Location::class,
                self::KEY_LABEL => 'Spot-locatie',
                self::KEY_REQUIRED => true,
            ])
            ->add('extra', TextType::class, [
                self::KEY_DATA => $options['data']->extra ? $options['data']->extra->extra : null,
                self::KEY_LABEL => 'Extra',
                self::KEY_MAPPED => false,
                self::KEY_REQUIRED => false,
            ])
            ->add('userExtra', TextType::class, [
                self::KEY_DATA => $options['data']->extra ? $options['data']->extra->userExtra : null,
                self::KEY_LABEL => 'Verborgen informatie',
                self::KEY_MAPPED => false,
                self::KEY_REQUIRED => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SpotEntity::class]);
    }
}
