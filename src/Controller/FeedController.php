<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\TrainTableYear;
use App\Helpers\TemplateHelper;
use App\Helpers\TrainTableHelper;
use App\Repository\TrainTable;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeedController
{
    use DateTrait;

    private const DEFAULT_LIMIT = 10;
    private const DEFAULT_BACKGROUND_COLOR = 'FFFFFF';
    private const DEFAULT_FOREGROUND_COLOR = '000000';

    private int $foregroundColor = 0;

    private int $lineNumber = 1;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TemplateHelper $templateHelper,
        private readonly TranslatorInterface $translator,
        private readonly TrainTableHelper $trainTableHelper,
    ) {
    }

    public function indexAction(): Response
    {
        return $this->templateHelper->render('somda/feeds.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Feeds',
        ]);
    }

    public function imageAction(Request $request, string $locationName, ?int $dayNumber = null, ?string $startTime = null)
    {
        \header('Content-Type: image/png');

        $limit = (int) $request->query->get('limit', self::DEFAULT_LIMIT);
        $image = \ImageCreate(750, 15 * ($limit + 1));
        $backgroundColor = $this->getColorAllocation(
            $image,
            $request->query->get('bg-color', self::DEFAULT_BACKGROUND_COLOR)
        );
        \ImageFill($image, 0, 0, $backgroundColor);
        $this->foregroundColor = $this->getColorAllocation(
            $image,
            $request->query->get('fg-color', self::DEFAULT_FOREGROUND_COLOR)
        );

        /**
         * @var Location $location
         */
        $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $locationName]);
        if (null === $location) {
            $this->doText($image, 'Het opgegeven station ' . $locationName . ' is niet bekend in Somda');
            return new Response(\imagepng($image), 200, ['Content-Type' => 'image/png']);
        }

        if ($limit === 1) {
            $this->doText(
                $image,
                \sprintf($this->translator->trans('passingRoutes.feedHeader.single'), $location->description)
            );
        } else {
            $this->doText(
                $image,
                \sprintf($this->translator->trans('passingRoutes.feedHeader.multiple'), $limit, $location->description)
            );
        }

        $passingRoutes = $this->getPassingRoutes($location, $dayNumber, $startTime);
        foreach ($passingRoutes as $passingRoute) {
            $out = $this->timeDatabaseToDisplay($passingRoute['time']) . ' - ';
            $out .= $this->translator->trans('general.action.' . $passingRoute['action']) . ' trein ';
            $out .= $passingRoute['route_number'] . ' (' . $passingRoute['fl_first_description'] . ' - ';
            $out .= $passingRoute['fl_last_description'] . ')';
            $out .= ' - ' . $passingRoute[TrainTable::FIELD_TRANSPORTER_NAME] . ' ';
            $out .= $passingRoute[TrainTable::FIELD_CHARACTERISTIC_DESCRIPTION];
            $this->doText($image, $out);
        }

        return new Response(imagepng($image), 200, ['Content-Type' => 'image/png']);
    }

    private function getColorAllocation(\GdImage $id, string $color): int
    {
        if ($color[0] == '#') {
            $color = \substr($color, 1);
        }

        if (\strlen($color) === 6) {
            list($red, $green, $blue) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) === 3) {
            list($red, $green, $blue) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return ImageColorAllocate($id, 255, 255, 255);
        }

        return ImageColorAllocate($id, \hexdec($red), \hexdec($green), \hexdec($blue));
    }

    private function getPassingRoutes(Location $location, ?int $dayNumber, ?string $startTime): array
    {
        $trainTableYearId = $this->doctrine
            ->getRepository(TrainTableYear::class)
            ->findTrainTableYearByDate(new \DateTime())
            ->id;
        $this->trainTableHelper->setTrainTableYear($trainTableYearId);
        $this->trainTableHelper->setLocation($location->name);

        return $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime);
    }

    private function doText(\GdImage $id, string $text): void
    {
        $text = \str_replace(
            ['&amp;', '&uuml;', '&ouml;', '&oslash;', '&egrave;', '&eacute;', '&euml;'],
            ['&', 'u', 'o', 'o', 'e', 'e', 'e'],
            $text
        );

        \ImageString($id, 2, 5, 15 * ($this->lineNumber - 1), $text, $this->foregroundColor);
        $this->lineNumber += 1;
    }
}
