<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShiftType
 *
 * @ORM\Table(name="shift_type")
 * @ORM\Entity
 */
class ShiftType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shift_type", type="string", length=16, nullable=false)
     */
    private $shiftType;

    /**
     * @var bool
     *
     * @ORM\Column(name="swappable", type="boolean", nullable=false, options={"default"="1"})
     */
    private $swappable = true;


}
