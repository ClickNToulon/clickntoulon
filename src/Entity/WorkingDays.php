<?php

namespace App\Entity;

use App\Repository\WorkingDaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkingDaysRepository::class)
 */
class WorkingDays
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $shop_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $day;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $is_closed;

    /**
     * @ORM\OneToMany(targetEntity=WorkingHours::class, mappedBy="workingDays")
     */
    private ArrayCollection $working_hours;

    public function __construct()
    {
        $this->working_hours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopId(): ?int
    {
        return $this->shop_id;
    }

    public function setShopId(int $shop_id): self
    {
        $this->shop_id = $shop_id;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getIsClosed(): ?bool
    {
        return $this->is_closed;
    }

    public function setIsClosed(bool $is_closed): self
    {
        $this->is_closed = $is_closed;

        return $this;
    }

    /**
     * @return Collection|WorkingHours[]
     */
    public function getWorkingHours(): Collection
    {
        return $this->working_hours;
    }

    public function addWorkingHour(WorkingHours $workingHour): self
    {
        if (!$this->working_hours->contains($workingHour)) {
            $this->working_hours[] = $workingHour;
            $workingHour->setWorkingDays($this);
        }

        return $this;
    }

    public function removeWorkingHour(WorkingHours $workingHour): self
    {
        if ($this->working_hours->removeElement($workingHour)) {
            // set the owning side to null (unless already changed)
            if ($workingHour->getWorkingDays() === $this) {
                $workingHour->setWorkingDays(null);
            }
        }

        return $this;
    }
}
