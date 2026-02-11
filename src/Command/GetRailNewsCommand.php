<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\RailNews;
use App\Entity\RailNewsSource;
use App\Entity\RailNewsSourceFeed;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:get-rail-news',
    description: 'Read all the rail-news providers and process the news items',
    hidden: false,
)]

class GetRailNewsCommand extends Command
{
    private const TITLE_ONLY = 'titleOnly';
    private const POSITIVE_WORD = 'positiveWord';
    private const NEGATIVE_WORD = 'negativeWord';

    private static array $word_matches = [
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' spoor ', self::NEGATIVE_WORD => 'opgespoord'],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' rail ', self::NEGATIVE_WORD => 'vangrail'],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' station ', self::NEGATIVE_WORD => 'tankstation'],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' rails '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' trein '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' machinist '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' conducteur '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' chipkaart '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' ov-chipkaart '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' hispeed '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' HSL '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' fyra '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' syntus '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' noordned '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' veolia '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' acts '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' railion '],
        [self::TITLE_ONLY => true, self::POSITIVE_WORD => ' NS ', self::NEGATIVE_WORD => 'SNS'],
    ];

    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = \stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        \libxml_set_streams_context($context);
        \libxml_use_internal_errors(true);

        /** @var RailNewsSourceFeed[] $feeds */
        $feeds = $this->doctrine->getRepository(RailNewsSourceFeed::class)->findAll();
        foreach ($feeds as $feed) {
            $rss = \simplexml_load_file($feed->url);
            $errors = \libxml_get_errors();
            if (false === $rss || !empty($errors)) {
                foreach (\libxml_get_errors() as $error) {
                    $output->writeln('  Could not load feed: '.$error->message);
                }
                continue;
            }
            \libxml_clear_errors();

            foreach ($rss->channel->item as $item) {
                if (!$feed->filter_results || $this->isArticleMatch($item)) {
                    $this->saveItem($feed->source, $item, $this->getItemDescription($item));
                }
            }
            $this->doctrine->getManager()->flush();
        }

        return 0;
    }

    private function isArticleMatch(\SimpleXMLElement $item): bool
    {
        // Disapprove news-items in the future
        if (isset($item->pubDate) && new \DateTime($item->pubDate->__toString()) > new \DateTime()) {
            return false;
        }

        foreach (self::$word_matches as $word_match) {
            if ($this->isWordMatch($word_match, $item)) {
                if (isset($word_match[self::NEGATIVE_WORD])) {
                    if (\stripos($item->title->__toString(), $word_match[self::NEGATIVE_WORD]) === false
                        && \stripos($item->description->__toString(), $word_match[self::NEGATIVE_WORD]) === false
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

    private function isWordMatch(array $word_match, \SimpleXMLElement $item): bool
    {
        if (null === $item->title || null === $item->description) {
            return false;
        }

        if ($word_match[self::TITLE_ONLY]) {
            return \stripos($item->title->__toString(), $word_match[self::POSITIVE_WORD]) !== false;
        }

        return \stripos($item->title->__toString(), $word_match[self::POSITIVE_WORD]) !== false
            || \stripos($item->description->__toString(), $word_match[self::POSITIVE_WORD]) !== false;
    }

    private function getItemDescription(\SimpleXMLElement $item): string
    {
        $description = \trim(\strip_tags(\str_replace('?', '', $item->description->__toString())));
        $description = \preg_replace('/\s\s+/', ' ', $description);
        if (\strlen($description) < 1) {
            return $item->title->__toString();
        }

        return $description;
    }

    private function saveItem(RailNewsSource $source, \SimpleXMLElement $item, string $description): void
    {
        /** @var RailNews|null $rail_news */
        $rail_news = $this->doctrine->getRepository(RailNews::class)->findOneBy(['url' => $item->link->__toString()]);
        if (null === $rail_news) {
            $rail_news = new RailNews();
            $rail_news->title = $item->title->__toString();
            $rail_news->url = $item->link->__toString();
            $rail_news->introduction = \html_entity_decode($description, ENT_NOQUOTES, 'UTF-8');
            $rail_news->timestamp = isset($item->pubDate) ? new \DateTime($item->pubDate->__toString()) : new \DateTime();
            $rail_news->approved = false;
            $rail_news->active = false;
            $rail_news->automatic_updates = true;
            $rail_news->source = $source;

            $this->doctrine->getManager()->persist($rail_news);
        } elseif ($rail_news->automatic_updates) {
            $rail_news->title = $item->title->__toString();
            $rail_news->url = $item->link->__toString();
            $rail_news->introduction = \html_entity_decode($description, ENT_NOQUOTES, 'UTF-8');
            $rail_news->timestamp = isset($item->pubDate) ? new \DateTime($item->pubDate->__toString()) : new \DateTime();
        }
    }
}
