<?php

namespace App\Form;

use App\Entity\TrainComposition as TrainCompositionEntity;
use App\Entity\TrainCompositionBase;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainComposition extends AbstractType
{
    public const OPTION_MANAGEMENT_ROLE = 'managementRole';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var TrainCompositionEntity $trainComposition
         */
        $trainComposition = $options['data'];

        for ($car = 1; $car <= TrainCompositionEntity::NUMBER_OF_CARS; ++$car) {
            if (null !== $trainComposition->getType()->getCar($car)) {
                $builder->add('car' . $car, TextType::class, [
                    FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 15],
                    FormGenerics::KEY_LABEL => $trainComposition->getType()->getCar($car),
                    FormGenerics::KEY_REQUIRED => false,
                ]);
            }
        }

        $builder->add('note', TextType::class, [
            FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 255],
            FormGenerics::KEY_LABEL => 'Opmerkingen',
            FormGenerics::KEY_REQUIRED => false,
        ]);

        if ($options[self::OPTION_MANAGEMENT_ROLE]) {
            $builder
                ->add('last_update_timestamp', DateType::class, [
                    FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'datepicker'],
                    FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                    FormGenerics::KEY_HTML5 => false,
                    FormGenerics::KEY_LABEL => 'Update datum',
                    FormGenerics::KEY_REQUIRED => true,
                    FormGenerics::KEY_WIDGET => 'single_text',
                ])
                ->add('extra', TextType::class, [
                    FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                    FormGenerics::KEY_LABEL => 'Extra',
                    FormGenerics::KEY_REQUIRED => false,
                ])
                ->add('indexLine', ChoiceType::class, [
                    FormGenerics::KEY_CHOICES => ['Ja' => true, 'Nee' => false],
                    FormGenerics::KEY_EXPANDED => true,
                    FormGenerics::KEY_LABEL => 'Index-regel',
                    FormGenerics::KEY_REQUIRED => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TrainCompositionBase::class, self::OPTION_MANAGEMENT_ROLE => false]);
    }
}
