<?php

namespace App\Command;

use App\Entity\RailNews;
use App\Entity\RailNewsSourceFeed;
use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use FeedIo\Feed\ItemInterface;
use FeedIo\FeedIo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetRailNewsCommand extends Command implements ScheduledJobInterface
{
    private static $wordMatches = [
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'spoor', 'negativeWord' => 'opgespoord'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'rail', 'negativeWord' => 'vangrail'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'station', 'negativeWord' => 'tankstation'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'trein'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'machinist'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'conducteur'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'chipkaart'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'hispeed'],
        ['caseSensitive' => true, 'description' => true, 'positiveWord' => 'HSL'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'fyra'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'syntus'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'noordned'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'veolia'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'acts'],
        ['caseSensitive' => false, 'description' => true, 'positiveWord' => 'railion'],
        ['caseSensitive' => true, 'description' => false, 'positiveWord' => 'NS', 'negativeWord' => 'SNS'],
    ];

    /**
     * @var string
     */
    protected static $defaultName = 'app:get-rail-news';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FeedIo
     */
    private FeedIo $feedIo;

    /**
     * @param ManagerRegistry $doctrine
     * @param FeedIo $feedIo
     */
    public function __construct(ManagerRegistry $doctrine, FeedIo $feedIo)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
        $this->feedIo = $feedIo;
    }

    /**
     *
     */
    public function __invoke()
    {
        $this->execute();
    }

    /**
     * @return string
     */
    public function getSchedulerExpresion(): string
    {
        return '14,29,44,59 * * * *';
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Read all the rail-news providers and process the news items');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var RailNewsSourceFeed[] $feeds
         */
        $feeds = $this->doctrine->getRepository(RailNewsSourceFeed::class)->findAll();
        foreach ($feeds as $feed) {
            /**
             * @var ItemInterface[] $items
             */
            $items = $this->feedIo->read($feed->url, null, new DateTime('-1 day'))->getFeed();
            foreach ($items as $item) {
                if (!$feed->filterResults || $this->isArticleMatch($item)) {
                    $description = trim(strip_tags(str_replace('?', '', $item->getDescription())));
                    $description = preg_replace('/\s\s+/', ' ', $description);

                    /**
                     * @var RailNews $railNews
                     */
                    $railNews = $this->doctrine->getRepository(RailNews::class)->findOneBy(['url' => $item->getLink()]);
                    if (is_null($railNews)) {
                        $railNews = new RailNews();
                        $railNews->title = $item->getTitle();
                        $railNews->url = $item->getLink();
                        $railNews->introduction = $description;
                        $railNews->timestamp = $item->getLastModified() ?? new DateTime();
                        $railNews->approved = false;
                        $railNews->active = false;
                        $railNews->automaticUpdates = true;
                        $railNews->source = $feed->source;

                        $this->doctrine->getManager()->persist($railNews);
                    } elseif ($railNews->automaticUpdates) {
                        $railNews->title = $item->getTitle();
                        $railNews->url = $item->getLink();
                        $railNews->introduction = $description;
                        $railNews->timestamp = $item->getLastModified() ?? new DateTime();
                    }
                }
            }
            $this->doctrine->getManager()->flush();
        }

        return 0;
    }

    /**
     * @param ItemInterface $item
     * @return bool
     */
    private function isArticleMatch(ItemInterface $item): bool
    {
        foreach (self::$wordMatches as $wordMatch) {
            if ($wordMatch['description']) {
                if ($wordMatch['caseSensitive']) {
                    $positiveMatch = strpos($item->getTitle(), $wordMatch['positiveWord']) !== false
                        || strpos($item->getDescription(), $wordMatch['positiveWord']) !== false;
                } else {
                    $positiveMatch = stripos($item->getTitle(), $wordMatch['positiveWord']) !== false
                        || stripos($item->getDescription(), $wordMatch['positiveWord']) !== false;
                }
            } else {
                if ($wordMatch['caseSensitive']) {
                    $positiveMatch = strpos($item->getTitle(), $wordMatch['positiveWord']) !== false;
                } else {
                    $positiveMatch = stripos($item->getTitle(), $wordMatch['positiveWord']) !== false;
                }
            }

            if ($positiveMatch) {
                if (isset($wordMatch['negativeWord'])) {
                    if (stripos($item->getTitle(), $wordMatch['negativeWord']) === false
                        && stripos($item->getDescription(), $wordMatch['negativeWord']) === false
                    ) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }

        return false;
    }
}
