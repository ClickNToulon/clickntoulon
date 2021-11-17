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
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?User $owner;

    /**
     * @ORM\Column(type="array")
     */
    private ?array $products;

    /**
     * @ORM\Column(type="array")
     */
    private ?array $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnerId(): User
    {
        return $this->owner;
    }

    public function setOwnerId(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getQuantity(): array
    {
        return $this->quantity;
    }

    public function setQuantity(array $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}