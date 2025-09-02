<?php

declare(strict_types=1);

namespace App\Form;

use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class SpotBulkEditDate extends AbstractType
{
    /**
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'datepicker'],
                FormGenerics::KEY_DATA => new \DateTime(),
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Nieuwe datum',
                FormGenerics::KEY_REQUIRED => true,
                FormGenerics::KEY_WIDGET => 'single_text',
            ]);
    }
}
