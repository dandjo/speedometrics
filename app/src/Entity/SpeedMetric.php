<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SpeedMetricRepository")
 */
class SpeedMetric
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $minSpeed;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxSpeed;

    /**
     * @ORM\Column(type="integer")
     */
    private $amountVehicles;

    /**
     * @ORM\ManyToOne(targetEntity="DateTimeContainer", inversedBy="speedMetrics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dateTimeContainer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinSpeed(): ?int
    {
        return $this->minSpeed;
    }

    public function setMinSpeed(int $minSpeed): self
    {
        $this->minSpeed = $minSpeed;

        return $this;
    }

    public function getMaxSpeed(): ?int
    {
        return $this->maxSpeed;
    }

    public function setMaxSpeed(int $maxSpeed): self
    {
        $this->maxSpeed = $maxSpeed;

        return $this;
    }

    public function getAmountVehicles(): ?int
    {
        return $this->amountVehicles;
    }

    public function setAmountVehicles(int $amountVehicles): self
    {
        $this->amountVehicles = $amountVehicles;

        return $this;
    }

    public function getDateTimeContainer(): ?DateTimeContainer
    {
        return $this->dateTimeContainer;
    }

    public function setDateTimeContainer(?DateTimeContainer $dateTimeContainer): self
    {
        $this->dateTimeContainer = $dateTimeContainer;

        return $this;
    }
}
