<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Location;
use App\Helpers\TemplateHelper;
use App\Helpers\TrainTableHelper;
use App\Repository\TrainTableRepository;
use App\Repository\TrainTableYearRepository;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeedController
{
    use DateTrait;

    private const DEFAULT_LIMIT = 10;
    private const DEFAULT_BACKGROUND_COLOR = 'FFFFFF';
    private const DEFAULT_FOREGROUND_COLOR = '000000';

    private int $foreground_color = 0;

    private int $line_number = 1;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TranslatorInterface $translator,
        private readonly TemplateHelper $template_helper,
        private readonly TrainTableHelper $train_table_helper,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
    }

    public function indexAction(): Response
    {
        return $this->template_helper->render('somda/feeds.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Feeds',
        ]);
    }

    public function imageAction(Request $request, string $location_name, ?int $day_number = null, ?string $start_time = null)
    {
        \header('Content-Type: image/png');

        $limit = (int) $request->query->get('limit', self::DEFAULT_LIMIT);
        $image = \imagecreate(750, 15 * ($limit + 1));
        $background_color = $this->getColorAllocation(
            $image,
            $request->query->get('bg-color', self::DEFAULT_BACKGROUND_COLOR)
        );
        \imagefill($image, 0, 0, $background_color);
        $this->foreground_color = $this->getColorAllocation(
            $image,
            $request->query->get('fg-color', self::DEFAULT_FOREGROUND_COLOR)
        );

        /**
         * @var Location $location
         */
        $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $location_name]);
        if (null === $location) {
            $this->doText($image, 'Het opgegeven station '.$location_name.' is niet bekend in Somda');

            $temp_filename = (new Filesystem())->tempnam(\sys_get_temp_dir(), 'image_', '.png');
            \imagepng($image, $temp_filename);

            return new BinaryFileResponse($temp_filename, 200, ['Content-Type' => 'image/png']);
        }

        if (1 === $limit) {
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

        $passing_routes = $this->getPassingRoutes($location, $day_number, $start_time);
        foreach ($passing_routes as $passing_route) {
            $out = $this->timeDatabaseToDisplay($passing_route['time']).' - ';
            $out .= $this->translator->trans('general.action.'.$passing_route['action']).' trein ';
            $out .= $passing_route['route_number'].' ('.$passing_route['fl_first_description'].' - ';
            $out .= $passing_route['fl_last_description'].')';
            $out .= ' - '.$passing_route[TrainTableRepository::FIELD_TRANSPORTER_NAME].' ';
            $out .= $passing_route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION];
            $this->doText($image, $out);
        }

        $temp_filename = (new Filesystem())->tempnam(\sys_get_temp_dir(), 'image_', '.png');
        \imagepng($image, $temp_filename);

        return new BinaryFileResponse($temp_filename, 200, ['Content-Type' => 'image/png']);
    }

    private function getColorAllocation(\GdImage $id, string $color): int
    {
        if ($color[0] == '#') {
            $color = \substr($color, 1);
        }

        if (\strlen($color) === 6) {
            $red = (string) $color[0].$color[1];
            $green = (string) $color[2].$color[3];
            $blue = (string) $color[4].$color[5];
        } elseif (\strlen($color) === 3) {
            $red = (string) $color[0].$color[0];
            $green = (string) $color[1].$color[1];
            $blue = (string) $color[2].$color[2];
        } else {
            return imagecolorallocate($id, 255, 255, 255);
        }

        return imagecolorallocate($id, \hexdec($red), \hexdec($green), \hexdec($blue));
    }

    private function getPassingRoutes(Location $location, ?int $day_number, ?string $start_time): array
    {
        $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;
        $this->train_table_helper->setTrainTableYear($train_table_year_id);
        $this->train_table_helper->setLocation($location->name);

        return $this->train_table_helper->getPassingRoutes($day_number, $start_time);
    }

    private function doText(\GdImage $id, string $text): void
    {
        $text = \str_replace(
            ['&amp;', '&uuml;', '&ouml;', '&oslash;', '&egrave;', '&eacute;', '&euml;'],
            ['&', 'u', 'o', 'o', 'e', 'e', 'e'],
            $text
        );

        \imagestring($id, 2, 5, 15 * ($this->line_number - 1), $text, $this->foreground_color);
        ++$this->line_number;
    }
}
