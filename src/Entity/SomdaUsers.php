<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaUsers
 *
 * @ORM\Table(name="somda_users", indexes={@ORM\Index(name="idx_49053_uname", columns={"username"})})
 * @ORM\Entity
 */
class SomdaUsers
{
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="active", type="bigint", nullable=false)
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="spots_ok", type="bigint", nullable=false)
     */
    private $spotsOk = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=10, nullable=false)
     */
    private $username = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=false)
     */
    private $password = '';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(name="cookie_ok", type="string", length=3, nullable=false)
     */
    private $cookieOk = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="actkey", type="string", length=32, nullable=false)
     */
    private $actkey = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="date", nullable=false)
     */
    private $regdate;


}
