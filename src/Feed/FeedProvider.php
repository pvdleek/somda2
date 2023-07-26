<?php

namespace App\Feed;

use App\Entity\Spot;
use App\Generics\DateGenerics;
use App\Helpers\SpotHelper;
use App\Helpers\UserHelper;
use App\Repository\SpecialRoute as RepositorySpecialRoute;
use App\Repository\Spot as RepositorySpot;
use App\Repository\Train as RepositoryTrain;
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

    private ?string $trainFilter = null;

    public function __construct(
        private readonly UserHelper $userHelper,
        private readonly SpotHelper $spotHelper,
        private readonly RouterInterface $router,
        private readonly RepositorySpecialRoute $repositorySpecialRoute,
        private readonly RepositorySpot $repositorySpot,
        private readonly RepositoryTrain $repositoryTrain,
    ) {
    }

    /**
     * @throws FeedNotFoundException
     */
    public function getFeed(Request $request) : FeedInterface
    {
        $feedType = $request->attributes->get('id');

        $this->limit = (int) $request->query->get('limit', 10);
        $this->trainFilter = $request->query->get('train');

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
                    '<spotDate>' . $spot->spotDate->format('Y-m-d') . '</spotDate>' .
                    '<trainNumber>' . $spot->train->number . '</trainNumber>' .
                    '<trainName>' .
                        ($spot->train->namePattern ? $spot->train->namePattern->name : 'unknown') .
                    '</trainName>' .
                    '<locationAbbreviation>' . $spot->location->name . '</locationAbbreviation>' .
                    '<locationDescription>' . $spot->location->description . '</locationDescription>' .
                    '<routeNumber>' . $spot->route->number . '</routeNumber>' .
                    '<position>' . $spot->position->name . '</position>' .
                    ']]>'
                )
                ->setTitle($this->spotHelper->getDisplaySpot($spot, true))
                ->setPublicId($this->router->generate(
                    'spots_search',
                    ['maxMonths' => 12, 'searchParameters' => '/0/' . $spot->train->number . '/'],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLink($this->router->generate(
                    'spots_search',
                    ['maxMonths' => 12, 'searchParameters' => '/0//' . $spot->route->number],
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
        if (\is_null($this->trainFilter)) {
            return $this->repositorySpot->findBy([], ['timestamp' => 'DESC'], $this->limit);
        }

        $train = $this->repositoryTrain->findOneBy(['number' => $this->trainFilter]);
        if (!\is_null($train)) {
            return $this->repositorySpot->findBy(['train' => $train], ['timestamp' => 'DESC'], $this->limit);
        }

        return [];
    }

    private function getSpecialRouteItems(): \Generator
    {
        $specialRoutes = $this->repositorySpecialRoute->findForFeed($this->limit);
        foreach ($specialRoutes as $specialRoute) {
            $author = new ItemAuthor($this->userHelper->getAdministratorUser());

            $date = \is_null($specialRoute->endDate) ? $specialRoute->startDate->format(DateGenerics::DATE_FORMAT) :
                $specialRoute->startDate->format(DateGenerics::DATE_FORMAT) . ' t/m ' .
                $specialRoute->endDate->format(DateGenerics::DATE_FORMAT);

            $item = new Item;
            $item
                ->setSummary('<![CDATA[' . $specialRoute->text . ']]>')
                ->setTitle($specialRoute->title . ' - ' . $date)
                ->setPublicId($this->router->generate(
                    'special_route',
                    ['id' => $specialRoute->id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLink($this->router->generate(
                    'special_route',
                    ['id' => $specialRoute->id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ))
                ->setLastModified($specialRoute->publicationTimestamp)
                ->setAuthor($author);

            yield $item;
        }
    }
}
