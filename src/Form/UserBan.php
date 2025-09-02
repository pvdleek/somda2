<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User as UserEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserBan extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ban_expire_timestamp', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'ban-datepicker'],
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Verloopdatum',
                FormGenerics::KEY_REQUIRED => false,
                FormGenerics::KEY_WIDGET => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserEntity::class]);
    }
}
