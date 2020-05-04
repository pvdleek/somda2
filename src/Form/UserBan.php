<?php

namespace App\Form;

use App\Entity\User as UserEntity;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserBan extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('banExpireTimestamp', DateType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_CLASS=> 'ban-datepicker'],
                self::KEY_FORMAT=> 'dd-MM-yyyy',
                self::KEY_HTML5 => false,
                self::KEY_LABEL => 'Verloopdatum',
                self::KEY_REQUIRED => false,
                self::KEY_WIDGET => 'single_text',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserEntity::class]);
    }
}
