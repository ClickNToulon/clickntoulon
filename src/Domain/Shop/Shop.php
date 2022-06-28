<?php

namespace App\Domain\Shop;

use App\Domain\Auth\User;
use App\Domain\Buyer\Order;
use App\Domain\Product\Product;
use App\Helper\Day;
use Cocur\Slugify\Slugify;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Exception;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[ORM\Entity(repositoryClass: ShopRepository::class)]
class Shop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: Types::TEXT)]
    private string $address;

    #[ORM\Column(type: Types::STRING, length: 5)]
    private string $postalCode;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $city;

    #[ORM\Column(type: Types::INTEGER, length: 255, nullable: true)]
    private ?int $phone;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $email;

    #[ORM\Column(type: Types::STRING, length: 225, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $updatedAt;

    #[ORM\ManyToMany(targetEntity: Payment::class)]
    private ArrayCollection|PersistentCollection $payments;

    #[ORM\Column(type: Types::TEXT)]
    private string $image;

    #[ORM\OneToMany(mappedBy: "shop", targetEntity: Order::class, orphanRemoval: true)]
    private ArrayCollection|PersistentCollection $orders;

    #[ORM\OneToMany(mappedBy: "shop", targetEntity: Product::class)]
    private ArrayCollection|PersistentCollection $products;

    #[ORM\ManyToOne(targetEntity: Tag::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Tag $tag;

    #[ORM\Column(type: Types::INTEGER)]
    private int $status;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isBanned = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: "shop", targetEntity: OpeningHours::class)]
    #[ORM\OrderBy(["day" => "ASC", "start" => "ASC"])]
    private ArrayCollection|PersistentCollection $openingHours;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt = new DateTime('now', new DateTimeZone("Europe/Paris"));
        $this->updatedAt = new DateTime('now', new DateTimeZone("Europe/Paris"));
        $this->payments = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->openingHours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
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

    public function getSlug(): ?string
    {
        return (new Slugify())->slugify($this->name);
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function setTag(Tag $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
        }
        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        $this->payments->removeElement($payment);
        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setShop($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getShop() === $this) {
                $order->setShop(null);
            }
        }
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
            $product->setShop($this);
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getShop() === $this) {
                $product->setShop(null);
            }
        }
        return $this;
    }

    public function getOpeningHours(): Collection
    {
        return $this->openingHours;
    }

    public function addOpeningHour(OpeningHours $openingHour): self
    {
        if (!$this->openingHours->contains($openingHour)) {
            $this->openingHours[] = $openingHour;
            $openingHour->setShop($this);
        }
        return $this;
    }

    public function removeOpeningHour(OpeningHours $openingHour): self
    {
        if ($this->openingHours->contains($openingHour)) {
            $this->openingHours->removeElement($openingHour);
            // set the owning side to null (unless already changed)
            if ($openingHour->getShop() === $this) {
                $openingHour->setShop(null);
            }
        }
        return $this;
    }

    /** Génère un tableau associatif sur une semaine de couple jour → horaires d'ouverture */
    public function getFormattedWeekOpeningHours(): array
    {
        $weekOpenHours = $this->getOpeningHours();
        $weekDays = [];
        // regroupe les différents horaires du même jour sous la même clé
        foreach ($weekOpenHours as $openingHour) {
            /** @var OpeningHours $openingHour */
            if (null !== $openingHour->getStart() | null !== $openingHour->getEnd()) {
                $weekDays[$openingHour->getDay()][] = $openingHour->toStringFormat();
            } else {
                $weekDays[$openingHour->getDay()][] = 'Fermé';
            }
        }
        return array_combine(Day::getWeekDays(), $this->dayOpeningHoursToString($weekDays));
    }

    /** Renvoie le tableau après avoir éclaté les tableaux de valeur associé à chaque clé en une chaine de caractère */
    private function dayOpeningHoursToString(array $dayOpeningHours): array
    {
        foreach ($dayOpeningHours as $key => $value) {
            if($value[0] == 'Fermé' && $value[1] == 'Fermé') {
                $value = array_slice($value, 1, 1);
            } elseif ($value[0] == 'Fermé') {
                $value = array_reverse($value);
            }
            $value = implode(', ', $value);
            $dayOpeningHours[$key] = $value;
        }
        return $dayOpeningHours;
    }

    public function __toString()
    {
        return $this->name;
    }
}
