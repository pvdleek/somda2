<?php

namespace App\Form;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumDiscussionMove extends BaseForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('forum', EntityType::class, [
            self::KEY_CHOICE_LABEL => function (ForumForum $forum) {
                return $forum->category->name . ' - ' . $forum->name;
            },
            self::KEY_CLASS => ForumForum::class,
            self::KEY_LABEL => 'Kies een nieuw locatie',
            self::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                return $repository
                    ->createQueryBuilder('f')
                    ->join('f.category', 'c')
                    ->orderBy('c.order', 'ASC')
                    ->addOrderBy('f.order', 'ASC');
            },
            self::KEY_REQUIRED => true,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ForumDiscussion::class]);
    }
}
