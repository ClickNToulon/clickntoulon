<?php

namespace App\Entity;

use App\Repository\WorkingHoursRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkingHoursRepository::class)
 */
class WorkingHours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $TimeOpen;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $TimeClose;

    /**
     * @ORM\ManyToOne(targetEntity=WorkingDays::class, inversedBy="workingHours")
     */
    private ?WorkingDays $workingDays;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeOpen(): ?DateTimeInterface
    {
        return $this->TimeOpen;
    }

    public function setTimeOpen(DateTimeInterface $TimeOpen): self
    {
        $this->TimeOpen = $TimeOpen;

        return $this;
    }

    public function getTimeClose(): ?DateTimeInterface
    {
        return $this->TimeClose;
    }

    public function setTimeClose(DateTimeInterface $TimeClose): self
    {
        $this->TimeClose = $TimeClose;

        return $this;
    }

    public function getWorkingDays(): ?WorkingDays
    {
        return $this->workingDays;
    }

    public function setWorkingDays(?WorkingDays $workingDays): self
    {
        $this->workingDays = $workingDays;

        return $this;
    }
}
