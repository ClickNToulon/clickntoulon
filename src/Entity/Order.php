<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Exception;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: "`order`")]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id;

    #[ORM\Column(type: Types::STRING, length: 12)]
    private string $orderNumber;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $buyer;

    #[ORM\ManyToOne(targetEntity: Shop::class, inversedBy: "orders")]
    #[ORM\JoinColumn(nullable: false)]
    private Shop $shop;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $day;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    private ArrayCollection|PersistentCollection $products;

    #[ORM\Column(type: Types::JSON)]
    private ?array $quantity;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $total;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $status;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->orderNumber = strtoupper(bin2hex(random_bytes(6)));
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getBuyer(): User
    {
        return $this->buyer;
    }

    public function setBuyer(User $buyer): self
    {
        $this->buyer = $buyer;
        return $this;
    }

    public function getShop(): Shop
    {
        return $this->shop;
    }

    public function setShop(Shop $shop): self
    {
        $this->shop = $shop;
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

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);
        return $this;
    }

    public function getQuantity(): array
    {
        return $this->quantity;
    }

    public function setQuantity(?array $quantity): self
    {
        $this->quantity = $quantity;
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
