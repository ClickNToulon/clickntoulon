<?php

namespace App\Domain\Product;

use App\Domain\Shop\Shop;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
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

    #[ORM\OneToMany(mappedBy: "product", targetEntity: PriceHistory::class)]
    protected ArrayCollection|PersistentCollection $priceHistory;

    #[ORM\Column(type: Types::JSON)]
    private ?array $images;

    #[ORM\ManyToOne(targetEntity: ProductType::class, inversedBy: "products")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductType $type;

    public function __construct() {
        $this->priceHistory = new ArrayCollection();
        $this->images = [];
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

    /**
     * @return Collection
     */
    public function getPriceHistory(): Collection
    {
        return $this->priceHistory;
    }

    /**
     * @param PriceHistory $priceHistory
     * @return $this
     */
    public function addPriceHistory(PriceHistory $priceHistory): self
    {
        if (!$this->priceHistory->contains($priceHistory)) {
            $this->priceHistory[] = $priceHistory;
            $priceHistory->setProduct($this);
        }

        return $this;
    }

    /**
     * @param PriceHistory $priceHistory
     * @return $this
     */
    public function removePriceHistory(PriceHistory $priceHistory): self
    {
        if ($this->priceHistory->contains($priceHistory)) {
            $this->priceHistory->removeElement($priceHistory);
            // set the owning side to null (unless already changed)
            if ($priceHistory->getProduct() === $this) {
                $priceHistory->setProduct(null);
            }
        }

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