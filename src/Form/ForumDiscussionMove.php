<?php

namespace App\Form;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumDiscussionMove extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('forum', EntityType::class, [
            'choice_label' => function (ForumForum $forum) {
                return $forum->getCategory()->getName() . ' - ' . $forum->getName();
            },
            'class' => ForumForum::class,
            'label' => 'Kies een nieuw locatie',
            'query_builder' => function (EntityRepository $repository) {
                return $repository
                    ->createQueryBuilder('f')
                    ->join('f.category', 'c')
                    ->orderBy('c.order', 'ASC')
                    ->addOrderBy('f.order', 'ASC');
            },
            'required' => true,
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
