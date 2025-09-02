<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Generics\FormGenerics;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumDiscussionMove extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('forum', EntityType::class, [
            FormGenerics::KEY_CHOICE_LABEL => function (ForumForum $forum) {
                return $forum->category->name.' - '.$forum->name;
            },
            FormGenerics::KEY_CLASS => ForumForum::class,
            FormGenerics::KEY_LABEL => 'Kies een nieuw locatie',
            FormGenerics::KEY_QUERY_BUILDER => function (EntityRepository $repository) {
                return $repository
                    ->createQueryBuilder('f')
                    ->join('f.category', 'c')
                    ->orderBy('c.order', 'ASC')
                    ->addOrderBy('f.order', 'ASC');
            },
            FormGenerics::KEY_REQUIRED => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ForumDiscussion::class]);
    }
}
