<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shift
 *
 * @ORM\Table(name="Shift", indexes={@ORM\Index(name="branch_id", columns={"branch_id"}), @ORM\Index(name="shift_type_id", columns={"shift_type_id"}), @ORM\Index(name="worker_id", columns={"worker_id"})})
 * @ORM\Entity
 */
class Shift
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
     * @var int
     *
     * @ORM\Column(name="start_shift", type="integer", nullable=false)
     */
    private $startShift;

    /**
     * @var int
     *
     * @ORM\Column(name="end_shift", type="integer", nullable=false)
     */
    private $endShift;

    /**
     * @var bool
     *
     * @ORM\Column(name="swapping", type="boolean", nullable=false)
     */
    private $swapping = '0';

    /**
     * @var \Branch
     *
     * @ORM\ManyToOne(targetEntity="Branch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     * })
     */
    private $branch;

    /**
     * @var \ShiftType
     *
     * @ORM\ManyToOne(targetEntity="ShiftType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shift_type_id", referencedColumnName="id")
     * })
     */
    private $shiftType;

    /**
     * @var \Workers
     *
     * @ORM\ManyToOne(targetEntity="Workers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="worker_id", referencedColumnName="id")
     * })
     */
    private $worker;


}
