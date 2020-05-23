<?php

namespace App\Form;

use App\Entity\TrainComposition as TrainCompositionEntity;
use App\Entity\TrainCompositionBase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainComposition extends BaseForm
{
    public const OPTION_MANAGEMENT_ROLE = 'managementRole';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var TrainCompositionEntity $trainComposition
         */
        $trainComposition = $options['data'];

        for ($car = 1; $car <= TrainCompositionEntity::NUMBER_OF_CARS; ++$car) {
            if (!is_null($trainComposition->getType()->getCar($car))) {
                $builder->add('car' . $car, TextType::class, [
                    self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_MAX_LENGTH => 15],
                    self::KEY_LABEL => $trainComposition->getType()->getCar($car),
                    self::KEY_REQUIRED => true,
                ]);
            }
        }

        $builder->add('note', TextType::class, [
            self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_MAX_LENGTH => 255],
            self::KEY_LABEL => 'Opmerkingen',
            self::KEY_REQUIRED => false,
        ]);

        if ($options[self::OPTION_MANAGEMENT_ROLE]) {
            $builder
                ->add('extra', TextType::class, [
                    self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                    self::KEY_LABEL => 'Extra',
                    self::KEY_REQUIRED => false,
                ])
                ->add('indexLine', ChoiceType::class, [
                    self::KEY_CHOICES => ['Ja' => true, 'Nee' => false],
                    self::KEY_EXPANDED => true,
                    self::KEY_LABEL => 'Index-regel',
                    self::KEY_REQUIRED => true,
                ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TrainCompositionBase::class, self::OPTION_MANAGEMENT_ROLE => false]);
    }
}
