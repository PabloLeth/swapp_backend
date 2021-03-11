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
     * @ORM\Column(name="start_shift", type="datetime", nullable=false)
     */
    private $startShift;

    /**
     * @var int
     *
     * @ORM\Column(name="end_shift", type="datetime", nullable=false)
     */
    private $endShift;

    /**
     * @var bool
     *
     * @ORM\Column(name="swapping", type="boolean", nullable=false)
     */
    private $swapping = '0';

     /**
     * @var bool
     *
     * @ORM\Column(name="swappable", type="boolean", nullable=false)
     */
    private $swappable = '0';
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartShift(): ?\DateTimeInterface
    {
        return $this->startShift;
    }

    public function setStartShift(\DateTimeInterface $startShift): self
    {
        $this->startShift = $startShift;

        return $this;
    }

    public function getEndShift(): ?\DateTimeInterface
    {
        return $this->endShift;
    }

    public function setEndShift(\DateTimeInterface $endShift): self
    {
        $this->endShift = $endShift;

        return $this;
    }

    public function getSwapping(): ?bool
    {
        return $this->swapping;
    }

    public function setSwapping(bool $swapping): self
    {
        $this->swapping = $swapping;

        return $this;
    }

    public function getSwappable(): ?bool
    {
        return $this->swappable;
    }

    public function setSwappable(bool $swappable): self
    {
        $this->swappable = $swappable;

        return $this;
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getShiftType(): ?ShiftType
    {
        return $this->shiftType;
    }

    public function setShiftType(?ShiftType $shiftType): self
    {
        $this->shiftType = $shiftType;

        return $this;
    }

    public function getWorker(): ?Workers
    {
        return $this->worker;
    }

    public function setWorker(?Workers $worker): self
    {
        $this->worker = $worker;

        return $this;
    }



}
