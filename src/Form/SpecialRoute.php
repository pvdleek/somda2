<?php

namespace App\Form;

use App\Entity\SpecialRoute as SpecialRouteEntity;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialRoute extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_CLASS=> 'special-route-datepicker'],
                self::KEY_FORMAT=> 'dd-MM-yyyy',
                self::KEY_HTML5 => false,
                self::KEY_LABEL => 'Startdatum',
                self::KEY_REQUIRED => false,
                self::KEY_WIDGET => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_CLASS=> 'special-route-datepicker'],
                self::KEY_FORMAT=> 'dd-MM-yyyy',
                self::KEY_HTML5 => false,
                self::KEY_LABEL => 'Einddatum',
                self::KEY_REQUIRED => false,
                self::KEY_WIDGET => 'single_text',
            ])
            ->add('title', TextType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_MAX_LENGTH => 255],
                self::KEY_LABEL => 'Titel',
                self::KEY_REQUIRED => true,
            ])
            ->add('image', ChoiceType::class, [
                self::KEY_CHOICES => $this->getImages(),
                self::KEY_LABEL => 'Afbeelding',
                self::KEY_REQUIRED => true,
            ])
            ->add('text', CKEditorType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_ATTRIBUTES_ROWS => 10, self::KEY_ATTRIBUTES_COLS => 80],
                self::KEY_LABEL => 'Bijzondere rit',
                self::KEY_REQUIRED => true,
            ])
            ->add('public', CheckboxType::class, [
                self::KEY_LABEL => 'Rit gepubliceerd',
            ]);
    }


    /**
     * @return array
     */
    private function getImages(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../public/images/materieel');
        $imageList = [];
        foreach ($finder as $file) {
            $imageList[strtolower($file->getFilenameWithoutExtension())] = $file->getRelativePathname();
        }
        asort($imageList);
        return $imageList;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SpecialRouteEntity::class]);
    }
}
