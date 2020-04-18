<?php

namespace App\Form;

use App\Entity\UserInfo as UserInfoEntity;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfo extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../public/images/avatar');
        $avatarList = [];
        foreach ($finder as $file) {
            $avatarList[strtolower($file->getFilenameWithoutExtension())] = $file->getRelativePathname();
        }
        asort($avatarList);

        $builder
            ->add('avatar', ChoiceType::class, [
                'choices' => $avatarList,
                'label' => 'Jouw avatar',
                'required' => true,
            ])
            ->add('birthDate', DateType::class, [
                'attr' => ['class' => 'birthday-picker'],
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'label' => 'Jouw geboortedatum',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('city', TextType::class, [
                'label' => 'Jouw woonplaats',
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Niet opgegeven' => UserInfoEntity::GENDER_UNKNOWN,
                    'Man' => UserInfoEntity::GENDER_MALE,
                    'Vrouw' => UserInfoEntity::GENDER_FEMALE,
                ],
                'label' => 'Jouw geslacht',
                'required' => true,
            ])
            ->add('info', TextType::class, [
                'label' => 'Jouw handtekening in het forum',
                'required' => false,
            ])
            ->add('mobilePhone', TextType::class, [
                'label' => 'Jouw mobiele nummer',
                'required' => false,
            ])
            ->add('skype', TextType::class, [
                'label' => 'Jouw skype account',
                'required' => false,
            ])
            ->add('website', TextType::class, [
                'label' => 'Jouw Skype account',
                'required' => false,
            ])
            ->add('facebookAccount', TextType::class, [
                'label' => 'Jouw Facebook account',
                'required' => false,
            ])
            ->add('flickrAccount', TextType::class, [
                'label' => 'Jouw Flickr account',
                'required' => false,
            ])
            ->add('twitterAccount', TextType::class, [
                'label' => 'Jouw Twitter account',
                'required' => false,
            ])
            ->add('youtubeAccount', TextType::class, [
                'label' => 'Jouw Youtube account',
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserInfoEntity::class]);
    }
}
