<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Location;
use App\Entity\LocationCategory;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLocationsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:update-locations';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Update the locations from the official list at NS');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://gateway.apiportal.ns.nl/reisinformatie-api/api/v2/stations');
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Ocp-Apim-Subscription-Key:' . $_ENV['NS_API_PRIMARY_KEY']]);

        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        /**
         * @var LocationCategory $notValidCategory
         */
        $notValidCategory = $this->doctrine->getRepository(LocationCategory::class)->find(
            LocationCategory::NO_LONGER_VALID_ID
        );

        foreach ($result['payload'] as $station) {
            $category = $this->doctrine->getRepository(LocationCategory::class)->findOneBy(
                ['code' => $station['land']]
            );
            if (is_null($category)) {
                $category = new LocationCategory();
                $category->code = $category->name = $station['land'];

                $this->doctrine->getManager()->persist($category);
            }

            $locationName = ucfirst(strtolower($station['code']));
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(
                ['name' => $locationName, 'category' => $category]
            );
            if (is_null($location)) {
                $location = new Location();
                $location->name = $locationName;
                $location->category = $category;

                $this->doctrine->getManager()->persist($location);
            }

            $location->latitude = $station['lat'];
            $location->longitude = $station['lng'];
            $location->description = $station['namen']['lang'];

            if (isset($station['eindDatum']) && new DateTime($station['eindDatum']) < new DateTime()) {
                $location->category = $notValidCategory;
            }

            $this->doctrine->getManager()->flush();
        }

        return 0;
    }
}
