<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpeedCategoryRepository")
 */
class SpeedCategory
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
    private $rangeFrom;

    /**
     * @ORM\Column(type="integer")
     */
    private $rangeTo;

    /**
     * @ORM\Column(type="integer")
     */
    private $amountVehicles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSet", inversedBy="speedCategories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataSet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRangeFrom(): ?int
    {
        return $this->rangeFrom;
    }

    public function setRangeFrom(int $rangeFrom): self
    {
        $this->rangeFrom = $rangeFrom;

        return $this;
    }

    public function getRangeTo(): ?int
    {
        return $this->rangeTo;
    }

    public function setRangeTo(int $rangeTo): self
    {
        $this->rangeTo = $rangeTo;

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

    public function getDataSet(): ?DataSet
    {
        return $this->dataSet;
    }

    public function setDataSet(?DataSet $dataSet): self
    {
        $this->dataSet = $dataSet;

        return $this;
    }
}
