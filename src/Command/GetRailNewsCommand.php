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
    private const CASE_SENSITIVE = 'caseSensitive';
    private const DESCRIPTION = 'description';
    private const POSITIVE_WORD = 'positiveWord';
    private const NEGATIVE_WORD = 'negativeWord';

    private static $wordMatches = [
        [
            self::CASE_SENSITIVE => false,
            self::DESCRIPTION => true,
            self::POSITIVE_WORD => 'spoor',
            self::NEGATIVE_WORD => 'opgespoord'
        ],
        [
            self::CASE_SENSITIVE => false,
            self::DESCRIPTION => true,
            self::POSITIVE_WORD => 'rail',
            self::NEGATIVE_WORD => 'vangrail'
        ],
        [
            self::CASE_SENSITIVE => false,
            self::DESCRIPTION => true,
            self::POSITIVE_WORD => 'station',
            self::NEGATIVE_WORD => 'tankstation'
        ],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'trein'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'machinist'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'conducteur'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'chipkaart'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'hispeed'],
        [self::CASE_SENSITIVE => true, self::DESCRIPTION => true, self::POSITIVE_WORD => 'HSL'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'fyra'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'syntus'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'noordned'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'veolia'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'acts'],
        [self::CASE_SENSITIVE => false, self::DESCRIPTION => true, self::POSITIVE_WORD => 'railion'],
        [
            self::CASE_SENSITIVE => true,
            self::DESCRIPTION => false,
            self::POSITIVE_WORD => 'NS',
            self::NEGATIVE_WORD => 'SNS'
        ],
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
            if ($wordMatch[self::DESCRIPTION]) {
                if ($wordMatch[self::CASE_SENSITIVE]) {
                    $positiveMatch = strpos($item->getTitle(), $wordMatch[self::POSITIVE_WORD]) !== false
                        || strpos($item->getDescription(), $wordMatch[self::POSITIVE_WORD]) !== false;
                } else {
                    $positiveMatch = stripos($item->getTitle(), $wordMatch[self::POSITIVE_WORD]) !== false
                        || stripos($item->getDescription(), $wordMatch[self::POSITIVE_WORD]) !== false;
                }
            } else {
                if ($wordMatch[self::CASE_SENSITIVE]) {
                    $positiveMatch = strpos($item->getTitle(), $wordMatch[self::POSITIVE_WORD]) !== false;
                } else {
                    $positiveMatch = stripos($item->getTitle(), $wordMatch[self::POSITIVE_WORD]) !== false;
                }
            }

            if ($positiveMatch) {
                if (isset($wordMatch[self::NEGATIVE_WORD])) {
                    if (stripos($item->getTitle(), $wordMatch[self::NEGATIVE_WORD]) === false
                        && stripos($item->getDescription(), $wordMatch[self::NEGATIVE_WORD]) === false
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
