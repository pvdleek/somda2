<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Location;
use App\Entity\LocationCategory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-locations',
    description: 'Update the locations from the official list at NS',
    hidden: false,
)]

class UpdateLocationsCommand extends Command
{
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
        $curl = \curl_init();

        \curl_setopt($curl, CURLOPT_URL, 'https://gateway.apiportal.ns.nl/reisinformatie-api/api/v2/stations');
        \curl_setopt($curl, CURLOPT_POST, 0);
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($curl, CURLOPT_HTTPHEADER, ['Ocp-Apim-Subscription-Key:'.$_ENV['NS_API_PRIMARY_KEY']]);

        $result = (array) \json_decode(\curl_exec($curl), true);
        \curl_close($curl);

        /**
         * @var LocationCategory $notValidCategory
         */
        $notValidCategory = $this->doctrine->getRepository(LocationCategory::class)->find(LocationCategory::NO_LONGER_VALID_ID);

        foreach ($result['payload'] as $station) {
            $category = $this->doctrine->getRepository(LocationCategory::class)->findOneBy(
                ['code' => $station['land']]
            );
            if (null === $category) {
                $category = new LocationCategory();
                $category->code = $category->name = $station['land'];

                $this->doctrine->getManager()->persist($category);
            }

            $location_name = \ucfirst(\strtolower($station['code']));
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $location_name]);
            if (null === $location) {
                $location = new Location();
                $location->name = $location_name;
                $location->category = $category;

                $this->doctrine->getManager()->persist($location);
            }

            $location->latitude = $station['lat'];
            $location->longitude = $station['lng'];
            $location->description = $station['namen']['lang'];

            if (isset($station['eindDatum']) && new \DateTime($station['eindDatum']) < new \DateTime()) {
                $location->category = $notValidCategory;
            } else {
                $location->category = $category;
            }

            $this->doctrine->getManager()->flush();
        }

        return 0;
    }
}
