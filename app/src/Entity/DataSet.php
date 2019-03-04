<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataSetRepository")
 */
class DataSet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="dataSets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SpeedCategory", mappedBy="dataSet", orphanRemoval=true)
     */
    private $speedCategories;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $dateTime;

    public function __construct()
    {
        $this->speedCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|SpeedCategory[]
     */
    public function getSpeedCategories(): Collection
    {
        return $this->speedCategories;
    }

    public function addSpeedCategory(SpeedCategory $speedCategory): self
    {
        if (!$this->speedCategories->contains($speedCategory)) {
            $this->speedCategories[] = $speedCategory;
            $speedCategory->setDataSet($this);
        }

        return $this;
    }

    public function removeSpeedCategory(SpeedCategory $speedCategory): self
    {
        if ($this->speedCategories->contains($speedCategory)) {
            $this->speedCategories->removeElement($speedCategory);
            // set the owning side to null (unless already changed)
            if ($speedCategory->getDataSet() === $this) {
                $speedCategory->setDataSet(null);
            }
        }

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
