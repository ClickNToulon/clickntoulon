<?php

namespace App\Entity;

use App\Repository\TimeTableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeTableRepository::class)
 */
class TimeTable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $shop_id;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $mon_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $mon_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $mon_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $mon_pm_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $tue_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $tue_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $tue_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $tue_pm_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $wed_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $wed_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $wed_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $wed_pm_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $thu_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $thu_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $thu_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $thu_pm_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fri_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fri_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fri_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fri_pm_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sat_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sat_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sat_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sat_pm_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sun_am_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sun_am_cl;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sun_pm_op;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $sun_pm_cl;

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

    public function getMonAmOp(): ?\DateTimeInterface
    {
        return $this->mon_am_op;
    }

    public function setMonAmOp(?\DateTimeInterface $mon_am_op): self
    {
        $this->mon_am_op = $mon_am_op;

        return $this;
    }

    public function getMonAmCl(): ?\DateTimeInterface
    {
        return $this->mon_am_cl;
    }

    public function setMonAmCl(?\DateTimeInterface $mon_am_cl): self
    {
        $this->mon_am_cl = $mon_am_cl;

        return $this;
    }

    public function getMonPmOp(): ?\DateTimeInterface
    {
        return $this->mon_pm_op;
    }

    public function setMonPmOp(?\DateTimeInterface $mon_pm_op): self
    {
        $this->mon_pm_op = $mon_pm_op;

        return $this;
    }

    public function getMonPmCl(): ?\DateTimeInterface
    {
        return $this->mon_pm_cl;
    }

    public function setMonPmCl(?\DateTimeInterface $mon_pm_cl): self
    {
        $this->mon_pm_cl = $mon_pm_cl;

        return $this;
    }

    public function getTueAmOp(): ?\DateTimeInterface
    {
        return $this->tue_am_op;
    }

    public function setTueAmOp(?\DateTimeInterface $tue_am_op): self
    {
        $this->tue_am_op = $tue_am_op;

        return $this;
    }

    public function getTueAmCl(): ?\DateTimeInterface
    {
        return $this->tue_am_cl;
    }

    public function setTueAmCl(?\DateTimeInterface $tue_am_cl): self
    {
        $this->tue_am_cl = $tue_am_cl;

        return $this;
    }

    public function getTuePmOp(): ?\DateTimeInterface
    {
        return $this->tue_pm_op;
    }

    public function setTuePmOp(?\DateTimeInterface $tue_pm_op): self
    {
        $this->tue_pm_op = $tue_pm_op;

        return $this;
    }

    public function getTuePmCl(): ?\DateTimeInterface
    {
        return $this->tue_pm_cl;
    }

    public function setTuePmCl(?\DateTimeInterface $tue_pm_cl): self
    {
        $this->tue_pm_cl = $tue_pm_cl;

        return $this;
    }

    public function getWedAmOp(): ?\DateTimeInterface
    {
        return $this->wed_am_op;
    }

    public function setWedAmOp(?\DateTimeInterface $wed_am_op): self
    {
        $this->wed_am_op = $wed_am_op;

        return $this;
    }

    public function getWedAmCl(): ?\DateTimeInterface
    {
        return $this->wed_am_cl;
    }

    public function setWedAmCl(?\DateTimeInterface $wed_am_cl): self
    {
        $this->wed_am_cl = $wed_am_cl;

        return $this;
    }

    public function getWedPmOp(): ?\DateTimeInterface
    {
        return $this->wed_pm_op;
    }

    public function setWedPmOp(?\DateTimeInterface $wed_pm_op): self
    {
        $this->wed_pm_op = $wed_pm_op;

        return $this;
    }

    public function getWedPmCl(): ?\DateTimeInterface
    {
        return $this->wed_pm_cl;
    }

    public function setWedPmCl(?\DateTimeInterface $wed_pm_cl): self
    {
        $this->wed_pm_cl = $wed_pm_cl;

        return $this;
    }

    public function getThuAmOp(): ?\DateTimeInterface
    {
        return $this->thu_am_op;
    }

    public function setThuAmOp(?\DateTimeInterface $thu_am_op): self
    {
        $this->thu_am_op = $thu_am_op;

        return $this;
    }

    public function getThuAmCl(): ?\DateTimeInterface
    {
        return $this->thu_am_cl;
    }

    public function setThuAmCl(?\DateTimeInterface $thu_am_cl): self
    {
        $this->thu_am_cl = $thu_am_cl;

        return $this;
    }

    public function getThuPmOp(): ?\DateTimeInterface
    {
        return $this->thu_pm_op;
    }

    public function setThuPmOp(?\DateTimeInterface $thu_pm_op): self
    {
        $this->thu_pm_op = $thu_pm_op;

        return $this;
    }

    public function getThuPmCl(): ?\DateTimeInterface
    {
        return $this->thu_pm_cl;
    }

    public function setThuPmCl(?\DateTimeInterface $thu_pm_cl): self
    {
        $this->thu_pm_cl = $thu_pm_cl;

        return $this;
    }

    public function getFriAmOp(): ?\DateTimeInterface
    {
        return $this->fri_am_op;
    }

    public function setFriAmOp(?\DateTimeInterface $fri_am_op): self
    {
        $this->fri_am_op = $fri_am_op;

        return $this;
    }

    public function getFriAmCl(): ?\DateTimeInterface
    {
        return $this->fri_am_cl;
    }

    public function setFriAmCl(?\DateTimeInterface $fri_am_cl): self
    {
        $this->fri_am_cl = $fri_am_cl;

        return $this;
    }

    public function getFriPmOp(): ?\DateTimeInterface
    {
        return $this->fri_pm_op;
    }

    public function setFriPmOp(?\DateTimeInterface $fri_pm_op): self
    {
        $this->fri_pm_op = $fri_pm_op;

        return $this;
    }

    public function getFriPmCl(): ?\DateTimeInterface
    {
        return $this->fri_pm_cl;
    }

    public function setFriPmCl(?\DateTimeInterface $fri_pm_cl): self
    {
        $this->fri_pm_cl = $fri_pm_cl;

        return $this;
    }

    public function getSatAmOp(): ?\DateTimeInterface
    {
        return $this->sat_am_op;
    }

    public function setSatAmOp(?\DateTimeInterface $sat_am_op): self
    {
        $this->sat_am_op = $sat_am_op;

        return $this;
    }

    public function getSatAmCl(): ?\DateTimeInterface
    {
        return $this->sat_am_cl;
    }

    public function setSatAmCl(?\DateTimeInterface $sat_am_cl): self
    {
        $this->sat_am_cl = $sat_am_cl;

        return $this;
    }

    public function getSatPmOp(): ?\DateTimeInterface
    {
        return $this->sat_pm_op;
    }

    public function setSatPmOp(?\DateTimeInterface $sat_pm_op): self
    {
        $this->sat_pm_op = $sat_pm_op;

        return $this;
    }

    public function getSatPmCl(): ?\DateTimeInterface
    {
        return $this->sat_pm_cl;
    }

    public function setSatPmCl(?\DateTimeInterface $sat_pm_cl): self
    {
        $this->sat_pm_cl = $sat_pm_cl;

        return $this;
    }

    public function getSunAmOp(): ?\DateTimeInterface
    {
        return $this->sun_am_op;
    }

    public function setSunAmOp(?\DateTimeInterface $sun_am_op): self
    {
        $this->sun_am_op = $sun_am_op;

        return $this;
    }

    public function getSunAmCl(): ?\DateTimeInterface
    {
        return $this->sun_am_cl;
    }

    public function setSunAmCl(?\DateTimeInterface $sun_am_cl): self
    {
        $this->sun_am_cl = $sun_am_cl;

        return $this;
    }

    public function getSunPmOp(): ?\DateTimeInterface
    {
        return $this->sun_pm_op;
    }

    public function setSunPmOp(?\DateTimeInterface $sun_pm_op): self
    {
        $this->sun_pm_op = $sun_pm_op;

        return $this;
    }

    public function getSunPmCl(): ?\DateTimeInterface
    {
        return $this->sun_pm_cl;
    }

    public function setSunPmCl(?\DateTimeInterface $sun_pm_cl): self
    {
        $this->sun_pm_cl = $sun_pm_cl;

        return $this;
    }
}
