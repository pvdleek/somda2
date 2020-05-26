<?php

namespace App\Form;

use App\Entity\Location;
use DateTime;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpotBulk extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', EntityType::class, [
                self::KEY_CHOICE_LABEL => function (Location $location) {
                    return $location->name . ' - ' . $location->description;
                },
                self::KEY_CHOICE_VALUE => 'name',
                self::KEY_CLASS => Location::class,
                self::KEY_PREFERRED_CHOICES => [$options['defaultLocation']],
                self::KEY_LABEL => 'Spot-locatie',
                self::KEY_REQUIRED => true,
            ])
            ->add('date', DateType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_CLASS=> 'datepicker'],
                self::KEY_DATA => new DateTime(),
                self::KEY_FORMAT=> 'dd-MM-yyyy',
                self::KEY_HTML5 => false,
                self::KEY_LABEL => 'Datum van de spot(s)',
                self::KEY_REQUIRED => true,
                self::KEY_WIDGET => 'single_text',
            ])
            ->add('spots', TextareaType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 20, self::KEY_ATTRIBUTES_COLS => 60],
                self::KEY_LABEL => 'Spots',
                self::KEY_REQUIRED => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['defaultLocation' => null]);
    }
}
