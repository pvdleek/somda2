<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaDonDonatie
 *
 * @ORM\Table(name="somda_don_donatie")
 * @ORM\Entity
 */
class SomdaDonDonatie
{
    /**
     * @var int
     *
     * @ORM\Column(name="don_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $donId;

    /**
     * @var int
     *
     * @ORM\Column(name="don_datetime", type="bigint", nullable=false)
     */
    private $donDatetime;

    /**
     * @var int
     *
     * @ORM\Column(name="don_uid", type="bigint", nullable=false)
     */
    private $donUid;

    /**
     * @var int
     *
     * @ORM\Column(name="don_amount", type="bigint", nullable=false)
     */
    private $donAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="don_transaction_id", type="string", length=32, nullable=false)
     */
    private $donTransactionId;

    /**
     * @var int
     *
     * @ORM\Column(name="don_ok", type="bigint", nullable=false)
     */
    private $donOk = '0';


}
