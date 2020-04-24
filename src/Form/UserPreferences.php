<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\User as UserEntity;
use App\Entity\UserPreference;
use App\Exception\UnknownUserPreferenceTable;
use App\Exception\UnknownUserPreferenceType;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferences extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var UserPreference[] $allSettings
         * @var UserHelper $userHelper
         */
        $allSettings = $options['allSettings'];
        $userHelper = $options['userHelper'];

        foreach ($allSettings as $setting) {
            if ($setting->order > 0) {
                $value = $userHelper->getPreferenceByKey($setting->key)->value;
                $typePart = explode('|', $setting->type);
                switch($typePart[0]) {
                    case 'number':
                        $builder->add($setting->key, ChoiceType::class, [
                            'choices' => array_combine(range(1, (int)$typePart[1]), range(1, (int)$typePart[1])),
                            'data' => (int)$value,
                            'label' => $setting->description,
                            'mapped' => false,
                            'required' => true,
                        ]);
                        break;
                    case 'text':
                        $builder->add($setting->key, TextType::class, [
                            'data' => $value,
                            'label' => $setting->description,
                            'mapped' => false,
                            'required' => true,
                        ]);
                        break;
                    case 'boolean':
                        $builder->add($setting->key, CheckboxType::class, [
                            'data' => (int)$value === 1,
                            'label' => $setting->description,
                            'mapped' => false,
                            'required' => true,
                        ]);
                        break;
                    case 'table':
                        if ($typePart[1] !== 'location') {
                            throw new UnknownUserPreferenceTable(
                                'Unknown setting table ' . $typePart[1] . ' for key ' . $setting->key
                            );
                        }

                        $builder->add($setting->key, EntityType::class, [
                            'choice_label' => function (Location $location) {
                                return $location->name . ' - ' . $location->description;
                            },
                            'choice_value' => $typePart[2],
                            'class' => Location::class,
                            'data' => $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $value]),
                            'label' => $setting->description,
                            'mapped' => false,
                            'required' => true,
                        ]);
                        break;
                    default:
                        throw new UnknownUserPreferenceType(
                            'Unknown setting type ' . $typePart[0] . ' for key ' . $setting->key
                        );
                }
            }
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEntity::class,
            'allSettings' => [],
            'userHelper' => null,
        ]);
    }
}
