<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OpeningHoursRepository;

/**
 * @ORM\Entity(repositoryClass=OpeningHoursRepository::class)
 */
class OpeningHours
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="openingHours")
     */
    private ?Shop $shop;

    /**
     * @ORM\Column(type="integer", length=1)
     */
    private int $day;

    /**
     * @ORM\Column(type="datetime", length=5, nullable=true)
     */
    private ?DateTime $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setStart(?DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function toStringFormat(): string
    {
        $dateTimeZone = new DateTimeZone("Europe/Paris");
        return $this->getStart()->setTimezone($dateTimeZone)->format('H\hi').' - '.$this->getEnd()->setTimezone($dateTimeZone)->format('H\hi');
    }
}