<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
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
    private ?int $basket_id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $buyer_id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $shop_id;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $day;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $time_begin;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $time_end;

    /**
     * @ORM\Column(type="text")
     */
    private string $products_id;

    /**
     * @ORM\Column(type="text")
     */
    private string $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $status;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $number;

    public function __construct()
    {
        $this->number = strtoupper(bin2hex(random_bytes(6)));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBasketId(): ?int
    {
        return $this->basket_id;
    }

    public function setBasketId(int $basket_id): self
    {
        $this->basket_id = $basket_id;

        return $this;
    }

    public function getBuyerId(): ?int
    {
        return $this->buyer_id;
    }

    public function setBuyerId(int $buyer_id): self
    {
        $this->buyer_id = $buyer_id;

        return $this;
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

    public function getDay(): ?DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getTimeBegin(): ?DateTimeInterface
    {
        return $this->time_begin;
    }

    public function setTimeBegin(?DateTimeInterface $time_begin): self
    {
        $this->time_begin = $time_begin;

        return $this;
    }

    public function getTimeEnd(): ?DateTimeInterface
    {
        return $this->time_end;
    }

    public function setTimeEnd(?DateTimeInterface $time_end): self
    {
        $this->time_end = $time_end;

        return $this;
    }

    public function getProductsId(): ?string
    {
        return $this->products_id;
    }

    public function setProductsId(string $products_id): self
    {
        $this->products_id = $products_id;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

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
}
