<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="DateTimeContainerRepository")
 */
class DateTimeContainer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="dateTimeContainers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="SpeedMetric", mappedBy="dateTimeContainer", orphanRemoval=true)
     */
    private $speedMetrics;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $dateTime;

    public function __construct()
    {
        $this->speedMetrics = new ArrayCollection();
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
     * @return Collection|SpeedMetric[]
     */
    public function getSpeedMetrics(): Collection
    {
        return $this->speedMetrics;
    }

    public function addSpeedMetric(SpeedMetric $speedMetric): self
    {
        if (!$this->speedMetrics->contains($speedMetric)) {
            $this->speedMetrics[] = $speedMetric;
            $speedMetric->setDateTimeContainer($this);
        }

        return $this;
    }

    public function removeSpeedMetric(SpeedMetric $speedMetric): self
    {
        if ($this->speedMetrics->contains($speedMetric)) {
            $this->speedMetrics->removeElement($speedMetric);
            // set the owning side to null (unless already changed)
            if ($speedMetric->getDateTimeContainer() === $this) {
                $speedMetric->setDateTimeContainer(null);
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
