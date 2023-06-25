<?php

namespace App\Form;

use App\Entity\Location;
use App\Generics\FormGenerics;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpotBulkEditLocation extends AbstractType
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
                FormGenerics::KEY_LABEL => 'Nieuwe spot-locatie',
                FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                    return $repository
                        ->createQueryBuilder('l')
                        ->andWhere('l.spotAllowed = TRUE')
                        ->orderBy('l.name', 'ASC');
                },
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['defaultLocation' => null]);
    }
}
