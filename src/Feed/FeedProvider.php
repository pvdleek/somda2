<?php

namespace App\Feed;

use App\Entity\SpecialRoute;
use App\Entity\Spot;
use App\Entity\Train;
use App\Generics\DateGenerics;
use App\Helpers\SpotHelper;
use App\Helpers\UserHelper;
use DateTime;
use Debril\RssAtomBundle\Exception\FeedException\FeedNotFoundException;
use Debril\RssAtomBundle\Provider\FeedProviderInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use FeedIo\Feed;
use FeedIo\FeedInterface;
use FeedIo\Feed\Item;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class FeedProvider implements FeedProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var SpotHelper
     */
    private SpotHelper $spotHelper;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var int
     */
    private int $limit = 10;

    /**
     * @var string|null
     */
    private ?string $trainFilter = null;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param SpotHelper $spotHelper
     * @param RouterInterface $router
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        SpotHelper $spotHelper,
        RouterInterface $router
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->spotHelper = $spotHelper;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return FeedInterface
     * @throws FeedNotFoundException
     */
    public function getFeed(Request $request) : FeedInterface
    {
        $feedType = $request->attributes->get('id');

        $this->limit = (int)$request->query->get('limit', 10);
        $this->trainFilter = $request->query->get('train', null);

        $feed = new Feed();
        $feed
            ->setTitle($feedType === 'spots' ? 'Somda spots' : 'Somda bijzondere ritten')
            ->setDescription('RSS feed van Somda')
            ->setLanguage('nl')
            ->setPublicId($this->router->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->setLastModified(new DateTime());

        $items = $feedType === 'spots' ? $this->getSpotItems() : $this->getSpecialRouteItems();
        foreach ($items as $item) {
            $feed->add($item);
        }

        return $feed;
    }

    /**
     * @return Generator
     */
    private function getSpotItems(): Generator
    {
        foreach ($this->getSpots() as $spot) {
            $author = new ItemAuthor($spot->user);

            $item = new Item;
            $item
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
                ->setDescription(
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
                ->setAuthor($author);

            yield $item;
        }
    }

    /**
     * @return Spot[]
     */
    private function getSpots(): array
    {
        if (is_null($this->trainFilter)) {
            return $this->doctrine->getRepository(Spot::class)->findBy([], ['timestamp' => 'DESC'], $this->limit);
        }

        $train = $this->doctrine->getRepository(Train::class)->findOneBy(['number' => $this->trainFilter]);
        if (!is_null($train)) {
            return $this->doctrine
                ->getRepository(Spot::class)
                ->findBy(['train' => $train], ['timestamp' => 'DESC'], $this->limit);
        }

        return [];
    }

    /**
     * @return Generator
     */
    private function getSpecialRouteItems(): Generator
    {
        /**
         * @var SpecialRoute[] $specialRoutes
         */
        $specialRoutes = $this->doctrine->getRepository(SpecialRoute::class)->findForFeed($this->limit);
        foreach ($specialRoutes as $specialRoute) {
            $author = new ItemAuthor($this->userHelper->getAdministratorUser());

            $date = is_null($specialRoute->endDate) ? $specialRoute->startDate->format(DateGenerics::DATE_FORMAT) :
                $specialRoute->startDate->format(DateGenerics::DATE_FORMAT) . ' t/m ' .
                $specialRoute->endDate->format(DateGenerics::DATE_FORMAT);

            $item = new Item;
            $item
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
                ->setDescription('<![CDATA[' . $specialRoute->text . ']]>')
                ->setAuthor($author);

            yield $item;
        }
    }
}
