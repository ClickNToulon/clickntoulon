<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BasketRepository::class)
 */
class Basket
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
    private ?int $owner_id;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $products_id;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Shop::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $shop_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnerId(): ?int
    {
        return $this->owner_id;
    }

    public function setOwnerId(int $owner_id): self
    {
        $this->owner_id = $owner_id;

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

    public function getShopId(): ?Shop
    {
        return $this->shop_id;
    }

    public function setShopId(?Shop $shop_id): self
    {
        $this->shop_id = $shop_id;

        return $this;
    }
}
