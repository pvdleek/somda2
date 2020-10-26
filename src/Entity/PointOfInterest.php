<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="poi_point_of_interest", indexes={
 *     @ORM\Index(name="IDX_poi_loc_id", columns={"poi_loc_id"}),
 *     @ORM\Index(name="IDX_poi_section_1_loc_id", columns={"poi_section_1_loc_id"}),
 *     @ORM\Index(name="IDX_poi_section_2_loc_id", columns={"poi_section_2_loc_id"}),
 *     @ORM\Index(name="IDX_poi_routes_loc_id", columns={"poi_routes_loc_id"}),
 *     @ORM\Index(name="IDX_poi_pon_id", columns={"poi_pon_id"}),
 * })
 * @ORM\Entity
 */
class PointOfInterest
{
    /**
     * @var int|null
     * @ORM\Column(name="poi_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="poi_name", type="string", length=50, nullable=false)
     */
    public string $name = '';

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="pois")
     * @ORM\JoinColumn(name="poi_loc_id", referencedColumnName="loc_id")
     */
    public Location $location;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="poi_section_1_loc_id", referencedColumnName="loc_id")
     */
    public Location $locationSection1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="poi_section_2_loc_id", referencedColumnName="loc_id")
     */
    public Location $locationSection2;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="poi_routes_loc_id", referencedColumnName="loc_id")
     */
    public Location $locationForRoutes;

    /**
     * @var string|null
     * @ORM\Column(name="poi_kilometre", type="string", length=25, nullable=true)
     */
    public ?string $kilometre;

    /**
     * @var string|null
     * @ORM\Column(name="poi_gps", type="string", length=25, nullable=true)
     */
    public ?string $gps;

    /**
     * @var string|null
     * @ORM\Column(name="poi_sun_position_winter", type="string", length=50, nullable=true)
     */
    public ?string $sunPositionWinter;

    /**
     * @var string|null
     * @ORM\Column(name="poi_sun_position_summer", type="string", length=50, nullable=true)
     */
    public ?string $sunPositionSummer;

    /**
     * @var string|null
     * @ORM\Column(name="poi_google_url", type="string", length=200, nullable=true)
     */
    public ?string $googleUrl;

    /**
     * @var string
     * @ORM\Column(name="poi_photo", type="string", length=25, nullable=false, options={"default"="geen_foto.jpg"})
     */
    public string $photo = 'geen_foto.jpg';

    /**
     * @var PointOfInterestCategory
     * @ORM\ManyToOne(targetEntity="PointOfInterestCategory", inversedBy="pois")
     * @ORM\JoinColumn(name="poi_pon_id", referencedColumnName="pon_id")
     */
    public PointOfInterestCategory $category;

    /**
     * @var PointOfInterestText
     * @ORM\OneToOne(targetEntity="PointOfInterestText", mappedBy="poi")
     */
    public PointOfInterestText $text;
}
