<?php

namespace App\Entity;

use App\Classes\Day;
use App\Repository\ShopRepository;
use Cocur\Slugify\Slugify;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
 */
class Shop
{
    /*const Tag = [
        0 => "Boulangerie - Pâtisserie",
        1 => "Boucher",
        2 => "Bijouterie - Horlogerie",
        3 => "Épicerie",
        4 => "Quincaillerie",
        5 => "Librairie",
        6 => "Musée",
        7 => "Fleuriste",
        8 => "Pharmacie",
        9 => "Poisonnerie",
        10 => 'Fromagerie',
        11 => 'Chocolaterie',
        12 =>'Restaurant',
        13 => "Brasserie",
        14 => "Station-Service",
        15 => "Crèmerie",
        16 => "Droguerie",
        17 => "Papeterie",
        18 => "Friperie"
    ];*/

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private User $owner;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="text")
     */
    private string $address;

    /**
     * @ORM\Column(type="integer")
     */
    private int $postalCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Payment::class)
     */
    private $payments;

    /**
     * @ORM\Column(type="text")
     */
    private string $image;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="shop", orphanRemoval=true)
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="shop", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="shop")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=Tag::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Tag $tag;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isBanned = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=OpeningHours::class, mappedBy="shop")
     * @ORM\OrderBy({"day" = "ASC", "start" = "ASC"})
     */
    private $openingHours;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
        $this->createdAt = new DateTime('now', $dateTimeZoneFrance);
        $this->updatedAt = new DateTime('now', $dateTimeZoneFrance);
        $this->payments = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

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

    /**
     * @return Collection|Payment[]
     */
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

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setShop($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getShop() === $this) {
                $category->setShop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
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

    /**
     * @return Collection|Product[]
     */
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

    /**
     * @return Collection|OpeningHours[]
     */
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

    /**
     * Génère un tableau associatif sur une semaine de couple jour => horaires d'ouverture
     *
     */
    public function getFormattedWeekOpeningHours()
    {
        $weekOpenHours = $this->getOpeningHours();
        $weekDays = [];

        // regroupe les différents horaires du même jour sous la même clé
        foreach ($weekOpenHours as $openingHour) {
            /**@var $openingHour OpeningHours */
            if (null !== $openingHour->getStart() | null !== $openingHour->getEnd()) {
                $weekDays[$openingHour->getDay()][] = $openingHour->toStringFormat();
            } else {
                $weekDays[$openingHour->getDay()][] = 'Fermé';
            }
        }

        return array_combine(Day::getWeekDays(), $this->dayOpeningHoursToString($weekDays));
    }

    /**
     * Renvoie le tableau après avoir éclaté les tableaux de valeur associé à chaque clé en une chaine de
     * caractère
     *
     * @param array $dayOpeningHours
     *
     * @return array
     */
    private function dayOpeningHoursToString(array $dayOpeningHours): array
    {
        foreach ($dayOpeningHours as $key => $value) {
            $value = implode(', ', $value);
            $dayOpeningHours[$key] = $value;
        }

        return $dayOpeningHours;
    }

}
