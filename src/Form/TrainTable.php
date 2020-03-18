<?php

namespace App\Form;

use App\Entity\TrainTableYear;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TrainTable extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trainTableYear', EntityType::class, [
                'choice_label' => 'name',
                'class' => TrainTableYear::class,
                'label' => 'Kies de dienstregeling',
            ])
            ->add('routeNumber', TextType::class, ['label' => 'Geef het treinnummer']);
    }
}
