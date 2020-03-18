<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaUsersOnlineid
 *
 * @ORM\Table(name="somda_users_onlineid")
 * @ORM\Entity
 */
class SomdaUsersOnlineid
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="onlineid", type="string", length=32, nullable=false)
     */
    private $onlineid;

    /**
     * @var int
     *
     * @ORM\Column(name="expire_datetime", type="bigint", nullable=false)
     */
    private $expireDatetime = '0';


}
