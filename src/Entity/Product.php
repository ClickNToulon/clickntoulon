<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Shop::class, inversedBy: "products")]
    #[ORM\JoinColumn(nullable: false)]
    private Shop $shop;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description;

    #[ORM\Column(type: Types::FLOAT, precision: 2, scale: 2)]
    private ?float $unitPrice;

    #[ORM\Column(type: Types::FLOAT, precision: 2, scale: 2, nullable: true)]
    private ?float $unitPriceDiscount;

    #[ORM\Column(type: Types::JSON)]
    private ?array $images;

    #[ORM\ManyToOne(targetEntity: ProductType::class, inversedBy: "products")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductType $type;

    public function __construct() {
        $this->images = [];
        $this->unitPriceDiscount = null;
    }

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

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getUnitPriceDiscount(): ?float
    {
        return $this->unitPriceDiscount;
    }

    public function setUnitPriceDiscount(?float $unitPriceDiscount): self
    {
        $this->unitPriceDiscount = $unitPriceDiscount;
        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function getType(): ?ProductType
    {
        return $this->type;
    }

    public function setType(?ProductType $type): self
    {
        $this->type = $type;
        return $this;
    }
}