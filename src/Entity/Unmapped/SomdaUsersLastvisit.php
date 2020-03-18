<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaUsersLastvisit
 *
 * @ORM\Table(name="somda_users_lastvisit")
 * @ORM\Entity
 */
class SomdaUsersLastvisit
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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=10, nullable=false)
     */
    private $username = '';

    /**
     * @var string
     *
     * @ORM\Column(name="real_name", type="string", length=40, nullable=false)
     */
    private $realName = '';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="lastvisit", type="datetime", nullable=true)
     */
    private $lastvisit;


}
