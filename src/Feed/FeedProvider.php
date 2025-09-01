<?php

namespace App\Feed;

use App\Entity\Spot;
use App\Generics\DateGenerics;
use App\Helpers\SpotHelper;
use App\Helpers\UserHelper;
use App\Repository\SpecialRouteRepository;
use App\Repository\SpotRepository;
use App\Repository\TrainRepository;
use Debril\RssAtomBundle\Exception\FeedException\FeedNotFoundException;
use Debril\RssAtomBundle\Provider\FeedProviderInterface;
use FeedIo\Feed;
use FeedIo\FeedInterface;
use FeedIo\Feed\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class FeedProvider implements FeedProviderInterface
{
    private int $limit = 10;

    private ?string $train_filter = null;

    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly SpotHelper $spot_helper,
        private readonly RouterInterface $router,
        private readonly SpecialRouteRepository $special_route_repository,
        private readonly SpotRepository $spot_repository,
        private readonly TrainRepository $train_repository,
    ) {
    }

    /**
     * @throws FeedNotFoundException
     */
    public function getFeed(Request $request) : FeedInterface
    {
        $feedType = $request->attributes->get('id');

        $this->limit = (int) $request->query->get('limit', 10);
        $this->train_filter = $request->query->get('train');

        $feed = new Feed();
        $feed
            ->setDescription('RSS feed van Somda')
            ->setLanguage('nl')
            ->setTitle($feedType === 'spots' ? 'Somda spots' : 'Somda bijzondere ritten')
            ->setPublicId($this->router->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setLastModified(new \DateTime());

        $items = $feedType === 'spots' ? $this->getSpotItems() : $this->getSpecialRouteItems();
        foreach ($items as $item) {
            $feed->add($item);
        }

        return $feed;
    }

    private function getSpotItems(): \Generator
    {
        foreach ($this->getSpots() as $spot) {
            $author = new ItemAuthor($spot->user);

            $item = new Item;
            $item
                ->setSummary(
                    '<![CDATA[' .
                    '<spotDate>' . $spot->spot_date->format('Y-m-d') . '</spotDate>' .
                    '<trainNumber>' . $spot->train->number . '</trainNumber>' .
                    '<trainName>' .
                        ($spot->train->name_pattern ? $spot->train->name_pattern->name : 'unknown') .
                    '</trainName>' .
                    '<locationAbbreviation>' . $spot->location->name . '</locationAbbreviation>' .
                    '<locationDescription>' . $spot->location->description . '</locationDescription>' .
                    '<routeNumber>' . $spot->route->number . '</routeNumber>' .
                    '<position>' . $spot->position->name . '</position>' .
                    ']]>'
                )
                ->setTitle($this->spot_helper->getDisplaySpot($spot, true))
                ->setPublicId($this->router->generate(
                    'spots_search',
                    ['max_months' => 1, 'search_parameters' => '/0//' . $spot->train->number . '/'],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLink($this->router->generate(
                    'spots_search',
                    ['max_months' => 1, 'search_parameters' => '/0///' . $spot->route->number],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLastModified($spot->timestamp)
                ->setAuthor($author);

            yield $item;
        }
    }

    /**
     * @return Spot[]
     */
    private function getSpots(): array
    {
        if (null === $this->train_filter) {
            return $this->spot_repository->findBy([], ['timestamp' => 'DESC'], $this->limit);
        }

        $train = $this->train_repository->findOneBy(['number' => $this->train_filter]);
        if (null !== $train) {
            return $this->spot_repository->findBy(['train' => $train], ['timestamp' => 'DESC'], $this->limit);
        }

        return [];
    }

    private function getSpecialRouteItems(): \Generator
    {
        foreach ($this->special_route_repository->findForFeed($this->limit) as $special_route) {
            $author = new ItemAuthor($this->user_helper->getAdministratorUser());

            $date = null === $special_route->end_date ? $special_route->start_date->format(DateGenerics::DATE_FORMAT) :
                $special_route->start_date->format(DateGenerics::DATE_FORMAT) . ' t/m ' .
                $special_route->end_date->format(DateGenerics::DATE_FORMAT);

            $item = new Item;
            $item
                ->setSummary('<![CDATA[' . $special_route->text . ']]>')
                ->setTitle($special_route->title . ' - ' . $date)
                ->setPublicId($this->router->generate(
                    'special_route',
                    ['id' => $special_route->id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLink($this->router->generate(
                    'special_route',
                    ['id' => $special_route->id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLastModified($special_route->publication_timestamp)
                ->setAuthor($author);

            yield $item;
        }
    }
}
