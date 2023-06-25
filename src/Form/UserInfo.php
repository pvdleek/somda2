<?php

namespace App\Form;

use App\Entity\UserInfo as UserInfoEntity;
use App\Generics\FormGenerics;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfo extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', ChoiceType::class, [
                FormGenerics::KEY_CHOICES => $this->getAvatars(),
                FormGenerics::KEY_LABEL => 'Jouw avatar',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('birthDate', DateType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_CLASS=> 'birthday-picker'],
                FormGenerics::KEY_FORMAT=> 'dd-MM-yyyy',
                FormGenerics::KEY_HTML5 => false,
                FormGenerics::KEY_LABEL => 'Jouw geboortedatum',
                FormGenerics::KEY_REQUIRED => false,
                FormGenerics::KEY_WIDGET => 'single_text',
            ])
            ->add('city', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw woonplaats',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('gender', ChoiceType::class, [
                FormGenerics::KEY_CHOICES => [
                    'Niet opgegeven' => UserInfoEntity::GENDER_UNKNOWN,
                    'Man' => UserInfoEntity::GENDER_MALE,
                    'Vrouw' => UserInfoEntity::GENDER_FEMALE,
                ],
                FormGenerics::KEY_LABEL => 'Jouw geslacht',
                FormGenerics::KEY_REQUIRED => true,
            ])
            ->add('mobilePhone', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw mobiele nummer',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('skype', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw skype account',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('website', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw website',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('facebookAccount', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw Facebook account',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('flickrAccount', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw Flickr account',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('twitterAccount', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw Twitter account',
                FormGenerics::KEY_REQUIRED => false,
            ])
            ->add('youtubeAccount', TextType::class, [
                FormGenerics::KEY_LABEL => 'Jouw Youtube account',
                FormGenerics::KEY_REQUIRED => false,
            ]);
    }

    private function getAvatars(): array
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../public/images/avatar');
        $avatarList = [];
        foreach ($finder as $file) {
            $avatarList[\strtolower($file->getFilenameWithoutExtension())] = $file->getRelativePathname();
        }
        \asort($avatarList);
        return $avatarList;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserInfoEntity::class]);
    }
}
