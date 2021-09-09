<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float", precision=2, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true, precision=2, scale=2)
     */
    private $deal_price;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deal_start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deal_end;

    /**
     * @ORM\Column(type="json")
     */
    private $images;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDealPrice(): ?float
    {
        return $this->deal_price;
    }

    public function setDealPrice(?float $deal_price): self
    {
        $this->deal_price = $deal_price;

        return $this;
    }

    public function getDealStart(): ?\DateTimeInterface
    {
        return $this->deal_start;
    }

    public function setDealStart(?\DateTimeInterface $deal_start): self
    {
        $this->deal_start = $deal_start;

        return $this;
    }

    public function getDealEnd(): ?\DateTimeInterface
    {
        return $this->deal_end;
    }

    public function setDealEnd(?\DateTimeInterface $deal_end): self
    {
        $this->deal_end = $deal_end;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images[] = $images;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

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
}