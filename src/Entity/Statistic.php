<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StatisticRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: StatisticRepository::class)]
#[ORM\Table(name: 'somda_stats')]
#[ORM\UniqueConstraint(name: 'unq_somda_stats__datum', fields: ['datum'])]
#[UniqueEntity(fields: ['datum'])]
class Statistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'datum', type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp = null;

    #[ORM\Column(name: 'uniek', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $visitors_unique = 0;

    #[ORM\Column(name: 'pageviews', type: 'integer', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $visitors_total = 0;

    #[ORM\Column(name: 'pageviews_home', type: 'integer', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $visitors_home = 0;

    #[ORM\Column(name: 'pageviews_func', type: 'integer', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $visitors_functions = 0;

    #[ORM\Column(name: 'spots', type: 'integer', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $number_of_spots = 0;

    #[ORM\Column(name: 'posts', type: 'integer', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $number_of_posts = 0;
}
