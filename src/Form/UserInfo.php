<?php

namespace App\Form;

use App\Entity\UserInfo as UserInfoEntity;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfo extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', ChoiceType::class, [
                self::KEY_CHOICES => $this->getAvatars(),
                self::KEY_LABEL => 'Jouw avatar',
                self::KEY_REQUIRED => true,
            ])
            ->add('birthDate', DateType::class, [
                self::KEY_ATTRIBUTES => [self::KEY_CLASS=> 'birthday-picker'],
                self::KEY_FORMAT=> 'dd-MM-yyyy',
                self::KEY_HTML5 => false,
                self::KEY_LABEL => 'Jouw geboortedatum',
                self::KEY_REQUIRED => false,
                self::KEY_WIDGET => 'single_text',
            ])
            ->add('city', TextType::class, [
                self::KEY_LABEL => 'Jouw woonplaats',
                self::KEY_REQUIRED => false,
            ])
            ->add('gender', ChoiceType::class, [
                self::KEY_CHOICES => [
                    'Niet opgegeven' => UserInfoEntity::GENDER_UNKNOWN,
                    'Man' => UserInfoEntity::GENDER_MALE,
                    'Vrouw' => UserInfoEntity::GENDER_FEMALE,
                ],
                self::KEY_LABEL => 'Jouw geslacht',
                self::KEY_REQUIRED => true,
            ])
            ->add('mobilePhone', TextType::class, [
                self::KEY_LABEL => 'Jouw mobiele nummer',
                self::KEY_REQUIRED => false,
            ])
            ->add('skype', TextType::class, [
                self::KEY_LABEL => 'Jouw skype account',
                self::KEY_REQUIRED => false,
            ])
            ->add('website', TextType::class, [
                self::KEY_LABEL => 'Jouw Skype account',
                self::KEY_REQUIRED => false,
            ])
            ->add('facebookAccount', TextType::class, [
                self::KEY_LABEL => 'Jouw Facebook account',
                self::KEY_REQUIRED => false,
            ])
            ->add('flickrAccount', TextType::class, [
                self::KEY_LABEL => 'Jouw Flickr account',
                self::KEY_REQUIRED => false,
            ])
            ->add('twitterAccount', TextType::class, [
                self::KEY_LABEL => 'Jouw Twitter account',
                self::KEY_REQUIRED => false,
            ])
            ->add('youtubeAccount', TextType::class, [
                self::KEY_LABEL => 'Jouw Youtube account',
                self::KEY_REQUIRED => false,
            ]);
    }

    /**
     * @return array
     */
    private function getAvatars(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../public/images/avatar');
        $avatarList = [];
        foreach ($finder as $file) {
            $avatarList[strtolower($file->getFilenameWithoutExtension())] = $file->getRelativePathname();
        }
        asort($avatarList);
        return $avatarList;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserInfoEntity::class]);
    }
}
