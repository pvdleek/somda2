<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\User as UserEntity;
use App\Entity\UserPreference;
use App\Exception\UnknownUserPreferenceTable;
use App\Exception\UnknownUserPreferenceType;
use App\Generics\FormGenerics;
use App\Helpers\UserHelper;
use Doctrine\ORM\EntityRepository;
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
    private ManagerRegistry $doctrine;

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
                switch ($typePart[0]) {
                    case 'number':
                        $builder->add($setting->key, ChoiceType::class, [
                            FormGenerics::KEY_CHOICES => array_combine(
                                range(1, (int)$typePart[1]),
                                range(1, (int)$typePart[1])
                            ),
                            FormGenerics::KEY_DATA => (int)$value,
                            FormGenerics::KEY_LABEL => $setting->description,
                            FormGenerics::KEY_MAPPED => false,
                            FormGenerics::KEY_REQUIRED => true,
                        ]);
                        break;
                    case 'text':
                        $builder->add($setting->key, TextType::class, [
                            FormGenerics::KEY_DATA => $value,
                            FormGenerics::KEY_LABEL => $setting->description,
                            FormGenerics::KEY_MAPPED => false,
                            FormGenerics::KEY_REQUIRED => true,
                        ]);
                        break;
                    case 'boolean':
                        $builder->add($setting->key, CheckboxType::class, [
                            FormGenerics::KEY_DATA => (int)$value === 1,
                            FormGenerics::KEY_LABEL => $setting->description,
                            FormGenerics::KEY_MAPPED => false,
                            FormGenerics::KEY_REQUIRED => true,
                        ]);
                        break;
                    case 'table':
                        if ($typePart[1] !== 'location') {
                            throw new UnknownUserPreferenceTable(
                                'Unknown setting table ' . $typePart[1] . ' for key ' . $setting->key
                            );
                        }

                        $builder->add($setting->key, EntityType::class, [
                            FormGenerics::KEY_CHOICE_LABEL => function (Location $location) {
                                return $location->name . ' - ' . $location->description;
                            },
                            FormGenerics::KEY_CHOICE_VALUE => $typePart[2],
                            FormGenerics::KEY_CLASS => Location::class,
                            FormGenerics::KEY_DATA => $this->doctrine->getRepository(Location::class)->findOneBy(
                                ['name' => $value]
                            ),
                            FormGenerics::KEY_LABEL => $setting->description,
                            FormGenerics::KEY_MAPPED => false,
                            FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                                return $repository
                                    ->createQueryBuilder('l')
                                    ->andWhere('l.spotAllowed = TRUE')
                                    ->orderBy('l.name', 'ASC');
                            },
                            FormGenerics::KEY_REQUIRED => true,
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
