<?php

namespace App\Form;

use App\Entity\SpecialRoute as SpecialRouteEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialRoute extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'special-route-datepicker'],
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Startdatum',
                FormGenerics::KEY_REQUIRED => false,
                FormGenerics::KEY_WIDGET => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'special-route-datepicker'],
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Einddatum',
                FormGenerics::KEY_REQUIRED => false,
                FormGenerics::KEY_WIDGET => 'single_text',
            ])
            ->add('title', TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                FormGenerics::KEY_LABEL => 'Titel',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('image', ChoiceType::class, [
                FormGenerics::KEY_CHOICES => $this->getImages(),
                FormGenerics::KEY_LABEL => 'Afbeelding',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('text', HiddenType::class, [
                FormGenerics::KEY_LABEL => 'Bijzondere rit',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('public', CheckboxType::class, [
                FormGenerics::KEY_LABEL => 'Rit gepubliceerd',
            ]);
    }


    private function getImages(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../html/images/materieel');
        $imageList = [];
        foreach ($finder as $file) {
            $imageList[\strtolower($file->getFilenameWithoutExtension())] = $file->getRelativePathname();
        }
        \asort($imageList);
        return $imageList;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SpecialRouteEntity::class]);
    }
}
