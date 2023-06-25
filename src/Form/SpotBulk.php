<?php

namespace App\Form;

use App\Entity\Location;
use App\Generics\FormGenerics;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpotBulk extends AbstractType
{
    /**
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', EntityType::class, [
                FormGenerics::KEY_CHOICE_LABEL => function (Location $location) {
                    return $location->name . ' - ' . $location->description;
                },
                FormGenerics::KEY_CHOICE_VALUE => 'name',
                FormGenerics::KEY_CLASS => Location::class,
                FormGenerics::KEY_PREFERRED_CHOICES => [$options['defaultLocation']],
                FormGenerics::KEY_LABEL => 'Spot-locatie',
                FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                    return $repository
                        ->createQueryBuilder('l')
                        ->andWhere('l.spotAllowed = TRUE')
                        ->orderBy('l.name', 'ASC');
                },
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('date', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'datepicker'],
                FormGenerics::KEY_DATA => new \DateTime(),
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Datum van de spot(s)',
                FormGenerics::KEY_REQUIRED => true,
                FormGenerics::KEY_WIDGET => 'single_text',
            ])
            ->add('spots', TextareaType::class, [
                FormGenerics::KEY_ATTRIBUTES => [
                    FormGenerics::KEY_ATTRIBUTES_ROWS => 20,
                    FormGenerics::KEY_ATTRIBUTES_COLS => 60,
                ],
                FormGenerics::KEY_LABEL => 'Spots',
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['defaultLocation' => null]);
    }
}
