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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShiftType(): ?string
    {
        return $this->shiftType;
    }

    public function setShiftType(string $shiftType): self
    {
        $this->shiftType = $shiftType;

        return $this;
    }



}
