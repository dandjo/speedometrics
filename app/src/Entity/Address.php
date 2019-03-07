<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zip;

    /**
     * @ORM\OneToMany(targetEntity="DateTimeContainer", mappedBy="address", orphanRemoval=true)
     */
    private $dateTimeContainers;

    public function __construct()
    {
        $this->dateTimeContainers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * @return Collection|DateTimeContainer[]
     */
    public function getDateTimeContainers(): Collection
    {
        return $this->dateTimeContainers;
    }

    public function addDateTimeContainer(DateTimeContainer $dateTimeContainer): self
    {
        if (!$this->dateTimeContainers->contains($dateTimeContainer)) {
            $this->dateTimeContainers[] = $dateTimeContainer;
            $dateTimeContainer->setAddress($this);
        }

        return $this;
    }

    public function removeDateTimeContainer(DateTimeContainer $dateTimeContainer): self
    {
        if ($this->dateTimeContainers->contains($dateTimeContainer)) {
            $this->dateTimeContainers->removeElement($dateTimeContainer);
            // set the owning side to null (unless already changed)
            if ($dateTimeContainer->getAddress() === $this) {
                $dateTimeContainer->setAddress(null);
            }
        }

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function toString(): string
    {
        return sprintf('%s %s, %s %s',
            $this->getStreet(),
            $this->getNumber(),
            $this->getZip(),
            $this->getCity()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'street' => $this->getStreet(),
            'number' => $this->getNumber(),
            'city' => $this->getCity(),
            'zip' => $this->getZip(),
            'serialized' => $this->toString(),
        ];
    }
}
