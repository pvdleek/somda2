<?php

declare(strict_types=1);

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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferences extends AbstractType
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var UserPreference[] $all_settings
         */
        $all_settings = $options['all_settings'];
        /**
         * @var UserHelper $user_helper
         */
        $user_helper = $options['user_helper'];

        foreach ($all_settings as $setting) {
            if ($setting->order > 0) {
                $value = $user_helper->getPreferenceByKey($setting->key)->value;
                $type_part = \explode('|', $setting->type);
                switch ($type_part[0]) {
                    case 'number':
                        $builder->add($setting->key, ChoiceType::class, [
                            FormGenerics::KEY_CHOICES => \array_combine(
                                \range(1, (int) $type_part[1]),
                                \range(1, (int) $type_part[1])
                            ),
                            FormGenerics::KEY_DATA => (int) $value,
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
                            FormGenerics::KEY_DATA => (int) $value === 1,
                            FormGenerics::KEY_LABEL => $setting->description,
                            FormGenerics::KEY_MAPPED => false,
                            FormGenerics::KEY_REQUIRED => true,
                        ]);
                        break;
                    case 'table':
                        if ($type_part[1] !== 'location') {
                            throw new UnknownUserPreferenceTable(
                                'Unknown setting table '.$type_part[1].' for key '.$setting->key
                            );
                        }

                        $builder->add($setting->key, EntityType::class, [
                            FormGenerics::KEY_CHOICE_LABEL => function (Location $location) {
                                return $location->name.' - '.$location->description;
                            },
                            FormGenerics::KEY_CHOICE_VALUE => $type_part[2],
                            FormGenerics::KEY_CLASS => Location::class,
                            FormGenerics::KEY_DATA => $this->doctrine->getRepository(Location::class)->findOneBy(
                                ['name' => $value]
                            ),
                            FormGenerics::KEY_LABEL => $setting->description,
                            FormGenerics::KEY_MAPPED => false,
                            FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                                return $repository
                                    ->createQueryBuilder('l')
                                    ->andWhere('l.spot_allowed = TRUE')
                                    ->orderBy('l.name', 'ASC');
                            },
                            FormGenerics::KEY_REQUIRED => true,
                        ]);
                        break;
                    default:
                        throw new UnknownUserPreferenceType(
                            'Unknown setting type '.$type_part[0].' for key '.$setting->key
                        );
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEntity::class,
            'all_settings' => [],
            'user_helper' => null,
        ]);
    }
}
