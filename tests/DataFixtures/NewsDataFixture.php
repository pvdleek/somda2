<?php

namespace App\Tests\DataFixtures;

use App\Entity\News;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NewsDataFixture implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return News[]
     */
    public function load(ObjectManager $manager): array
    {
        $news1 = new News();
        $news1->title = 'News item 1';
        $news1->timestamp = new DateTime('-253 hours');
        $manager->persist($news1);

        $news2 = new News();
        $news2->title = 'News item 2';
        $news2->timestamp = new DateTime('-34 days');
        $news2->archived = true;
        $manager->persist($news2);

        $news3 = new News();
        $news3->title = 'News item 3';
        $news3->timestamp = new DateTime('-28 days');
        $news3->archived = false;
        $manager->persist($news3);

        $manager->flush();

        return [$news1, $news2, $news3];
    }
}
